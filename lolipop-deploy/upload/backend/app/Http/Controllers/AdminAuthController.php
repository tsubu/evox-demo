<?php

namespace App\Http\Controllers;

use App\Models\UserAdmin;
use App\Models\OtpCodeAdmin;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * 管理者ログイン画面表示
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * 管理者ログイン処理（ステップ1: 電話番号・パスワード入力）
     */
    public function login(Request $request)
    {
        $request->validate([
            'admin_phone' => 'required|string|max:20',
            'admin_password' => 'required|string|min:8',
            'temp_id' => 'required|string|max:13',
        ]);

        // デバッグログ
        \Log::info('Admin login attempt', [
            'admin_phone' => $request->admin_phone,
            'admin_password_length' => strlen($request->admin_password),
            'request_data' => $request->all(),
            'request_headers' => $request->headers->all()
        ]);

        // 管理者ユーザーを検索
        $admin = UserAdmin::where('admin_phone', $request->admin_phone)->first();

        if (!$admin) {
            \Log::warning('Admin not found', ['admin_phone' => $request->admin_phone]);
            return response()->json([
                'message' => '電話番号またはパスワードが正しくありません。'
            ], 401);
        }

        if (!Hash::check($request->admin_password, $admin->admin_password)) {
            \Log::warning('Admin password mismatch', [
                'admin_phone' => $request->admin_phone,
                'password_provided' => $request->admin_password,
                'password_hash' => $admin->admin_password
            ]);
            return response()->json([
                'message' => '電話番号またはパスワードが正しくありません。'
            ], 401);
        }

        // フロントエンドから送信されたtemp_idを使用
        $tempId = $request->temp_id;

        // 既存のOTPコードをチェック（重複送信防止）
        $existingOtp = OtpCodeAdmin::where('adminopt_temp_id', $tempId)
            ->where('adminopt_expires_at', '>', now())
            ->where('adminopt_is_used', false)
            ->first();

        if ($existingOtp) {
            \Log::info('OTP code already exists for temp_id', [
                'temp_id' => $tempId,
                'existing_otp' => $existingOtp->adminopt_code
            ]);
            
            return response()->json([
                'message' => '認証コードは既に送信されています。',
                'temp_id' => $tempId
            ]);
        }

        // OTPコードを生成
        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        \Log::info('Creating OTP code', [
            'temp_id' => $tempId,
            'otp_code' => $otpCode,
            'admin_phone' => $request->admin_phone
        ]);

        // OTPコードを保存
        OtpCodeAdmin::create([
            'adminopt_temp_id' => $tempId,
            'adminopt_code' => $otpCode,
            'adminopt_expires_at' => now()->addMinutes(10),
            'adminopt_is_used' => false,
        ]);

        // SMS送信
        $message = "【EvoX管理画面】認証コード: {$otpCode}\nこのコードは10分間有効です。";
        $this->smsService->send($request->admin_phone, $message);

        return response()->json([
            'message' => '認証コードを送信しました。',
            'temp_id' => $tempId
        ]);
    }

    /**
     * OTP認証（ステップ2）
     */
    public function verifyOtp(Request $request)
    {
        // デバッグログ
        \Log::info('Admin OTP verification request', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_data' => $request->all(),
            'input_data' => $request->input(),
            'json_data' => $request->json() ? $request->json()->all() : null
        ]);
        
        // フォームデータとJSONデータの両方に対応
        $tempId = $request->input('temp_id');
        $otpCode = $request->input('otp_code');
        $adminPhone = $request->input('admin_phone');
        
        // JSONデータの場合
        if ($request->isJson()) {
            $tempId = $request->json('temp_id') ?? $tempId;
            $otpCode = $request->json('otp_code') ?? $otpCode;
            $adminPhone = $request->json('admin_phone') ?? $adminPhone;
        }
        
        try {
            $request->validate([
                'temp_id' => 'required|string|max:13',
                'otp_code' => 'required|string|size:6',
                'admin_phone' => 'required|string|max:20',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Admin OTP validation failed', [
                'errors' => $e->errors(),
                'input_data' => $request->all()
            ]);
            
            return response()->json([
                'message' => '入力データが無効です。',
                'errors' => $e->errors()
            ], 400);
        }
        

        $otpCode = OtpCodeAdmin::where('adminopt_temp_id', $tempId)
            ->where('adminopt_code', $otpCode)
            ->where('adminopt_expires_at', '>', now())
            ->where('adminopt_is_used', false)
            ->first();

        if (!$otpCode) {
            return response()->json([
                'message' => '認証コードが無効です。'
            ], 400);
        }

        // OTPコードを使用済みにマーク
        $otpCode->update(['adminopt_is_used' => true]);

        // 管理者ユーザーを取得
        $admin = UserAdmin::where('admin_phone', $adminPhone)->first();

        if (!$admin) {
            return response()->json([
                'message' => '管理者が見つかりません。'
            ], 404);
        }

        // 認証済みにマーク
        $admin->update([
            'admin_is_verified' => true,
            'admin_verified_at' => now(),
        ]);

        // セッションに管理者をログイン
        Auth::guard('admin')->login($admin);
        
        // セッションを保存
        $request->session()->save();

        \Log::info('Admin login successful', [
            'admin_id' => $admin->id,
            'admin_name' => $admin->admin_name,
            'session_id' => session()->getId()
        ]);

        return response()->json([
            'success' => true,
            'message' => '認証が完了しました。',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'admin_name' => $admin->admin_name,
                    'admin_phone' => $admin->admin_phone,
                ],
                'redirect_url' => '/admin/dashboard'
            ]
        ]);
    }

    /**
     * 管理者ログアウト
     */
    public function logout(Request $request)
    {
        // セッションからログアウト
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin-' . env('ADMIN_URL_HASH', 'evox2025'));
    }

    /**
     * 管理者情報取得
     */
    public function me(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return response()->json([
                'message' => '認証されていません。'
            ], 401);
        }

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->admin_name,
                'email' => $admin->admin_email,
                'phone' => $admin->admin_phone,
            ]
        ]);
    }
}
