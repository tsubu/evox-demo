<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\QrUseList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    /**
     * QRコード読み取り処理
     */
    public function claim(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            // QRコードを検索
            $qrCode = QrCode::where('qr_code', $request->code)
                           ->where('qr_is_active', true)
                           ->first();

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => '無効なQRコードです。'
                ], 404);
            }

            // ユーザー認証チェック
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインが必要です。'
                ], 401);
            }

            $user = Auth::user();

            // 既に使用済みかチェック
            $existingUse = QrUseList::where('qruse_user_id', $user->id)
                                   ->where('qruse_qr_code_id', $qrCode->id)
                                   ->first();

            if ($existingUse) {
                return response()->json([
                    'success' => false,
                    'message' => 'このQRコードは既に使用済みです。',
                    'data' => [
                        'code' => $qrCode->qr_code,
                        'title' => $qrCode->qr_title,
                        'description' => $qrCode->qr_description,
                        'points' => $existingUse->qruse_points_earned,
                        'already_used' => true,
                        'used_at' => $existingUse->qruse_created_at
                    ]
                ], 400);
            }

            // ライブイベント（標準的なQRコード）の場合
            if ($qrCode->qr_is_liveevent) {
                return $this->processReward($user, $qrCode);
            }

            // ゲームグッズ（オプション機能付き）の場合
            if ($qrCode->qr_options_enabled) {
                return response()->json([
                    'success' => true,
                    'message' => 'オプション選択画面を表示します。',
                    'data' => [
                        'qr_code_id' => $qrCode->id,
                        'code' => $qrCode->qr_code,
                        'title' => $qrCode->qr_title,
                        'description' => $qrCode->qr_description,
                        'points' => $qrCode->qr_points,
                        'is_liveevent' => false,
                        'artist_name' => $qrCode->qr_artist_name,
                        'has_options' => true,
                        'options' => [
                            'expressions' => $qrCode->qr_avatar_expressions ?? [],
                            'actions' => $qrCode->qr_avatar_actions ?? [],
                            'background_colors' => $qrCode->qr_background_colors ?? [],
                            'effects' => $qrCode->qr_effects ?? [],
                            'sounds' => $qrCode->qr_sounds ?? []
                        ]
                    ]
                ]);
            }

            // その他のQRコード（直接報酬付与）
            return $this->processReward($user, $qrCode);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'QRコードの処理中にエラーが発生しました。'
            ], 500);
        }
    }

    /**
     * オプション選択後の報酬処理
     */
    public function processOptions(Request $request)
    {
        $request->validate([
            'qr_code_id' => 'required|integer|exists:qr_codes,id',
            'selected_expression' => 'nullable|string|max:100',
            'selected_action' => 'nullable|string|max:100',
            'selected_background' => 'nullable|string|max:100',
            'selected_effect' => 'nullable|string|max:100',
            'selected_sound' => 'nullable|string|max:100',
        ]);

        try {
            $user = Auth::user();
            $qrCode = QrCode::findOrFail($request->qr_code_id);

            // 既に使用済みかチェック
            $existingUse = QrUseList::where('qruse_user_id', $user->id)
                                   ->where('qruse_qr_code_id', $qrCode->id)
                                   ->first();

            if ($existingUse) {
                return response()->json([
                    'success' => false,
                    'message' => 'このQRコードは既に使用済みです。'
                ], 400);
            }

            // オプション選択結果を保存して報酬を付与
            return $this->processReward($user, $qrCode, $request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'オプション処理中にエラーが発生しました。'
            ], 500);
        }
    }

    /**
     * 報酬処理の共通メソッド
     */
    private function processReward($user, $qrCode, $options = null)
    {
        DB::beginTransaction();

        try {
            // QR使用履歴を作成
            $qrUse = QrUseList::create([
                'qruse_user_id' => $user->id,
                'qruse_qr_code_id' => $qrCode->id,
                'qruse_points_earned' => $qrCode->qr_points,
                'qruse_claimed_at' => now(),
                'qruse_selected_expression' => $options?->selected_expression,
                'qruse_selected_action' => $options?->selected_action,
                'qruse_selected_background' => $options?->selected_background,
                'qruse_selected_effect' => $options?->selected_effect,
                'qruse_selected_sound' => $options?->selected_sound,
                'qruse_options_selected_at' => $options ? now() : null,
            ]);

            // ユーザーのポイントを更新（仮実装 - 実際のポイントシステムに合わせて調整）
            // $user->increment('points', $qrCode->qr_points);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '報酬を受け取りました！',
                'data' => [
                    'code' => $qrCode->qr_code,
                    'title' => $qrCode->qr_title,
                    'description' => $qrCode->qr_description,
                    'points' => $qrCode->qr_points,
                    'is_liveevent' => $qrCode->qr_is_liveevent,
                    'artist_name' => $qrCode->qr_artist_name,
                    'has_options' => $qrCode->qr_options_enabled,
                    'selected_options' => $options ? [
                        'expression' => $options->selected_expression,
                        'action' => $options->selected_action,
                        'background' => $options->selected_background,
                        'effect' => $options->selected_effect,
                        'sound' => $options->selected_sound,
                    ] : null,
                    'claimed_at' => $qrUse->qruse_claimed_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * QRコード使用履歴取得
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $history = QrUseList::with('qrCode')
                           ->where('qruse_user_id', $user->id)
                           ->orderBy('qruse_created_at', 'desc')
                           ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * ライブ会場参加者一覧取得（管理者用）
     */
    public function getLiveEventParticipants(Request $request)
    {
        $request->validate([
            'qr_code_id' => 'required|integer|exists:qr_codes,id',
            'format' => 'nullable|string|in:json,csv'
        ]);

        try {
            $qrCode = QrCode::findOrFail($request->qr_code_id);
            
            // ライブイベントかチェック
            if (!$qrCode->qr_is_liveevent) {
                return response()->json([
                    'success' => false,
                    'message' => 'このQRコードはライブイベントではありません。'
                ], 400);
            }

            // 参加者一覧を取得
            $participants = QrUseList::with(['user', 'qrCode'])
                                   ->where('qruse_qr_code_id', $qrCode->id)
                                   ->orderBy('qruse_claimed_at', 'asc')
                                   ->get();

            $data = $participants->map(function ($participant) use ($qrCode) {
                // このアーティストの参加回数を取得
                $artistParticipationCount = $participant->user->getArtistParticipationCount($qrCode->qr_artist_name);
                
                return [
                    'id' => $participant->id,
                    'user_id' => $participant->user->id,
                    'phone_number' => $participant->user->phone_number,
                    'nickname' => $participant->user->nickname,
                    'points_earned' => $participant->qruse_points_earned,
                    'claimed_at' => $participant->qruse_claimed_at,
                    'qr_code' => $participant->qrCode->qr_code,
                    'event_title' => $participant->qrCode->qr_title,
                    'artist_name' => $participant->qrCode->qr_artist_name,
                    'artist_participation_count' => $artistParticipationCount
                ];
            });

            $format = $request->get('format', 'json');

            if ($format === 'csv') {
                return $this->generateCsvResponse($data, $qrCode);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'event_info' => [
                        'title' => $qrCode->qr_title,
                        'artist_name' => $qrCode->qr_artist_name,
                        'total_participants' => $data->count(),
                        'total_points' => $data->sum('points_earned')
                    ],
                    'participants' => $data
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '参加者一覧の取得に失敗しました。'
            ], 500);
        }
    }

    /**
     * CSVレスポンス生成
     */
    private function generateCsvResponse($data, $qrCode)
    {
        $filename = 'live_event_participants_' . $qrCode->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $qrCode) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // ヘッダー行
            fputcsv($file, [
                'イベント名',
                'アーティスト名',
                '参加者ID',
                'ニックネーム',
                '獲得ポイント',
                '参加時刻',
                'QRコード',
                '総参加数'
            ]);
            
            // データ行
            foreach ($data as $participant) {
                fputcsv($file, [
                    $qrCode->qr_title,
                    $qrCode->qr_artist_name,
                    $participant['user_id'],
                    $participant['nickname'],
                    $participant['points_earned'],
                    $participant['claimed_at'],
                    $participant['qr_code'],
                    $participant['artist_participation_count']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
