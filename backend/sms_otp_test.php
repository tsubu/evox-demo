<?php

require_once 'vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SMS OTP認証自動テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'sms_otp_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'sms_otp_cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

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

// 2. ログインを実行（OTPコードを要求）
echo "2. ログインを実行中（OTPコードを要求）...\n";
$tempId = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 13);
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
    if (isset($data['message']) && strpos($data['message'], '認証コードを送信') !== false) {
        echo "✅ OTPコード送信成功\n";
        echo "   一時ID: " . $tempId . "\n\n";
    } else {
        echo "❌ OTPコード送信失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ OTPコード送信失敗: HTTP " . $httpCode . "\n";
    echo "レスポンス: " . $response . "\n";
    exit(1);
}

// 3. データベースからOTPコードを取得
echo "3. データベースからOTPコードを取得中...\n";
try {
    $otpRecord = \App\Models\OtpCodeAdmin::where('adminopt_temp_id', $tempId)
        ->where('adminopt_is_used', false)
        ->where('adminopt_expires_at', '>', now())
        ->first();
    
    if ($otpRecord) {
        $otpCode = $otpRecord->adminopt_code;
        echo "✅ OTPコード取得成功: " . $otpCode . "\n\n";
    } else {
        echo "❌ OTPコードが見つかりません\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ OTPコード取得エラー: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. OTP認証を実行
echo "4. OTP認証を実行中...\n";
$otpData = [
    'temp_id' => $tempId,
    'otp_code' => $otpCode,
    'admin_phone' => '+818090330374'
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/verify-otp');
curl_setopt($ch, CURLOPT_POST, true);
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
    if (isset($data['success']) && $data['success']) {
        echo "✅ OTP認証成功\n";
        echo "   セッション認証完了\n\n";
    } else {
        echo "❌ OTP認証失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ OTP認証失敗: HTTP " . $httpCode . "\n";
    echo "レスポンス: " . $response . "\n";
    exit(1);
}

// 5. ダッシュボードにアクセス
echo "5. ダッシュボードにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
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

// 6. セッションの状況を確認
echo "6. セッションの状況を確認中...\n";
if (file_exists('sms_otp_cookies.txt')) {
    $cookies = file_get_contents('sms_otp_cookies.txt');
    echo "保存されたCookie:\n" . $cookies . "\n";

    if (strpos($cookies, 'laravel-session') !== false) {
        echo "✅ Laravelセッションが保存されています\n";
    } else {
        echo "❌ Laravelセッションが保存されていません\n";
    }
} else {
    echo "❌ Cookieファイルが作成されていません\n";
}

// 7. ログアウトをテスト
echo "\n7. ログアウトをテスト中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/logout');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['_token' => $csrfToken]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200 || $httpCode === 302) {
    echo "✅ ログアウト成功\n";
} else {
    echo "❌ ログアウト失敗: HTTP " . $httpCode . "\n";
}

curl_close($ch);

// クリーンアップ
if (file_exists('sms_otp_cookies.txt')) {
    unlink('sms_otp_cookies.txt');
}

echo "\n=== テスト完了 ===\n";

if ($httpCode === 200 && strpos($response, 'EvoX 管理画面') !== false) {
    echo "🎉 SMS OTP認証は完全に動作しています！\n";
} else {
    echo "⚠️ SMS OTP認証に一部問題があります。\n";
    echo "認証は成功していますが、ダッシュボードアクセスに問題があります。\n";
}
