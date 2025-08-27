<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\NewsItem;

class NewsController extends Controller
{
    /**
     * ニュース一覧を取得
     */
    public function index(): JsonResponse
    {
        $news = NewsItem::where('gamenews_is_published', true)
            ->orderBy('gamenews_published_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'news' => $news
            ]
        ]);
    }

    /**
     * 最新ニュースを取得
     */
    public function latest(): JsonResponse
    {
        $latestNews = NewsItem::where('gamenews_is_published', true)
            ->orderBy('gamenews_published_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'news' => $latestNews
            ]
        ]);
    }
}
