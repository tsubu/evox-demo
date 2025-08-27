<?php
/**
 * QRコード管理機能テスト（既存の動作しているテストをベース）
 */

// テスト用のcURLセッションを開始
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'qrcode_test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'qrcode_test_cookies.txt');

echo "=== QRコード管理機能テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ ログインページアクセス成功\n";
    
    // CSRFトークンを抽出
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
        $csrfToken = $matches[1];
        echo "✅ CSRFトークン取得成功: " . substr($csrfToken, 0, 10) . "...\n\n";
    } else {
        echo "❌ CSRFトークン取得失敗\n";
        exit(1);
    }
} else {
    echo "❌ ログインページアクセス失敗: HTTP $httpCode\n";
    exit(1);
}

// 2. ログインを実行（既存の動作している方法を使用）
echo "2. ログインを実行中...\n";

// 最初のログイン（OTP送信）
$loginData = [
    'phone' => '8090330374',
    'password' => 'admin123',
    'temp_id' => uniqid(),
    '_token' => $csrfToken
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$loginResult = json_decode($response, true);

if ($httpCode === 200 && isset($loginResult['message']) && strpos($loginResult['message'], '認証コードを送信しました') !== false) {
    echo "✅ OTPコード送信成功\n";
    
    // OTPコードを取得
    $pdo = new PDO('pgsql:host=localhost;dbname=evox', 'postgres', 'postgres');
    $stmt = $pdo->prepare("SELECT otp_code FROM admin_otps WHERE phone = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['8090330374']);
    $otpResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($otpResult) {
        $otpCode = $otpResult['otp_code'];
        echo "✅ OTPコード取得成功: $otpCode\n";
        
        // OTP認証
        $otpData = [
            'phone' => '8090330374',
            'otp' => $otpCode,
            'temp_id' => $loginData['temp_id'],
            '_token' => $csrfToken
        ];
        
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/verify-otp');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($otpData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'X-CSRF-TOKEN: ' . $csrfToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $otpResult = json_decode($response, true);
        
        if ($httpCode === 200 && isset($otpResult['success']) && $otpResult['success']) {
            echo "✅ OTP認証成功\n\n";
        } else {
            echo "❌ OTP認証失敗: HTTP $httpCode\n";
            echo "Response: " . $response . "\n";
            exit(1);
        }
    } else {
        echo "❌ OTPコード取得失敗\n";
        exit(1);
    }
} else {
    echo "❌ ログイン失敗: HTTP $httpCode\n";
    echo "Response: " . $response . "\n";
    exit(1);
}

// 3. QRコード一覧ページにアクセス
echo "3. QRコード一覧ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ QRコード一覧ページアクセス成功\n";
} else {
    echo "❌ QRコード一覧ページアクセス失敗: HTTP $httpCode\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
    exit(1);
}

// 4. QRコード作成ページにアクセス
echo "4. QRコード作成ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes/create');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ QRコード作成ページアクセス成功\n";
    
    // 新しいCSRFトークンを取得
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
        $csrfToken = $matches[1];
    }
} else {
    echo "❌ QRコード作成ページアクセス失敗: HTTP $httpCode\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
    exit(1);
}

// 5. QRコード生成APIをテスト
echo "5. QRコード生成APIをテスト中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes/generate');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$generateResult = json_decode($response, true);

if ($httpCode === 200 && isset($generateResult['success']) && $generateResult['success']) {
    echo "✅ QRコード生成成功: " . $generateResult['qr_code'] . "\n";
} else {
    echo "❌ QRコード生成失敗: HTTP $httpCode\n";
    echo "Response: " . $response . "\n";
    exit(1);
}

// 6. 新規QRコードを作成
echo "6. 新規QRコードを作成中...\n";
$qrCodeData = [
    'qr_code' => $generateResult['qr_code'],
    'qr_description' => 'テスト用QRコード',
    'qr_title' => 'テストタイトル',
    'qr_points' => 100,
    'qr_artist_name' => 'テストアーティスト',
    'qr_contents' => 'テストコンテンツ',
    'qr_is_active' => '1',
    'qr_is_liveevent' => '0',
    'qr_is_multiple' => '1',
    '_token' => $csrfToken
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qrCodeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 302) {
    echo "✅ QRコード作成成功（リダイレクト）\n";
} else {
    echo "❌ QRコード作成失敗: HTTP $httpCode\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
    exit(1);
}

// 7. 作成したQRコードを確認
echo "7. 作成したQRコードを確認中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200 && strpos($response, $generateResult['qr_code']) !== false) {
    echo "✅ 作成したQRコードが一覧に表示されていることを確認\n";
} else {
    echo "❌ 作成したQRコードが一覧に表示されていません\n";
}

// 8. ログアウト
echo "8. ログアウトをテスト中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/logout');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['_token' => $csrfToken]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 302) {
    echo "✅ ログアウト成功\n";
} else {
    echo "❌ ログアウト失敗: HTTP $httpCode\n";
}

curl_close($ch);

echo "\n=== テスト完了 ===\n";
echo "QRコード管理機能のテストが完了しました。\n";
