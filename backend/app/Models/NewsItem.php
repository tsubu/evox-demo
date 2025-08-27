<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    use HasFactory;

    protected $table = 'news_items';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'gamenews_created_at';
    const UPDATED_AT = 'gamenews_updated_at';

    protected $fillable = [
        'gamenews_title',
        'gamenews_content',
        'gamenews_image_url',
        'gamenews_is_published',
        'gamenews_published_at',
        'gamenews_publish_start_at',
        'gamenews_publish_end_at',
    ];

    protected $casts = [
        'gamenews_is_published' => 'boolean',
        'gamenews_published_at' => 'datetime',
        'gamenews_publish_start_at' => 'datetime',
        'gamenews_publish_end_at' => 'datetime',
    ];

    /**
     * 公開中のニュースを取得するスコープ
     */
    public function scopePublished($query)
    {
        return $query->where('gamenews_is_published', true)
                    ->where(function ($q) {
                        $q->whereNull('gamenews_publish_start_at')
                          ->orWhere('gamenews_publish_start_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('gamenews_publish_end_at')
                          ->orWhere('gamenews_publish_end_at', '>', now());
                    });
    }

    /**
     * 最新ニュースを取得するスコープ
     */
    public function scopeLatest($query, $limit = 5)
    {
        return $query->published()
                    ->orderBy('gamenews_created_at', 'desc')
                    ->limit($limit);
    }
}
