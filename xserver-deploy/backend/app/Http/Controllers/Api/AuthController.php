<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * ログイン処理（第1段階：SMS認証コード送信）
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // 電話番号の正規化処理
        $normalizedPhone = $this->normalizePhoneNumber($request->phone);

        $user = User::where('phone', $normalizedPhone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '電話番号またはパスワードが正しくありません。'
            ], 401);
        }

        // 6桁のOTPコードを生成
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // 一時IDを生成
        $tempId = Str::uuid()->toString();
        
        // OTPコードをデータベースに保存
        OtpCode::create([
            'useropt_temp_id' => $tempId,
            'useropt_phone' => $normalizedPhone,
            'useropt_code' => $otpCode,
            'useropt_expires_at' => now()->addMinutes(10), // 10分で期限切れ
            'useropt_is_used' => false,
        ]);

        // SMSでOTPコードを送信
        try {
            $smsService = new SmsService();
            $smsService->send($normalizedPhone, "EvoX認証コード: {$otpCode}");
            
            return response()->json([
                'success' => true,
                'message' => '認証コードを送信しました。',
                'data' => [
                    'temp_id' => $tempId,
                    'phone' => $normalizedPhone
                ]
            ]);
        } catch (\Exception $e) {
            // SMS送信に失敗した場合、開発環境ではコードを返す
            if (app()->environment('local')) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS送信に失敗しました。OTPコード: ' . $otpCode,
                    'data' => [
                        'temp_id' => $tempId,
                        'phone' => $normalizedPhone,
                        'otp_code' => $otpCode // 開発環境のみ
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'SMS送信に失敗しました。'
            ], 500);
        }
    }

    /**
     * OTP認証コード検証（第2段階：ログイン完了）
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // OTPコードを検証
        $otpCode = OtpCode::where('useropt_temp_id', $request->temp_id)
            ->where('useropt_code', $request->code)
            ->where('useropt_expires_at', '>', now())
            ->where('useropt_is_used', false)
            ->first();

        if (!$otpCode) {
            return response()->json([
                'success' => false,
                'message' => '認証コードが正しくありません。'
            ], 401);
        }

        // ユーザーを取得
        $user = User::where('phone', $otpCode->useropt_phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ユーザーが見つかりません。'
            ], 404);
        }

        // 認証トークンを生成
        $token = $user->createToken('auth-token')->plainTextToken;

        // 使用済みOTPコードを削除
        $otpCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'ログインに成功しました。',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'nickname' => $user->nickname,
                ]
            ]
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'ログアウトしました。'
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
}
