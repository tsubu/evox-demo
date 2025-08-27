<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * 管理者一覧表示
     */
    public function index()
    {
        $admins = UserAdmin::orderBy('admin_created_at', 'desc')->paginate(20);
        $totalAdmins = UserAdmin::count();
        $activeAdmins = UserAdmin::where('admin_is_verified', true)->count();
        $inactiveAdmins = UserAdmin::where('admin_is_verified', false)->count();

        return view('admin.admins', compact('admins', 'totalAdmins', 'activeAdmins', 'inactiveAdmins'));
    }

    /**
     * 管理者作成フォーム表示
     */
    public function create()
    {
        return view('admin.admin-create');
    }

    /**
     * 管理者作成処理
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_name' => 'required|string|max:255',
            'admin_phone' => 'required|string|max:20|unique:users_admin,admin_phone',
            'admin_email' => 'nullable|email|max:255|unique:users_admin,admin_email',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_password_confirmation' => 'required|string|min:8',
        ], [
            'admin_name.required' => '管理者名は必須です。',
            'admin_phone.required' => '電話番号は必須です。',
            'admin_phone.unique' => 'この電話番号は既に使用されています。',
            'admin_email.email' => '有効なメールアドレスを入力してください。',
            'admin_email.unique' => 'このメールアドレスは既に使用されています。',
            'admin_password.required' => 'パスワードは必須です。',
            'admin_password.min' => 'パスワードは8文字以上で入力してください。',
            'admin_password.confirmed' => 'パスワードが一致しません。',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 国別コードと電話番号を組み合わせ
        $fullPhoneNumber = $request->country_code . $request->admin_phone;

        // 管理者を作成
        $admin = UserAdmin::create([
            'admin_name' => $request->admin_name,
            'admin_phone' => $fullPhoneNumber,
            'admin_email' => $request->admin_email,
            'admin_password' => Hash::make($request->admin_password),
            'admin_is_verified' => true, // 管理者が作成する場合は認証済みとする
            'admin_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins')
            ->with('success', '管理者「' . $admin->admin_name . '」を作成しました。');
    }

    /**
     * 管理者編集フォーム表示
     */
    public function edit($id)
    {
        $admin = UserAdmin::findOrFail($id);
        return view('admin.admin-edit', compact('admin'));
    }

    /**
     * 管理者更新処理
     */
    public function update(Request $request, $id)
    {
        $admin = UserAdmin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'admin_name' => 'required|string|max:255',
            'admin_phone' => 'required|string|max:20|unique:users_admin,admin_phone,' . $id,
            'admin_email' => 'nullable|email|max:255|unique:users_admin,admin_email,' . $id,
            'admin_password' => 'nullable|string|min:8|confirmed',
            'admin_password_confirmation' => 'nullable|string|min:8',
        ], [
            'admin_name.required' => '管理者名は必須です。',
            'admin_phone.required' => '電話番号は必須です。',
            'admin_phone.unique' => 'この電話番号は既に使用されています。',
            'admin_email.email' => '有効なメールアドレスを入力してください。',
            'admin_email.unique' => 'このメールアドレスは既に使用されています。',
            'admin_password.min' => 'パスワードは8文字以上で入力してください。',
            'admin_password.confirmed' => 'パスワードが一致しません。',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 国別コードと電話番号を組み合わせ
        $fullPhoneNumber = $request->country_code . $request->admin_phone;

        // 更新データを準備
        $updateData = [
            'admin_name' => $request->admin_name,
            'admin_phone' => $fullPhoneNumber,
            'admin_email' => $request->admin_email,
        ];

        // パスワードが入力されている場合のみ更新
        if ($request->filled('admin_password')) {
            $updateData['admin_password'] = Hash::make($request->admin_password);
        }

        $admin->update($updateData);

        return redirect()->route('admin.admins')
            ->with('success', '管理者「' . $admin->admin_name . '」を更新しました。');
    }

    /**
     * 管理者削除処理
     */
    public function destroy($id)
    {
        $admin = UserAdmin::findOrFail($id);
        $adminName = $admin->admin_name;
        
        // 自分自身は削除できない
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admin.admins')
                ->with('error', '自分自身を削除することはできません。');
        }

        $admin->delete();

        return redirect()->route('admin.admins')
            ->with('success', '管理者「' . $adminName . '」を削除しました。');
    }
}
