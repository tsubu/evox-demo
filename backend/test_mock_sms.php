<?php

require_once __DIR__ . '/vendor/autoload.php';

// Laravelアプリケーションを起動
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SmsService;

echo "=== モックSMS送信テスト ===\n";

// SmsServiceのインスタンスを作成
$smsService = new SmsService();

// テスト用の電話番号とメッセージ
$testNumber = '+818090330374';
$testMessage = '【EvoX】モックSMSテスト: ' . date('Y-m-d H:i:s');

echo "送信先: {$testNumber}\n";
echo "メッセージ: {$testMessage}\n";
echo "プロバイダー: " . config('services.sms.provider', 'mock') . "\n";

// SMS送信を実行
$result = $smsService->send($testNumber, $testMessage);

if ($result) {
    echo "\n✅ モックSMS送信成功！\n";
    echo "開発環境では実際のSMSは送信されませんが、\n";
    echo "ログに送信情報が記録されます。\n";
} else {
    echo "\n❌ モックSMS送信失敗\n";
}

echo "\n=== ログ確認 ===\n";
echo "Laravelログファイルを確認してください:\n";
echo "tail -f storage/logs/laravel.log\n";

echo "\n=== 設定確認 ===\n";
echo "SMS_PROVIDER: " . config('services.sms.provider') . "\n";
echo "Twilio設定:\n";
echo "  - Account SID: " . (config('services.sms.twilio.account_sid') ? '設定済み' : '未設定') . "\n";
echo "  - Auth Token: " . (config('services.sms.twilio.auth_token') ? '設定済み' : '未設定') . "\n";
echo "  - From Number: " . (config('services.sms.twilio.from_number') ?: '未設定') . "\n";
