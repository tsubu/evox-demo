<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class MyPageController extends Controller
{
    /**
     * プロフィールを取得
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        // ゲーム基本情報を取得
        $gameBase = $user->gameBase;
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'points' => $gameBase ? $gameBase->gamebase_points : 0,
                    'nickname' => $gameBase ? $gameBase->gamebase_nickname : null,
                    'avatar_choice' => $gameBase ? $gameBase->gamebase_avatar_choice : null,
                ]
            ]
        ]);
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $user->update([
            'name' => $request->name,
        ]);

        // ゲーム基本情報を取得
        $gameBase = $user->gameBase;

        return response()->json([
            'success' => true,
            'message' => 'プロフィールを更新しました。',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'points' => $gameBase ? $gameBase->gamebase_points : 0,
                    'nickname' => $gameBase ? $gameBase->gamebase_nickname : null,
                    'avatar_choice' => $gameBase ? $gameBase->gamebase_avatar_choice : null,
                ]
            ]
        ]);
    }

    /**
     * ポイント情報を取得
     */
    public function points(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // ゲーム基本情報を取得
        $gameBase = $user->gameBase;

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $gameBase ? $gameBase->gamebase_points : 0
            ]
        ]);
    }
}
