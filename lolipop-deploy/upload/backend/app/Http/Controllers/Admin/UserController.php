<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GameBase;
use App\Helpers\AvatarHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // ユーザー一覧を取得（ページネーション付き）
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        // 統計情報
        $totalUsers = User::count();
        $activeUsers = User::where(function($query) {
            $query->whereNotNull('email_verified_at')
                  ->orWhereExists(function($subQuery) {
                      $subQuery->select(DB::raw(1))
                               ->from('pre_registrations')
                               ->whereColumn('pre_registrations.prereg_phone', 'users.phone')
                               ->where('pre_registrations.prereg_is_verified', true)
                               ->whereNotNull('pre_registrations.prereg_verified_at');
                  });
        })->count();
        $incompleteUsers = $totalUsers - $activeUsers;

        return view('admin.users', compact('users', 'totalUsers', 'activeUsers', 'incompleteUsers'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user-detail', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $avatarOptions = AvatarHelper::getAvatarOptions();
        return view('admin.user-edit', compact('user', 'avatarOptions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'nickname' => 'nullable|string|max:255',
            'avatar_choice' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
        ]);

        // 基本情報を更新
        $user->update($request->only([
            'name', 'email', 'phone', 'nickname', 'avatar_choice', 'birthday'
        ]));

        // メール認証状況を更新
        if ($request->has('email_verified')) {
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }
        } else {
            if ($user->email_verified_at) {
                $user->update(['email_verified_at' => null]);
            }
        }

        // SMS認証状況を更新
        $preRegistration = \App\Models\PreRegistration::where('prereg_phone', $user->phone)->first();
        
        if ($request->has('sms_verified')) {
            // SMS認証を有効にする
            if (!$preRegistration) {
                // pre_registrationsレコードが存在しない場合は作成
                \App\Models\PreRegistration::create([
                    'prereg_temp_id' => 'admin_' . time(), // 20文字以内に制限
                    'prereg_phone' => $user->phone,
                    'prereg_password' => 'admin_managed',
                    'prereg_is_verified' => true,
                    'prereg_verified_at' => now(),
                ]);
            } else {
                $preRegistration->update([
                    'prereg_is_verified' => true,
                    'prereg_verified_at' => now()
                ]);
            }
        } else {
            // SMS認証を無効にする
            if ($preRegistration) {
                $preRegistration->update([
                    'prereg_is_verified' => false,
                    'prereg_verified_at' => null
                ]);
            }
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'ユーザー情報と認証状況を更新しました。');
    }

    /**
     * ユーザーの認証状態を変更
     */
    public function toggleVerification($id)
    {
        $user = User::findOrFail($id);
        
        try {
            if ($user->email_verified_at) {
                // 認証済みの場合は未認証に変更
                $user->update(['email_verified_at' => null]);
                $message = 'ユーザーを未認証状態に変更しました。';
            } else {
                // 未認証の場合は認証済みに変更
                $user->update(['email_verified_at' => now()]);
                $message = 'ユーザーを認証済み状態に変更しました。';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_verified' => $user->fresh()->isVerified()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SMS認証状態を変更
     */
    public function toggleSmsVerification($id)
    {
        $user = User::findOrFail($id);
        
        try {
            // pre_registrationsテーブルで該当ユーザーのSMS認証状態を確認
            $preRegistration = \App\Models\PreRegistration::where('prereg_phone', $user->phone)->first();
            
            if ($preRegistration && $preRegistration->prereg_is_verified) {
                // SMS認証済みの場合は未認証に変更
                $preRegistration->update([
                    'prereg_is_verified' => false,
                    'prereg_verified_at' => null
                ]);
                $message = 'SMS認証を未認証状態に変更しました。';
            } else {
                // SMS未認証の場合は認証済みに変更
                if (!$preRegistration) {
                    // pre_registrationsレコードが存在しない場合は作成
                    $preRegistration = \App\Models\PreRegistration::create([
                        'prereg_temp_id' => 'admin_' . time(), // 20文字以内に制限
                        'prereg_phone' => $user->phone,
                        'prereg_password' => 'admin_managed',
                        'prereg_is_verified' => true,
                        'prereg_verified_at' => now(),
                    ]);
                } else {
                    $preRegistration->update([
                        'prereg_is_verified' => true,
                        'prereg_verified_at' => now()
                    ]);
                }
                $message = 'SMS認証を認証済み状態に変更しました。';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_sms_verified' => $user->fresh()->isSmsVerified()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'ユーザーを削除しました。');
    }

    /**
     * ブラックリスト状態を切り替え
     */
    public function toggleBlacklist($id)
    {
        $user = User::findOrFail($id);
        
        // ブラックリスト状態を切り替え（現在は簡易実装）
        // 実際の実装では、usersテーブルにis_blacklistedカラムを追加する必要があります
        $isBlacklisted = !($user->is_blacklisted ?? false);
        
        try {
            $user->update(['is_blacklisted' => $isBlacklisted]);
            
            $status = $isBlacklisted ? 'ブラックリストに追加' : 'ブラックリストから削除';
            
            return response()->json([
                'success' => true,
                'message' => "ユーザー「{$user->name}」を{$status}しました。",
                'is_blacklisted' => $isBlacklisted
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました: ' . $e->getMessage()
            ], 500);
        }
    }
}
