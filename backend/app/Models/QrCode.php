<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'qr_created_at';
    const UPDATED_AT = 'qr_updated_at';

    protected $fillable = [
        'qr_code',
        'qr_title',
        'qr_description',
        'qr_points',
        'qr_is_active',
        'qr_is_liveevent',
        'qr_is_multiple',
        'qr_expires_at',
        'qr_artist_name',
        'qr_event_content',
        'qr_options_enabled',
        'qr_avatar_expressions',
        'qr_avatar_actions',
        'qr_background_colors',
        'qr_effects',
        'qr_sounds',
        'theme_id',
    ];

    protected $casts = [
        'qr_is_active' => 'boolean',
        'qr_is_liveevent' => 'boolean',
        'qr_is_multiple' => 'boolean',
        'qr_expires_at' => 'datetime',
        'qr_options_enabled' => 'boolean',
        'qr_avatar_expressions' => 'array',
        'qr_avatar_actions' => 'array',
        'qr_background_colors' => 'array',
        'qr_effects' => 'array',
        'qr_sounds' => 'array',
    ];

    /**
     * このQRコードのテーマ
     */
    public function theme()
    {
        return $this->belongsTo(QrTheme::class, 'theme_id');
    }

    /**
     * このQRコードの使用履歴
     */
    public function usageHistory()
    {
        return $this->hasMany(QrUseList::class, 'qruse_qr_code_id');
    }

    public function qrUseList()
    {
        return $this->hasMany(QrUseList::class, 'qruse_qr_code_id');
    }

    public function qrLiveEvents()
    {
        return $this->hasMany(QrLiveEvent::class, 'qrevent_qr_code_id');
    }
}
