<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_name',
        'theme_description',
        'theme_is_active',
        'theme_expressions',
        'theme_actions',
        'theme_backgrounds',
        'theme_effects',
        'theme_sounds',
        'theme_color',
        'theme_icon',
    ];

    protected $casts = [
        'theme_is_active' => 'boolean',
        'theme_expressions' => 'array',
        'theme_actions' => 'array',
        'theme_backgrounds' => 'array',
        'theme_effects' => 'array',
        'theme_sounds' => 'array',
    ];

    /**
     * このテーマを使用しているQRコード
     */
    public function qrCodes()
    {
        return $this->hasMany(QrCode::class, 'theme_id');
    }

    /**
     * アクティブなテーマのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('theme_is_active', true);
    }

    /**
     * テーマ別オプションを取得
     */
    public function getThemeOptions()
    {
        return [
            'expressions' => $this->theme_expressions ?? [],
            'actions' => $this->theme_actions ?? [],
            'backgrounds' => $this->theme_backgrounds ?? [],
            'effects' => $this->theme_effects ?? [],
            'sounds' => $this->theme_sounds ?? [],
        ];
    }
}
