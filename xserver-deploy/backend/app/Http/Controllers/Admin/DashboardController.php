<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\QrCode;
use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボード表示
     */
    public function index()
    {
        // 管理者認証チェック（ミドルウェアで既にチェック済み）
        \Log::info('Dashboard access attempt', [
            'is_authenticated' => Auth::guard('admin')->check(),
            'user' => Auth::guard('admin')->user(),
            'session_id' => session()->getId(),
            'request_url' => request()->url(),
            'request_method' => request()->method()
        ]);

        // 統計データを取得
        $userCount = User::count();
        $todayRegistrations = User::whereDate('created_at', today())->count();
        $qrCodeCount = QrCode::count();
        $newsCount = NewsItem::count();

        return view('admin.dashboard', compact(
            'userCount',
            'todayRegistrations',
            'qrCodeCount',
            'newsCount'
        ));
    }
}
