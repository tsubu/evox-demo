<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\PreRegistration;
use App\Models\OtpCode;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * ログイン（ステップ1: パスワード認証 + SMS送信）
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:20|regex:/^\+[1-9]\d{1,14}$/',
            'password' => 'required|string|min:8|max:255',
            'temp_id' => 'required|string|size:13'
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

        // 既存の一時的な登録情報を削除（同じtemp_idが存在する場合）
        PreRegistration::where('prereg_temp_id', $request->temp_id)->delete();
        
        // 新しい一時的な登録情報を作成（ログイン用）
        PreRegistration::create([
            'prereg_temp_id' => $request->temp_id,
            'prereg_phone' => $normalizedPhone,
            'prereg_password' => '', // ログイン時はパスワードを保存しない
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
     * ログイン（ステップ2: OTP認証）
     */
    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'temp_id' => 'required|string|size:13',
            'code' => 'required|string|size:6',
        ]);

        // OTPコードを検証
        $otpCode = OtpCode::where('useropt_temp_id', $request->temp_id)
            ->where('useropt_code', $request->code)
            ->where('useropt_is_used', false)
            ->where('useropt_expires_at', '>', Carbon::now())
            ->first();

        if (!$otpCode) {
            return response()->json([
                'success' => false,
                'message' => '認証コードが無効です。'
            ], 400);
        }

        // 事前登録情報からユーザーを特定
        $preRegistration = PreRegistration::where('prereg_temp_id', $request->temp_id)->first();
        
        if (!$preRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'ログイン情報が見つかりません。'
            ], 400);
        }

        $user = User::where('phone', $preRegistration->prereg_phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ユーザーが見つかりません。'
            ], 400);
        }

        // OTPコードを使用済みに更新
        $otpCode->update(['useropt_is_used' => true]);

        // 認証トークンを生成
        $token = $user->createToken('auth-token')->plainTextToken;

        // ログイン完了後、一時的な登録情報を削除
        $preRegistration->delete();

        return response()->json([
            'success' => true,
            'message' => 'ログインに成功しました。',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * ログアウト
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'ログアウトしました。'
        ]);
    }

    /**
     * 現在のユーザー情報を取得
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                ]
            ]
        ]);
    }
}
