<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ログインページのルート
Route::get('/login', function () {
    return response()->json(['message' => 'ログインページ'], 200);
})->name('login');

// 管理者ログインページ（ランダムURL）
Route::get('/admin-' . env('ADMIN_URL_HASH', 'evox2025'), function () {
    return view('admin.login');
})->name('admin.login');

// 管理画面ダッシュボード
Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');

// 管理画面の各機能
Route::get('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])
    ->middleware('admin')
    ->name('admin.users');

Route::get('/admin/news', [App\Http\Controllers\Admin\NewsController::class, 'index'])
    ->middleware('admin')
    ->name('admin.news');

Route::get('/admin/qrcodes', [App\Http\Controllers\Admin\QrCodeController::class, 'index'])
    ->middleware('admin')
    ->name('admin.qrcodes');

Route::get('/admin/stats', [App\Http\Controllers\Admin\StatsController::class, 'index'])
    ->middleware('admin')
    ->name('admin.stats');

Route::get('/admin/penlight-control', [App\Http\Controllers\Admin\PenlightController::class, 'index'])
    ->middleware('admin')
    ->name('admin.penlight-control');

// ユーザー管理の詳細・編集・削除・ブラックリスト
Route::get('/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])
    ->middleware('admin')
    ->name('admin.users.show');
Route::get('/admin/users/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.users.edit');
Route::put('/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])
    ->middleware('admin')
    ->name('admin.users.update');
Route::delete('/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.users.destroy');
Route::post('/admin/users/{id}/blacklist', [App\Http\Controllers\Admin\UserController::class, 'toggleBlacklist'])
    ->middleware('admin')
    ->name('admin.users.blacklist');
Route::post('/admin/users/{id}/toggle-verification', [App\Http\Controllers\Admin\UserController::class, 'toggleVerification'])
    ->middleware('admin')
    ->name('admin.users.toggle-verification');
Route::post('/admin/users/{id}/toggle-sms-verification', [App\Http\Controllers\Admin\UserController::class, 'toggleSmsVerification'])
    ->middleware('admin')
    ->name('admin.users.toggle-sms-verification');

// ニュース管理の作成・編集・削除
Route::get('/admin/news/create', [App\Http\Controllers\Admin\NewsController::class, 'create'])
    ->middleware('admin')
    ->name('admin.news.create');
Route::post('/admin/news', [App\Http\Controllers\Admin\NewsController::class, 'store'])
    ->middleware('admin')
    ->name('admin.news.store');
Route::get('/admin/news/{id}/edit', [App\Http\Controllers\Admin\NewsController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.news.edit');
Route::put('/admin/news/{id}', [App\Http\Controllers\Admin\NewsController::class, 'update'])
    ->middleware('admin')
    ->name('admin.news.update');
Route::delete('/admin/news/{id}', [App\Http\Controllers\Admin\NewsController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.news.destroy');
Route::get('/admin/news/add-test-data', [App\Http\Controllers\Admin\NewsController::class, 'addTestData'])
    ->middleware('admin')
    ->name('admin.news.add-test-data');

// QRコード管理の作成・編集・削除
Route::get('/admin/qrcodes/create', [App\Http\Controllers\Admin\QrCodeController::class, 'create'])
    ->middleware('admin')
    ->name('admin.qrcodes.create');
Route::post('/admin/qrcodes', [App\Http\Controllers\Admin\QrCodeController::class, 'store'])
    ->middleware('admin')
    ->name('admin.qrcodes.store');
            Route::get('/admin/qrcodes/{id}', [App\Http\Controllers\Admin\QrCodeController::class, 'show'])
                ->middleware('admin')
                ->name('admin.qrcodes.show');

            // QRコードオプション管理
            Route::get('/admin/qrcode-options', [App\Http\Controllers\Admin\QrCodeOptionsController::class, 'index'])
                ->middleware('admin')
                ->name('admin.qrcode-options.index');
            Route::post('/admin/qrcode-options/apply-defaults', [App\Http\Controllers\Admin\QrCodeOptionsController::class, 'applyDefaults'])
                ->middleware('admin')
                ->name('admin.qrcode-options.apply-defaults');
            Route::post('/admin/qrcode-options/update-custom', [App\Http\Controllers\Admin\QrCodeOptionsController::class, 'updateCustomOptions'])
                ->middleware('admin')
                ->name('admin.qrcode-options.update-custom');
            Route::post('/admin/qrcode-options/toggle', [App\Http\Controllers\Admin\QrCodeOptionsController::class, 'toggleOptions'])
                ->middleware('admin')
                ->name('admin.qrcode-options.toggle');
Route::get('/admin/qrcodes/{id}/edit', [App\Http\Controllers\Admin\QrCodeController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.qrcodes.edit');
Route::put('/admin/qrcodes/{id}', [App\Http\Controllers\Admin\QrCodeController::class, 'update'])
    ->middleware('admin')
    ->name('admin.qrcodes.update');
Route::delete('/admin/qrcodes/{id}', [App\Http\Controllers\Admin\QrCodeController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.qrcodes.destroy');
Route::get('/admin/qrcodes/{id}/duplicate', [App\Http\Controllers\Admin\QrCodeController::class, 'duplicate'])
    ->middleware('admin')
    ->name('admin.qrcodes.duplicate');
Route::get('/admin/qrcodes/generate', [App\Http\Controllers\Admin\QrCodeController::class, 'generate'])
    ->middleware('admin')
    ->name('admin.qrcodes.generate');
Route::get('/admin/qrcodes/{id}/download/png', [App\Http\Controllers\Admin\QrCodeController::class, 'downloadPng'])
    ->middleware('admin')
    ->name('admin.qrcodes.download.png');
Route::get('/admin/qrcodes/{id}/download/svg', [App\Http\Controllers\Admin\QrCodeController::class, 'downloadSvg'])
    ->middleware('admin')
    ->name('admin.qrcodes.download.svg');

// 管理者ログアウト
Route::post('/admin/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])
    ->name('admin.logout');

// 管理者追加関連
Route::get('/admin/admins', [App\Http\Controllers\Admin\AdminController::class, 'index'])
    ->middleware('admin')
    ->name('admin.admins');
Route::get('/admin/admins/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])
    ->middleware('admin')
    ->name('admin.admins.create');
Route::post('/admin/admins', [App\Http\Controllers\Admin\AdminController::class, 'store'])
    ->middleware('admin')
    ->name('admin.admins.store');
Route::get('/admin/admins/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.admins.edit');
Route::put('/admin/admins/{id}', [App\Http\Controllers\Admin\AdminController::class, 'update'])
    ->middleware('admin')
    ->name('admin.admins.update');
Route::delete('/admin/admins/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.admins.destroy');

    // ペンライト制御
    Route::get('/admin/penlight-control', [App\Http\Controllers\Admin\PenlightController::class, 'index'])
        ->middleware('admin')
        ->name('admin.penlight-control');
    
    // ペンライト制御API
    Route::prefix('/admin/penlight')->middleware('admin')->group(function () {
        Route::get('/songs', [App\Http\Controllers\Admin\PenlightController::class, 'songs'])->name('admin.penlight.songs');
        Route::get('/artists', [App\Http\Controllers\Admin\PenlightController::class, 'artists'])->name('admin.penlight.artists');
        Route::get('/presets', [App\Http\Controllers\Admin\PenlightController::class, 'presets'])->name('admin.penlight.presets');
        Route::get('/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'preset'])->name('admin.penlight.presets.show');
        Route::post('/presets', [App\Http\Controllers\Admin\PenlightController::class, 'storePreset'])->name('admin.penlight.presets.store');
        Route::put('/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'updatePreset'])->name('admin.penlight.presets.update');
        Route::delete('/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'destroyPreset'])->name('admin.penlight.presets.destroy');
        Route::post('/presets/{id}/activate', [App\Http\Controllers\Admin\PenlightController::class, 'activatePreset'])->name('admin.penlight.presets.activate');
        Route::post('/presets/{id}/deactivate', [App\Http\Controllers\Admin\PenlightController::class, 'deactivatePreset'])->name('admin.penlight.presets.deactivate');
        Route::post('/presets/deactivate-all', [App\Http\Controllers\Admin\PenlightController::class, 'deactivateAllPresets'])->name('admin.penlight.presets.deactivate-all');
        Route::post('/presets/reorder', [App\Http\Controllers\Admin\PenlightController::class, 'reorderPresets'])->name('admin.penlight.presets.reorder');
        Route::post('/create-presets', [App\Http\Controllers\Admin\PenlightController::class, 'createPresets'])->name('admin.penlight.create-presets');
        Route::get('/websocket-stats', [App\Http\Controllers\Admin\PenlightController::class, 'websocketStats'])->name('admin.penlight.websocket-stats');
    });

    // アーティスト管理
    Route::prefix('/admin/artists')->middleware('admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ArtistController::class, 'index'])->name('admin.artists.index');
        Route::get('/create', [App\Http\Controllers\Admin\ArtistController::class, 'create'])->name('admin.artists.create');
        Route::post('/', [App\Http\Controllers\Admin\ArtistController::class, 'store'])->name('admin.artists.store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\ArtistController::class, 'edit'])->name('admin.artists.edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\ArtistController::class, 'update'])->name('admin.artists.update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ArtistController::class, 'destroy'])->name('admin.artists.destroy');
        Route::get('/{id}/songs', [App\Http\Controllers\Admin\ArtistController::class, 'songs'])->name('admin.artists.songs');
        
        // API用ルート
        Route::get('/api/list', [App\Http\Controllers\Admin\ArtistController::class, 'apiList'])->name('admin.artists.api.list');
        Route::get('/{id}/songs/api/list', [App\Http\Controllers\Admin\ArtistController::class, 'apiSongsList'])->name('admin.artists.songs.api.list');
        
        // 楽曲管理
        Route::get('/{artistId}/songs/create', [App\Http\Controllers\Admin\ArtistController::class, 'createSong'])->name('admin.songs.create');
        Route::post('/{artistId}/songs', [App\Http\Controllers\Admin\ArtistController::class, 'storeSong'])->name('admin.songs.store');
        Route::get('/{artistId}/songs/{songId}/edit', [App\Http\Controllers\Admin\ArtistController::class, 'editSong'])->name('admin.songs.edit');
        Route::put('/{artistId}/songs/{songId}', [App\Http\Controllers\Admin\ArtistController::class, 'updateSong'])->name('admin.songs.update');
        Route::delete('/{artistId}/songs/{songId}', [App\Http\Controllers\Admin\ArtistController::class, 'destroySong'])->name('admin.songs.destroy');
    });
