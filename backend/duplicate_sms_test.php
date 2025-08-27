<?php

require_once 'vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 重複SMS送信防止テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'duplicate_test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'duplicate_test_cookies.txt');
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

// 2. 1回目のログインを実行
echo "2. 1回目のログインを実行中...\n";
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
    echo "1回目レスポンス: " . $response . "\n";
    if (isset($data['message']) && strpos($data['message'], '認証コードを送信') !== false) {
        echo "✅ 1回目のOTPコード送信成功\n\n";
    } else {
        echo "❌ 1回目のOTPコード送信失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ 1回目のOTPコード送信失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 3. 2回目のログインを実行（同じtemp_idで）
echo "3. 2回目のログインを実行中（同じtemp_id）...\n";
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
    echo "2回目レスポンス: " . $response . "\n";
    if (isset($data['message']) && strpos($data['message'], '既に送信されています') !== false) {
        echo "✅ 重複送信防止機能が正常に動作しています\n\n";
    } else {
        echo "❌ 重複送信防止機能が動作していません: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ 2回目のリクエスト失敗: HTTP " . $httpCode . "\n";
}

// 4. データベースのOTPコード数を確認
echo "4. データベースのOTPコード数を確認中...\n";
try {
    $otpCount = \App\Models\OtpCodeAdmin::where('adminopt_temp_id', $tempId)->count();
    echo "一時ID {$tempId} のOTPコード数: {$otpCount}\n";
    
    if ($otpCount === 1) {
        echo "✅ OTPコードは1つだけ作成されています（重複防止成功）\n";
    } else {
        echo "❌ OTPコードが複数作成されています（重複防止失敗）\n";
    }
} catch (Exception $e) {
    echo "❌ データベース確認エラー: " . $e->getMessage() . "\n";
}

// 5. 新しいtemp_idで正常な認証をテスト
echo "\n5. 新しいtemp_idで正常な認証をテスト中...\n";
$newTempId = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 13);
$newLoginData = [
    'admin_phone' => '+818090330374',
    'admin_password' => 'admin123',
    'temp_id' => $newTempId
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newLoginData));
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
    echo "新しいtemp_idレスポンス: " . $response . "\n";
    if (isset($data['message']) && strpos($data['message'], '認証コードを送信') !== false) {
        echo "✅ 新しいtemp_idでのOTPコード送信成功\n";
    } else {
        echo "❌ 新しいtemp_idでのOTPコード送信失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ 新しいtemp_idでのリクエスト失敗: HTTP " . $httpCode . "\n";
}

curl_close($ch);

// クリーンアップ
if (file_exists('duplicate_test_cookies.txt')) {
    unlink('duplicate_test_cookies.txt');
}

echo "\n=== テスト完了 ===\n";
echo "重複SMS送信防止機能のテストが完了しました。\n";
