<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\OtpCode;
use App\Models\PreRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OtpController extends Controller
{
    /**
     * OTPコードを検証
     */
    public function verify(Request $request): JsonResponse
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

        // 事前登録情報を取得
        $preRegistration = PreRegistration::where('prereg_temp_id', $request->temp_id)->first();

        if (!$preRegistration) {
            return response()->json([
                'success' => false,
                'message' => '事前登録情報が見つかりません。'
            ], 400);
        }

        // OTPコードを使用済みに更新
        $otpCode->update(['useropt_is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => '認証が完了しました。パスワードを設定してください。',
            'data' => [
                'temp_id' => $request->temp_id,
                'phone' => $preRegistration->prereg_phone
            ]
        ]);
    }
}
