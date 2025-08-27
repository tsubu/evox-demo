<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use Illuminate\Http\Request;

class QrCodeOptionsController extends Controller
{
    /**
     * オプション選択肢の一覧を表示
     */
    public function index()
    {
        // デフォルトのオプション選択肢を定義
        $defaultOptions = [
            'qr_avatar_expressions' => [
                '笑顔',
                '怒り',
                '悲しみ',
                '驚き',
                '普通'
            ],
            'qr_avatar_actions' => [
                'ダンス',
                'ジャンプ',
                '手を振る',
                '座る',
                '走る'
            ],
            'qr_background_colors' => [
                '青空',
                '夕日',
                '星空',
                '森',
                '海'
            ],
            'qr_effects' => [
                'キラキラ',
                '虹',
                '花火',
                '雪',
                '雨'
            ],
            'qr_sounds' => [
                'ファンファーレ',
                '拍手',
                '音楽',
                '効果音',
                '無音'
            ]
        ];

        // オプションが有効なQRコードの統計
        $stats = [
            'total_qrcodes' => QrCode::count(),
            'options_enabled' => QrCode::where('qr_options_enabled', true)->count(),
            'options_disabled' => QrCode::where('qr_options_enabled', false)->count(),
        ];

        return view('admin.qrcode-options', compact('defaultOptions', 'stats'));
    }

    /**
     * デフォルトオプションを既存のQRコードに適用
     */
    public function applyDefaults(Request $request)
    {
        $request->validate([
            'qr_code_ids' => 'required|array',
            'qr_code_ids.*' => 'exists:qr_codes,id'
        ]);

        $defaultOptions = [
            'qr_avatar_expressions' => [
                '笑顔',
                '怒り',
                '悲しみ',
                '驚き',
                '普通'
            ],
            'qr_avatar_actions' => [
                'ダンス',
                'ジャンプ',
                '手を振る',
                '座る',
                '走る'
            ],
            'qr_background_colors' => [
                '青空',
                '夕日',
                '星空',
                '森',
                '海'
            ],
            'qr_effects' => [
                'キラキラ',
                '虹',
                '花火',
                '雪',
                '雨'
            ],
            'qr_sounds' => [
                'ファンファーレ',
                '拍手',
                '音楽',
                '効果音',
                '無音'
            ]
        ];

        $updatedCount = 0;
        foreach ($request->qr_code_ids as $qrCodeId) {
            $qrCode = QrCode::find($qrCodeId);
            if ($qrCode) {
                $qrCode->update([
                    'qr_options_enabled' => true,
                    'qr_avatar_expressions' => $defaultOptions['qr_avatar_expressions'],
                    'qr_avatar_actions' => $defaultOptions['qr_avatar_actions'],
                    'qr_background_colors' => $defaultOptions['qr_background_colors'],
                    'qr_effects' => $defaultOptions['qr_effects'],
                    'qr_sounds' => $defaultOptions['qr_sounds']
                ]);
                $updatedCount++;
            }
        }

        return redirect()->route('admin.qrcode-options.index')
            ->with('success', "{$updatedCount}個のQRコードにデフォルトオプションを適用しました。");
    }

    /**
     * カスタムオプションを一括更新
     */
    public function updateCustomOptions(Request $request)
    {
        $request->validate([
            'qr_avatar_expressions' => 'nullable|array|max:10',
            'qr_avatar_expressions.*' => 'nullable|string|max:100',
            'qr_avatar_actions' => 'nullable|array|max:10',
            'qr_avatar_actions.*' => 'nullable|string|max:100',
            'qr_background_colors' => 'nullable|array|max:10',
            'qr_background_colors.*' => 'nullable|string|max:100',
            'qr_effects' => 'nullable|array|max:10',
            'qr_effects.*' => 'nullable|string|max:100',
            'qr_sounds' => 'nullable|array|max:10',
            'qr_sounds.*' => 'nullable|string|max:100'
        ]);

        // 空の値を除去
        $customOptions = [
            'qr_avatar_expressions' => $request->qr_avatar_expressions ? array_filter($request->qr_avatar_expressions) : null,
            'qr_avatar_actions' => $request->qr_avatar_actions ? array_filter($request->qr_avatar_actions) : null,
            'qr_background_colors' => $request->qr_background_colors ? array_filter($request->qr_background_colors) : null,
            'qr_effects' => $request->qr_effects ? array_filter($request->qr_effects) : null,
            'qr_sounds' => $request->qr_sounds ? array_filter($request->qr_sounds) : null
        ];

        // オプションが有効なQRコードを一括更新
        $updatedCount = QrCode::where('qr_options_enabled', true)->update($customOptions);

        return redirect()->route('admin.qrcode-options.index')
            ->with('success', "オプション選択肢を更新しました。{$updatedCount}個のQRコードが影響を受けました。");
    }

    /**
     * オプション機能を一括有効/無効化
     */
    public function toggleOptions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:enable,disable',
            'qr_code_ids' => 'required|array',
            'qr_code_ids.*' => 'exists:qr_codes,id'
        ]);

        $enabled = $request->action === 'enable';
        $updatedCount = QrCode::whereIn('id', $request->qr_code_ids)
            ->update(['qr_options_enabled' => $enabled]);

        $actionText = $enabled ? '有効化' : '無効化';
        return redirect()->route('admin.qrcode-options.index')
            ->with('success', "{$updatedCount}個のQRコードのオプション機能を{$actionText}しました。");
    }
}
