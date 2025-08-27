<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

// 環境変数を読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio設定
$accountSid = $_ENV['TWILIO_ACCOUNT_SID'] ?? null;
$authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? null;
$fromNumber = $_ENV['TWILIO_FROM_NUMBER'] ?? null;

echo "=== Twilio詳細診断 ===\n";
echo "Account SID: " . ($accountSid ? substr($accountSid, 0, 10) . '...' : '未設定') . "\n";
echo "Auth Token: " . ($authToken ? substr($authToken, 0, 10) . '...' : '未設定') . "\n";
echo "From Number: " . ($fromNumber ?: '未設定') . "\n\n";

if (!$accountSid || !$authToken || !$fromNumber) {
    echo "❌ Twilio設定が不足しています。\n";
    exit(1);
}

try {
    echo "📡 Twilio APIに接続中...\n";
    $client = new Client($accountSid, $authToken);
    
    // 1. アカウント情報を取得
    echo "\n=== アカウント情報 ===\n";
    try {
        $account = $client->api->accounts($accountSid)->fetch();
        echo "✅ アカウント接続成功\n";
        echo "アカウント名: " . $account->friendlyName . "\n";
        echo "ステータス: " . $account->status . "\n";
        echo "タイプ: " . $account->type . "\n";
        echo "作成日: " . $account->dateCreated->format('Y-m-d H:i:s') . "\n";
        
        if ($account->status !== 'active') {
            echo "⚠️  アカウントが非アクティブです: " . $account->status . "\n";
        }
    } catch (RestException $e) {
        echo "❌ アカウント情報取得失敗: " . $e->getMessage() . "\n";
        echo "エラーコード: " . $e->getCode() . "\n";
    }
    
    // 2. 電話番号情報を取得
    echo "\n=== 電話番号情報 ===\n";
    try {
        $incomingPhoneNumbers = $client->incomingPhoneNumbers->read(['phoneNumber' => $fromNumber]);
        if (empty($incomingPhoneNumbers)) {
            echo "❌ 電話番号が見つかりません: {$fromNumber}\n";
        } else {
            $phoneNumber = $incomingPhoneNumbers[0];
            echo "✅ 電話番号情報取得成功\n";
            echo "電話番号: " . $phoneNumber->phoneNumber . "\n";
            echo "フレンドリ名: " . $phoneNumber->friendlyName . "\n";
            echo "SMS機能: " . ($phoneNumber->capabilities->sms ? '有効' : '無効') . "\n";
            echo "音声機能: " . ($phoneNumber->capabilities->voice ? '有効' : '無効') . "\n";
            echo "MMS機能: " . ($phoneNumber->capabilities->mms ? '有効' : '無効') . "\n";
            
            if (!$phoneNumber->capabilities->sms) {
                echo "⚠️  SMS機能が無効です\n";
            }
        }
    } catch (RestException $e) {
        echo "❌ 電話番号情報取得失敗: " . $e->getMessage() . "\n";
    }
    
    // 3. 利用可能な電話番号を一覧表示
    echo "\n=== 利用可能な電話番号 ===\n";
    try {
        $phoneNumbers = $client->incomingPhoneNumbers->read([], 5);
        if (empty($phoneNumbers)) {
            echo "❌ 利用可能な電話番号がありません\n";
        } else {
            echo "✅ 利用可能な電話番号:\n";
            foreach ($phoneNumbers as $number) {
                echo "  - " . $number->phoneNumber . " (SMS: " . ($number->capabilities->sms ? '有効' : '無効') . ")\n";
            }
        }
    } catch (RestException $e) {
        echo "❌ 電話番号一覧取得失敗: " . $e->getMessage() . "\n";
    }
    
    // 4. アカウントの使用量を確認
    echo "\n=== アカウント使用量 ===\n";
    try {
        $usageRecords = $client->usage->records->read([], 1);
        if (!empty($usageRecords)) {
            $usage = $usageRecords[0];
            echo "✅ 使用量情報取得成功\n";
            echo "期間: " . $usage->period . "\n";
            echo "カテゴリ: " . $usage->category . "\n";
            echo "使用量: " . $usage->usage . " " . $usage->usageUnit . "\n";
        }
    } catch (RestException $e) {
        echo "❌ 使用量情報取得失敗: " . $e->getMessage() . "\n";
    }
    
    // 5. 簡単なSMS送信テスト
    echo "\n=== SMS送信テスト ===\n";
    $testNumber = '+818090330374';
    $testMessage = '【EvoX】診断テスト: ' . date('Y-m-d H:i:s');
    
    try {
        echo "送信先: {$testNumber}\n";
        echo "メッセージ: {$testMessage}\n";
        echo "送信元: {$fromNumber}\n";
        
        $message = $client->messages->create(
            $testNumber,
            [
                'from' => $fromNumber,
                'body' => $testMessage
            ]
        );
        
        echo "✅ SMS送信成功！\n";
        echo "Message SID: {$message->sid}\n";
        echo "Status: {$message->status}\n";
        echo "Price: {$message->price}\n";
        
    } catch (RestException $e) {
        echo "❌ SMS送信失敗\n";
        echo "エラー: " . $e->getMessage() . "\n";
        echo "エラーコード: " . $e->getCode() . "\n";
        
        // エラーコードの詳細説明
        switch ($e->getCode()) {
            case 20003:
                echo "→ 認証エラー: Account SIDまたはAuth Tokenが無効です\n";
                break;
            case 20008:
                echo "→ 無効な電話番号: 送信先の電話番号が無効です\n";
                break;
            case 20012:
                echo "→ 無効な送信元: 送信元の電話番号が無効です\n";
                break;
            case 20016:
                echo "→ アカウント無効: アカウントが無効化されています\n";
                break;
            case 20017:
                echo "→ アカウント一時停止: アカウントが一時停止されています\n";
                break;
            case 20018:
                echo "→ アカウント削除: アカウントが削除されています\n";
                break;
            case 20019:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20020:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20021:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20022:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20023:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20024:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20025:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20026:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20027:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20028:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20029:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            case 20030:
                echo "→ アカウント無効: アカウントが無効です\n";
                break;
            default:
                echo "→ その他のエラー: Twilioサポートに問い合わせてください\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Twilioクライアント初期化失敗: " . $e->getMessage() . "\n";
}

echo "\n=== 推奨される対処法 ===\n";
echo "1. Twilioコンソールでアカウントの状態を確認\n";
echo "2. Auth Tokenを再生成\n";
echo "3. 電話番号が認証済みか確認\n";
echo "4. アカウントに十分なクレジットがあるか確認\n";
echo "5. 開発環境ではSMS_PROVIDER=mockを使用\n";
