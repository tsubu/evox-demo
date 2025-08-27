<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

// 環境変数を読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio設定
$accountSid = $_ENV['TWILIO_ACCOUNT_SID'] ?? null;
$authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? null;
$fromNumber = $_ENV['TWILIO_FROM_NUMBER'] ?? null;

echo "=== Twilio設定確認 ===\n";
echo "Account SID: " . ($accountSid ? substr($accountSid, 0, 10) . '...' : '未設定') . "\n";
echo "Auth Token: " . ($authToken ? substr($authToken, 0, 10) . '...' : '未設定') . "\n";
echo "From Number: " . ($fromNumber ?: '未設定') . "\n";

if (!$accountSid || !$authToken || !$fromNumber) {
    echo "\n❌ Twilio設定が不足しています。.envファイルを確認してください。\n";
    exit(1);
}

// テスト用の電話番号
$testNumber = '+818090330374'; // テスト用の電話番号
$testMessage = '【EvoX】テストメッセージ: ' . date('Y-m-d H:i:s');

echo "\n=== SMS送信テスト ===\n";
echo "送信先: {$testNumber}\n";
echo "メッセージ: {$testMessage}\n";
echo "送信元: {$fromNumber}\n";

try {
    $client = new Client($accountSid, $authToken);
    
    echo "\n📤 SMS送信中...\n";
    
    $message = $client->messages->create(
        $testNumber,
        [
            'from' => $fromNumber,
            'body' => $testMessage
        ]
    );

    echo "\n✅ SMS送信成功！\n";
    echo "Message SID: {$message->sid}\n";
    echo "Status: {$message->status}\n";
    echo "Price: {$message->price}\n";
    echo "Price Unit: {$message->priceUnit}\n";
    
} catch (Exception $e) {
    echo "\n❌ SMS送信失敗\n";
    echo "エラー: " . $e->getMessage() . "\n";
    echo "エラーコード: " . $e->getCode() . "\n";
    
    // 詳細なエラー情報を表示
    if (method_exists($e, 'getResponse')) {
        $response = $e->getResponse();
        if ($response) {
            echo "レスポンス: " . $response->getBody()->getContents() . "\n";
        }
    }
}

echo "\n=== 設定確認 ===\n";
echo "1. Twilioアカウントが有効か確認してください\n";
echo "2. 電話番号が認証済みか確認してください\n";
echo "3. 送信先の電話番号が正しい形式か確認してください\n";
echo "4. アカウントに十分なクレジットがあるか確認してください\n";
