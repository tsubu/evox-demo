<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;

class NewsController extends Controller
{
    /**
     * 最新ニュースを取得
     */
    public function latest()
    {
        $news = NewsItem::published()
            ->orderBy('gamenews_created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}
