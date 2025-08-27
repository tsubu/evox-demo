<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GameBase;
use App\Models\QrCode;
use App\Models\QrUseList;
use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        // 基本統計
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->orWhereHas('preRegistrations', function($query) {
            $query->where('prereg_is_verified', true);
        })->count();
        $totalQrCodes = QrCode::count();
        $totalNews = NewsItem::count();

        // 今日の統計
        $today = Carbon::today();
        $todayRegistrations = User::whereDate('created_at', $today)->count();
        $todayQrScans = QrUseList::whereDate('qruse_claimed_at', $today)->count();

        // 今月の統計
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyRegistrations = User::where('created_at', '>=', $thisMonth)->count();
        $monthlyQrScans = QrUseList::where('qruse_claimed_at', '>=', $thisMonth)->count();

        // 登録推移（過去30日）
        $registrationTrend = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // QRコード使用状況（実際の使用回数を含む）
        $qrCodeUsage = QrCode::withCount('qrUseList')
            ->orderBy('qr_use_list_count', 'desc')
            ->limit(10)
            ->get();

        // キャラクター選択統計
        $characterStats = User::selectRaw('avatar_choice, COUNT(*) as count')
            ->whereNotNull('avatar_choice')
            ->groupBy('avatar_choice')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($item) {
                return (object) [
                    'character_name' => $this->getAvatarName($item->avatar_choice),
                    'count' => $item->count
                ];
            });

        // チャート用データ
        $chartData = $this->prepareChartData($registrationTrend, $characterStats);

        return view('admin.stats', compact(
            'totalUsers',
            'activeUsers',
            'totalQrCodes',
            'totalNews',
            'todayRegistrations',
            'todayQrScans',
            'monthlyRegistrations',
            'monthlyQrScans',
            'registrationTrend',
            'qrCodeUsage',
            'characterStats',
            'chartData'
        ));
    }

    /**
     * アバター名を取得
     */
    private function getAvatarName($avatarChoice)
    {
        $avatarNames = [
            'car001' => 'BABY ARASHI 01',
            'car002' => 'BABY ARASHI 02',
            'car003' => 'BABY ARASHI 03',
            'car004' => 'BABY ARASHI 04'
        ];

        return $avatarNames[$avatarChoice] ?? $avatarChoice;
    }

    /**
     * チャート用データを準備
     */
    private function prepareChartData($registrationTrend, $characterStats)
    {
        // 登録推移データ（過去7日分）
        $registrationData = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = $i == 0 ? '今日' : $i . '日前';
            
            $count = $registrationTrend->where('date', $date)->first();
            $registrationData[] = $count ? $count->count : 0;
        }

        // キャラクター選択データ
        $characterLabels = $characterStats->pluck('character_name')->toArray();
        $characterData = $characterStats->pluck('count')->toArray();

        return [
            'registration' => [
                'labels' => $labels,
                'data' => $registrationData
            ],
            'character' => [
                'labels' => $characterLabels,
                'data' => $characterData
            ]
        ];
    }
}
