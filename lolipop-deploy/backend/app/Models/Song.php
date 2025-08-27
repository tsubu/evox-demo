<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'title',
        'title_kana',
        'description',
        'image_path',
        'bpm',
        'key',
        'duration',
        'is_active',
        // 古いフィールド名（後方互換性のため）
        'song_name',
        'song_description',
        'song_color',
        'song_pattern',
        'song_brightness',
        'song_bpm',
        'song_intensity',
        'song_color_scheme',
        'song_is_active',
        // 新しいペンライト音声フィールド
        'penlight_audio_sync',
        'penlight_music_intensity',
        'penlight_noise_gate_threshold',
        'penlight_frequency_low',
        'penlight_frequency_high'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bpm' => 'integer',
        'duration' => 'integer',
        'penlight_audio_sync' => 'boolean',
        'penlight_music_intensity' => 'decimal:2',
        'penlight_noise_gate_threshold' => 'decimal:2',
        'penlight_frequency_low' => 'integer',
        'penlight_frequency_high' => 'integer'
    ];

    // リレーション
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function penlightPresets()
    {
        return $this->hasMany(PenlightPreset::class);
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('song_is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('song_name', 'like', "%{$search}%")
              ->orWhere('title_kana', 'like', "%{$search}%");
        });
    }

    public function scopeByArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    // アクセサ
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
