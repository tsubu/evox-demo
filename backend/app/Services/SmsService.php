<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class SmsService
{
    private $provider;
    private $config;

    public function __construct()
    {
        $this->provider = config('services.sms.provider', 'mock');
        $this->config = config('services.sms');
    }

    /**
     * SMSを送信
     */
    public function send(string $to, string $message): bool
    {
        try {
            switch ($this->provider) {
                case 'twilio':
                    return $this->sendViaTwilio($to, $message);
                case 'twilio_sns':
                    return $this->sendViaTwilioSns($to, $message);
                case 'sns':
                    return $this->sendViaSns($to, $message);
                case 'email':
                    return $this->sendViaEmail($to, $message);
                case 'nexmo':
                    return $this->sendViaNexmo($to, $message);
                case 'plivo':
                    return $this->sendViaPlivo($to, $message);
                case 'mock':
                default:
                    return $this->sendMock($to, $message);
            }
        } catch (\Exception $e) {
            Log::error('SMS送信エラー', [
                'to' => $to,
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Twilio経由でSMS送信
     */
    private function sendViaTwilio(string $to, string $message): bool
    {
        $accountSid = $this->config['twilio']['account_sid'] ?? null;
        $authToken = $this->config['twilio']['auth_token'] ?? null;
        $fromNumber = $this->config['twilio']['from_number'] ?? null;

        // デバッグ情報をログに出力
        Log::info('Twilio SMS送信開始', [
            'to' => $to,
            'from' => $fromNumber,
            'account_sid' => $accountSid ? substr($accountSid, 0, 10) . '...' : 'null',
            'auth_token' => $authToken ? substr($authToken, 0, 10) . '...' : 'null'
        ]);

        if (!$accountSid || !$authToken || !$fromNumber) {
            Log::error('Twilio設定が不足しています', [
                'account_sid' => $accountSid ? '設定済み' : '未設定',
                'auth_token' => $authToken ? '設定済み' : '未設定',
                'from_number' => $fromNumber ?: '未設定'
            ]);
            return false;
        }

        try {
            $client = new Client($accountSid, $authToken);
            $message = $client->messages->create(
                $to,
                [
                    'from' => $fromNumber,
                    'body' => $message
                ]
            );

            Log::info('Twilio SMS送信成功', [
                'to' => $to,
                'message_id' => $message->sid,
                'status' => $message->status
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SMS送信失敗', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Twilio SNS経由でSMS送信（WhatsApp Business API等）
     */
    private function sendViaTwilioSns(string $to, string $message): bool
    {
        $accountSid = $this->config['twilio']['account_sid'] ?? null;
        $authToken = $this->config['twilio']['auth_token'] ?? null;
        $fromNumber = $this->config['twilio']['from_number'] ?? null;
        $snsChannel = $this->config['twilio']['sns_channel'] ?? 'whatsapp'; // whatsapp, messenger, etc.

        if (!$accountSid || !$authToken || !$fromNumber) {
            Log::error('Twilio SNS設定が不足しています');
            return false;
        }

        try {
            $client = new Client($accountSid, $authToken);
            
            // SNSチャンネルに応じて送信先をフォーマット
            $formattedTo = $this->formatSnsRecipient($to, $snsChannel);
            $formattedFrom = $this->formatSnsSender($fromNumber, $snsChannel);
            
            $message = $client->messages->create(
                $formattedTo,
                [
                    'from' => $formattedFrom,
                    'body' => $message
                ]
            );

            Log::info('Twilio SNS送信成功', [
                'to' => $to,
                'channel' => $snsChannel,
                'message_id' => $message->sid,
                'status' => $message->status
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SNS送信失敗', [
                'to' => $to,
                'channel' => $snsChannel,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * SNS受信者をフォーマット
     */
    private function formatSnsRecipient(string $to, string $channel): string
    {
        switch ($channel) {
            case 'whatsapp':
                // WhatsApp: whatsapp:+1234567890
                return "whatsapp:{$to}";
            case 'messenger':
                // Facebook Messenger: messenger:+1234567890
                return "messenger:{$to}";
            case 'instagram':
                // Instagram: instagram:+1234567890
                return "instagram:{$to}";
            default:
                return $to;
        }
    }

    /**
     * SNS送信者をフォーマット
     */
    private function formatSnsSender(string $from, string $channel): string
    {
        switch ($channel) {
            case 'whatsapp':
                // WhatsApp: whatsapp:+1234567890
                return "whatsapp:{$from}";
            case 'messenger':
                // Facebook Messenger: messenger:+1234567890
                return "messenger:{$from}";
            case 'instagram':
                // Instagram: instagram:+1234567890
                return "instagram:{$from}";
            default:
                return $from;
        }
    }

    /**
     * AWS SNS経由でSMS送信
     */
    private function sendViaSns(string $to, string $message): bool
    {
        $region = $this->config['sns']['region'] ?? 'ap-northeast-1';
        $accessKey = $this->config['sns']['access_key_id'] ?? null;
        $secretKey = $this->config['sns']['secret_access_key'] ?? null;

        if (!$accessKey || !$secretKey) {
            Log::error('AWS SNS設定が不足しています');
            return false;
        }

        // AWS SDK for PHPを使用する場合
        // ここでは簡易的なHTTPリクエストで実装
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $date = gmdate('Ymd');
        
        // 実際の実装ではAWS SDK for PHPを使用することを推奨
        Log::info('AWS SNS SMS送信（実装要）', [
            'to' => $to,
            'message' => $message,
            'region' => $region
        ]);
        
        return true; // 実装完了までtrueを返す
    }

    /**
     * モックSMS送信（開発用）
     */
    public function sendMock(string $to, string $message): bool
    {
        Log::info('モックSMS送信', [
            'to' => $to,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]);

        // 開発環境ではコンソールに出力
        if (app()->environment('local', 'development')) {
            echo "\n=== SMS送信（モック） ===\n";
            echo "宛先: {$to}\n";
            echo "メッセージ: {$message}\n";
            echo "送信時刻: " . now()->toISOString() . "\n";
            echo "========================\n\n";
        }

        return true;
    }

    /**
     * OTPメッセージを生成
     */
    public function generateOtpMessage(string $code): string
    {
        return "【EvoX】認証コード: {$code}\nこのコードは10分間有効です。";
    }
}
