<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PreRegistration;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PreregController extends Controller
{
    /**
     * 事前登録を保存（電話番号のみ）
     */
    public function store(Request $request): JsonResponse
    {
        // 電話番号の正規化処理
        $normalizedPhone = $this->normalizePhoneNumber($request->phone);
        
        // 既に登録済みかチェック
        $existingUser = User::where('phone', $normalizedPhone)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'この電話番号は既に登録済みです。ログインページからログインしてください。',
                'code' => 'ALREADY_REGISTERED'
            ], 400);
        }

        $request->validate([
            'phone' => 'required|string|min:8|max:20|regex:/^\+[1-9]\d{1,14}$/',
            'temp_id' => 'required|string|size:13'
        ]);

        // 事前登録を保存（パスワードなし）
        $preRegistration = PreRegistration::create([
            'prereg_temp_id' => $request->temp_id,
            'prereg_phone' => $normalizedPhone,
            'prereg_password' => '', // パスワードは後で設定
        ]);

        // OTPコードを生成
        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpCode::create([
            'useropt_temp_id' => $request->temp_id,
            'useropt_code' => $otpCode,
            'useropt_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // SMS送信
        $smsService = new SmsService();
        $message = $smsService->generateOtpMessage($otpCode);
        $smsSent = $smsService->send($normalizedPhone, $message);

        $responseData = [
            'temp_id' => $request->temp_id,
        ];

        // 開発環境ではOTPコードも返す
        if (app()->environment('local', 'development')) {
            $responseData['otp_code'] = $otpCode;
        }

        return response()->json([
            'success' => true,
            'message' => $smsSent ? '認証コードを送信しました。' : '認証コードの送信に失敗しました。',
            'data' => $responseData
        ]);
    }

    /**
     * 電話番号を正規化する（全角数字・漢数字を半角数字に変換）
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // 全角数字を半角数字に変換
        $fullWidthToHalfWidth = [
            '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9'
        ];
        
        // 漢数字を半角数字に変換
        $kanjiToNumber = [
            '零' => '0', '一' => '1', '二' => '2', '三' => '3', '四' => '4',
            '五' => '5', '六' => '6', '七' => '7', '八' => '8', '九' => '9'
        ];
        
        $result = $phone;
        
        // 全角数字を変換
        foreach ($fullWidthToHalfWidth as $fullWidth => $halfWidth) {
            $result = str_replace($fullWidth, $halfWidth, $result);
        }
        
        // 漢数字を変換
        foreach ($kanjiToNumber as $kanji => $number) {
            $result = str_replace($kanji, $number, $result);
        }
        
        // 数字以外の文字を除去（+記号は保持）
        $result = preg_replace('/[^0-9+]/', '', $result);
        
        return $result;
    }

    /**
     * パスワード設定とユーザー作成
     */
    public function setPassword(Request $request): JsonResponse
    {
        $request->validate([
            'temp_id' => 'required|string|size:13',
            'password' => 'required|string|min:8|max:255',
        ]);

        // 事前登録情報を取得
        $preRegistration = PreRegistration::where('prereg_temp_id', $request->temp_id)->first();

        if (!$preRegistration) {
            return response()->json([
                'success' => false,
                'message' => '事前登録情報が見つかりません。'
            ], 400);
        }

        // パスワードを更新
        $preRegistration->update([
            'prereg_password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'パスワード設定が完了しました。'
        ]);
    }



    /**
     * 登録完了
     */
    public function complete(Request $request): JsonResponse
    {
        $request->validate([
            'temp_id' => 'required|string|size:13',
        ]);

        // 事前登録情報を取得
        $preRegistration = PreRegistration::where('prereg_temp_id', $request->temp_id)->first();

        if (!$preRegistration) {
            return response()->json([
                'success' => false,
                'message' => '事前登録情報が見つかりません。'
            ], 400);
        }

        // ユーザーを作成
        $user = User::create([
            'name' => 'ユーザー' . substr($preRegistration->prereg_phone, -4), // 電話番号の下4桁を使用
            'email' => $preRegistration->prereg_phone . '@evox.local',
            'phone' => $preRegistration->prereg_phone,
            'password' => $preRegistration->prereg_password,
            'points' => 100, // 初期ポイント
            'is_admin' => false,
        ]);

        // 認証トークンを生成
        $token = $user->createToken('auth-token')->plainTextToken;

        // 事前登録情報を削除
        $preRegistration->delete();

        return response()->json([
            'success' => true,
            'message' => '登録が完了しました。',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
}
