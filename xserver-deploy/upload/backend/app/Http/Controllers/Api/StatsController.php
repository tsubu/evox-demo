<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\QrUseList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * QRコード使用統計
     */
    public function qrUsageStats(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $query = QrUseList::with('qrCode')
                         ->select(
                             'qruse_qr_code_id',
                             DB::raw('COUNT(*) as usage_count'),
                             DB::raw('SUM(qruse_points_earned) as total_points')
                         )
                         ->groupBy('qruse_qr_code_id')
                         ->orderBy('usage_count', 'desc');

        // 期間フィルター
        switch ($period) {
            case 'day':
                $query->where('qruse_created_at', '>=', Carbon::today());
                break;
            case 'week':
                $query->where('qruse_created_at', '>=', Carbon::now()->startOfWeek());
                break;
            case 'month':
                $query->where('qruse_created_at', '>=', Carbon::now()->startOfMonth());
                break;
            case 'year':
                $query->where('qruse_created_at', '>=', Carbon::now()->startOfYear());
                break;
        }

        $stats = $query->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * オプション選択統計
     */
    public function optionSelectionStats(Request $request)
    {
        $qrCodeId = $request->get('qr_code_id');
        
        $query = QrUseList::whereNotNull('qruse_options_selected_at');
        
        if ($qrCodeId) {
            $query->where('qruse_qr_code_id', $qrCodeId);
        }

        $stats = [
            'expressions' => $query->select('qruse_selected_expression', DB::raw('COUNT(*) as count'))
                                 ->whereNotNull('qruse_selected_expression')
                                 ->groupBy('qruse_selected_expression')
                                 ->orderBy('count', 'desc')
                                 ->get(),
            'actions' => $query->select('qruse_selected_action', DB::raw('COUNT(*) as count'))
                             ->whereNotNull('qruse_selected_action')
                             ->groupBy('qruse_selected_action')
                             ->orderBy('count', 'desc')
                             ->get(),
            'backgrounds' => $query->select('qruse_selected_background', DB::raw('COUNT(*) as count'))
                                 ->whereNotNull('qruse_selected_background')
                                 ->groupBy('qruse_selected_background')
                                 ->orderBy('count', 'desc')
                                 ->get(),
            'effects' => $query->select('qruse_selected_effect', DB::raw('COUNT(*) as count'))
                             ->whereNotNull('qruse_selected_effect')
                             ->groupBy('qruse_selected_effect')
                             ->orderBy('count', 'desc')
                             ->get(),
            'sounds' => $query->select('qruse_selected_sound', DB::raw('COUNT(*) as count'))
                            ->whereNotNull('qruse_selected_sound')
                            ->groupBy('qruse_selected_sound')
                            ->orderBy('count', 'desc')
                            ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * テーマ別統計
     */
    public function themeStats(Request $request)
    {
        $stats = QrCode::with('theme')
                      ->select('theme_id', DB::raw('COUNT(*) as qr_count'))
                      ->whereNotNull('theme_id')
                      ->groupBy('theme_id')
                      ->orderBy('qr_count', 'desc')
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * 時系列統計
     */
    public function timeSeriesStats(Request $request)
    {
        $period = $request->get('period', 'day'); // hour, day, week, month
        
        $query = QrUseList::select(
            DB::raw('DATE(qruse_created_at) as date'),
            DB::raw('COUNT(*) as usage_count'),
            DB::raw('SUM(qruse_points_earned) as total_points')
        )
        ->groupBy('date')
        ->orderBy('date');

        // 期間フィルター
        switch ($period) {
            case 'hour':
                $query = QrUseList::select(
                    DB::raw('DATE_FORMAT(qruse_created_at, "%Y-%m-%d %H:00:00") as time_slot'),
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('SUM(qruse_points_earned) as total_points')
                )
                ->groupBy('time_slot')
                ->orderBy('time_slot');
                break;
            case 'week':
                $query = QrUseList::select(
                    DB::raw('YEARWEEK(qruse_created_at) as week'),
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('SUM(qruse_points_earned) as total_points')
                )
                ->groupBy('week')
                ->orderBy('week');
                break;
            case 'month':
                $query = QrUseList::select(
                    DB::raw('DATE_FORMAT(qruse_created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('SUM(qruse_points_earned) as total_points')
                )
                ->groupBy('month')
                ->orderBy('month');
                break;
        }

        $stats = $query->limit(30)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * ユーザー行動分析
     */
    public function userBehaviorStats(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('user_is_active', true)->count(),
            'users_with_options' => QrUseList::whereNotNull('qruse_options_selected_at')
                                           ->distinct('qruse_user_id')
                                           ->count(),
            'average_points_per_user' => QrUseList::select(DB::raw('AVG(total_points) as avg_points'))
                                                ->fromSub(
                                                    QrUseList::select('qruse_user_id', DB::raw('SUM(qruse_points_earned) as total_points'))
                                                            ->groupBy('qruse_user_id'),
                                                    'user_points'
                                                )
                                                ->first()->avg_points ?? 0,
            'most_active_users' => QrUseList::select('qruse_user_id', DB::raw('COUNT(*) as usage_count'))
                                          ->groupBy('qruse_user_id')
                                          ->orderBy('usage_count', 'desc')
                                          ->limit(10)
                                          ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
