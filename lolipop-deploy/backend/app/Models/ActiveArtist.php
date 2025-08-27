<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'artist_name',
        'is_active',
        'activated_at',
        'deactivated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    // アーティストをアクティブにする
    public static function activateArtist($artistId, $artistName)
    {
        // 他のアーティストを非アクティブにする
        self::where('is_active', true)->update([
            'is_active' => false,
            'deactivated_at' => now()
        ]);

        // 指定されたアーティストをアクティブにする
        $activeArtist = self::updateOrCreate(
            ['artist_id' => $artistId],
            [
                'artist_name' => $artistName,
                'is_active' => true,
                'activated_at' => now(),
                'deactivated_at' => null
            ]
        );

        return $activeArtist;
    }

    // アーティストを非アクティブにする
    public static function deactivateArtist()
    {
        return self::where('is_active', true)->update([
            'is_active' => false,
            'deactivated_at' => now()
        ]);
    }

    // 現在アクティブなアーティストを取得
    public static function getCurrentActive()
    {
        return self::where('is_active', true)->first();
    }
}
