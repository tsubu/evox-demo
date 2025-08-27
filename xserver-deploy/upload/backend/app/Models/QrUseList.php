<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrUseList extends Model
{
    use HasFactory;

    protected $table = 'qr_use_list';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'qruse_created_at';
    const UPDATED_AT = 'qruse_updated_at';

    protected $fillable = [
        'qruse_user_id',
        'qruse_qr_code_id',
        'qruse_points_earned',
        'qruse_claimed_at',
        'qruse_selected_expression',
        'qruse_selected_action',
        'qruse_selected_background',
        'qruse_selected_effect',
        'qruse_selected_sound',
        'qruse_options_selected_at',
    ];

    protected $casts = [
        'qruse_claimed_at' => 'datetime',
        'qruse_options_selected_at' => 'datetime',
    ];

    /**
     * QRコードとのリレーション
     */
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class, 'qruse_qr_code_id');
    }

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'qruse_user_id');
    }
}
