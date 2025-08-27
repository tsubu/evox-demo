<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\QrCode;
use App\Models\QrUseList;
use App\Models\User;
use Carbon\Carbon;

class QrController extends Controller
{
    /**
     * QRコードをクレーム
     */
    public function claim(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        // QRコードを検索
        $qrCode = QrCode::where('qr_code', $request->code)
            ->where('qr_is_active', true)
            ->where(function ($query) {
                $query->whereNull('qr_expires_at')
                      ->orWhere('qr_expires_at', '>', Carbon::now());
            })
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QRコードが見つからないか、無効です。'
            ], 400);
        }

        // 既にクレーム済みかチェック
        $existingClaim = QrUseList::where('qruse_user_id', $user->id)
            ->where('qruse_qr_code_id', $qrCode->id)
            ->first();

        if ($existingClaim) {
            return response()->json([
                'success' => false,
                'message' => 'このQRコードは既にクレーム済みです。'
            ], 400);
        }

        // クレームを作成
        $claim = QrUseList::create([
            'qruse_user_id' => $user->id,
            'qruse_qr_code_id' => $qrCode->id,
            'qruse_points_earned' => $qrCode->qr_points,
            'qruse_claimed_at' => Carbon::now(),
        ]);

        // ゲーム基本情報を取得または作成
        $gameBase = $user->gameBase;
        
        if (!$gameBase) {
            $gameBase = $user->gameBase()->create([
                'gamebase_userid' => $user->id,
                'gamebase_points' => $qrCode->qr_points,
            ]);
        } else {
            $gameBase->increment('gamebase_points', $qrCode->qr_points);
        }

        return response()->json([
            'success' => true,
            'message' => 'QRコードをクレームしました！',
            'data' => [
                'qr_code' => [
                    'title' => $qrCode->qr_title,
                    'description' => $qrCode->qr_description,
                    'points' => $qrCode->qr_points,
                ],
                'points_earned' => $qrCode->qr_points,
                'total_points' => $gameBase->fresh()->gamebase_points,
            ]
        ]);
    }
}
