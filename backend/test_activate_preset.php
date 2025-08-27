<?php

require_once 'vendor/autoload.php';

use App\Models\PenlightPreset;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== プリセットアクティブ化テスト ===\n";

// コマンドライン引数からプリセットIDを取得
$presetId = $argv[1] ?? 2; // デフォルトはプリセット2

echo "プリセットID {$presetId} をアクティブ化します...\n";

try {
    // プリセットを取得
    $preset = PenlightPreset::with('song.artist')->find($presetId);
    
    if (!$preset) {
        echo "エラー: プリセットID {$presetId} が見つかりません\n";
        exit(1);
    }
    
    echo "プリセット情報:\n";
    echo "- 名前: {$preset->name}\n";
    echo "- 楽曲: {$preset->song->song_name}\n";
    echo "- アーティスト: {$preset->song->artist->artist_name}\n";
    echo "- パターン: {$preset->pattern}\n";
    echo "- BPM: {$preset->bpm}\n";
    echo "- 明度: {$preset->brightness}\n";
    
    // 他のプリセットを非アクティブ化
    PenlightPreset::where('is_active', true)->update(['is_active' => false]);
    
    // 指定されたプリセットをアクティブ化
    $preset->update([
        'is_active' => true,
        'activated_at' => now()
    ]);
    
    echo "\n✅ プリセット「{$preset->name}」をアクティブ化しました\n";
    
    // 現在アクティブなプリセットを確認
    $activePreset = PenlightPreset::where('is_active', true)->first();
    if ($activePreset) {
        echo "現在アクティブなプリセット: {$activePreset->name} (ID: {$activePreset->id})\n";
    }
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nテスト完了\n";

