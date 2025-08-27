<?php

require_once 'vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ブラウザ自動テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ ログインページアクセス成功\n";
    
    // CSRFトークンを抽出
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches);
    $csrfToken = $matches[1] ?? '';
    
    if ($csrfToken) {
        echo "✅ CSRFトークン取得成功: " . substr($csrfToken, 0, 10) . "...\n\n";
    } else {
        echo "❌ CSRFトークンの取得に失敗\n";
        exit(1);
    }
} else {
    echo "❌ ログインページアクセス失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 2. OTP認証コードをリクエスト
echo "2. OTP認証コードをリクエスト中...\n";
$tempId = 'test_' . substr(time(), -6);
$loginData = [
    'admin_phone' => '+818090330374',
    'admin_password' => 'admin123',
    'temp_id' => $tempId
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "JSONレスポンス: " . $response . "\n";
    if (isset($data['message']) && strpos($data['message'], '送信') !== false) {
        echo "✅ OTP認証コードリクエスト成功\n";
        echo "   temp_id: " . $tempId . "\n\n";
    } else {
        echo "❌ OTP認証コードリクエスト失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ OTP認証コードリクエスト失敗: HTTP " . $httpCode . "\n";
    echo "レスポンス: " . $response . "\n";
    exit(1);
}

// 3. データベースからOTPコードを取得
echo "3. OTPコードをデータベースから取得中...\n";
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=evox', 'tsuburayatoshifumi', '');
    $stmt = $pdo->prepare("SELECT adminopt_code FROM otp_codes_admin WHERE adminopt_temp_id = ? ORDER BY adminopt_created_at DESC LIMIT 1");
    $stmt->execute([$tempId]);
    $otpCode = $stmt->fetchColumn();
    
    if ($otpCode) {
        echo "✅ OTPコード取得成功: " . $otpCode . "\n\n";
    } else {
        echo "❌ OTPコードが見つかりません\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. OTP認証を実行
echo "4. OTP認証を実行中...\n";
$otpData = [
    'temp_id' => $tempId,
    'otp_code' => $otpCode,
    'admin_phone' => '+818090330374',
    '_token' => $csrfToken
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/verify-otp');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($otpData));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "JSONレスポンス: " . $response . "\n";
    if (isset($data['success']) && $data['success'] && isset($data['data']['token'])) {
        $token = $data['data']['token'];
        echo "✅ OTP認証成功\n";
        echo "   トークン: " . substr($token, 0, 20) . "...\n\n";
    } else {
        echo "❌ OTP認証失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ OTP認証失敗: HTTP " . $httpCode . "\n";
    echo "レスポンス: " . $response . "\n";
    exit(1);
}

// 5. トークンをCookieに手動で設定
echo "5. トークンをCookieに手動で設定中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Cookie: admin_token=' . $token . '; path=/; max-age=' . (7 * 24 * 60 * 60) . '; SameSite=Lax'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "ダッシュボードアクセス結果:\n";
echo "HTTP Status: " . $httpCode . "\n";

if ($httpCode === 200) {
    if (strpos($response, 'EvoX 管理画面') !== false) {
        echo "✅ ダッシュボードアクセス成功\n";
        echo "✅ ダッシュボードの内容確認成功\n\n";
    } else {
        echo "⚠️ ダッシュボードアクセス成功だが、内容に問題がある可能性があります\n\n";
    }
} else {
    echo "❌ ダッシュボードアクセス失敗: HTTP " . $httpCode . "\n";
    
    // リダイレクトの場合は新しいURLを取得
    if ($httpCode === 302 || $httpCode === 301) {
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        echo "リダイレクト先: " . $redirectUrl . "\n";
        
        if (strpos($redirectUrl, 'admin-ai8edq64p2i5') !== false) {
            echo "❌ ログインページにリダイレクトされました（認証失敗）\n";
        }
    }
}

// 6. 別の方法でダッシュボードにアクセス
echo "6. 別の方法でダッシュボードにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Authorizationヘッダー付きアクセス結果:\n";
echo "HTTP Status: " . $httpCode . "\n";

if ($httpCode === 200) {
    if (strpos($response, 'EvoX 管理画面') !== false) {
        echo "✅ Authorizationヘッダー付きダッシュボードアクセス成功\n";
    } else {
        echo "⚠️ Authorizationヘッダー付きアクセス成功だが、内容に問題がある可能性があります\n";
    }
} else {
    echo "❌ Authorizationヘッダー付きアクセス失敗: HTTP " . $httpCode . "\n";
}

// 7. Cookieの状況を確認
echo "\n7. Cookieの状況を確認中...\n";
$cookies = file_get_contents('test_cookies.txt');
echo "保存されたCookie:\n" . $cookies . "\n";

if (strpos($cookies, 'admin_token') !== false) {
    echo "✅ admin_tokenがCookieに保存されています\n";
} else {
    echo "❌ admin_tokenがCookieに保存されていません\n";
}

curl_close($ch);

// クリーンアップ
if (file_exists('test_cookies.txt')) {
    unlink('test_cookies.txt');
}

echo "\n=== テスト完了 ===\n";

if ($httpCode === 200 && strpos($response, 'EvoX 管理画面') !== false) {
    echo "🎉 管理画面のログイン機能は完全に動作しています！\n";
} else {
    echo "⚠️ 管理画面のログイン機能に一部問題があります。\n";
    echo "認証は成功していますが、ダッシュボードアクセスに問題があります。\n";
}
