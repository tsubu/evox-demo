<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_name',
        'name_kana',
        'artist_description',
        'image_path',
        'is_active'
    ];

    protected $casts = [
        'artist_is_active' => 'boolean',
        'artist_brightness' => 'integer',
        'artist_bpm' => 'integer'
    ];

    /**
     * アーティストの楽曲を取得
     */
    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    /**
     * アクティブな楽曲のみを取得
     */
    public function activeSongs()
    {
        return $this->songs()->where('song_is_active', true);
    }

    /**
     * アクティブなアーティストのみを取得
     */
    public function scopeActive($query)
    {
        return $query->where('artist_is_active', true);
    }

    /**
     * ペンライト設定を配列で取得
     */
    public function getPenlightSettings()
    {
        return [
            'color' => $this->artist_color,
            'pattern' => $this->artist_pattern,
            'brightness' => $this->artist_brightness,
            'bpm' => $this->artist_bpm,
            'intensity' => $this->artist_intensity,
            'color_scheme' => $this->artist_color_scheme
        ];
    }
}
