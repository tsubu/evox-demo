<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EvoX 管理画面ログインテスト ===\n\n";

// 1. 管理画面ログインページにアクセス
echo "1. 管理画面ログインページにアクセス...\n";
$response = Http::get('http://localhost:8000/admin-ai8edq64p2i5');

if ($response->successful()) {
    echo "✅ ログインページにアクセス成功\n";
    echo "   ステータスコード: " . $response->status() . "\n";
} else {
    echo "❌ ログインページにアクセス失敗\n";
    echo "   ステータスコード: " . $response->status() . "\n";
    exit(1);
}

// 2. OTP認証コードをリクエスト
echo "\n2. OTP認証コードをリクエスト...\n";
$loginData = [
    'admin_phone' => '+818090330374',
    'admin_password' => 'admin123',
    'temp_id' => 'test_' . substr(time(), -6)
];

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Accept' => 'application/json'
])->post('http://localhost:8000/api/admin/auth/login', $loginData);

if ($response->successful()) {
    $data = $response->json();
    echo "✅ OTP認証コードリクエスト成功\n";
    echo "   レスポンス: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    echo "   生レスポンス: " . $response->body() . "\n";
    
    // temp_idを直接使用（リクエストで送信したもの）
    $tempId = $loginData['temp_id'];
    echo "   temp_id: " . $tempId . "\n";
} else {
    echo "❌ OTP認証コードリクエスト失敗\n";
    echo "   ステータスコード: " . $response->status() . "\n";
    echo "   レスポンス: " . $response->body() . "\n";
    exit(1);
}

// 3. データベースからOTPコードを取得
echo "\n3. OTPコードをデータベースから取得...\n";
try {
    // PostgreSQLに接続
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=evox', 'tsuburayatoshifumi', '');
    $stmt = $pdo->prepare("SELECT adminopt_code FROM otp_codes_admin WHERE adminopt_temp_id = ? ORDER BY adminopt_created_at DESC LIMIT 1");
    $stmt->execute([$tempId]);
    $otpCode = $stmt->fetchColumn();
    
    if ($otpCode) {
        echo "✅ OTPコード取得成功: " . $otpCode . "\n";
    } else {
        echo "❌ OTPコードが見つかりません\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. OTP認証を実行
echo "\n4. OTP認証を実行...\n";
$otpData = [
    'temp_id' => $tempId,
    'otp_code' => $otpCode,
    'admin_phone' => '+818090330374'
];

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Accept' => 'application/json'
])->post('http://localhost:8000/api/admin/auth/verify-otp', $otpData);

if ($response->successful()) {
    $data = $response->json();
    echo "✅ OTP認証成功\n";
    echo "   レスポンス: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    
    if (isset($data['data']['token'])) {
        $token = $data['data']['token'];
        echo "   トークン: " . substr($token, 0, 20) . "...\n";
    } else {
        echo "❌ トークンが見つかりません\n";
        exit(1);
    }
} else {
    echo "❌ OTP認証失敗\n";
    echo "   ステータスコード: " . $response->status() . "\n";
    echo "   レスポンス: " . $response->body() . "\n";
    exit(1);
}

// 5. ダッシュボードにアクセス
echo "\n5. ダッシュボードにアクセス...\n";
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token
])->get('http://localhost:8000/admin/dashboard');

if ($response->successful()) {
    echo "✅ ダッシュボードアクセス成功\n";
    echo "   ステータスコード: " . $response->status() . "\n";
    
    // ダッシュボードの内容を確認
    $content = $response->body();
    if (strpos($content, 'EvoX 管理画面') !== false) {
        echo "✅ ダッシュボードの内容確認成功\n";
    } else {
        echo "⚠️  ダッシュボードの内容に問題がある可能性があります\n";
    }
} else {
    echo "❌ ダッシュボードアクセス失敗\n";
    echo "   ステータスコード: " . $response->status() . "\n";
    echo "   レスポンス: " . $response->body() . "\n";
    exit(1);
}

// 6. ログアウトをテスト
echo "\n6. ログアウトをテスト...\n";
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token
])->post('http://localhost:8000/admin/logout');

if ($response->successful() || $response->status() === 302) {
    echo "✅ ログアウト成功\n";
    echo "   ステータスコード: " . $response->status() . "\n";
} else {
    echo "❌ ログアウト失敗\n";
    echo "   ステータスコード: " . $response->status() . "\n";
}

echo "\n=== テスト完了 ===\n";
echo "✅ 管理画面のログイン機能は正常に動作しています！\n";
