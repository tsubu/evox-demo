<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\QrLiveEvent;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    public function index()
    {
        // リアルイベントQRコード（qr_is_liveevent = true）
        $eventQrCodes = QrCode::where('qr_is_liveevent', true)
            ->orderBy('qr_created_at', 'desc')
            ->get();

        // ゲームグッズQRコード（qr_is_liveevent = false）
        $goodsQrCodes = QrCode::where('qr_is_liveevent', false)
            ->orderBy('qr_created_at', 'desc')
            ->get();

        return view('admin.qrcodes', compact('eventQrCodes', 'goodsQrCodes'));
    }

    public function create()
    {
        return view('admin.qrcode-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string|unique:qr_codes,qr_code',
            'qr_description' => 'required|string|max:255',
            'qr_title' => 'required|string|max:255',
            'qr_points' => 'nullable|integer|min:0',
            'qr_artist_name' => 'nullable|string|max:255',
            'qr_expires_at' => 'nullable|date',
            'qr_contents' => 'nullable|string',
            'qr_is_active' => 'boolean',
            'qr_is_liveevent' => 'boolean',
            'qr_is_multiple' => 'boolean',
            'qr_type' => 'required|in:event,goods',
            'qr_options_enabled' => 'boolean',
            'qr_avatar_expressions' => 'nullable|array|max:5',
            'qr_avatar_expressions.*' => 'nullable|string|max:100',
            'qr_avatar_actions' => 'nullable|array|max:5',
            'qr_avatar_actions.*' => 'nullable|string|max:100',
            'qr_background_colors' => 'nullable|array|max:5',
            'qr_background_colors.*' => 'nullable|string|max:100',
            'qr_effects' => 'nullable|array|max:5',
            'qr_effects.*' => 'nullable|string|max:100',
            'qr_sounds' => 'nullable|array|max:5',
            'qr_sounds.*' => 'nullable|string|max:100'
        ]);

        // QRコードタイプに基づいてフラグを設定
        $isLiveEvent = $request->qr_type === 'event';
        $isMultiple = $request->has('qr_is_multiple');

        // オプション配列をフィルタリング（空の値を除去）
        $avatarExpressions = $request->qr_avatar_expressions ? array_filter($request->qr_avatar_expressions) : null;
        $avatarActions = $request->qr_avatar_actions ? array_filter($request->qr_avatar_actions) : null;
        $backgroundColors = $request->qr_background_colors ? array_filter($request->qr_background_colors) : null;
        $effects = $request->qr_effects ? array_filter($request->qr_effects) : null;
        $sounds = $request->qr_sounds ? array_filter($request->qr_sounds) : null;

        QrCode::create([
            'qr_code' => $request->qr_code,
            'qr_description' => $request->qr_description,
            'qr_title' => $request->qr_title,
            'qr_points' => $request->qr_points ?? 0,
            'qr_artist_name' => $request->qr_artist_name,
            'qr_expires_at' => $request->qr_expires_at,
            'qr_contents' => $request->qr_contents,
            'qr_is_active' => $request->has('qr_is_active'),
            'qr_is_liveevent' => $isLiveEvent,
            'qr_is_multiple' => $isMultiple,
            'qr_options_enabled' => $request->has('qr_options_enabled'),
            'qr_avatar_expressions' => $avatarExpressions,
            'qr_avatar_actions' => $avatarActions,
            'qr_background_colors' => $backgroundColors,
            'qr_effects' => $effects,
            'qr_sounds' => $sounds,
            'qr_created_at' => now()
        ]);

        $typeName = $isLiveEvent ? 'リアルイベント' : 'ゲームグッズ';
        return redirect()->route('admin.qrcodes')->with('success', "{$typeName}QRコードを作成しました。");
    }

    public function show($id)
    {
        $qrCode = QrCode::findOrFail($id);
        return view('admin.qrcode-detail', compact('qrCode'));
    }

    public function edit($id)
    {
        $qrCode = QrCode::findOrFail($id);
        return view('admin.qrcode-edit', compact('qrCode'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'qr_code' => 'required|string|unique:qr_codes,qr_code,' . $id,
            'qr_description' => 'required|string|max:255',
            'qr_title' => 'required|string|max:255',
            'qr_points' => 'nullable|integer|min:0',
            'qr_artist_name' => 'nullable|string|max:255',
            'qr_expires_at' => 'nullable|date',
            'qr_contents' => 'nullable|string',
            'qr_is_active' => 'boolean',
            'qr_is_liveevent' => 'boolean',
            'qr_is_multiple' => 'boolean',
            'qr_type' => 'required|in:event,goods',
            'qr_options_enabled' => 'boolean',
            'qr_avatar_expressions' => 'nullable|array|max:5',
            'qr_avatar_expressions.*' => 'nullable|string|max:100',
            'qr_avatar_actions' => 'nullable|array|max:5',
            'qr_avatar_actions.*' => 'nullable|string|max:100',
            'qr_background_colors' => 'nullable|array|max:5',
            'qr_background_colors.*' => 'nullable|string|max:100',
            'qr_effects' => 'nullable|array|max:5',
            'qr_effects.*' => 'nullable|string|max:100',
            'qr_sounds' => 'nullable|array|max:5',
            'qr_sounds.*' => 'nullable|string|max:100'
        ]);

        $qrCode = QrCode::findOrFail($id);
        
        // QRコードタイプに基づいてフラグを設定
        $isLiveEvent = $request->qr_type === 'event';
        $isMultiple = $request->has('qr_is_multiple');

        // オプション配列をフィルタリング（空の値を除去）
        $avatarExpressions = $request->qr_avatar_expressions ? array_filter($request->qr_avatar_expressions) : null;
        $avatarActions = $request->qr_avatar_actions ? array_filter($request->qr_avatar_actions) : null;
        $backgroundColors = $request->qr_background_colors ? array_filter($request->qr_background_colors) : null;
        $effects = $request->qr_effects ? array_filter($request->qr_effects) : null;
        $sounds = $request->qr_sounds ? array_filter($request->qr_sounds) : null;

        $qrCode->update([
            'qr_code' => $request->qr_code,
            'qr_description' => $request->qr_description,
            'qr_title' => $request->qr_title,
            'qr_points' => $request->qr_points ?? 0,
            'qr_artist_name' => $request->qr_artist_name,
            'qr_expires_at' => $request->qr_expires_at,
            'qr_contents' => $request->qr_contents,
            'qr_is_active' => $request->has('qr_is_active'),
            'qr_is_liveevent' => $isLiveEvent,
            'qr_is_multiple' => $isMultiple,
            'qr_options_enabled' => $request->has('qr_options_enabled'),
            'qr_avatar_expressions' => $avatarExpressions,
            'qr_avatar_actions' => $avatarActions,
            'qr_background_colors' => $backgroundColors,
            'qr_effects' => $effects,
            'qr_sounds' => $sounds
        ]);

        $typeName = $isLiveEvent ? 'リアルイベント' : 'ゲームグッズ';
        return redirect()->route('admin.qrcodes')->with('success', "{$typeName}QRコードを更新しました。");
    }

    public function destroy($id)
    {
        $qrCode = QrCode::findOrFail($id);
        $qrCode->delete();

        return redirect()->route('admin.qrcodes')->with('success', 'QRコードを削除しました。');
    }

    public function duplicate($id)
    {
        $originalQrCode = QrCode::findOrFail($id);
        
        // 新しいQRコードを生成
        $newQrCode = 'QR' . strtoupper(Str::random(23));
        
        // 複製を作成
        $duplicatedQrCode = $originalQrCode->replicate();
        $duplicatedQrCode->qr_code = $newQrCode;
        $duplicatedQrCode->qr_title = $originalQrCode->qr_title . ' (コピー)';
        $duplicatedQrCode->qr_is_active = false; // 複製時は非アクティブにする
        $duplicatedQrCode->qr_created_at = now();
        $duplicatedQrCode->save();

        $typeName = $originalQrCode->qr_is_liveevent ? 'リアルイベント' : 'ゲームグッズ';
        return redirect()->route('admin.qrcodes')->with('success', "{$typeName}QRコードを複製しました。新しいQRコード: {$newQrCode}");
    }

    public function generate()
    {
        // ランダムなQRコードを生成（25文字）
        $qrCode = 'QR' . strtoupper(Str::random(23));
        
        return response()->json([
            'success' => true,
            'qr_code' => $qrCode
        ]);
    }

    /**
     * QRコードをPNG形式でダウンロード
     */
    public function downloadPng($id)
    {
        $qrCode = QrCode::findOrFail($id);
        $qrCodeService = new QrCodeService();
        
        $pngData = $qrCodeService->generatePng($qrCode->qr_code);
        
        $filename = 'qr_' . $qrCode->qr_code . '.png';
        
        return response($pngData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * QRコードをSVG形式でダウンロード
     */
    public function downloadSvg($id)
    {
        $qrCode = QrCode::findOrFail($id);
        $qrCodeService = new QrCodeService();
        
        $svgData = $qrCodeService->generateSvg($qrCode->qr_code);
        
        $filename = 'qr_' . $qrCode->qr_code . '.svg';
        
        return response($svgData)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
