@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($qrCode->qr_is_liveevent)
                            <i class="fas fa-music"></i> リアルイベントQRコード詳細
                        @else
                            <i class="fas fa-gift"></i> ゲームグッズ（仮）QRコード詳細
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>QRコード情報</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $qrCode->id }}</td>
                                </tr>
                                <tr>
                                    <th>QRコード</th>
                                    <td><code>{{ $qrCode->qr_code }}</code></td>
                                </tr>
                                <tr>
                                    <th>タイトル</th>
                                    <td>{{ $qrCode->qr_title ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>説明</th>
                                    <td>{{ $qrCode->qr_description ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>ポイント</th>
                                    <td>
                                        @if($qrCode->qr_points > 0)
                                            <span class="badge badge-success">{{ $qrCode->qr_points }}pt</span>
                                        @else
                                            <span class="text-muted">0pt</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($qrCode->qr_is_liveevent)
                                <tr>
                                    <th>アーティスト名</th>
                                    <td>{{ $qrCode->qr_artist_name ?? '未設定' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>有効期限</th>
                                    <td>
                                        @if($qrCode->qr_expires_at)
                                            {{ $qrCode->qr_expires_at->format('Y年m月d日 H:i') }}
                                        @else
                                            <span class="text-muted">無期限</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>状態</th>
                                    <td>
                                        @if($qrCode->qr_is_active)
                                            <span class="badge badge-success">アクティブ</span>
                                        @else
                                            <span class="badge badge-warning">非アクティブ</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(!$qrCode->qr_is_liveevent)
                                <tr>
                                    <th>複数回使用</th>
                                    <td>
                                        @if($qrCode->qr_is_multiple)
                                            <span class="badge badge-info">複数回</span>
                                        @else
                                            <span class="badge badge-secondary">1回のみ</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>作成日</th>
                                    <td>{{ $qrCode->qr_created_at->format('Y年m月d日 H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>QRコード画像</h5>
                            <div class="text-center">
                                <div class="qr-code-preview mb-3">
                                    <!-- QRコードのプレビュー画像を表示 -->
                                    @php
                                        $qrCodeService = new \App\Services\QrCodeService();
                                        $pngData = $qrCodeService->generatePng($qrCode->qr_code);
                                    @endphp
                                    <img src="data:image/png;base64,{{ base64_encode($pngData) }}" 
                                         alt="QR Code" 
                                         class="img-fluid" 
                                         style="max-width: 300px;">
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.qrcodes.download.png', $qrCode->id) }}" 
                                       class="btn btn-success">
                                        <i class="fas fa-download"></i> PNG形式でダウンロード
                                    </a>
                                    <a href="{{ route('admin.qrcodes.download.svg', $qrCode->id) }}" 
                                       class="btn btn-warning">
                                        <i class="fas fa-download"></i> SVG形式でダウンロード
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($qrCode->qr_contents)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>詳細内容</h5>
                            <div class="card">
                                <div class="card-body">
                                    <pre>{{ $qrCode->qr_contents }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($qrCode->qr_options_enabled)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="fas fa-cog"></i> オプション設定</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @if($qrCode->qr_avatar_expressions)
                                        <div class="col-md-6">
                                            <h6>アバター表情オプション</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($qrCode->qr_avatar_expressions as $expression)
                                                    <li class="list-group-item">{{ $expression }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if($qrCode->qr_avatar_actions)
                                        <div class="col-md-6">
                                            <h6>アバター行動オプション</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($qrCode->qr_avatar_actions as $action)
                                                    <li class="list-group-item">{{ $action }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="row mt-3">
                                        @if($qrCode->qr_background_colors)
                                        <div class="col-md-4">
                                            <h6>背景色オプション</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($qrCode->qr_background_colors as $color)
                                                    <li class="list-group-item">{{ $color }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if($qrCode->qr_effects)
                                        <div class="col-md-4">
                                            <h6>エフェクトオプション</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($qrCode->qr_effects as $effect)
                                                    <li class="list-group-item">{{ $effect }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if($qrCode->qr_sounds)
                                        <div class="col-md-4">
                                            <h6>サウンドオプション</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($qrCode->qr_sounds as $sound)
                                                    <li class="list-group-item">{{ $sound }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($qrCode->qr_is_liveevent)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="fas fa-users"></i> 参加者一覧</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-primary" onclick="loadParticipants()">
                                                    <i class="fas fa-sync"></i> 参加者一覧を更新
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="downloadParticipants('json')">
                                                    <i class="fas fa-download"></i> JSON形式でダウンロード
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="downloadParticipants('csv')">
                                                    <i class="fas fa-file-csv"></i> CSV形式でダウンロード
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="participantStats" class="text-right">
                                                <span class="badge badge-primary">参加者数: <span id="participantCount">-</span></span>
                                                <span class="badge badge-success">総ポイント: <span id="totalPoints">-</span></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="participantsTable" class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>参加者ID</th>
                                                    <th>ニックネーム</th>
                                                    <th>参加時刻</th>
                                                    <th>総参加数</th>
                                                </tr>
                                            </thead>
                                            <tbody id="participantsList">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        参加者一覧を読み込み中...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@if($qrCode->qr_is_liveevent)
<script>
function loadParticipants() {
    const qrCodeId = {{ $qrCode->id }};
    
    fetch(`/api/admin/qr/participants?qr_code_id=${qrCodeId}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayParticipants(data.data);
            } else {
                alert('参加者一覧の取得に失敗しました: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('参加者一覧の取得に失敗しました');
        });
}

function displayParticipants(data) {
    const tbody = document.getElementById('participantsList');
    const countSpan = document.getElementById('participantCount');
    const pointsSpan = document.getElementById('totalPoints');
    
    // 統計情報を更新
    countSpan.textContent = data.event_info.total_participants;
    pointsSpan.textContent = data.event_info.total_points;
    
    if (data.participants.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">参加者はいません</td></tr>';
        return;
    }
    
    let html = '';
    data.participants.forEach(participant => {
        const claimedAt = new Date(participant.claimed_at).toLocaleString('ja-JP');
        html += `
            <tr>
                <td>${participant.user_id}</td>
                <td>${participant.nickname || '未設定'}</td>
                <td>${claimedAt}</td>
                <td><span class="badge badge-info">${participant.artist_participation_count}回</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function downloadParticipants(format) {
    const qrCodeId = {{ $qrCode->id }};
    const url = `/api/admin/qr/participants?qr_code_id=${qrCodeId}&format=${format}`;
    
    if (format === 'csv') {
        // CSVダウンロード
        window.location.href = url;
    } else {
        // JSONダウンロード
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `live_event_participants_${qrCodeId}_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.json`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    alert('ダウンロードに失敗しました: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('ダウンロードに失敗しました');
            });
    }
}

// ページ読み込み時に参加者一覧を自動取得
document.addEventListener('DOMContentLoaded', function() {
    loadParticipants();
});
</script>
@endif

@endsection
