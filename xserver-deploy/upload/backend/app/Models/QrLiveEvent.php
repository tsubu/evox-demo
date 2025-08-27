<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrLiveEvent extends Model
{
    use HasFactory;

    protected $table = 'qr_liveevents';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'qrevent_created_at';
    const UPDATED_AT = 'qrevent_updated_at';

    protected $fillable = [
        'qrevent_user_id',
        'qrevent_qr_code_id',
        'qrevent_artist_name',
        'qrevent_nickname',
        'qrevent_avatar_choice',
        'qrevent_uniquecode',
    ];

    protected $casts = [
        'qrevent_created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'qrevent_user_id');
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class, 'qrevent_qr_code_id');
    }
}
