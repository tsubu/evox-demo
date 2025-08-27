<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    /**
     * 事前登録処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users,phone',
            'temp_id' => 'required|string',
        ]);

        // 電話番号の正規化処理
        $normalizedPhone = $this->normalizePhoneNumber($request->phone);

        // フロントエンドから送信された仮IDを使用
        $tempId = $request->temp_id;

        // ユーザーを作成（仮登録状態）
        $user = User::create([
            'phone' => $normalizedPhone,
            'name' => 'temp_' . $tempId, // 仮の名前
            'email' => 'temp_' . $tempId . '@temp.com', // 仮のメール
            'password' => Hash::make('temp_password'), // 仮のパスワード
        ]);

        // OTPコードを生成
        $otp = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // OTPコードをデータベースに保存
        OtpCode::create([
            'useropt_temp_id' => $tempId,
            'useropt_code' => $otp,
            'useropt_expires_at' => Carbon::now()->addMinutes(10),
            'useropt_is_used' => false,
        ]);

        // SMS送信
        $smsService = new SmsService();
        $message = $smsService->generateOtpMessage($otp);
        $smsSent = $smsService->send($normalizedPhone, $message);

        // レスポンスデータ
        $responseData = [
            'temp_id' => $tempId,
            'phone' => $normalizedPhone
        ];

        // 開発環境ではOTPコードも返す（SMS送信が失敗した場合のフォールバック）
        if (app()->environment('local', 'development') && !$smsSent) {
            $responseData['otp_code'] = $otp;
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
     * OTP認証処理
     */
    public function verify(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        // デバッグログ
        \Log::info('OTP verification request', [
            'temp_id' => $request->temp_id,
            'otp' => $request->otp
        ]);

        // OTPコードを検証
        $otpCode = OtpCode::where('useropt_temp_id', $request->temp_id)
            ->where('useropt_code', $request->otp)
            ->where('useropt_is_used', false)
            ->where('useropt_expires_at', '>', Carbon::now())
            ->first();

        \Log::info('OTP verification result', [
            'otp_code_found' => $otpCode ? 'yes' : 'no',
            'current_time' => Carbon::now()->toISOString()
        ]);

        if (!$otpCode) {
            return response()->json([
                'success' => false,
                'message' => '認証コードが無効です。'
            ], 400);
        }

        // OTPコードを使用済みにマーク
        $otpCode->update(['useropt_is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => '認証に成功しました。',
            'data' => [
                'temp_id' => $request->temp_id
            ]
        ]);
    }

    /**
     * パスワード設定処理
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // デバッグ用ログ
        \Log::info('setPassword - temp_id: ' . $request->temp_id);
        \Log::info('setPassword - searching for name: temp_' . $request->temp_id);

        $user = User::where('name', 'temp_' . $request->temp_id)->first();
        
        \Log::info('setPassword - user found: ' . ($user ? 'yes' : 'no'));
        if ($user) {
            \Log::info('setPassword - user id: ' . $user->id . ', name: ' . $user->name);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '無効な仮IDです。'
            ], 400);
        }

        // パスワードを設定
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'パスワードが設定されました。',
            'data' => [
                'temp_id' => $request->temp_id
            ]
        ]);
    }

    /**
     * 登録完了処理
     */
    public function complete(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // デバッグ用ログ
        \Log::info('complete - temp_id: ' . $request->temp_id);
        \Log::info('complete - searching for name: temp_' . $request->temp_id);

        $user = User::where('name', 'temp_' . $request->temp_id)->first();
        
        \Log::info('complete - user found: ' . ($user ? 'yes' : 'no'));
        if ($user) {
            \Log::info('complete - user id: ' . $user->id . ', name: ' . $user->name);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '無効な仮IDです。'
            ], 400);
        }

        // ユーザー情報を更新
        $user->update([
            'password' => Hash::make($request->password),
            'name' => 'user_' . $user->id, // 仮の名前
            'email' => 'user_' . $user->id . '@user.com', // 仮のメールアドレス
        ]);

        // 100ポイント付与（新規登録ボーナス）
        $gameBase = $user->gameBase;
        if (!$gameBase) {
            $gameBase = \App\Models\GameBase::create([
                'gamebase_userid' => $user->id,
                'gamebase_points' => 100, // 新規登録ボーナス
                'gamebase_level' => 1,
                'gamebase_exp' => 0,
            ]);
        } else {
            $gameBase->update([
                'gamebase_points' => $gameBase->gamebase_points + 100
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '登録が完了しました。',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'nickname' => $user->name,
                ]
            ]
        ]);
    }

    /**
     * 登録者数を取得
     */
    public function getCount()
    {
        $count = User::count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
