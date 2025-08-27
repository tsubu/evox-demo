<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ニュース関連
Route::get('/news/latest', [App\Http\Controllers\Api\NewsController::class, 'latest']);

// アバター関連
Route::get('/avatars', [App\Http\Controllers\Api\AvatarController::class, 'index']);
Route::get('/avatars/{id}', [App\Http\Controllers\Api\AvatarController::class, 'show']);

// 登録者数取得
Route::get('/registrations/count', [App\Http\Controllers\Api\RegistrationController::class, 'getCount']);

// ログ関連のAPI（認証不要）
Route::post('/logs/store', [App\Http\Controllers\Api\LogController::class, 'store']);

// アーティスト関連
Route::get('/artists', [App\Http\Controllers\Api\ArtistController::class, 'index']);

// ユーザー登録関連
Route::post('/entry', [App\Http\Controllers\Api\RegistrationController::class, 'store']);
Route::post('/entry/verify', [App\Http\Controllers\Api\RegistrationController::class, 'verify']);
Route::post('/entry/complete', [App\Http\Controllers\Api\RegistrationController::class, 'complete']);
Route::post('/entry/set-password', [App\Http\Controllers\Api\RegistrationController::class, 'setPassword']);

// 認証関連
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/login/verify-otp', [App\Http\Controllers\Api\AuthController::class, 'verifyLoginOtp']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

// QRコード関連（認証必要）
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/qr/claim', [App\Http\Controllers\Api\QrCodeController::class, 'claim']);
    Route::post('/qr/process-options', [App\Http\Controllers\Api\QrCodeController::class, 'processOptions']);
    Route::get('/qr/history', [App\Http\Controllers\Api\QrCodeController::class, 'history']);
    
    // 統計分析API
    Route::get('/stats/qr-usage', [App\Http\Controllers\Api\StatsController::class, 'qrUsageStats']);
    Route::get('/stats/option-selection', [App\Http\Controllers\Api\StatsController::class, 'optionSelectionStats']);
    Route::get('/stats/themes', [App\Http\Controllers\Api\StatsController::class, 'themeStats']);
    Route::get('/stats/time-series', [App\Http\Controllers\Api\StatsController::class, 'timeSeriesStats']);
    Route::get('/stats/user-behavior', [App\Http\Controllers\Api\StatsController::class, 'userBehaviorStats']);
    
    // ペンライト制御API
    Route::get('/penlight/settings/{roomId?}', [App\Http\Controllers\Api\PenlightController::class, 'getSettings']);
    
    // プロフィール関連API
    Route::get('/profile/check', [App\Http\Controllers\Api\ProfileController::class, 'check']);
    Route::post('/profile/avatar', [App\Http\Controllers\Api\ProfileController::class, 'setAvatar']);
    Route::post('/profile/nickname', [App\Http\Controllers\Api\ProfileController::class, 'setNickname']);
    Route::post('/profile/update-password', [App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);
    Route::post('/profile/update-profile', [App\Http\Controllers\Api\ProfileController::class, 'updateProfile']);
    
    // マイページ関連API
    Route::get('/mypage/profile', [App\Http\Controllers\Api\MyPageController::class, 'profile']);
    Route::get('/mypage/points', [App\Http\Controllers\Api\MyPageController::class, 'points']);
    Route::get('/mypage/activities', [App\Http\Controllers\Api\MyPageController::class, 'activities']);
});

// 管理者用API
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // ペンライト制御API（新仕様）
    Route::get('/penlight/artists', [App\Http\Controllers\Admin\PenlightController::class, 'artists']);
    Route::get('/penlight/songs', [App\Http\Controllers\Admin\PenlightController::class, 'songs']);
    Route::get('/penlight/presets', [App\Http\Controllers\Admin\PenlightController::class, 'presets']);
    Route::get('/penlight/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'preset']);
    Route::post('/penlight/presets', [App\Http\Controllers\Admin\PenlightController::class, 'storePreset']);
    Route::put('/penlight/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'updatePreset']);
    Route::delete('/penlight/presets/{id}', [App\Http\Controllers\Admin\PenlightController::class, 'destroyPreset']);
    Route::post('/penlight/presets/{id}/activate', [App\Http\Controllers\Admin\PenlightController::class, 'activatePreset']);
    Route::post('/penlight/presets/{id}/deactivate', [App\Http\Controllers\Admin\PenlightController::class, 'deactivatePreset']);
    Route::post('/penlight/presets/reorder', [App\Http\Controllers\Admin\PenlightController::class, 'reorderPresets']);
    Route::get('/penlight/websocket-stats', [App\Http\Controllers\Api\PenlightController::class, 'getWebSocketStats']);
    
    // QRコード参加者一覧API
    Route::get('/qr/participants', [App\Http\Controllers\Api\QrCodeController::class, 'getLiveEventParticipants']);
    
    // ログ関連のAPI
    Route::get('/logs', [App\Http\Controllers\Api\LogController::class, 'index']);
    Route::get('/logs/{filename}', [App\Http\Controllers\Api\LogController::class, 'show']);
});

// ペンライト配信状況取得
Route::get('/penlight/streaming-status', [App\Http\Controllers\Api\PenlightController::class, 'getStreamingStatus']);

// 管理画面用アーティスト制御（認証必要）
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/activate-artist', [App\Http\Controllers\Api\ArtistController::class, 'activateArtist']);
    Route::post('/admin/deactivate-artist', [App\Http\Controllers\Api\ArtistController::class, 'deactivateArtist']);
});


// 管理者認証関連
Route::prefix('admin')->middleware('web')->group(function () {
    Route::post('/auth/login', [App\Http\Controllers\AdminAuthController::class, 'login']);
    Route::post('/auth/verify-otp', [App\Http\Controllers\AdminAuthController::class, 'verifyOtp']);
    Route::post('/auth/logout', [App\Http\Controllers\AdminAuthController::class, 'logout']);
});
