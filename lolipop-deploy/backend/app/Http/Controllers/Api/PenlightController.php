<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenlightController extends Controller
{
    /**
     * 現在の配信状況を取得
     */
    public function getStreamingStatus()
    {
        try {
            // デバッグ用：セッション情報をログに出力
            \Log::info('Penlight API - Session check', [
                'session_id' => session()->getId(),
                'has_active_preset' => session()->has('active_preset'),
                'all_session_keys' => array_keys(session()->all())
            ]);
            
            // データベースから現在アクティブなプリセットを取得
            $activePreset = \App\Models\PenlightPreset::with('song.artist')
                ->where('is_active', true)
                ->first();
            
            $streamingStatus = [
                'is_streaming' => false,
                'preset' => null
            ];
            
            if ($activePreset) {
                $streamingStatus = [
                    'is_streaming' => true,
                    'preset' => [
                        'id' => $activePreset->id,
                        'name' => $activePreset->name,
                        'description' => $activePreset->description,
                        'pattern' => $activePreset->pattern,
                        'brightness' => $activePreset->brightness,
                        'bpm' => $activePreset->bpm,
                        'intensity' => $activePreset->intensity,
                        'audio_sync' => $activePreset->audio_sync,
                        'music_intensity' => $activePreset->music_intensity,
                        'noise_gate_threshold' => $activePreset->noise_gate_threshold,
                        'frequency_low' => $activePreset->frequency_low,
                        'frequency_high' => $activePreset->frequency_high,
                        'penlight_color' => $activePreset->penlight_color,
                        'color' => $activePreset->song->song_color ?? '#ff0000',
                        'activated_at' => $activePreset->activated_at,
                        'song' => [
                            'id' => $activePreset->song->id,
                            'title' => $activePreset->song->song_name,
                            'artist' => $activePreset->song->artist->artist_name ?? 'Unknown Artist'
                        ]
                    ]
                ];
                
                \Log::info('Penlight API - Active preset found', [
                    'preset_id' => $activePreset->id,
                    'preset_name' => $activePreset->name
                ]);
            } else {
                \Log::info('Penlight API - No active preset found');
            }
            
            return response()->json([
                'success' => true,
                'data' => $streamingStatus
            ]);
        } catch (\Exception $e) {
            \Log::error('Penlight API - Error getting streaming status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '配信状況の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
