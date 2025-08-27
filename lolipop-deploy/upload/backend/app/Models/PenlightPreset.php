<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenlightPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'name',
        'description',
        'order',
        'pattern',
        'color',
        'penlight_color',
        'brightness',
        'bpm',
        'intensity',
        'color_scheme',
        'timing',
        'effects',
        'audio_sync',
        'music_intensity',
        'noise_gate_threshold',
        'frequency_low',
        'frequency_high',
        'is_active',
        'is_default',
        'activated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'brightness' => 'integer',
        'bpm' => 'integer',
        'order' => 'integer',
        'intensity' => 'string',
        'color_scheme' => 'array',
        'timing' => 'array',
        'effects' => 'array',
        'audio_sync' => 'array',
        'music_intensity' => 'decimal:2',
        'noise_gate_threshold' => 'decimal:2',
        'frequency_low' => 'integer',
        'frequency_high' => 'integer',
        'activated_at' => 'datetime'
    ];

    // リレーション
    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // アクセサ
    public function getIsActivatedAttribute()
    {
        return !is_null($this->activated_at);
    }

    // ミューテータ
    public function setColorSchemeAttribute($value)
    {
        $this->attributes['color_scheme'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setTimingAttribute($value)
    {
        $this->attributes['timing'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setEffectsAttribute($value)
    {
        $this->attributes['effects'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setAudioSyncAttribute($value)
    {
        $this->attributes['audio_sync'] = is_array($value) ? json_encode($value) : $value;
    }

    // メソッド
    public function activate()
    {
        $this->update([
            'activated_at' => now(),
            'is_default' => false
        ]);

        // 他のプリセットを非アクティブにする
        $this->song->penlightPresets()
            ->where('id', '!=', $this->id)
            ->update(['activated_at' => null]);
    }

    public function deactivate()
    {
        $this->update(['activated_at' => null]);
    }

    public function setAsDefault()
    {
        // 他のデフォルトプリセットを解除
        $this->song->penlightPresets()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    // プリセット設定を配列で取得
    public function getSettingsArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'pattern' => $this->pattern,
            'color' => $this->color,
            'brightness' => $this->brightness,
            'bpm' => $this->bpm,
            'color_scheme' => $this->color_scheme ?? [],
            'timing' => $this->timing ?? [],
            'effects' => $this->effects ?? [],
            'audio_sync' => $this->audio_sync ?? [],
            'is_activated' => $this->is_activated
        ];
    }
}

