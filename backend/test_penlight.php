<?php

require_once 'vendor/autoload.php';

use App\Models\PenlightPreset;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ペンライト配信テスト ===\n";

// 現在アクティブなプリセットを確認
$activePreset = PenlightPreset::where('is_active', true)->first();

if ($activePreset) {
    echo "現在アクティブなプリセット:\n";
    echo "- ID: {$activePreset->id}\n";
    echo "- 名前: {$activePreset->name}\n";
    echo "- パターン: {$activePreset->pattern}\n";
    echo "- BPM: {$activePreset->bpm}\n";
    echo "- 明度: {$activePreset->brightness}\n";
    echo "- 音楽連動: " . ($activePreset->audio_sync ? 'ON' : 'OFF') . "\n";
} else {
    echo "現在アクティブなプリセットはありません\n";
}

// 利用可能なプリセットを表示
echo "\n利用可能なプリセット:\n";
$presets = PenlightPreset::with('song.artist')->get();

foreach ($presets as $preset) {
    echo "- ID: {$preset->id}, 名前: {$preset->name}, 楽曲: {$preset->song->song_name}, アーティスト: {$preset->song->artist->artist_name}\n";
}

echo "\nテスト完了\n";
