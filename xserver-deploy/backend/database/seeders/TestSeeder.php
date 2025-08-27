<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAdmin;
use App\Models\User;
use App\Models\NewsItem;
use App\Models\QrCode;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テスト用管理者ユーザーを作成
        UserAdmin::create([
            'admin_name' => 'Test Administrator',
            'admin_phone' => '+818090330374',
            'admin_password' => Hash::make('admin123'),
            'admin_is_verified' => true,
            'admin_verified_at' => now(),
        ]);

        // テスト用ユーザーを作成
        User::create([
            'name' => 'Test User',
            'phone' => '+818090330375',
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        // テスト用ニュースを作成
        NewsItem::create([
            'title' => 'Test News',
            'content' => 'This is a test news item.',
            'is_published' => true,
        ]);

        // テスト用QRコードを作成
        QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);
    }
}
