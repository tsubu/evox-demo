<?php

require_once 'vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ユーザー管理機能テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'user_management_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'user_management_cookies.txt');
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

// 2. ログインを実行
echo "2. ログインを実行中...\n";
$loginData = [
    'admin_phone' => '+818090330374',
    'admin_password' => 'admin123',
    'temp_id' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 13)
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
    if (isset($data['message']) && strpos($data['message'], '認証コードを送信') !== false) {
        echo "✅ OTPコード送信成功\n";
        
        // データベースからOTPコードを取得
        $tempId = $loginData['temp_id'];
        try {
            $otpRecord = \App\Models\OtpCodeAdmin::where('adminopt_temp_id', $tempId)
                ->where('adminopt_is_used', false)
                ->where('adminopt_expires_at', '>', now())
                ->first();
            
            if ($otpRecord) {
                $otpCode = $otpRecord->adminopt_code;
                echo "✅ OTPコード取得成功: " . $otpCode . "\n";
                
                // OTP認証を実行
                $otpData = [
                    'temp_id' => $tempId,
                    'otp_code' => $otpCode,
                    'admin_phone' => $loginData['admin_phone']
                ];
                
                curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/admin/auth/verify-otp');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($otpData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                    'X-CSRF-TOKEN: ' . $csrfToken
                ]);
                
                $otpResponse = curl_exec($ch);
                $otpHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if ($otpHttpCode === 200) {
                    $otpData = json_decode($otpResponse, true);
                    if (isset($otpData['success']) && $otpData['success']) {
                        echo "✅ OTP認証成功\n\n";
                    } else {
                        echo "❌ OTP認証失敗: " . ($otpData['message'] ?? 'Unknown error') . "\n";
                        exit(1);
                    }
                } else {
                    echo "❌ OTP認証失敗: HTTP " . $otpHttpCode . "\n";
                    exit(1);
                }
            } else {
                echo "❌ OTPコードが見つかりません\n";
                exit(1);
            }
        } catch (Exception $e) {
            echo "❌ OTPコード取得エラー: " . $e->getMessage() . "\n";
            exit(1);
        }
    } else {
        echo "❌ OTPコード送信失敗: " . ($data['message'] ?? 'Unknown error') . "\n";
        exit(1);
    }
} else {
    echo "❌ ログイン失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 3. ユーザー一覧ページにアクセス
echo "3. ユーザー一覧ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/users');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    if (strpos($response, 'ユーザー管理') !== false) {
        echo "✅ ユーザー一覧ページアクセス成功\n\n";
    } else {
        echo "⚠️ ユーザー一覧ページアクセス成功だが、内容に問題がある可能性があります\n\n";
    }
} else {
    echo "❌ ユーザー一覧ページアクセス失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 4. データベースからユーザーを取得して詳細ページをテスト
echo "4. ユーザー詳細ページをテスト中...\n";
try {
    $user = \App\Models\User::first();
    if ($user) {
        echo "   テスト対象ユーザー: ID {$user->id}, 名前: " . ($user->name ?? '未設定') . "\n";
        
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/users/' . $user->id);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200) {
            if (strpos($response, 'ユーザー詳細') !== false) {
                echo "✅ ユーザー詳細ページアクセス成功\n\n";
            } else {
                echo "⚠️ ユーザー詳細ページアクセス成功だが、内容に問題がある可能性があります\n\n";
            }
        } else {
            echo "❌ ユーザー詳細ページアクセス失敗: HTTP " . $httpCode . "\n";
        }
    } else {
        echo "⚠️ テスト対象のユーザーが見つかりません\n\n";
    }
} catch (Exception $e) {
    echo "❌ ユーザー詳細ページテストエラー: " . $e->getMessage() . "\n\n";
}

// 5. ログアウトをテスト
echo "5. ログアウトをテスト中...\n";
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
if (file_exists('user_management_cookies.txt')) {
    unlink('user_management_cookies.txt');
}

echo "\n=== テスト完了 ===\n";
echo "ユーザー管理機能のテストが完了しました。\n";
