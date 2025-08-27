<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserAdmin;
use App\Models\OtpCodeAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminBasicTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用の管理者ユーザーを作成
        $this->admin = UserAdmin::create([
            'admin_name' => 'Test Administrator',
            'admin_phone' => '+818090330374',
            'admin_password' => Hash::make('admin123'),
            'admin_is_verified' => true,
            'admin_verified_at' => now(),
        ]);
    }

    public function test_admin_can_access_login_page()
    {
        $response = $this->get('/admin-ai8edq64p2i5');
        
        $response->assertStatus(200);
        $response->assertSee('EvoX 管理画面');
        $response->assertSee('管理者ログイン');
    }

    public function test_admin_can_request_otp()
    {
        $response = $this->postJson('/api/admin/auth/login', [
            'admin_phone' => '+818090330374',
            'admin_password' => 'admin123',
            'temp_id' => 'test123456789'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => '認証コードを送信しました。'
        ]);

        // OTPコードがデータベースに保存されていることを確認
        $this->assertDatabaseHas('otp_codes_admin', [
            'adminopt_temp_id' => 'test123456789'
        ]);
    }

    public function test_admin_can_verify_otp_and_get_token()
    {
        // まずOTPコードをリクエスト
        $this->postJson('/api/admin/auth/login', [
            'admin_phone' => '+818090330374',
            'admin_password' => 'admin123',
            'temp_id' => 'test123456789'
        ]);

        // OTPコードを取得
        $otpCode = OtpCodeAdmin::where('adminopt_temp_id', 'test123456789')->first();
        
        // OTP認証を実行
        $response = $this->postJson('/api/admin/auth/verify-otp', [
            'temp_id' => 'test123456789',
            'otp_code' => $otpCode->adminopt_code,
            'admin_phone' => '+818090330374'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => '認証が完了しました。'
        ]);

        // トークンがデータベースに保存されていることを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => UserAdmin::class,
            'tokenable_id' => $this->admin->id
        ]);
    }

    public function test_admin_cannot_access_protected_routes_without_token()
    {
        $routes = [
            '/admin/users',
            '/admin/news',
            '/admin/qrcodes',
            '/admin/stats',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/admin-ai8edq64p2i5');
        }
    }

    public function test_admin_cannot_access_protected_routes_with_invalid_token()
    {
        $routes = [
            '/admin/users',
            '/admin/news',
            '/admin/qrcodes',
            '/admin/stats',
        ];

        foreach ($routes as $route) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid_token'
            ])->get($route);
            $response->assertRedirect('/admin-ai8edq64p2i5');
        }
    }

    public function test_otp_code_expires_after_10_minutes()
    {
        // まずOTPコードをリクエスト
        $this->postJson('/api/admin/auth/login', [
            'admin_phone' => '+818090330374',
            'admin_password' => 'admin123',
            'temp_id' => 'test123456789'
        ]);

        // OTPコードを取得して有効期限を過去に設定
        $otpCode = OtpCodeAdmin::where('adminopt_temp_id', 'test123456789')->first();
        $otpCode->update([
            'adminopt_expires_at' => now()->subMinutes(11)
        ]);

        // 期限切れのOTPコードで認証を試行
        $response = $this->postJson('/api/admin/auth/verify-otp', [
            'temp_id' => 'test123456789',
            'otp_code' => $otpCode->adminopt_code,
            'admin_phone' => '+818090330374'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => '認証コードが無効です。'
        ]);
    }
}
