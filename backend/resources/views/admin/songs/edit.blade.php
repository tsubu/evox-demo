@extends('admin.layout')

@section('title', '楽曲編集')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ $artist->artist_name }} - 楽曲編集</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.artists.songs', $artist->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 楽曲一覧に戻る
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">楽曲情報編集</h3>
                </div>
                <div class="card-body">
                    <form id="song-form" action="{{ route('admin.songs.update', [$artist->id, $song->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="song_name">楽曲名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('song_name') is-invalid @enderror" 
                                           id="song_name" name="song_name" value="{{ old('song_name', $song->song_name) }}" required>
                                    @error('song_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="song_color">楽曲カラー <span class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('song_color') is-invalid @enderror" 
                                           id="song_color" name="song_color" value="{{ old('song_color', $song->song_color) }}" required>
                                    @error('song_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="song_description">説明</label>
                            <textarea class="form-control @error('song_description') is-invalid @enderror" 
                                      id="song_description" name="song_description" rows="3">{{ old('song_description', $song->song_description) }}</textarea>
                            @error('song_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 隠しフィールドでペンライト設定を保存 -->
                        <input type="hidden" name="song_pattern" value="{{ old('song_pattern', $song->song_pattern) }}">
                        <input type="hidden" name="song_brightness" value="{{ old('song_brightness', $song->song_brightness) }}">
                        <input type="hidden" name="song_bpm" value="{{ old('song_bpm', $song->song_bpm) }}">
                        <input type="hidden" name="song_intensity" value="{{ old('song_intensity', $song->song_intensity) }}">
                        <input type="hidden" name="song_color_scheme" value="{{ old('song_color_scheme', $song->song_color_scheme) }}">
                        <input type="hidden" name="song_is_active" value="{{ old('song_is_active', $song->song_is_active) ? '1' : '0' }}">
                        <input type="hidden" name="penlight_color" value="{{ old('penlight_color', $song->penlight_color ?? 'rgba(0,0,0,0)') }}">
                        <!-- 新しいペンライト音声フィールド -->
                        <input type="hidden" name="penlight_audio_sync" value="{{ old('penlight_audio_sync', $song->penlight_audio_sync) ? '1' : '0' }}">
                        <input type="hidden" name="penlight_music_intensity" value="{{ old('penlight_music_intensity', $song->penlight_music_intensity ?? 0.8) }}">
                        <input type="hidden" name="penlight_noise_gate_threshold" value="{{ old('penlight_noise_gate_threshold', $song->penlight_noise_gate_threshold ?? 35.0) }}">
                        <input type="hidden" name="penlight_frequency_low" value="{{ old('penlight_frequency_low', $song->penlight_frequency_low ?? 2) }}">
                        <input type="hidden" name="penlight_frequency_high" value="{{ old('penlight_frequency_high', $song->penlight_frequency_high ?? 25) }}">

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 更新
                            </button>
                            <a href="{{ route('admin.artists.songs', $artist->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 詳細ペンライト制御 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">詳細ペンライト制御</h3>
                </div>
                <div class="card-body">
                    <form id="detailedPenlightForm">
                        <!-- 基本設定 -->
                        <h5>基本設定</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="penlight_color">ペンライト調整色</label>
                                    <input type="color" class="form-control" id="penlight_color" 
                                           value="{{ old('penlight_color', $song->penlight_color ?? '#ff0000') }}">
                                    <small class="form-text text-muted">ペンライト専用の色を設定</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="penlight_pattern">アニメーションパターン</label>
                                    <select class="form-control" id="penlight_pattern">
                                        <option value="solid">単色（アニメーションなし）</option>
                                        <option value="blink">派手な点滅</option>
                                        <option value="fade">派手なフェード</option>
                                        <option value="wave">回転波紋</option>
                                        <option value="pulse">派手なパルス</option>
                                        <option value="rainbow">虹色変化</option>
                                        <option value="strobe">ストロボ</option>
                                    </select>
                                    <small class="form-text text-muted">ペンライトの動きを選択</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="penlight_brightness">明度</label>
                                    <input type="range" class="form-control-range" id="penlight_brightness" 
                                           value="{{ old('song_brightness', $song->song_brightness) ?? 80 }}" min="1" max="100">
                                    <div class="d-flex justify-content-between">
                                        <small>暗い</small>
                                        <small id="brightness_value">80</small>
                                        <small>明るい</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="penlight_bpm">アニメーション速度</label>
                                    <input type="range" class="form-control-range" id="penlight_bpm" 
                                           value="{{ old('song_bpm', $song->song_bpm) ?? 120 }}" min="60" max="200">
                                    <div class="d-flex justify-content-between">
                                        <small>60</small>
                                        <small id="bpm_value">120 BPM</small>
                                        <small>200</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        



                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penlight_intensity">エフェクト強度</label>
                                    <select class="form-control" id="penlight_intensity">
                                        <option value="low">低（控えめ）</option>
                                        <option value="medium" selected>中（標準）</option>
                                        <option value="high">高（派手）</option>
                                    </select>
                                    <small class="form-text text-muted">アニメーションの派手さを調整</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="penlight_is_active" checked>
                                        <label class="custom-control-label" for="penlight_is_active">ペンライト有効</label>
                                    </div>
                                    <small class="form-text text-muted">この楽曲でペンライトを有効にする</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="penlight_audio_sync" 
                                               {{ old('penlight_audio_sync', $song->penlight_audio_sync) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="penlight_audio_sync">音楽連動モード</label>
                                    </div>
                                    <small class="form-text text-muted">音楽のリズムに合わせて動作</small>
                                </div>
                            </div>
                        </div>

                        <!-- 音楽連動設定（音楽連動モードが有効な場合のみ表示） -->
                        <div id="musicSyncSettings" style="display: none;">
                            <h5 class="mt-4">音楽連動設定</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="penlight_music_intensity">音楽感度</label>
                                        <input type="range" class="form-control-range" id="penlight_music_intensity" 
                                               value="{{ old('penlight_music_intensity', $song->penlight_music_intensity ?? 0.8) }}" 
                                               min="0" max="1" step="0.1">
                                        <div class="d-flex justify-content-between">
                                            <small>低</small>
                                            <small id="music_intensity_value">{{ old('penlight_music_intensity', $song->penlight_music_intensity ?? 0.8) }}</small>
                                            <small>高</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- プレビューエリア -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>プレビュー</h5>
                                <div class="penlight-preview" id="penlightPreview">
                                    <div class="light-effect" id="previewLightEffect"></div>
                                </div>
                            </div>
                        </div>

                        <!-- ボタンエリア -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" onclick="updateSongAndPenlight()">
                                        <i class="fas fa-save"></i> 更新
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetPenlightSettings()">
                                        <i class="fas fa-undo"></i> リセット
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="previewPenlight()">
                                        <i class="fas fa-eye"></i> プレビュー
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 削除フォーム -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> 危険な操作
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">この楽曲を削除すると、関連するペンライト設定もすべて削除されます。この操作は取り消せません。</p>
                <form action="{{ route('admin.songs.destroy', [$artist->id, $song->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('本当にこの楽曲を削除しますか？\n\nこの操作により、関連するペンライト設定もすべて削除されます。\nこの操作は取り消せません。')">
                        <i class="fas fa-trash"></i> 楽曲を削除
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.penlight-preview {
    height: 100px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #000000;
}

.light-effect {
    width: 100%;
    height: 100%;
    border-radius: 8px;
    transition: all 0.1s ease;
}

@keyframes blink {
    0%, 20% { opacity: 1; transform: scale(1); filter: brightness(1.5); }
    21%, 40% { opacity: 0.3; transform: scale(0.8); filter: brightness(0.8); }
    41%, 60% { opacity: 1; transform: scale(1.2); filter: brightness(2); }
    61%, 80% { opacity: 0.4; transform: scale(0.9); filter: brightness(0.9); }
    81%, 100% { opacity: 1; transform: scale(1); filter: brightness(1.5); }
}

@keyframes fade {
    0% { opacity: 0.3; transform: scale(0.8); filter: brightness(0.8); }
    25% { opacity: 0.6; transform: scale(0.9); filter: brightness(1.2); }
    50% { opacity: 1; transform: scale(1.1); filter: brightness(1.8); }
    75% { opacity: 0.6; transform: scale(0.9); filter: brightness(1.2); }
    100% { opacity: 0.3; transform: scale(0.8); filter: brightness(0.8); }
}

@keyframes wave {
    0% { transform: scale(1) rotate(0deg); filter: brightness(1) hue-rotate(0deg); }
    25% { transform: scale(1.3) rotate(90deg); filter: brightness(1.5) hue-rotate(90deg); }
    50% { transform: scale(1.5) rotate(180deg); filter: brightness(2) hue-rotate(180deg); }
    75% { transform: scale(1.3) rotate(270deg); filter: brightness(1.5) hue-rotate(270deg); }
    100% { transform: scale(1) rotate(360deg); filter: brightness(1) hue-rotate(360deg); }
}

@keyframes pulse {
    0% { transform: scale(1); filter: brightness(1) contrast(1); }
    25% { transform: scale(1.2); filter: brightness(1.8) contrast(1.3); }
    50% { transform: scale(1.4); filter: brightness(2.5) contrast(1.5); }
    75% { transform: scale(1.2); filter: brightness(1.8) contrast(1.3); }
    100% { transform: scale(1); filter: brightness(1) contrast(1); }
}

@keyframes musicPulse {
    0% { transform: scale(1) rotate(0deg); filter: brightness(1) saturate(1); }
    20% { transform: scale(1.1) rotate(5deg); filter: brightness(1.3) saturate(1.2); }
    40% { transform: scale(1.2) rotate(-5deg); filter: brightness(1.6) saturate(1.4); }
    60% { transform: scale(1.1) rotate(5deg); filter: brightness(1.3) saturate(1.2); }
    80% { transform: scale(1.05) rotate(-3deg); filter: brightness(1.1) saturate(1.1); }
    100% { transform: scale(1) rotate(0deg); filter: brightness(1) saturate(1); }
}

@keyframes rainbow {
    0% { filter: hue-rotate(0deg) brightness(1.5) saturate(1.5); }
    16.66% { filter: hue-rotate(60deg) brightness(1.8) saturate(1.8); }
    33.33% { filter: hue-rotate(120deg) brightness(1.5) saturate(1.5); }
    50% { filter: hue-rotate(180deg) brightness(1.8) saturate(1.8); }
    66.66% { filter: hue-rotate(240deg) brightness(1.5) saturate(1.5); }
    83.33% { filter: hue-rotate(300deg) brightness(1.8) saturate(1.8); }
    100% { filter: hue-rotate(360deg) brightness(1.5) saturate(1.5); }
}

@keyframes strobe {
    0%, 10% { opacity: 1; transform: scale(1); filter: brightness(2) contrast(2); }
    11%, 20% { opacity: 0.3; transform: scale(0.5); filter: brightness(0.8) contrast(1.5); }
    21%, 30% { opacity: 1; transform: scale(1.2); filter: brightness(2.5) contrast(2.5); }
    31%, 40% { opacity: 0.3; transform: scale(0.5); filter: brightness(0.8) contrast(1.5); }
    41%, 50% { opacity: 1; transform: scale(1.1); filter: brightness(2) contrast(2); }
    51%, 60% { opacity: 0.3; transform: scale(0.5); filter: brightness(0.8) contrast(1.5); }
    61%, 70% { opacity: 1; transform: scale(1.3); filter: brightness(2.5) contrast(2.5); }
    71%, 80% { opacity: 0.3; transform: scale(0.5); filter: brightness(0.8) contrast(1.5); }
    81%, 90% { opacity: 1; transform: scale(1.1); filter: brightness(2) contrast(2); }
    91%, 100% { opacity: 0.3; transform: scale(0.5); filter: brightness(0.8) contrast(1.5); }
}
</style>

@push('scripts')
<script>
// 初期設定
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoadedイベントが実行されました');
    
    // 楽曲の基本設定をペンライト制御に反映（デフォルト値を設定）
    const songColor = document.querySelector('input[name="song_color"]').value || '#ff0000';
    const songPattern = document.querySelector('input[name="song_pattern"]').value || 'solid';
    const songBrightness = document.querySelector('input[name="song_brightness"]').value || '80';
    const songBpm = document.querySelector('input[name="song_bpm"]').value || '120';
    const songIntensity = document.querySelector('input[name="song_intensity"]').value || 'medium';
    const songColorScheme = document.querySelector('input[name="song_color_scheme"]').value || 'artist';
    
    console.log('取得した楽曲設定:', { songColor, songPattern, songBrightness, songBpm, songIntensity, songColorScheme });
    
    // ペンライト制御に値を設定（ペンライト調整色は独立して設定）
    // document.getElementById('penlight_color').value = songColor; // 楽曲カラーとは独立
    
    // 各要素の存在をチェックしてから値を設定
    const penlightPattern = document.getElementById('penlight_pattern');
    const penlightBrightness = document.getElementById('penlight_brightness');
    const penlightBpm = document.getElementById('penlight_bpm');
    const penlightIntensity = document.getElementById('penlight_intensity');
    const penlightColorScheme = document.getElementById('penlight_color_scheme');
    const penlightMusicBpm = document.getElementById('penlight_music_bpm');
    
    if (penlightPattern) penlightPattern.value = songPattern;
    if (penlightBrightness) penlightBrightness.value = songBrightness;
    if (penlightBpm) penlightBpm.value = songBpm;
    if (penlightIntensity) penlightIntensity.value = songIntensity;
    if (penlightColorScheme) penlightColorScheme.value = songColorScheme;
    if (penlightMusicBpm) penlightMusicBpm.value = songBpm;
    
    console.log('ペンライト制御に設定された値:', {
        color: document.getElementById('penlight_color')?.value || 'N/A',
        pattern: penlightPattern?.value || 'N/A',
        brightness: penlightBrightness?.value || 'N/A',
        bpm: penlightBpm?.value || 'N/A',
        intensity: penlightIntensity?.value || 'N/A',
        colorScheme: penlightColorScheme?.value || 'N/A',
        musicBpm: penlightMusicBpm?.value || 'N/A'
    });
    
    // プレビューを更新
    updatePreview();
    
    // スライダーの値を表示する関数
    function updateSliderValues() {
        const brightnessSlider = document.getElementById('penlight_brightness');
        const brightnessValue = document.getElementById('brightness_value');
        const bpmSlider = document.getElementById('penlight_bpm');
        const bpmValue = document.getElementById('bpm_value');
        const musicBpmSlider = document.getElementById('penlight_music_bpm');
        const musicBpmValue = document.getElementById('music_bpm_value');
        const musicIntensitySlider = document.getElementById('penlight_music_intensity');
        const musicIntensityValue = document.getElementById('music_intensity_value');
        
        if (brightnessSlider && brightnessValue) {
            brightnessValue.textContent = brightnessSlider.value;
        }
        if (bpmSlider && bpmValue) {
            bpmValue.textContent = bpmSlider.value + ' BPM';
        }
        if (musicBpmSlider && musicBpmValue) {
            musicBpmValue.textContent = musicBpmSlider.value + ' BPM';
        }
        if (musicIntensitySlider && musicIntensityValue) {
            musicIntensityValue.textContent = musicIntensitySlider.value;
        }
    }
    

    
    // 音楽連動モードの表示制御
    function toggleMusicSyncSettings() {
        const audioSyncCheckbox = document.getElementById('penlight_audio_sync');
        const musicSyncSettings = document.getElementById('musicSyncSettings');
        
        if (audioSyncCheckbox && musicSyncSettings) {
            if (audioSyncCheckbox.checked) {
                musicSyncSettings.style.display = 'block';
            } else {
                musicSyncSettings.style.display = 'none';
            }
        }
    }
    
    // 初期表示
    updateSliderValues();
    toggleMusicSyncSettings();
    
    // 音楽連動モードの初期状態を設定
    const initialAudioSyncCheckbox = document.getElementById('penlight_audio_sync');
    if (initialAudioSyncCheckbox) {
        // チェックボックスの状態に基づいて音楽連動設定の表示/非表示を制御
        const musicSyncSettings = document.getElementById('musicSyncSettings');
        if (musicSyncSettings) {
            musicSyncSettings.style.display = initialAudioSyncCheckbox.checked ? 'block' : 'none';
        }
    }
    

    

    
    // 設定変更時にプレビューを更新し、隠しフィールドも更新
    const inputs = document.querySelectorAll('#detailedPenlightForm input, #detailedPenlightForm select');
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            // チェックボックスはchangeイベント
            input.addEventListener('change', function() {
                console.log('設定が変更されました:', input.id, input.checked);
                if (input.id === 'penlight_audio_sync') {
                    toggleMusicSyncSettings();
                }
                // プレビューを自動停止
                stopPreviewAndHide();
                updatePreview();
                updateHiddenFields();
            });
        } else if (input.type === 'range') {
            // スライダーはinputイベント（リアルタイム）とchangeイベントの両方
            input.addEventListener('input', function() {
                console.log('設定がリアルタイムで変更されました:', input.id, input.value);
                updateSliderValues();
                updatePreview();
            });
            input.addEventListener('change', function() {
                console.log('設定が変更されました:', input.id, input.value);
                // プレビューを自動停止
                stopPreviewAndHide();
                updateHiddenFields();
            });
        } else {
            // その他の入力はinputイベント（リアルタイム）とchangeイベントの両方
            input.addEventListener('input', function() {
                console.log('設定がリアルタイムで変更されました:', input.id, input.value);
                updatePreview();
            });
            input.addEventListener('change', function() {
                console.log('設定が変更されました:', input.id, input.value);
                // プレビューを自動停止
                stopPreviewAndHide();
                updateHiddenFields();
            });
        }
    });
    
    // プレビューボタンの初期状態を設定
    const previewButton = document.querySelector('button[onclick="previewPenlight()"]');
    previewButton.setAttribute('data-playing', 'false');
    
    // 音楽連動モードのチェックボックスにイベントリスナーを追加
    const eventAudioSyncCheckbox = document.getElementById('penlight_audio_sync');
    if (eventAudioSyncCheckbox) {
        eventAudioSyncCheckbox.addEventListener('change', function() {
            console.log('音楽連動モードの状態が変更されました:', this.checked);
            if (this.checked) {
                console.log('音楽連動モードが有効になりました');
            } else {
                console.log('音楽連動モードが無効になりました');
                // 音楽連動モードが無効になった場合は音声反応を停止
                stopAudioReaction();
            }
        });
    }
});

// 隠しフィールドを更新
function updateHiddenFields() {
    // 各要素の存在をチェックしてから値を設定
    const songPatternInput = document.querySelector('input[name="song_pattern"]');
    const songBrightnessInput = document.querySelector('input[name="song_brightness"]');
    const songBpmInput = document.querySelector('input[name="song_bpm"]');
    const songIntensityInput = document.querySelector('input[name="song_intensity"]');
    const songIsActiveInput = document.querySelector('input[name="song_is_active"]');
    const penlightColorInput = document.querySelector('input[name="penlight_color"]');
    
    const penlightPattern = document.getElementById('penlight_pattern');
    const penlightBrightness = document.getElementById('penlight_brightness');
    const penlightBpm = document.getElementById('penlight_bpm');
    const penlightIntensity = document.getElementById('penlight_intensity');
    const penlightIsActive = document.getElementById('penlight_is_active');
    const penlightColor = document.getElementById('penlight_color');
    
    if (songPatternInput && penlightPattern) {
        songPatternInput.value = penlightPattern.value;
    }
    if (songBrightnessInput && penlightBrightness) {
        songBrightnessInput.value = penlightBrightness.value;
    }
    if (songBpmInput && penlightBpm) {
        songBpmInput.value = penlightBpm.value;
    }
    if (songIntensityInput && penlightIntensity) {
        songIntensityInput.value = penlightIntensity.value;
    }
    if (songIsActiveInput && penlightIsActive) {
        songIsActiveInput.value = penlightIsActive.checked ? '1' : '0';
    }
    if (penlightColorInput && penlightColor) {
        penlightColorInput.value = penlightColor.value;
    }
    
    // 新しいペンライト音声フィールドを隠しフィールドに保存
    const hiddenAudioSyncCheckbox = document.getElementById('penlight_audio_sync');
    const musicIntensitySlider = document.getElementById('penlight_music_intensity');
    
    const penlightAudioSyncInput = document.querySelector('input[name="penlight_audio_sync"]');
    const penlightMusicIntensityInput = document.querySelector('input[name="penlight_music_intensity"]');
    
    if (penlightAudioSyncInput && hiddenAudioSyncCheckbox) {
        penlightAudioSyncInput.value = hiddenAudioSyncCheckbox.checked ? '1' : '0';
    }
    if (penlightMusicIntensityInput && musicIntensitySlider) {
        penlightMusicIntensityInput.value = musicIntensitySlider.value;
    }
    
    // 古い音楽連動設定も隠しフィールドに保存（後方互換性のため）
    if (hiddenAudioSyncCheckbox && hiddenAudioSyncCheckbox.checked) {
        const songMusicBpmInput = document.querySelector('input[name="song_music_bpm"]');
        const songMusicIntensityInput = document.querySelector('input[name="song_music_intensity"]');
        const songMusicColorSchemeInput = document.querySelector('input[name="song_music_color_scheme"]');
        
        const penlightMusicBpm = document.getElementById('penlight_music_bpm');
        const penlightMusicIntensity = document.getElementById('penlight_music_intensity');
        const penlightMusicColorScheme = document.getElementById('penlight_music_color_scheme');
        
        if (songMusicBpmInput && penlightMusicBpm) {
            songMusicBpmInput.value = penlightMusicBpm.value;
        }
        if (songMusicIntensityInput && penlightMusicIntensity) {
            songMusicIntensityInput.value = penlightMusicIntensity.value;
        }
        if (songMusicColorSchemeInput && penlightMusicColorScheme) {
            songMusicColorSchemeInput.value = penlightMusicColorScheme.value;
        }
    }
}

// プレビュー更新
function updatePreview() {
    console.log('updatePreview関数が呼ばれました');
    
    const songColor = document.getElementById('song_color').value;
    const penlightColor = document.getElementById('penlight_color').value;
    const pattern = document.getElementById('penlight_pattern').value;
    const brightness = document.getElementById('penlight_brightness').value;
    const bpm = document.getElementById('penlight_bpm').value;
    const intensity = document.getElementById('penlight_intensity').value;
    const audioSync = document.getElementById('penlight_audio_sync').checked;
    
    console.log('取得した値:', { songColor, penlightColor, pattern, brightness, bpm, intensity, audioSync });
    
    const lightEffect = document.getElementById('previewLightEffect');
    console.log('lightEffect要素:', lightEffect);
    
    // 現在の再生状態を保存
    const previewButton = document.querySelector('button[onclick="previewPenlight()"]');
    const isPlaying = previewButton.getAttribute('data-playing') === 'true';
    
    console.log('プレビュー更新開始');
    
    // 既存のスタイルをクリア（アニメーション再生状態は保持）
    const currentAnimationPlayState = lightEffect.style.animationPlayState;
    lightEffect.style = '';
    lightEffect.style.animationPlayState = currentAnimationPlayState;
    
    if (audioSync) {
        // 音楽連動モード（実際の音声反応）
        const musicIntensity = document.getElementById('penlight_music_intensity')?.value || 0.8;
        
        console.log('音楽連動設定:', { musicIntensity });
        
        lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
        lightEffect.style.opacity = brightness / 100;
        
        // 音声反応機能を開始（プレビュー再生中の場合のみ）
        if (isPlaying) {
            console.log('プレビュー再生中なので音声反応機能を開始します');
            startAudioReaction(lightEffect, musicIntensity, songColor, penlightColor);
        } else {
            console.log('プレビュー停止中なので音声反応機能を停止します');
            stopAudioReaction();
        }
        
        console.log('音楽連動モード（音声反応）のスタイルを適用');
    } else {
        // サーバー制御モード
        switch (pattern) {
            case 'blink':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `blink ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            case 'fade':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `fade ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            case 'wave':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `wave ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            case 'pulse':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `pulse ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            case 'rainbow':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `rainbow ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            case 'strobe':
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                lightEffect.style.opacity = brightness / 100;
                lightEffect.style.animation = `strobe ${60 / bpm}s infinite`;
                lightEffect.style.animationPlayState = isPlaying ? 'running' : 'paused';
                break;
            default:
                // 単色の場合は透明（何も表示しない）
                lightEffect.style.backgroundColor = 'transparent';
                lightEffect.style.opacity = '0';
                lightEffect.style.animation = 'none';
                lightEffect.style.animationPlayState = 'paused';
                break;
        }
        console.log('サーバー制御モードのスタイルを適用:', {
            backgroundColor: lightEffect.style.backgroundColor,
            opacity: lightEffect.style.opacity,
            animation: lightEffect.style.animation
        });
    }
    
    console.log('最終的なlightEffect.style:', lightEffect.style);
    console.log('プレビュー要素の現在のスタイル:', {
        backgroundColor: lightEffect.style.backgroundColor,
        opacity: lightEffect.style.opacity,
        animation: lightEffect.style.animation,
        animationPlayState: lightEffect.style.animationPlayState,
        width: lightEffect.style.width,
        height: lightEffect.style.height
    });
    
    // スタイルが正しく適用されているか確認
    console.log('lightEffect.style.backgroundColor:', lightEffect.style.backgroundColor);
    console.log('lightEffect.style.opacity:', lightEffect.style.opacity);
    console.log('lightEffect.style.animation:', lightEffect.style.animation);
}

// プレビューボタン
function previewPenlight() {
    console.log('プレビューボタンがクリックされました');
    
    const previewButton = document.querySelector('button[onclick="previewPenlight()"]');
    console.log('プレビューボタン要素:', previewButton);
    
    const isPlaying = previewButton.getAttribute('data-playing') === 'true';
    console.log('現在の再生状態:', isPlaying);
    
    if (isPlaying) {
        // プレビュー停止
        console.log('プレビューを停止します');
        stopPreview();
        previewButton.innerHTML = '<i class="fas fa-play"></i> プレビュー開始';
        previewButton.setAttribute('data-playing', 'false');
        previewButton.className = 'btn btn-info';
    } else {
        // プレビュー開始
        console.log('プレビューを開始します');
        updatePreview();
        startPreview();
        previewButton.innerHTML = '<i class="fas fa-stop"></i> プレビュー停止';
        previewButton.setAttribute('data-playing', 'true');
        previewButton.className = 'btn btn-warning';
    }
}

// プレビュー開始
function startPreview() {
    console.log('startPreview関数が呼ばれました');
    const lightEffect = document.getElementById('previewLightEffect');
    console.log('lightEffect要素:', lightEffect);
    
    // 音楽連動モードかどうかをチェック
    const audioSync = document.getElementById('penlight_audio_sync');
    if (audioSync && audioSync.checked) {
        console.log('音楽連動モードが有効です。音声反応機能を開始します。');
        // 音楽連動モードの場合は音声反応機能を開始
        const musicIntensity = document.getElementById('penlight_music_intensity')?.value || 0.8;
        const songColor = document.getElementById('song_color')?.value || 'rgba(0,0,0,0)';
        const penlightColor = document.getElementById('penlight_color')?.value || '#ff0000';
        
        startAudioReaction(lightEffect, musicIntensity, songColor, penlightColor);
    } else {
        console.log('音楽連動モードが無効です。通常のアニメーションを開始します。');
        if (lightEffect) {
            lightEffect.style.animationPlayState = 'running';
            console.log('アニメーション再生状態をrunningに設定しました');
        } else {
            console.error('lightEffect要素が見つかりません');
        }
    }
}

// プレビュー停止
function stopPreview() {
    console.log('stopPreview関数が呼ばれました');
    const lightEffect = document.getElementById('previewLightEffect');
    console.log('lightEffect要素:', lightEffect);
    
    if (lightEffect) {
        // アニメーションを停止
        lightEffect.style.animationPlayState = 'paused';
        lightEffect.style.animation = 'none';
        lightEffect.style.opacity = '0';
        lightEffect.style.background = 'transparent';
        console.log('アニメーション再生状態をpausedに設定しました');
    } else {
        console.error('lightEffect要素が見つかりません');
    }
    
    // 音声反応機能も停止
    stopAudioReaction();
    console.log('音声反応機能を停止しました');
}

// プレビュー停止と非表示
function stopPreviewAndHide() {
    console.log('stopPreviewAndHide関数が呼ばれました');
    const lightEffect = document.getElementById('previewLightEffect');
    const previewButton = document.querySelector('button[onclick="previewPenlight()"]');
    
    if (lightEffect) {
        // プレビューを停止
        lightEffect.style.animationPlayState = 'paused';
        
        // プレビューを非表示
        lightEffect.style.opacity = '0';
        lightEffect.style.backgroundColor = 'transparent';
        lightEffect.style.background = 'transparent';
        lightEffect.style.animation = 'none';
        
        console.log('プレビューを停止して非表示にしました');
    }
    
    if (previewButton) {
        // ボタンの状態を更新
        previewButton.innerHTML = '<i class="fas fa-play"></i> プレビュー開始';
        previewButton.setAttribute('data-playing', 'false');
        previewButton.className = 'btn btn-info';
    }
    
    // 音声反応も停止
    stopAudioReaction();
}

// 音声反応機能のグローバル変数
let audioContext = null;
let analyser = null;
let microphone = null;
let animationId = null;

// 楽曲カラーとペンライト調整色を組み合わせたグラデーションを作成
function createCombinedGradient(songColor, penlightColor) {
    // 透明色または無効な色の場合はデフォルト色を使用
    if (!songColor || songColor === 'rgba(0,0,0,0)' || songColor === '#000000') {
        songColor = '#ff0000';
    }
    if (!penlightColor || penlightColor === 'rgba(0,0,0,0)' || penlightColor === '#000000') {
        penlightColor = '#ff0000';
    }
    
    return `linear-gradient(45deg, ${songColor} 0%, ${penlightColor} 50%, ${songColor} 100%)`;
}

// 音声反応機能を開始
function startAudioReaction(lightEffect, musicIntensity, songColor, penlightColor) {
    console.log('音声反応機能を開始します');
    
    // 既存の音声処理を停止
    stopAudioReaction();
    
    // マイク権限の確認
    if (navigator.permissions) {
        navigator.permissions.query({ name: 'microphone' }).then(permissionStatus => {
            console.log('マイク権限状態:', permissionStatus.state);
            
            if (permissionStatus.state === 'denied') {
                alert('マイクへのアクセスが拒否されています。\n\nブラウザの設定でマイクの使用を許可してください。\n\n解決方法：\n1. ブラウザのアドレスバーの左側にあるカメラアイコンをクリック\n2. 「マイク」を「許可」に設定\n3. ページを再読み込みして再試行');
                startAudioSimulation(lightEffect, musicIntensity, songColor, penlightColor);
                return;
            }
            
            // 権限が許可されている場合は音声取得を開始
            startMicrophoneAccess(lightEffect, musicIntensity, songColor, penlightColor);
        }).catch(error => {
            console.log('権限確認エラー:', error);
            // 権限確認ができない場合は直接音声取得を試行
            startMicrophoneAccess(lightEffect, musicIntensity, songColor, penlightColor);
        });
    } else {
        // 権限APIがサポートされていない場合は直接音声取得を試行
        startMicrophoneAccess(lightEffect, musicIntensity, songColor, penlightColor);
    }
}

// マイクアクセス開始
function startMicrophoneAccess(lightEffect, musicIntensity, songColor, penlightColor) {
    // Web Audio APIの初期化
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    
    // マイクからの音声取得（ノイズ対策強化）
    navigator.mediaDevices.getUserMedia({ 
        audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: false,
            sampleRate: 44100
        } 
    })
    .then(stream => {
        console.log('マイクアクセスが許可されました');
        
        microphone = audioContext.createMediaStreamSource(stream);
        analyser = audioContext.createAnalyser();
        
        // アナライザーの設定（ノイズ対策強化）
        analyser.fftSize = 512; // より高解像度
        analyser.smoothingTimeConstant = 0.3; // より反応性向上
        
        microphone.connect(analyser);
        
        // 音声データの解析とペンライト制御
        const bufferLength = analyser.frequencyBinCount;
        const dataArray = new Uint8Array(bufferLength);
        
        function updatePenlightFromAudio() {
            if (!analyser) return;
            
            analyser.getByteFrequencyData(dataArray);
            
            // ライブ会場対応のノイズゲート機能付き音声レベル計算
            const noiseGateThreshold = 35; // ノイズゲート閾値（0-255）- ライブ会場対応で高めに設定
            const frequencyRange = {
                low: 2,    // 低周波数開始（配列インデックス）- 超低周波ノイズを除外
                high: 25   // 高周波数終了（配列インデックス）- より狭い範囲で音楽に集中
            };
            
            // 指定周波数範囲での平均値を計算（ノイズゲート適用）
            let sum = 0;
            let count = 0;
            let maxValue = 0;
            
            for (let i = frequencyRange.low; i <= frequencyRange.high && i < dataArray.length; i++) {
                const value = dataArray[i];
                if (value > noiseGateThreshold) {
                    sum += value;
                    count++;
                    if (value > maxValue) {
                        maxValue = value;
                    }
                }
            }
            
            // ライブ会場対応のノイズゲート適用後のレベル計算
            let audioLevel = 0;
            let average = 0;
            if (count > 0) {
                average = sum / count;
                // ライブ会場対応：平均値とピーク値の組み合わせを調整
                audioLevel = Math.max(average * 0.8, maxValue * 0.5);
                
                // さらに厳格なフィルタリング：急激な変化を抑制
                if (audioLevel > 100) {
                    audioLevel = 100 + (audioLevel - 100) * 0.3; // 高レベル時の感度を下げる
                }
            }
            const intensityMultiplier = parseFloat(musicIntensity);
            
            // デバッグ用：音声レベルをコンソールに出力（1秒に1回）
            if (Math.random() < 0.016) { // 約60fpsで1秒に1回
                console.log('音声レベル:', audioLevel, '平均値:', average.toFixed(2), 'ピーク値:', maxValue);
            }
            
            // 基本設定を取得
            const pattern = document.getElementById('penlight_pattern').value;
            const baseBpm = document.getElementById('penlight_bpm').value;
            const baseBrightness = document.getElementById('penlight_brightness').value;
            
            // 音声レベルに応じて基本設定を動的に調整
            const audioIntensity = audioLevel / 255; // 0-1の範囲
            
            // 音の強弱に応じてアニメーションを調整（ライブ会場対応で閾値を大幅に上げて環境音をカット）
            const minThreshold = 0.15; // 最小閾値（15%）- ライブ会場の環境音をカット
            
            if (audioIntensity > minThreshold) {
                // 音がなっている時：音の強弱に応じて基本設定を動的に調整
                const intensityFactor = Math.min(1.0, (audioIntensity - minThreshold) / (1.0 - minThreshold));
                const adjustedBpm = Math.max(60, Math.min(200, baseBpm * (1 + intensityFactor * intensityMultiplier)));
                const adjustedBrightness = Math.max(10, Math.min(100, baseBrightness * (1 + intensityFactor * intensityMultiplier)));
                
                // デバッグ用：調整された値をコンソールに出力
                if (Math.random() < 0.016) {
                    console.log('音声強弱検出 - 調整された値:', {
                        BPM: adjustedBpm.toFixed(1),
                        Brightness: adjustedBrightness.toFixed(1),
                        AudioIntensity: audioIntensity.toFixed(3),
                        IntensityFactor: intensityFactor.toFixed(3)
                    });
                }
                
                // アニメーションパターンに応じた処理（音楽連動モード）
                lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
                
                switch (pattern) {
                    case 'blink':
                        lightEffect.style.animation = `blink ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    case 'fade':
                        lightEffect.style.animation = `fade ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    case 'wave':
                        lightEffect.style.animation = `wave ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    case 'pulse':
                        lightEffect.style.animation = `pulse ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    case 'rainbow':
                        lightEffect.style.animation = `rainbow ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    case 'strobe':
                        lightEffect.style.animation = `strobe ${60 / adjustedBpm}s infinite`;
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                    default:
                        // 単色の場合は音声レベルに応じて明度を調整
                        lightEffect.style.animation = 'none';
                        lightEffect.style.opacity = adjustedBrightness / 100;
                        break;
                }
            } else {
                // 音が非常に小さい時：静止状態
                lightEffect.style.animation = 'none';
                lightEffect.style.opacity = '0';
                lightEffect.style.background = 'transparent';
                
                // デバッグ用：静止状態をコンソールに出力
                if (Math.random() < 0.016) {
                    console.log('音声なし - 静止状態:', {
                        AudioIntensity: audioIntensity.toFixed(3),
                        MinThreshold: minThreshold
                    });
                }
            }
            
            // デバッグ用：適用されたスタイルをコンソールに出力
            if (Math.random() < 0.016) {
                console.log('適用されたスタイル:', {
                    opacity: lightEffect.style.opacity,
                    animation: lightEffect.style.animation,
                    background: lightEffect.style.background
                });
            }
            
            // アニメーションを継続
            animationId = requestAnimationFrame(updatePenlightFromAudio);
        }
        
        updatePenlightFromAudio();
        
    })
    .catch(error => {
        console.error('マイクアクセスエラー:', error);
        
        // エラーの種類に応じた詳細なメッセージ
        let errorMessage = 'マイクアクセスエラーが発生しました。\n\n';
        
        if (error.name === 'NotAllowedError') {
            errorMessage += 'マイクへのアクセスが拒否されました。\n';
            errorMessage += 'ブラウザの設定でマイクの使用を許可してください。\n\n';
            errorMessage += '解決方法：\n';
            errorMessage += '1. ブラウザのアドレスバーの左側にあるカメラアイコンをクリック\n';
            errorMessage += '2. 「マイク」を「許可」に設定\n';
            errorMessage += '3. ページを再読み込みして再試行\n\n';
        } else if (error.name === 'NotFoundError') {
            errorMessage += 'マイクデバイスが見つかりません。\n';
            errorMessage += 'マイクが接続されているか確認してください。\n\n';
        } else if (error.name === 'NotReadableError') {
            errorMessage += 'マイクが他のアプリケーションで使用中です。\n';
            errorMessage += '他のアプリケーションを閉じてから再試行してください。\n\n';
        } else if (error.name === 'OverconstrainedError') {
            errorMessage += 'マイクの設定に問題があります。\n';
            errorMessage += 'ブラウザの設定を確認してください。\n\n';
        } else {
            errorMessage += '予期しないエラーが発生しました。\n';
            errorMessage += 'エラー詳細: ' + error.message + '\n\n';
        }
        
        errorMessage += 'テスト用の音声シミュレーションを開始します。';
        
        alert(errorMessage);
        
        // 音声反応ができない場合はテスト用の音声シミュレーションを開始
        startAudioSimulation(lightEffect, musicIntensity, songColor, penlightColor);
    });
}

// 音声反応機能を停止
function stopAudioReaction() {
    console.log('音声反応機能を停止します');
    
    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }
    
    if (microphone && microphone.mediaStream) {
        microphone.mediaStream.getTracks().forEach(track => track.stop());
        microphone = null;
    }
    
    if (analyser) {
        analyser = null;
    }
    
    // プレビューエフェクトを完全に停止
    const lightEffect = document.getElementById('previewLightEffect');
    if (lightEffect) {
        lightEffect.style.animation = 'none';
        lightEffect.style.opacity = '0';
        lightEffect.style.background = 'transparent';
        lightEffect.style.animationPlayState = 'paused';
    }
    
    // AudioContextは保持（再開時に使用）
}

// テスト用の音声シミュレーション機能
function startAudioSimulation(lightEffect, musicIntensity, songColor, penlightColor) {
    console.log('テスト用の音声シミュレーションを開始します');
    
    let time = 0;
    const intensityMultiplier = parseFloat(musicIntensity);
    
    function simulateAudio() {
        // 時間に基づいて音声レベルをシミュレート（0-255の範囲）
        const simulatedAudioLevel = Math.sin(time * 0.1) * 100 + 150; // 50-250の範囲で変化
        const audioLevel = Math.max(0, Math.min(255, simulatedAudioLevel));
        
        // デバッグ用：シミュレートされた音声レベルをコンソールに出力
        if (Math.random() < 0.016) {
            console.log('シミュレートされた音声レベル:', audioLevel.toFixed(2));
        }
        
        // 基本設定を取得
        const pattern = document.getElementById('penlight_pattern').value;
        const baseBpm = document.getElementById('penlight_bpm').value;
        const baseBrightness = document.getElementById('penlight_brightness').value;
        
        // 音声レベルに応じて基本設定を動的に調整
        const audioIntensity = audioLevel / 255; // 0-1の範囲
        
        // 音の強弱に応じてアニメーションを調整
        const minThreshold = 0.05; // 最小閾値（5%）
        
        if (audioIntensity > minThreshold) {
            // 音がなっている時：音の強弱に応じて基本設定を動的に調整
            const intensityFactor = Math.min(1.0, (audioIntensity - minThreshold) / (1.0 - minThreshold));
            const adjustedBpm = Math.max(60, Math.min(200, baseBpm * (1 + intensityFactor * intensityMultiplier)));
            const adjustedBrightness = Math.max(10, Math.min(100, baseBrightness * (1 + intensityFactor * intensityMultiplier)));
            
            // デバッグ用：調整された値をコンソールに出力
            if (Math.random() < 0.016) {
                console.log('シミュレート音声強弱検出 - 調整された値:', {
                    BPM: adjustedBpm.toFixed(1),
                    Brightness: adjustedBrightness.toFixed(1),
                    AudioIntensity: audioIntensity.toFixed(3),
                    IntensityFactor: intensityFactor.toFixed(3)
                });
            }
            
            // アニメーションパターンに応じた処理（音楽連動モード）
            lightEffect.style.background = createCombinedGradient(songColor, penlightColor);
            
            switch (pattern) {
                case 'blink':
                    lightEffect.style.animation = `blink ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                case 'fade':
                    lightEffect.style.animation = `fade ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                case 'wave':
                    lightEffect.style.animation = `wave ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                case 'pulse':
                    lightEffect.style.animation = `pulse ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                case 'rainbow':
                    lightEffect.style.animation = `rainbow ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                case 'strobe':
                    lightEffect.style.animation = `strobe ${60 / adjustedBpm}s infinite`;
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
                default:
                    // 単色の場合は音声レベルに応じて明度を調整
                    lightEffect.style.animation = 'none';
                    lightEffect.style.opacity = adjustedBrightness / 100;
                    break;
            }
        } else {
            // 音が非常に小さい時：静止状態
            lightEffect.style.animation = 'none';
            lightEffect.style.opacity = '0';
            lightEffect.style.background = 'transparent';
            
            // デバッグ用：静止状態をコンソールに出力
            if (Math.random() < 0.016) {
                console.log('シミュレート音声なし - 静止状態:', {
                    AudioIntensity: audioIntensity.toFixed(3),
                    MinThreshold: minThreshold
                });
            }
        }
        
        // デバッグ用：適用されたスタイルをコンソールに出力
        if (Math.random() < 0.016) {
            console.log('シミュレートされたスタイル:', {
                opacity: lightEffect.style.opacity,
                animation: lightEffect.style.animation,
                background: lightEffect.style.background
            });
        }
        
        time += 0.1;
        animationId = requestAnimationFrame(simulateAudio);
    }
    
    simulateAudio();
}

// 楽曲とペンライト設定を同時に更新
async function updateSongAndPenlight() {
    // 隠しフィールドを更新
    updateHiddenFields();
    
    // 楽曲カラーとペンライト色は独立して保持（入れ替えない）
    // document.getElementById('song_color').value = document.getElementById('penlight_color').value;
    
    // フォームを送信
    document.getElementById('song-form').submit();
}

// ペンライト設定をリセット
function resetPenlightSettings() {
    if (confirm('ペンライト設定を元の値にリセットしますか？')) {
        // ペンライト設定を隠しフィールドの値にリセット（楽曲カラーは変更しない）
        document.getElementById('penlight_color').value = document.querySelector('input[name="penlight_color"]').value;
        document.getElementById('penlight_pattern').value = document.querySelector('input[name="song_pattern"]').value;
        document.getElementById('penlight_brightness').value = document.querySelector('input[name="song_brightness"]').value;
        document.getElementById('penlight_bpm').value = document.querySelector('input[name="song_bpm"]').value;
        document.getElementById('penlight_intensity').value = document.querySelector('input[name="song_intensity"]').value;
        document.getElementById('penlight_color_scheme').value = document.querySelector('input[name="song_color_scheme"]').value;
        document.getElementById('penlight_music_bpm').value = document.querySelector('input[name="song_bpm"]').value;
        
        // プレビューを更新
        updatePreview();
        
        alert('ペンライト設定がリセットされました');
    }
}

// ペンライト制御に適用
async function applyToPenlightControl() {
    const settings = {
        room_id: '{{ $artist->artist_name }}',
        color: document.getElementById('penlight_color').value,
        pattern: document.getElementById('penlight_pattern').value,
        brightness: parseInt(document.getElementById('penlight_brightness').value),
        bpm: parseInt(document.getElementById('penlight_bpm').value),
        intensity: document.getElementById('penlight_intensity').value,
        color_scheme: document.getElementById('penlight_color_scheme').value,
        is_active: document.getElementById('penlight_is_active').checked,
        sync_mode: document.getElementById('penlight_sync_mode').checked,
        audio_sync: document.getElementById('penlight_audio_sync').checked,
        music_bpm: parseInt(document.getElementById('penlight_music_bpm').value),
        music_intensity: parseFloat(document.getElementById('penlight_music_intensity').value),
        music_color_scheme: document.getElementById('penlight_music_color_scheme').value
    };
    
    try {
        const response = await fetch('/api/admin/penlight/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(settings)
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                alert('ペンライト制御に適用されました');
            } else {
                alert('適用に失敗しました: ' + result.message);
            }
        }
    } catch (error) {
        console.error('ペンライト制御適用エラー:', error);
        alert('適用に失敗しました');
    }
}
</script>
@endpush
@endsection
