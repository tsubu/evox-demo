<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GameBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * プロフィール完了状況をチェック
     */
    public function check()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }

        // プロフィール完了状況を確認
        $hasAvatar = !empty($user->avatar_choice);
        $hasNickname = !empty($user->nickname) && $user->nickname !== 'user_' . $user->id;
        
        $missingSteps = [];
        if (!$hasAvatar) {
            $missingSteps[] = 'avatar';
        }
        if (!$hasNickname) {
            $missingSteps[] = 'nickname';
        }
        
        $isComplete = empty($missingSteps);

        return response()->json([
            'success' => true,
            'data' => [
                'is_complete' => $isComplete,
                'has_avatar' => $hasAvatar,
                'has_nickname' => $hasNickname,
                'missing_steps' => $missingSteps
            ]
        ]);
    }

    /**
     * アバターを設定
     */
    public function setAvatar(Request $request)
    {
        $request->validate([
            'avatar_choice' => 'required|string|max:50'
        ]);

        $user = Auth::user();
        $user->update([
            'avatar_choice' => $request->avatar_choice
        ]);

        return response()->json([
            'success' => true,
            'message' => 'アバターが設定されました'
        ]);
    }

    /**
     * ニックネームを設定
     */
    public function setNickname(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:50'
        ]);

        $user = Auth::user();
        $user->update([
            'nickname' => $request->nickname
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ニックネームが設定されました'
        ]);
    }

    /**
     * パスワードを更新
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        $user = Auth::user();
        
        // 現在のパスワードを確認
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '現在のパスワードが正しくありません'
            ], 400);
        }

        // 新しいパスワードを設定
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'パスワードが更新されました'
        ]);
    }

    /**
     * プロフィール情報を更新
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date|before:today'
        ]);

        $user = Auth::user();
        $user->update([
            'email' => $request->email,
            'name' => $request->name,
            'birthday' => $request->birthday
        ]);

        return response()->json([
            'success' => true,
            'message' => 'プロフィールが更新されました'
        ]);
    }
}
