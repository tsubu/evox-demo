<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreRegistration extends Model
{
    use HasFactory;

    protected $table = 'pre_registrations';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'prereg_created_at';
    const UPDATED_AT = 'prereg_updated_at';

    protected $fillable = [
        'prereg_temp_id',
        'prereg_phone',
        'prereg_password',
        'prereg_is_verified',
        'prereg_verified_at',
        'prereg_avatar_choice'
    ];

    protected $casts = [
        'prereg_is_verified' => 'boolean',
        'prereg_verified_at' => 'datetime',
    ];
}
