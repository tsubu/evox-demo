<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'useropt_created_at';
    const UPDATED_AT = 'useropt_updated_at';

    protected $fillable = [
        'useropt_temp_id',
        'useropt_phone',
        'useropt_code',
        'useropt_expires_at',
        'useropt_is_used',
    ];

    protected $casts = [
        'useropt_expires_at' => 'datetime',
        'useropt_is_used' => 'boolean',
    ];
}
