<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameBase extends Model
{
    use HasFactory;

    protected $table = 'game_base';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'gamebase_created_at';
    const UPDATED_AT = 'gamebase_updated_at';

    protected $fillable = [
        'gamebase_userid',
        'gamebase_points',
        'gamebase_email_verified_at',
        'gamebase_avatar_choice',
        'gamebase_nickname',
        'gamebase_is_profile_complete',
    ];

    protected $casts = [
        'gamebase_points' => 'integer',
        'gamebase_email_verified_at' => 'datetime',
        'gamebase_is_profile_complete' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'gamebase_userid');
    }
}
