@extends('admin.layout')

@section('title', 'ペンライト制御')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ペンライト制御</h3>
                </div>
                <div class="card-body">
                    <!-- 楽曲選択 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="song-select" class="form-label">楽曲選択</label>
                            <select class="form-control" id="song-select">
                                <option value="">楽曲を選択してください</option>
                            </select>
                        </div>
                    </div>

                    <!-- プリセット一覧 -->
                    <div class="row mb-3" id="presets-section" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">ペンライトプリセット一覧</h4>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" id="create-presets-btn">
                                            <i class="fas fa-plus"></i> プリセット作成
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="presets-list">
                                        <!-- プリセット一覧がここに表示されます -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- プリセット編集モーダル -->
<div class="modal fade" id="editPresetModal" tabindex="-1" role="dialog" aria-labelledby="editPresetModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPresetModalLabel">プリセット編集</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPresetForm">
                    <input type="hidden" id="edit-preset-id" name="preset_id">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit-preset-name">プリセット名</label>
                                <input type="text" class="form-control" id="edit-preset-name" name="name" required>
                            </div>
                        </div>
                    </div>

                    <h5>基本設定</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit-preset-color">楽曲カラー（編集不可）</label>
                                <input type="color" class="form-control" id="edit-preset-color" name="color" value="#FF0000" readonly>
                                <small class="form-text text-muted">楽曲カラーは楽曲編集でのみ変更可能</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit-preset-penlight-color">ペンライト調整色</label>
                                <input type="color" class="form-control" id="edit-preset-penlight-color" name="penlight_color" value="#FF0000">
                                <small class="form-text text-muted">ペンライト調整色を設定</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit-preset-pattern">アニメーションパターン</label>
                                <select class="form-control" id="edit-preset-pattern" name="pattern">
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
                                <label for="edit-preset-intensity">エフェクト強度</label>
                                <select class="form-control" id="edit-preset-intensity" name="intensity">
                                    <option value="low">低（控えめ）</option>
                                    <option value="medium" selected>中（標準）</option>
                                    <option value="high">高（派手）</option>
                                </select>
                                <small class="form-text text-muted">アニメーションの派手さを調整</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit-preset-brightness">明度</label>
                                <input type="range" class="form-control-range" id="edit-preset-brightness" name="brightness" min="1" max="100" value="80">
                                <div class="d-flex justify-content-between">
                                    <small>暗い</small>
                                    <small id="brightness-value">80</small>
                                    <small>明るい</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit-preset-bpm">アニメーション速度</label>
                                <input type="range" class="form-control-range" id="edit-preset-bpm" name="bpm" min="60" max="200" value="120">
                                <div class="d-flex justify-content-between">
                                    <small>60</small>
                                    <small id="bpm-value">120 BPM</small>
                                    <small>200</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <br>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="edit-preset-audio-sync" name="audio_sync">
                                    <label class="custom-control-label" for="edit-preset-audio-sync">音楽連動モード</label>
                                </div>
                                <small class="form-text text-muted">音楽のリズムに合わせて動作</small>
                            </div>
                        </div>
                    </div>

                    <!-- 音楽連動設定（音楽連動モードが有効な場合のみ表示） -->
                    <div id="edit-music-sync-settings" style="display: none;">
                        <h5 class="mt-4">音楽連動設定</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit-preset-music-intensity">音楽感度</label>
                                    <input type="range" class="form-control-range" id="edit-preset-music-intensity" name="music_intensity" min="0" max="1" step="0.1" value="0.8">
                                    <div class="d-flex justify-content-between">
                                        <small>低</small>
                                        <small id="edit-music-intensity-value">0.8</small>
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
                            <div class="penlight-preview" id="editPenlightPreview">
                                <div class="light-effect" id="editPreviewLightEffect"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ボタンエリア -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-info" id="edit-preview-btn" onclick="toggleEditPreview()">
                                    <i class="fas fa-play"></i> プレビュー開始
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="savePreset()">保存</button>
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



.mini-light-effect {
    transition: all 0.1s ease;
    animation-play-state: running !important;
    will-change: transform, filter, opacity;
}

.mini-preview {
    position: relative;
    overflow: hidden;
    animation-play-state: running !important;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('ペンライト制御ページが読み込まれました');
    
    const songSelect = document.getElementById('song-select');
    const presetsSection = document.getElementById('presets-section');
    const presetsList = document.getElementById('presets-list');
    const createPresetsBtn = document.getElementById('create-presets-btn');
    
    // T-BOLANの楽曲を自動的に読み込む
    loadTbolanSongs();
    
    // 楽曲選択時の処理
    songSelect.addEventListener('change', function() {
        const songId = this.value;
        if (songId) {
            console.log('選択された楽曲ID:', songId);
            presetsSection.style.display = 'block';
            // 楽曲選択時に全てのプリセットを非アクティブ化
            deactivateAllPresets().then(() => {
                loadPresets(songId);
            });
        } else {
            presetsSection.style.display = 'none';
        }
    });
    
    // T-BOLANの楽曲を読み込む関数
    function loadTbolanSongs() {
        console.log('T-BOLANの楽曲を読み込み中...');
        
        fetch('/admin/penlight/songs?artist_id=2', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('T-BOLAN楽曲APIレスポンス:', data);
            if (data.success && data.data.length > 0) {
                songSelect.disabled = false;
                data.data.forEach(song => {
                    const option = document.createElement('option');
                    option.value = song.id;
                    option.textContent = song.song_name;
                    songSelect.appendChild(option);
                });
                console.log('T-BOLAN楽曲数:', data.data.length);
                
                // 最初の楽曲を自動選択してプリセットを読み込む
                const firstSong = data.data[0];
                songSelect.value = firstSong.id;
                console.log('最初の楽曲を自動選択:', firstSong.song_name, 'ID:', firstSong.id);
                
                // プリセット一覧を表示して読み込む
                presetsSection.style.display = 'block';
                // 楽曲読み込み時に全てのプリセットを非アクティブ化
                deactivateAllPresets().then(() => {
                    loadPresets(firstSong.id);
                });
            } else {
                songSelect.innerHTML = '<option value="">T-BOLANの楽曲が見つかりません</option>';
                console.log('T-BOLANの楽曲が見つかりませんでした');
            }
        })
        .catch(error => {
            console.error('T-BOLAN楽曲取得エラー:', error);
            songSelect.innerHTML = '<option value="">楽曲の取得に失敗しました</option>';
        });
    }
    
    // 全てのプリセットを非アクティブ化
    function deactivateAllPresets() {
        console.log('全てのプリセットを非アクティブ化します');
        
        return fetch('/admin/penlight/presets/deactivate-all', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.log('プリセット非アクティブ化APIレスポンスステータス:', response.status);
                return response.text().then(text => {
                    console.log('エラーレスポンス本文:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('プリセット非アクティブ化APIレスポンス:', data);
            if (data.success) {
                console.log('全てのプリセットを非アクティブ化しました');
            } else {
                console.log('プリセット非アクティブ化に失敗しました:', data.message);
            }
        })
        .catch(error => {
            console.error('プリセット非アクティブ化エラー:', error);
            // エラーが発生しても処理を続行
        });
    }
    
    // プリセット一覧を読み込む
    function loadPresets(songId) {
        console.log('プリセット読み込み開始:', songId);
        
        fetch(`/admin/penlight/presets?song_id=${songId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('プリセットAPIレスポンスステータス:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('エラーレスポンス本文:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('プリセットAPIレスポンス:', data);
            displayPresets(data.data || []);
        })
        .catch(error => {
            console.error('プリセット取得エラー:', error);
            displayPresets([]);
        });
    }
    
    // プリセット一覧を表示
    function displayPresets(presets) {
        if (presets.length === 0) {
            presetsList.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    この楽曲にはプリセットがありません。プリセットを作成してください。
                </div>
            `;
            createPresetsBtn.style.display = 'block';
        } else {
            let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
            html += '<thead><tr>';
            html += '<th>No.</th>';
            html += '<th>プレビュー</th>';
            html += '<th>プリセット名</th>';
            html += '<th>パターン</th>';
            html += '<th>音楽連動</th>';
            html += '<th>操作</th>';
            html += '</tr></thead><tbody>';
            
            presets.forEach((preset, index) => {
                // 楽曲カラーとペンライト調整色を組み合わせたグラデーションを作成
                const songColor = preset.song && preset.song.song_color ? preset.song.song_color : (preset.color || '#FF0000');
                const penlightColor = preset.penlight_color || (preset.song && preset.song.penlight_color ? preset.song.penlight_color : '#FF0000');
                
                // 透明色または無効な色の場合はデフォルト色を使用
                const finalSongColor = (!songColor || songColor === 'rgba(0,0,0,0)' || songColor === '#000000') ? '#ff0000' : songColor;
                const finalPenlightColor = (!penlightColor || penlightColor === 'rgba(0,0,0,0)' || penlightColor === '#000000') ? '#ff0000' : penlightColor;
                const combinedColor = `linear-gradient(45deg, ${finalSongColor} 0%, ${finalPenlightColor} 50%, ${finalSongColor} 100%)`;
                
                // アニメーション設定
                const pattern = preset.pattern || 'solid';
                const bpm = preset.bpm || 120;
                const audioSync = preset.audio_sync || false;
                
                // 音楽連動モードの場合は、音声入力がない限りアニメーションを停止
                let animation = 'none';
                if (pattern && pattern !== 'solid') {
                    if (audioSync) {
                        // 音楽連動モードの場合は、音声レベルに応じてアニメーションを制御
                        animation = `${pattern} ${60 / bpm}s infinite paused`;
                    } else {
                        // 手動モードの場合は通常通りアニメーション
                        animation = `${pattern} ${60 / bpm}s infinite`;
                    }
                }
                
                html += `<tr data-preset-id="${preset.id}">`;
                html += `<td>${index + 1}</td>`;
                // 音楽連動モードの場合は透明にする
                const previewOpacity = audioSync ? 0.3 : (preset.brightness || 80) / 100;
                
                html += `<td>
                    <div class="mini-preview" style="width: 60px; height: 30px; border: 1px solid #ccc; border-radius: 4px; background: ${combinedColor}; opacity: ${previewOpacity};">
                        <div class="mini-light-effect" style="width: 100%; height: 100%; border-radius: 4px; background: ${combinedColor};" data-animation="${animation}" data-audio-sync="${audioSync}"></div>
                    </div>
                </td>`;
                html += `<td>${preset.name || 'プリセット' + (index + 1)}</td>`;
                html += `<td>${preset.pattern || 'solid'}</td>`;
                html += `<td><span class="badge badge-${preset.audio_sync ? 'success' : 'secondary'}">${preset.audio_sync ? '音楽連動' : '手動'}</span></td>`;
                html += '<td>';
                html += `<button class="btn btn-sm btn-primary edit-preset" data-preset-id="${preset.id}"><i class="fas fa-edit"></i> 編集</button> `;
                
                // プリセットの状態に応じてボタンを切り替え
                const isActive = preset.is_active === true;
                const buttonClass = isActive ? 'btn-warning' : 'btn-success';
                const buttonIcon = isActive ? 'fa-stop' : 'fa-play';
                const buttonText = isActive ? '配信停止' : '配信開始';
                const buttonAction = isActive ? 'deactivate-preset' : 'activate-preset';
                
                html += `<button class="btn btn-sm ${buttonClass} ${buttonAction}" data-preset-id="${preset.id}"><i class="fas ${buttonIcon}"></i> ${buttonText}</button>`;
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            presetsList.innerHTML = html;
            
            // ミニプレビューのアニメーションを強制的に開始
            setTimeout(() => {
                const miniEffects = document.querySelectorAll('.mini-light-effect');
                miniEffects.forEach(effect => {
                    const animation = effect.getAttribute('data-animation');
                    const audioSync = effect.getAttribute('data-audio-sync') === 'true';
                    
                    if (animation && animation !== 'none') {
                        // アニメーションを適用
                        effect.style.animation = animation;
                        
                        if (audioSync) {
                            // 音楽連動モードの場合は一時停止状態で開始し、透明にする
                            effect.style.animationPlayState = 'paused';
                            effect.style.opacity = '0.3';
                            console.log('ミニプレビューアニメーション適用（音楽連動モード）:', animation);
                        } else {
                            // 手動モードの場合は通常通り開始
                            effect.style.animationPlayState = 'running';
                            effect.style.opacity = '1';
                            console.log('ミニプレビューアニメーション適用（手動モード）:', animation);
                        }
                    }
                });
            }, 200);
            
            // 10個未満の場合は作成ボタンを表示
            if (presets.length < 10) {
                createPresetsBtn.style.display = 'block';
            } else {
                createPresetsBtn.style.display = 'none';
            }
        }
    }
    
    // プリセット作成ボタンのイベント
    createPresetsBtn.addEventListener('click', function() {
        const songId = songSelect.value;
        if (songId) {
            createPresets(songId);
        }
    });
    
    // プリセット作成
    function createPresets(songId) {
        console.log('プリセット作成開始:', songId);
        
        fetch('/admin/penlight/create-presets', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ song_id: songId })
        })
        .then(response => {
            console.log('プリセット作成APIレスポンスステータス:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('エラーレスポンス本文:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('プリセット作成APIレスポンス:', data);
            if (data.success) {
                alert('プリセットが作成されました');
                loadPresets(songId);
            } else {
                alert('プリセット作成に失敗しました: ' + (data.message || '不明なエラー'));
            }
        })
        .catch(error => {
            console.error('プリセット作成エラー:', error);
            alert('プリセット作成に失敗しました: ' + error.message);
        });
    }
    
    // プリセット編集ボタンのイベント（動的に追加される要素のため、イベント委譲を使用）
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-preset') || e.target.closest('.edit-preset')) {
            const presetId = e.target.dataset.presetId || e.target.closest('.edit-preset').dataset.presetId;
            editPreset(presetId);
        }
        
        if (e.target.classList.contains('activate-preset') || e.target.closest('.activate-preset')) {
            const presetId = e.target.dataset.presetId || e.target.closest('.activate-preset').dataset.presetId;
            activatePreset(presetId);
        }
        
        if (e.target.classList.contains('deactivate-preset') || e.target.closest('.deactivate-preset')) {
            const presetId = e.target.dataset.presetId || e.target.closest('.deactivate-preset').dataset.presetId;
            deactivatePreset(presetId);
        }
    });
    
    // プリセット編集
    function editPreset(presetId) {
        console.log('プリセット編集開始:', presetId);
        
        fetch(`/admin/penlight/presets/${presetId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('プリセット取得レスポンスステータス:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('エラーレスポンス本文:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('プリセット取得APIレスポンス:', data);
            if (data.success) {
                const preset = data.data;
                populateEditForm(preset);
                $('#editPresetModal').modal('show');
            } else {
                alert('プリセットの取得に失敗しました');
            }
        })
        .catch(error => {
            console.error('プリセット取得エラー:', error);
            alert('プリセットの取得に失敗しました: ' + error.message);
        });
    }
    
    // 編集フォームにデータを設定
    function populateEditForm(preset) {
        document.getElementById('edit-preset-id').value = preset.id;
        document.getElementById('edit-preset-name').value = preset.name || '';
        
        // 楽曲の色を設定（編集不可）
        const songColor = preset.song && preset.song.song_color ? preset.song.song_color : (preset.color || '#FF0000');
        document.getElementById('edit-preset-color').value = songColor;
        
        // ペンライト調整色を設定（編集可能）
        const penlightColor = preset.penlight_color || (preset.song && preset.song.penlight_color ? preset.song.penlight_color : '#FF0000');
        document.getElementById('edit-preset-penlight-color').value = penlightColor;
        
        document.getElementById('edit-preset-pattern').value = preset.pattern || 'solid';
        document.getElementById('edit-preset-brightness').value = preset.brightness || 80;
        document.getElementById('brightness-value').textContent = preset.brightness || 80;
        document.getElementById('edit-preset-bpm').value = preset.bpm || 120;
        document.getElementById('bpm-value').textContent = (preset.bpm || 120) + ' BPM';
        document.getElementById('edit-preset-intensity').value = preset.intensity || 'medium';
        console.log('プリセットのaudio_sync値:', preset.audio_sync);
        
        document.getElementById('edit-preset-audio-sync').checked = preset.audio_sync || false;
        
        console.log('設定後のaudio_sync要素のchecked状態:', document.getElementById('edit-preset-audio-sync').checked);
        document.getElementById('edit-preset-music-intensity').value = preset.music_intensity || 0.8;
        document.getElementById('edit-music-intensity-value').textContent = preset.music_intensity || 0.8;
        
        // 音楽連動設定の表示/非表示を制御
        toggleEditMusicSyncSettings();
        
        // プレビューを更新
        updateEditPreview();
    }
    
    // プリセットアクティブ化
    function activatePreset(presetId) {
        console.log('プリセットアクティブ化:', presetId);
        
        fetch(`/admin/penlight/presets/${presetId}/activate`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // プリセット一覧を再読み込み
                const songId = songSelect.value;
                loadPresets(songId);
            } else {
                alert('プリセットの配信開始に失敗しました: ' + (data.message || '不明なエラー'));
            }
        })
        .catch(error => {
            console.error('プリセットアクティブ化エラー:', error);
            alert('プリセットの配信開始に失敗しました: ' + error.message);
        });
    }
    
    // プリセットデアクティブ化
    function deactivatePreset(presetId) {
        console.log('プリセットデアクティブ化:', presetId);
        
        fetch(`/admin/penlight/presets/${presetId}/deactivate`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // プリセット一覧を再読み込み
                const songId = songSelect.value;
                loadPresets(songId);
            } else {
                alert('プリセットの配信停止に失敗しました: ' + (data.message || '不明なエラー'));
            }
        })
        .catch(error => {
            console.error('プリセットデアクティブ化エラー:', error);
            alert('プリセットの配信停止に失敗しました: ' + error.message);
        });
    }
    
    // スライダーの値を更新
    document.getElementById('edit-preset-brightness').addEventListener('input', function() {
        document.getElementById('brightness-value').textContent = this.value;
        updateEditPreview();
    });
    
    document.getElementById('edit-preset-bpm').addEventListener('input', function() {
        document.getElementById('bpm-value').textContent = this.value + ' BPM';
        updateEditPreview();
    });
    
    // 音楽感度スライダーの値を更新
    document.getElementById('edit-preset-music-intensity').addEventListener('input', function() {
        document.getElementById('edit-music-intensity-value').textContent = this.value;
        updateEditPreview(); // プレビューを更新
    });
    
    // 色の変更時にプレビューを更新
    document.getElementById('edit-preset-penlight-color').addEventListener('change', function() {
        updateEditPreview();
    });
    
    // パターン変更時にプレビューを更新
    document.getElementById('edit-preset-pattern').addEventListener('change', function() {
        updateEditPreview();
    });
    
    // 音楽連動チェックボックスのイベント
    document.getElementById('edit-preset-audio-sync').addEventListener('change', function() {
        toggleEditMusicSyncSettings();
        updateEditPreview(); // プレビューを更新
    });
    
    // 音楽連動設定の表示/非表示を制御
    function toggleEditMusicSyncSettings() {
        const audioSyncCheckbox = document.getElementById('edit-preset-audio-sync');
        const musicSyncSettings = document.getElementById('edit-music-sync-settings');
        
        if (audioSyncCheckbox && musicSyncSettings) {
            if (audioSyncCheckbox.checked) {
                musicSyncSettings.style.display = 'block';
            } else {
                musicSyncSettings.style.display = 'none';
            }
        }
    }
    
    // プレビュー更新関数
    function updateEditPreview() {
        const songColor = document.getElementById('edit-preset-color').value;
        const penlightColor = document.getElementById('edit-preset-penlight-color').value;
        const pattern = document.getElementById('edit-preset-pattern').value;
        const brightness = document.getElementById('edit-preset-brightness').value;
        const bpm = document.getElementById('edit-preset-bpm').value;
        const audioSync = document.getElementById('edit-preset-audio-sync').checked;
        const musicIntensity = parseFloat(document.getElementById('edit-preset-music-intensity').value || '0.8');
        
        console.log('プレビュー更新:', { songColor, penlightColor, pattern, brightness, bpm, audioSync, musicIntensity });
        
        const lightEffect = document.getElementById('editPreviewLightEffect');
        if (lightEffect) {
            // 楽曲カラーとペンライト調整色を組み合わせたグラデーションを作成
            const finalSongColor = (!songColor || songColor === 'rgba(0,0,0,0)' || songColor === '#000000') ? '#ff0000' : songColor;
            const finalPenlightColor = (!penlightColor || penlightColor === 'rgba(0,0,0,0)' || penlightColor === '#000000') ? '#ff0000' : penlightColor;
            const combinedColor = `linear-gradient(45deg, ${finalSongColor} 0%, ${finalPenlightColor} 50%, ${finalSongColor} 100%)`;
            
            lightEffect.style.background = combinedColor;
            lightEffect.style.opacity = brightness / 100;
            
            // パターンに応じてアニメーションを設定
            if (pattern && pattern !== 'solid') {
                if (audioSync) {
                    // 音楽連動モードの場合は一時停止状態で開始し、音声反応を開始
                    lightEffect.style.animation = `${pattern} ${60 / bpm}s infinite paused`;
                    console.log('音楽連動モード: 音声反応を開始');
                    startAudioReaction(lightEffect, musicIntensity, finalSongColor, finalPenlightColor);
                } else {
                    // 手動モードの場合は通常通りアニメーション
                    lightEffect.style.animation = `${pattern} ${60 / bpm}s infinite`;
                    console.log('手動モード: 音声反応を停止');
                    stopAudioReaction(); // 音声反応を停止
                }
            } else {
                lightEffect.style.animation = 'none';
                console.log('ソリッドパターン: 音声反応を停止');
                stopAudioReaction(); // 音声反応を停止
            }
        }
    }
    
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
    
    // 音声反応機能のグローバル変数
    let audioContext = null;
    let analyser = null;
    let microphone = null;
    let animationId = null;
    
    // 音声反応機能を開始
    function startAudioReaction(lightEffect, musicIntensity, songColor, penlightColor) {
        console.log('音声反応機能を開始します', { musicIntensity, songColor, penlightColor });
        
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
        
        // マイクからの音声取得
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
            
            // アナライザーの設定
            analyser.fftSize = 512;
            analyser.smoothingTimeConstant = 0.3;
            
            microphone.connect(analyser);
            
            // 音声データの解析とペンライト制御
            const bufferLength = analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            
            function updatePenlightFromAudio() {
                analyser.getByteFrequencyData(dataArray);
                
                // 音声レベルを計算（より詳細な分析）
                let sum = 0;
                let maxValue = 0;
                for (let i = 0; i < bufferLength; i++) {
                    sum += dataArray[i];
                    if (dataArray[i] > maxValue) {
                        maxValue = dataArray[i];
                    }
                }
                const average = sum / bufferLength;
                
                // 音楽感度に応じて閾値を調整（より低い閾値）
                const threshold = 10 + (1 - musicIntensity) * 30;
                
                console.log('音声レベル:', { average, maxValue, threshold, musicIntensity });
                
                if (average > threshold || maxValue > threshold * 2) {
                    // 音声レベルが閾値を超えた場合、アニメーションを開始
                    lightEffect.style.animationPlayState = 'running';
                    const brightnessMultiplier = 1 + (Math.min(average, 100) / 100) * 0.8;
                    lightEffect.style.filter = `brightness(${brightnessMultiplier})`;
                    lightEffect.style.opacity = '1';
                    console.log('音声反応: アニメーション開始, 明度:', brightnessMultiplier);
                } else {
                    // 音声レベルが低い場合、アニメーションを一時停止して透明にする
                    lightEffect.style.animationPlayState = 'paused';
                    lightEffect.style.filter = 'brightness(1)';
                    lightEffect.style.opacity = '0.3';
                    console.log('音声反応: アニメーション停止, 透明化');
                }
                
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
    
    // 音声シミュレーション（テスト用）
    function startAudioSimulation(lightEffect, musicIntensity, songColor, penlightColor) {
        console.log('音声シミュレーションを開始します');
        
        let beatCount = 0;
        const beatInterval = 500; // 500ms間隔でビート（より速い）
        
        function simulateAudioBeat() {
            beatCount++;
            
            // ビートに応じてアニメーションを制御
            if (beatCount % 2 === 0) {
                lightEffect.style.animationPlayState = 'running';
                lightEffect.style.filter = 'brightness(1.8)';
                lightEffect.style.opacity = '1';
                console.log('シミュレーション: アニメーション開始');
            } else {
                lightEffect.style.animationPlayState = 'paused';
                lightEffect.style.filter = 'brightness(1)';
                lightEffect.style.opacity = '0.3';
                console.log('シミュレーション: アニメーション停止, 透明化');
            }
            
            animationId = setTimeout(simulateAudioBeat, beatInterval);
        }
        
        simulateAudioBeat();
    }
    
    // 音声反応機能を停止
    function stopAudioReaction() {
        if (animationId) {
            if (typeof animationId === 'number') {
                cancelAnimationFrame(animationId);
            } else {
                clearTimeout(animationId);
            }
            animationId = null;
        }
        
        if (microphone) {
            microphone.disconnect();
            microphone = null;
        }
        
        if (analyser) {
            analyser.disconnect();
            analyser = null;
        }
    }
    
    // モーダルイベント
    $('#editPresetModal').on('shown.bs.modal', function() {
        // モーダルが表示された時の処理
        $(this).removeAttr('aria-hidden');
    });
    
    $('#editPresetModal').on('hidden.bs.modal', function() {
        // モーダルが非表示になった時の処理
        $(this).attr('aria-hidden', 'true');
        // 音声反応を停止
        stopAudioReaction();
    });
});

// プレビュー開始/停止（グローバルスコープで定義）
window.toggleEditPreview = function() {
    const previewBtn = document.getElementById('edit-preview-btn');
    const lightEffect = document.getElementById('editPreviewLightEffect');
    
    if (!previewBtn || !lightEffect) {
        console.error('プレビューボタンまたはライトエフェクト要素が見つかりません');
        return;
    }
    
    const isPlaying = previewBtn.getAttribute('data-playing') === 'true';
    
    if (isPlaying) {
        // プレビュー停止
        lightEffect.style.animationPlayState = 'paused';
        previewBtn.innerHTML = '<i class="fas fa-play"></i> プレビュー開始';
        previewBtn.setAttribute('data-playing', 'false');
        previewBtn.className = 'btn btn-info';
    } else {
        // プレビュー開始
        lightEffect.style.animationPlayState = 'running';
        previewBtn.innerHTML = '<i class="fas fa-stop"></i> プレビュー停止';
        previewBtn.setAttribute('data-playing', 'true');
        previewBtn.className = 'btn btn-warning';
    }
};

// プリセット保存（グローバルスコープで定義）
window.savePreset = function() {
    console.log('savePreset関数が呼び出されました');
    
    const presetId = document.getElementById('edit-preset-id').value;
    console.log('プリセットID:', presetId);
    
    const form = document.getElementById('editPresetForm');
    console.log('フォーム要素:', form);
    
    if (!form) {
        console.error('フォームが見つかりません');
        alert('フォームが見つかりません');
        return;
    }
    
    // フォームデータを直接要素から取得（存在しない要素はデフォルト値を使用）
    const audioSyncElement = document.getElementById('edit-preset-audio-sync');
    
    console.log('audio_sync要素のchecked状態:', audioSyncElement?.checked);
    
    const data = {
        name: document.getElementById('edit-preset-name')?.value || '',
        pattern: document.getElementById('edit-preset-pattern')?.value || 'solid',
        brightness: parseInt(document.getElementById('edit-preset-brightness')?.value || '50'),
        bpm: parseInt(document.getElementById('edit-preset-bpm')?.value || '120'),
        intensity: document.getElementById('edit-preset-intensity')?.value || 'medium',
        is_active: true, // 常に有効にする
        audio_sync: audioSyncElement?.checked || false,
        music_intensity: parseFloat(document.getElementById('edit-preset-music-intensity')?.value || '0.8'),
        penlight_color: document.getElementById('edit-preset-penlight-color')?.value || 'rgba(0,0,0,0)'
    };
    
    console.log('送信データ:', data);
    
    fetch(`/admin/penlight/presets/${presetId}`, {
        method: 'PUT',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('プリセット更新レスポンスステータス:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.log('エラーレスポンス本文:', text);
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('プリセット更新APIレスポンス:', data);
        if (data.success) {
            alert('プリセットを更新しました');
            $('#editPresetModal').modal('hide');
            
            // プリセット一覧を再読み込み（楽曲選択を維持）
            const songId = document.getElementById('song-select').value;
            if (songId) {
                // ページをリロードしてプリセット一覧を更新
                location.reload();
            }
        } else {
            alert('プリセットの更新に失敗しました: ' + (data.message || '不明なエラー'));
        }
    })
    .catch(error => {
        console.error('プリセット更新エラー:', error);
        alert('プリセットの更新に失敗しました: ' + error.message);
    });
};
</script>
@endpush
