<?php
/**
 * QRコード機能分離テスト
 */

// テスト用のcURLセッションを開始
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'qrcode_function_test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'qrcode_function_test_cookies.txt');

echo "=== QRコード機能分離テスト ===\n\n";

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

// 2. ログインを実行
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

// 3. QRコード一覧ページにアクセス（タブ機能確認）
echo "3. QRコード一覧ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ QRコード一覧ページアクセス成功\n";
    
    // タブ機能の確認
    if (strpos($response, 'リアルイベント') !== false && strpos($response, 'ゲームグッズ') !== false) {
        echo "✅ タブ機能が正常に表示されています\n";
    } else {
        echo "❌ タブ機能の表示に問題があります\n";
    }
} else {
    echo "❌ QRコード一覧ページアクセス失敗: HTTP $httpCode\n";
    exit(1);
}

// 4. リアルイベントQRコード作成ページにアクセス
echo "4. リアルイベントQRコード作成ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes/create?type=event');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ リアルイベントQRコード作成ページアクセス成功\n";
    
    // 新しいCSRFトークンを取得
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
        $csrfToken = $matches[1];
    }
    
    // リアルイベントフォームの確認
    if (strpos($response, 'リアルイベントQRコード作成') !== false && strpos($response, 'イベント名') !== false) {
        echo "✅ リアルイベントフォームが正常に表示されています\n";
    } else {
        echo "❌ リアルイベントフォームの表示に問題があります\n";
    }
} else {
    echo "❌ リアルイベントQRコード作成ページアクセス失敗: HTTP $httpCode\n";
    exit(1);
}

// 5. リアルイベントQRコードを作成
echo "5. リアルイベントQRコードを作成中...\n";

// QRコード生成
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
    
    // リアルイベントQRコード作成
    $eventQrCodeData = [
        'qr_code' => $generateResult['qr_code'],
        'qr_title' => 'テスト音楽イベント2024',
        'qr_description' => 'テスト用の音楽イベントです',
        'qr_artist_name' => 'テストアーティスト',
        'qr_contents' => '音楽イベントの詳細内容',
        'qr_is_active' => '1',
        'qr_is_liveevent' => '1',
        'qr_is_multiple' => '0',
        'qr_type' => 'event',
        '_token' => $csrfToken
    ];

    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($eventQrCodeData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'X-CSRF-TOKEN: ' . $csrfToken
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 302) {
        echo "✅ リアルイベントQRコード作成成功\n";
    } else {
        echo "❌ リアルイベントQRコード作成失敗: HTTP $httpCode\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
    }
} else {
    echo "❌ QRコード生成失敗: HTTP $httpCode\n";
    exit(1);
}

// 6. ゲームグッズQRコード作成ページにアクセス
echo "6. ゲームグッズQRコード作成ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes/create?type=goods');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ ゲームグッズQRコード作成ページアクセス成功\n";
    
    // 新しいCSRFトークンを取得
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
        $csrfToken = $matches[1];
    }
    
    // ゲームグッズフォームの確認
    if (strpos($response, 'ゲームグッズQRコード作成') !== false && strpos($response, 'アイテム名') !== false) {
        echo "✅ ゲームグッズフォームが正常に表示されています\n";
    } else {
        echo "❌ ゲームグッズフォームの表示に問題があります\n";
    }
} else {
    echo "❌ ゲームグッズQRコード作成ページアクセス失敗: HTTP $httpCode\n";
    exit(1);
}

// 7. ゲームグッズQRコードを作成
echo "7. ゲームグッズQRコードを作成中...\n";

// QRコード生成
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
    
    // ゲームグッズQRコード作成
    $goodsQrCodeData = [
        'qr_code' => $generateResult['qr_code'],
        'qr_title' => 'レアアバターアイテム',
        'qr_description' => 'テスト用のレアアバターアイテムです',
        'qr_points' => '100',
        'qr_contents' => 'レアアバターの詳細内容',
        'qr_is_active' => '1',
        'qr_is_multiple' => '1',
        'qr_type' => 'goods',
        '_token' => $csrfToken
    ];

    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($goodsQrCodeData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'X-CSRF-TOKEN: ' . $csrfToken
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 302) {
        echo "✅ ゲームグッズQRコード作成成功\n";
    } else {
        echo "❌ ゲームグッズQRコード作成失敗: HTTP $httpCode\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
    }
} else {
    echo "❌ QRコード生成失敗: HTTP $httpCode\n";
    exit(1);
}

// 8. 作成したQRコードを確認
echo "8. 作成したQRコードを確認中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/qrcodes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ QRコード一覧ページアクセス成功\n";
    
    // 作成したQRコードの確認
    if (strpos($response, 'テスト音楽イベント2024') !== false) {
        echo "✅ リアルイベントQRコードが一覧に表示されています\n";
    } else {
        echo "❌ リアルイベントQRコードが一覧に表示されていません\n";
    }
    
    if (strpos($response, 'レアアバターアイテム') !== false) {
        echo "✅ ゲームグッズQRコードが一覧に表示されています\n";
    } else {
        echo "❌ ゲームグッズQRコードが一覧に表示されていません\n";
    }
} else {
    echo "❌ QRコード一覧ページアクセス失敗: HTTP $httpCode\n";
}

// 9. ログアウト
echo "9. ログアウトをテスト中...\n";
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
echo "QRコード機能分離のテストが完了しました。\n";
