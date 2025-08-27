<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCodeAdmin extends Model
{
    use HasFactory;

    protected $table = 'otp_codes_admin';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'adminopt_created_at';
    const UPDATED_AT = 'adminopt_updated_at';

    protected $fillable = [
        'adminopt_temp_id',
        'adminopt_code',
        'adminopt_expires_at',
        'adminopt_is_used',
    ];

    protected $casts = [
        'adminopt_expires_at' => 'datetime',
        'adminopt_is_used' => 'boolean',
    ];
}
