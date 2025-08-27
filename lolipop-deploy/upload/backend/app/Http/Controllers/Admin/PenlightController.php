<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use App\Models\PenlightPreset;
use App\Models\ActiveArtist;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PenlightController extends Controller
{

    // 管理画面表示
    public function index()
    {
        $artists = Artist::active()->orderBy('artist_name')->get();
        return view('admin.penlight.index', compact('artists'));
    }

    // アーティスト一覧
    public function artists(Request $request): JsonResponse
    {
        $artists = Artist::active()
            ->when($request->search, function ($query, $search) {
                return $query->search($search);
            })
            ->withCount('songs')
            ->orderBy('artist_name')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $artists
        ]);
    }

    // 楽曲一覧
    public function songs(Request $request): JsonResponse
    {
        \Log::info('Penlight songs API called', [
            'artist_id' => $request->artist_id,
            'request_all' => $request->all()
        ]);

        $songs = Song::active()
            ->with('artist')
            ->withCount('penlightPresets')
            ->when($request->artist_id, function ($query, $artistId) {
                \Log::info('Filtering by artist_id: ' . $artistId);
                return $query->byArtist($artistId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->search($search);
            })
            ->orderBy('song_name');

        \Log::info('SQL Query: ' . $songs->toSql());
        \Log::info('SQL Bindings: ' . json_encode($songs->getBindings()));

        // ペンライト制御では全ての楽曲を表示するため、ページネーションを無効化
        $songs = $songs->get();

        \Log::info('Songs found: ' . $songs->count());

        return response()->json([
            'success' => true,
            'data' => $songs
        ]);
    }

    // プリセット一覧
    public function presets(Request $request): JsonResponse
    {
        $presets = PenlightPreset::with('song.artist')
            ->when($request->song_id, function ($query, $songId) {
                return $query->where('song_id', $songId);
            })
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $presets
        ]);
    }

    // プリセット詳細
    public function preset($id): JsonResponse
    {
        $preset = PenlightPreset::with('song.artist')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $preset
        ]);
    }

    // プリセット作成
    public function storePreset(Request $request): JsonResponse
    {
        $request->validate([
            'song_id' => 'required|exists:songs,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pattern' => 'required|string|in:solid,blink,fade,wave,pulse,rainbow,strobe',
            'color' => 'required|string|regex:/^(#[0-9A-F]{6}|rgba\(0,0,0,0\))$/i',
            'brightness' => 'required|integer|min:0|max:100',
            'bpm' => 'required|integer|min:60|max:200',
            'color_scheme' => 'nullable|array',
            'timing' => 'nullable|array',
            'effects' => 'nullable|array',
            'audio_sync' => 'nullable|array',
            'is_default' => 'boolean'
        ]);

        // プリセット数制限チェック
        $presetCount = PenlightPreset::where('song_id', $request->song_id)->count();
        if ($presetCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'プリセットは10個までしか作成できません'
            ], 422);
        }

        $preset = PenlightPreset::create($request->all());

        if ($request->is_default) {
            $preset->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'data' => $preset->load('song.artist'),
            'message' => 'プリセットを作成しました'
        ]);
    }

    // プリセット更新
    public function updatePreset(Request $request, $id): JsonResponse
    {
        $preset = PenlightPreset::with('song')->findOrFail($id);

        \Log::info('プリセット更新リクエスト', [
            'preset_id' => $id,
            'request_data' => $request->all(),
            'current_preset_color' => $preset->color,
            'song_color' => $preset->song->song_color ?? 'NULL'
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pattern' => 'required|string|in:solid,blink,fade,wave,pulse,rainbow,strobe',
            'brightness' => 'required|integer|min:0|max:100',
            'bpm' => 'required|integer|min:60|max:200',
            'intensity' => 'required|string|in:low,medium,high',
            'is_active' => 'boolean',
            'audio_sync' => 'boolean',
            'music_intensity' => 'nullable|numeric|min:0|max:1',
            'noise_gate_threshold' => 'nullable|numeric|min:0|max:1',
            'frequency_low' => 'nullable|integer|min:20|max:2000',
            'frequency_high' => 'nullable|integer|min:100|max:20000',
            'penlight_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        // 楽曲の色を継承（編集不可）
        $updateData = $request->except(['color']);
        $updateData['color'] = $preset->song->song_color ?? 'rgba(0,0,0,0)';

        \Log::info('プリセット更新データ', [
            'update_data' => $updateData,
            'final_color' => $updateData['color']
        ]);

        $preset->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $preset->load('song.artist'),
            'message' => 'プリセットを更新しました'
        ]);
    }

    // プリセット削除
    public function destroyPreset($id): JsonResponse
    {
        $preset = PenlightPreset::findOrFail($id);
        $preset->delete();

        return response()->json([
            'success' => true,
            'message' => 'プリセットを削除しました'
        ]);
    }

    // プリセットアクティベート
    public function activatePreset($id): JsonResponse
    {
        try {
            $preset = PenlightPreset::with('song.artist')->findOrFail($id);
            
            // 他のプリセットを非アクティブ化
            PenlightPreset::where('is_active', true)->update(['is_active' => false]);
            
            // 指定されたプリセットをアクティブ化
            $preset->update([
                'is_active' => true,
                'activated_at' => now()
            ]);
            
            // セッションに配信データを保存
            session(['active_preset' => [
                'id' => $preset->id,
                'name' => $preset->name,
                'description' => $preset->description,
                'pattern' => $preset->pattern,
                'brightness' => $preset->brightness,
                'bpm' => $preset->bpm,
                'intensity' => $preset->intensity,
                'audio_sync' => $preset->audio_sync,
                'music_intensity' => $preset->music_intensity,
                'noise_gate_threshold' => $preset->noise_gate_threshold,
                'frequency_low' => $preset->frequency_low,
                'frequency_high' => $preset->frequency_high,
                'penlight_color' => $preset->penlight_color,
                'color' => $preset->song->song_color ?? '#ff0000',
                'activated_at' => $preset->activated_at,
                'song' => [
                    'id' => $preset->song->id,
                    'title' => $preset->song->song_name,
                    'artist' => $preset->song->artist->artist_name ?? 'Unknown Artist'
                ]
            ]]);
            
            \Log::info('プリセット配信開始', [
                'preset_id' => $preset->id,
                'preset_name' => $preset->name,
                'song_name' => $preset->song->song_name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "プリセット「{$preset->name}」の配信を開始しました",
                'data' => $preset
            ]);
            
        } catch (\Exception $e) {
            \Log::error('プリセット配信開始エラー: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'プリセットの配信開始に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // プリセットデアクティベート
    public function deactivatePreset($id): JsonResponse
    {
        try {
            $preset = PenlightPreset::findOrFail($id);
            
            // すべてのプリセットを非アクティブ化
            PenlightPreset::where('is_active', true)->update(['is_active' => false]);
            
            // セッションから配信データを削除
            session()->forget('active_preset');
            
            \Log::info('プリセット配信停止', [
                'preset_id' => $preset->id,
                'preset_name' => $preset->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "プリセット「{$preset->name}」の配信を停止しました"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('プリセット配信停止エラー: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'プリセットの配信停止に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 全てのプリセットを非アクティブ化
    public function deactivateAllPresets(): JsonResponse
    {
        try {
            // すべてのプリセットを非アクティブ化
            PenlightPreset::where('is_active', true)->update(['is_active' => false]);
            
            // セッションから配信データを削除
            session()->forget('active_preset');
            
            \Log::info('全てのプリセットを非アクティブ化しました');
            
            return response()->json([
                'success' => true,
                'message' => '全てのプリセットの配信を停止しました'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('プリセット非アクティブ化エラー: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'プリセットの非アクティブ化に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // プリセット順序変更
    public function reorderPresets(Request $request): JsonResponse
    {
        $request->validate([
            'presets' => 'required|array',
            'presets.*.id' => 'required|exists:penlight_presets,id',
            'presets.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->presets as $presetData) {
            PenlightPreset::where('id', $presetData['id'])
                ->update(['order' => $presetData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'プリセットの順序を変更しました'
        ]);
    }

    // プリセット一括作成
    public function createPresets(Request $request): JsonResponse
    {
        $request->validate([
            'song_id' => 'required|exists:songs,id'
        ]);

        $songId = $request->song_id;
        $song = Song::with('artist')->findOrFail($songId);
        
        // 既存のプリセット数を確認
        $existingCount = PenlightPreset::where('song_id', $songId)->count();
        $maxPresets = 10;
        
        if ($existingCount >= $maxPresets) {
            return response()->json([
                'success' => false,
                'message' => "プリセットは既に{$maxPresets}個存在します"
            ], 422);
        }
        
        // 作成するプリセット数
        $createCount = $maxPresets - $existingCount;
        
        // 楽曲の基本設定を取得（適切な型変換付き、色は楽曲編集でのみ変更可能）
        $baseSettings = [
            'song_id' => $songId,
            'pattern' => $song->song_pattern ?? 'solid',
            'color' => $song->song_color ?? 'rgba(0,0,0,0)', // 楽曲の色を継承（編集不可）
            'penlight_color' => $song->penlight_color ?? 'rgba(0,0,0,0)', // ペンライト調整色を継承（編集不可）
            'brightness' => (int)($song->song_brightness ?? 50),
            'bpm' => (int)($song->song_bpm ?? 120),
            'intensity' => $this->convertIntensityToInt($song->song_intensity ?? 50),
            'color_scheme' => $this->convertColorSchemeToJson($song->song_color_scheme ?? 'default'),
            'audio_sync' => (bool)($song->penlight_audio_sync ?? false),
            'music_intensity' => (float)($song->penlight_music_intensity ?? 0.5),
            'noise_gate_threshold' => (float)($song->penlight_noise_gate_threshold ?? 0.1),
            'frequency_low' => (int)($song->penlight_frequency_low ?? 60),
            'frequency_high' => (int)($song->penlight_frequency_high ?? 2000),
            'is_active' => true
        ];
        
        $createdPresets = [];
        
        for ($i = 1; $i <= $createCount; $i++) {
            $presetData = array_merge($baseSettings, [
                'name' => "プリセット{$i}",
                'description' => "{$song->song_name}のプリセット{$i}",
                'order' => $existingCount + $i
            ]);
            
            $preset = PenlightPreset::create($presetData);
            $createdPresets[] = $preset;
        }
        
        \Log::info("プリセット作成完了", [
            'song_id' => $songId,
            'song_name' => $song->song_name,
            'created_count' => $createCount,
            'total_presets' => $existingCount + $createCount
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "{$createCount}個のプリセットを作成しました",
            'data' => [
                'created_count' => $createCount,
                'total_presets' => $existingCount + $createCount,
                'presets' => $createdPresets
            ]
        ]);
    }

    // 強度文字列を整数に変換
    private function convertIntensityToInt($intensity)
    {
        if (is_numeric($intensity)) {
            return (int)$intensity;
        }
        
        // 文字列の場合の変換
        $intensityMap = [
            'low' => 25,
            'medium' => 50,
            'high' => 75,
            'very_high' => 100
        ];
        
        return $intensityMap[strtolower($intensity)] ?? 50;
    }

    // カラースキームをJSON形式に変換
    private function convertColorSchemeToJson($colorScheme)
    {
        // 既にJSON形式の場合はそのまま返す
        if (is_array($colorScheme)) {
            return $colorScheme;
        }
        
        // 文字列の場合の変換
        $colorSchemeMap = [
            'default' => ['type' => 'default', 'colors' => ['#ffffff']],
            'artist' => ['type' => 'artist', 'colors' => ['#ffffff']],
            'rainbow' => ['type' => 'rainbow', 'colors' => ['#ff0000', '#ff7f00', '#ffff00', '#00ff00', '#0000ff', '#4b0082', '#9400d3']],
            'gradient' => ['type' => 'gradient', 'colors' => ['#ff0000', '#00ff00', '#0000ff']],
            'monochrome' => ['type' => 'monochrome', 'colors' => ['#ffffff']]
        ];
        
        return $colorSchemeMap[strtolower($colorScheme)] ?? $colorSchemeMap['default'];
    }


}
