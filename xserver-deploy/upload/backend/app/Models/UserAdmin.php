<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_admin';
    
    // タイムスタンプカラムの名前を指定
    const CREATED_AT = 'admin_created_at';
    const UPDATED_AT = 'admin_updated_at';

    protected $fillable = [
        'admin_name',
        'admin_email',
        'admin_phone',
        'admin_password',
        'admin_dels',
        'admin_remember_token',
        'admin_is_verified',
        'admin_verified_at',
    ];

    protected $hidden = [
        'admin_password',
        'admin_remember_token',
    ];

    protected $casts = [
        'admin_dels' => 'boolean',
        'admin_is_verified' => 'boolean',
        'admin_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->admin_password;
    }

    public function getRememberTokenName()
    {
        return 'admin_remember_token';
    }
}
