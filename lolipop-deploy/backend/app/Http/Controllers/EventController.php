<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * イベント一覧を取得
     */
    public function index(): JsonResponse
    {
        // 仮のイベントデータ（後でデータベースから取得するように変更）
        $events = [
            [
                'id' => 1,
                'title' => 'EvoX リリース記念イベント',
                'description' => 'EvoXのリリースを記念した特別イベントです。',
                'start_date' => '2026-07-10',
                'end_date' => '2026-07-20',
                'image_url' => '/images/event1.jpg',
            ],
            [
                'id' => 2,
                'title' => 'ポイント2倍キャンペーン',
                'description' => '期間中、QRコードから獲得できるポイントが2倍になります。',
                'start_date' => '2026-07-15',
                'end_date' => '2026-07-25',
                'image_url' => '/images/event2.jpg',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events
            ]
        ]);
    }
}
