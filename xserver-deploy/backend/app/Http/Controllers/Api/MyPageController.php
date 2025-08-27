<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GameBase;
use App\Models\QrUseList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
    /**
     * ユーザープロフィール情報を取得
     */
    public function profile()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'userId' => $user->id,
                'nickname' => $user->nickname ?: '',
                'phone' => $user->phone,
                'avatar_choice' => $user->avatar_choice,
                'email' => $user->email,
                'name' => $user->name,
                'birthday' => $user->birthday
            ]
        ]);
    }

    /**
     * ポイント情報を取得
     */
    public function points()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }

        $gameBase = $user->gameBase;
        
        if (!$gameBase) {
            // GameBaseが存在しない場合は作成
            $gameBase = GameBase::create([
                'gamebase_userid' => $user->id,
                'gamebase_points' => 0,
                'gamebase_level' => 1,
                'gamebase_exp' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $gameBase->gamebase_points,
                'availablePoints' => $gameBase->gamebase_points,
                'missionPoints' => 0, // 将来的に実装
                'totalPoints' => $gameBase->gamebase_points,
                'level' => $gameBase->gamebase_level,
                'exp' => $gameBase->gamebase_exp
            ]
        ]);
    }

    /**
     * 最近の活動を取得
     */
    public function activities()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }

        // QRコード使用履歴を取得
        $qrActivities = QrUseList::where('qruse_user_id', $user->id)
            ->orderBy('qruse_created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($qrUse) {
                return [
                    'id' => $qrUse->id,
                    'title' => 'QRコード読み取り',
                    'date' => $qrUse->qruse_created_at,
                    'points' => 50 // QRコード読み取りで獲得するポイント
                ];
            });

        // 新規登録ボーナス（初回のみ）
        $activities = [];
        
        if ($user->created_at->diffInDays(now()) < 1) {
            $activities[] = [
                'id' => 'registration_bonus',
                'title' => '新規登録特典',
                'date' => $user->created_at,
                'points' => 100
            ];
        }

        // QRコード活動を追加
        $activities = array_merge($activities, $qrActivities->toArray());

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities
            ]
        ]);
    }
}
