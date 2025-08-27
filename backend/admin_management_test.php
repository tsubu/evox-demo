<?php

require_once 'vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 管理者追加機能テスト ===\n\n";

// 1. ログインページにアクセスしてCSRFトークンを取得
echo "1. ログインページにアクセスしてCSRFトークンを取得中...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin-ai8edq64p2i5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'admin_management_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'admin_management_cookies.txt');
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

// 3. 管理者管理ページにアクセス
echo "3. 管理者管理ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/admins');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    if (strpos($response, '管理者管理') !== false) {
        echo "✅ 管理者管理ページアクセス成功\n\n";
    } else {
        echo "⚠️ 管理者管理ページアクセス成功だが、内容に問題がある可能性があります\n\n";
    }
} else {
    echo "❌ 管理者管理ページアクセス失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 4. 管理者作成ページにアクセス
echo "4. 管理者作成ページにアクセス中...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/admins/create');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    if (strpos($response, '新規管理者追加') !== false) {
        echo "✅ 管理者作成ページアクセス成功\n\n";
    } else {
        echo "⚠️ 管理者作成ページアクセス成功だが、内容に問題がある可能性があります\n\n";
    }
} else {
    echo "❌ 管理者作成ページアクセス失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 5. 新しい管理者を作成
echo "5. 新しい管理者を作成中...\n";
$newAdminData = [
    'admin_name' => 'テスト管理者2',
    'admin_phone' => '+818090330375',
    'admin_email' => 'testadmin2@example.com',
    'admin_password' => 'admin123456',
    'admin_password_confirmation' => 'admin123456'
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/admins');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($newAdminData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200 || $httpCode === 302) {
    if (strpos($response, '作成しました') !== false || $httpCode === 302) {
        echo "✅ 管理者作成成功\n\n";
    } else {
        echo "⚠️ 管理者作成成功だが、メッセージが確認できません\n\n";
    }
} else {
    echo "❌ 管理者作成失敗: HTTP " . $httpCode . "\n";
    exit(1);
}

// 6. 作成された管理者を確認
echo "6. 作成された管理者を確認中...\n";
try {
    $newAdmin = \App\Models\UserAdmin::where('admin_phone', '+818090330375')->first();
    if ($newAdmin) {
        echo "✅ 新しい管理者がデータベースに作成されました\n";
        echo "   管理者名: " . $newAdmin->admin_name . "\n";
        echo "   電話番号: " . $newAdmin->admin_phone . "\n";
        echo "   メール: " . $newAdmin->admin_email . "\n";
        echo "   認証状況: " . ($newAdmin->admin_is_verified ? '認証済み' : '未認証') . "\n\n";
    } else {
        echo "❌ 新しい管理者がデータベースに見つかりません\n\n";
    }
} catch (Exception $e) {
    echo "❌ データベース確認エラー: " . $e->getMessage() . "\n\n";
}

// 7. ログアウトをテスト
echo "7. ログアウトをテスト中...\n";
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
if (file_exists('admin_management_cookies.txt')) {
    unlink('admin_management_cookies.txt');
}

echo "\n=== テスト完了 ===\n";
echo "管理者追加機能のテストが完了しました。\n";
