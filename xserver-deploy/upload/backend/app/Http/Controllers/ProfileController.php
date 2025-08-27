<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GameBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * プロフィール完了状況を確認
     */
    public function checkProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です。'
            ], 401);
        }

        // ゲーム基本情報を取得
        $gameBase = $user->gameBase;
        
        if (!$gameBase) {
            // ゲーム基本情報が存在しない場合は、プロフィール未完了として扱う
            $profileStatus = [
                'is_complete' => false,
                'has_avatar' => false,
                'has_nickname' => false,
                'missing_steps' => ['avatar', 'nickname']
            ];
        } else {
            $profileStatus = [
                'is_complete' => $gameBase->gamebase_is_profile_complete,
                'has_avatar' => !empty($gameBase->gamebase_avatar_choice),
                'has_nickname' => !empty($gameBase->gamebase_nickname),
                'missing_steps' => []
            ];

            if (empty($gameBase->gamebase_avatar_choice)) {
                $profileStatus['missing_steps'][] = 'avatar';
            }

            if (empty($gameBase->gamebase_nickname)) {
                $profileStatus['missing_steps'][] = 'nickname';
            }
        }

        return response()->json([
            'success' => true,
            'data' => $profileStatus
        ]);
    }

    /**
     * キャラクター選択
     */
    public function setAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar_choice' => 'required|string|in:car001,car002,car003,car004',
        ]);

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です。'
            ], 401);
        }

        // ゲーム基本情報を取得または作成
        $gameBase = $user->gameBase;
        
        if (!$gameBase) {
            $gameBase = $user->gameBase()->create([
                'gamebase_userid' => $user->id,
                'gamebase_points' => 0,
                'gamebase_avatar_choice' => $request->avatar_choice,
            ]);
        } else {
            $gameBase->update([
                'gamebase_avatar_choice' => $request->avatar_choice,
            ]);
        }

        // ニックネームも設定済みの場合はプロフィール完了
        if (!empty($gameBase->gamebase_nickname)) {
            $gameBase->update(['gamebase_is_profile_complete' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'キャラクター選択が完了しました。'
        ]);
    }

    /**
     * ニックネーム設定
     */
    public function setNickname(Request $request): JsonResponse
    {
        $request->validate([
            'nickname' => 'required|string|max:20|min:1',
        ]);

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です。'
            ], 401);
        }

        // ゲーム基本情報を取得または作成
        $gameBase = $user->gameBase;
        
        if (!$gameBase) {
            $gameBase = $user->gameBase()->create([
                'gamebase_userid' => $user->id,
                'gamebase_points' => 0,
                'gamebase_nickname' => $request->nickname,
            ]);
        } else {
            $gameBase->update([
                'gamebase_nickname' => $request->nickname,
            ]);
        }

        // キャラクター選択も設定済みの場合はプロフィール完了
        if (!empty($gameBase->gamebase_avatar_choice)) {
            $gameBase->update(['gamebase_is_profile_complete' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'ニックネーム設定が完了しました。'
        ]);
    }
}
