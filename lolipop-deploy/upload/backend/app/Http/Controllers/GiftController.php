<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GiftController extends Controller
{
    /**
     * ギフトアイテム一覧を取得
     */
    public function items(Request $request): JsonResponse
    {
        // 仮のギフトアイテムデータ（後でデータベースから取得するように変更）
        $items = [
            [
                'id' => 1,
                'name' => 'EvoX オリジナルステッカー',
                'description' => 'EvoXのオリジナルステッカーです。',
                'points_required' => 100,
                'image_url' => '/images/gift1.jpg',
                'stock' => 50,
            ],
            [
                'id' => 2,
                'name' => 'EvoX オリジナルTシャツ',
                'description' => 'EvoXのオリジナルTシャツです。',
                'points_required' => 500,
                'image_url' => '/images/gift2.jpg',
                'stock' => 20,
            ],
            [
                'id' => 3,
                'name' => 'EvoX オリジナルマグカップ',
                'description' => 'EvoXのオリジナルマグカップです。',
                'points_required' => 300,
                'image_url' => '/images/gift3.jpg',
                'stock' => 30,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items
            ]
        ]);
    }

    /**
     * ギフト交換
     */
    public function exchange(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $user = $request->user();

        // 仮のギフトアイテムデータ
        $items = [
            1 => ['points_required' => 100, 'name' => 'EvoX オリジナルステッカー'],
            2 => ['points_required' => 500, 'name' => 'EvoX オリジナルTシャツ'],
            3 => ['points_required' => 300, 'name' => 'EvoX オリジナルマグカップ'],
        ];

        $itemId = $request->item_id;
        
        if (!isset($items[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'ギフトアイテムが見つかりません。'
            ], 400);
        }

        $item = $items[$itemId];

        // ゲーム基本情報を取得
        $gameBase = $user->gameBase;
        
        if (!$gameBase || $gameBase->gamebase_points < $item['points_required']) {
            return response()->json([
                'success' => false,
                'message' => 'ポイントが不足しています。'
            ], 400);
        }

        // ポイントを減算
        $gameBase->decrement('gamebase_points', $item['points_required']);

        return response()->json([
            'success' => true,
            'message' => 'ギフト交換が完了しました。',
            'data' => [
                'item' => [
                    'id' => $itemId,
                    'name' => $item['name'],
                    'points_required' => $item['points_required'],
                ],
                'remaining_points' => $gameBase->fresh()->gamebase_points,
            ]
        ]);
    }

    /**
     * 交換履歴を取得
     */
    public function history(Request $request): JsonResponse
    {
        // 仮の交換履歴データ（後でデータベースから取得するように変更）
        $history = [
            [
                'id' => 1,
                'item_name' => 'EvoX オリジナルステッカー',
                'points_used' => 100,
                'exchanged_at' => '2026-07-15 10:30:00',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'history' => $history
            ]
        ]);
    }
}
