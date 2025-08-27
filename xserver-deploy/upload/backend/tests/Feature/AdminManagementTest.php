<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserAdmin;
use App\Models\User;
use App\Models\NewsItem;
use App\Models\QrCode;
use App\Models\OtpCodeAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $token;

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

        // 認証トークンを取得
        $this->token = $this->getAdminToken();
    }

    protected function getAdminToken()
    {
        // OTPコードをリクエスト
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

        return $response->json('data.token');
    }

    /** @test */
    public function admin_can_access_user_management()
    {
        // テスト用ユーザーを作成
        User::create([
            'name' => 'Test User',
            'phone' => '+818090330375',
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('ユーザー管理');
        $response->assertSee('Test User');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '+818090330375',
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get("/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('+818090330375');
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '+818090330375',
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        
        // ユーザーが削除されていることを確認
        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    /** @test */
    public function admin_can_access_news_management()
    {
        // テスト用ニュースを作成
        NewsItem::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/news');

        $response->assertStatus(200);
        $response->assertSee('ニュース管理');
        $response->assertSee('Test News');
    }

    /** @test */
    public function admin_can_create_news()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/news/create');

        $response->assertStatus(200);
        $response->assertSee('ニュース作成');
    }

    /** @test */
    public function admin_can_store_news()
    {
        $newsData = [
            'title' => 'New Test News',
            'content' => 'New test content',
            'is_published' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/admin/news', $newsData);

        $response->assertRedirect('/admin/news');
        
        // ニュースが保存されていることを確認
        $this->assertDatabaseHas('news_items', [
            'title' => 'New Test News',
            'content' => 'New test content',
        ]);
    }

    /** @test */
    public function admin_can_edit_news()
    {
        $news = NewsItem::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get("/admin/news/{$news->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('ニュース編集');
        $response->assertSee('Test News');
    }

    /** @test */
    public function admin_can_update_news()
    {
        $news = NewsItem::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $updatedData = [
            'title' => 'Updated Test News',
            'content' => 'Updated test content',
            'is_published' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->put("/admin/news/{$news->id}", $updatedData);

        $response->assertRedirect('/admin/news');
        
        // ニュースが更新されていることを確認
        $this->assertDatabaseHas('news_items', [
            'id' => $news->id,
            'title' => 'Updated Test News',
            'content' => 'Updated test content',
        ]);
    }

    /** @test */
    public function admin_can_delete_news()
    {
        $news = NewsItem::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/admin/news/{$news->id}");

        $response->assertRedirect('/admin/news');
        
        // ニュースが削除されていることを確認
        $this->assertDatabaseMissing('news_items', [
            'id' => $news->id
        ]);
    }

    /** @test */
    public function admin_can_access_qr_code_management()
    {
        // テスト用QRコードを作成
        QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/qrcodes');

        $response->assertStatus(200);
        $response->assertSee('QRコード管理');
        $response->assertSee('TEST123');
    }

    /** @test */
    public function admin_can_create_qr_code()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/qrcodes/create');

        $response->assertStatus(200);
        $response->assertSee('QRコード作成');
    }

    /** @test */
    public function admin_can_store_qr_code()
    {
        $qrData = [
            'qr_code' => 'NEWTEST123',
            'qr_description' => 'New Test QR Code',
            'qr_is_active' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/admin/qrcodes', $qrData);

        $response->assertRedirect('/admin/qrcodes');
        
        // QRコードが保存されていることを確認
        $this->assertDatabaseHas('qr_codes', [
            'qr_code' => 'NEWTEST123',
            'qr_description' => 'New Test QR Code',
        ]);
    }

    /** @test */
    public function admin_can_edit_qr_code()
    {
        $qrCode = QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get("/admin/qrcodes/{$qrCode->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('QRコード編集');
        $response->assertSee('TEST123');
    }

    /** @test */
    public function admin_can_update_qr_code()
    {
        $qrCode = QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);

        $updatedData = [
            'qr_code' => 'UPDATED123',
            'qr_description' => 'Updated Test QR Code',
            'qr_is_active' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->put("/admin/qrcodes/{$qrCode->id}", $updatedData);

        $response->assertRedirect('/admin/qrcodes');
        
        // QRコードが更新されていることを確認
        $this->assertDatabaseHas('qr_codes', [
            'id' => $qrCode->id,
            'qr_code' => 'UPDATED123',
            'qr_description' => 'Updated Test QR Code',
        ]);
    }

    /** @test */
    public function admin_can_delete_qr_code()
    {
        $qrCode = QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/admin/qrcodes/{$qrCode->id}");

        $response->assertRedirect('/admin/qrcodes');
        
        // QRコードが削除されていることを確認
        $this->assertDatabaseMissing('qr_codes', [
            'id' => $qrCode->id
        ]);
    }

    /** @test */
    public function admin_can_access_statistics()
    {
        // テスト用データを作成
        User::create([
            'name' => 'Test User',
            'phone' => '+818090330375',
            'password' => Hash::make('password'),
            'email' => 'test@example.com',
        ]);

        NewsItem::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        QrCode::create([
            'qr_code' => 'TEST123',
            'qr_description' => 'Test QR Code',
            'qr_is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/admin/stats');

        $response->assertStatus(200);
        $response->assertSee('統計情報');
        $response->assertSee('総ユーザー数');
        $response->assertSee('ニュース記事数');
        $response->assertSee('総QRコード数');
    }

    /** @test */
    public function admin_cannot_access_protected_routes_without_token()
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

    /** @test */
    public function admin_cannot_access_protected_routes_with_invalid_token()
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
}
