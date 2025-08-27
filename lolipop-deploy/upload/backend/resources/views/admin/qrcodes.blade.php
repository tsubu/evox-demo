@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">QRコード管理</h3>
                    <div>
                        <a href="{{ route('admin.qrcode-options.index') }}" class="btn btn-info">
                            <i class="fas fa-cog"></i> オプション管理
                        </a>
                        <a href="{{ route('admin.qrcodes.create', ['type' => 'event']) }}" class="btn btn-success">
                            <i class="fas fa-music"></i> リアルイベント作成
                        </a>
                        <a href="{{ route('admin.qrcodes.create', ['type' => 'goods']) }}" class="btn btn-primary">
                            <i class="fas fa-gift"></i> ゲームグッズ（仮）作成
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- タブナビゲーション -->
                    <ul class="nav nav-tabs" id="qrCodeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="event-tab" data-toggle="tab" href="#event-content" role="tab" aria-controls="event-content" aria-selected="true">
                                <i class="fas fa-music"></i> リアルイベント
                                <span class="badge badge-light ml-1">{{ $eventQrCodes->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="goods-tab" data-toggle="tab" href="#goods-content" role="tab" aria-controls="goods-content" aria-selected="false">
                                <i class="fas fa-gift"></i> ゲームグッズ（仮）
                                <span class="badge badge-light ml-1">{{ $goodsQrCodes->count() }}</span>
                            </a>
                        </li>
                    </ul>

                    <!-- タブコンテンツ -->
                    <div class="tab-content" id="qrCodeTabContent">
                        <!-- リアルイベントタブ -->
                        <div class="tab-pane fade show active" id="event-content" role="tabpanel" aria-labelledby="event-tab">
                            <div class="mt-3">
                                <!-- 統計情報 -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-music"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">リアルイベント総数</span>
                                                <span class="info-box-number">{{ $eventQrCodes->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">アクティブ</span>
                                                <span class="info-box-number">{{ $eventQrCodes->where('qr_is_active', true)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-pause"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">非アクティブ</span>
                                                <span class="info-box-number">{{ $eventQrCodes->where('qr_is_active', false)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">ライブイベント</span>
                                                <span class="info-box-number">{{ $eventQrCodes->where('qr_is_liveevent', true)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- リアルイベント一覧テーブル -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>QRコード</th>
                                                <th>イベント名</th>
                                                <th>アーティスト</th>
                                                <th>説明</th>
                                                <th>ポイント</th>
                                                <th>有効期限</th>
                                                <th>状態</th>
                                                <th>オプション</th>
                                                <th>作成日</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($eventQrCodes as $qrCode)
                                            <tr>
                                                <td>{{ $qrCode->id }}</td>
                                                <td>
                                                    <code>{{ $qrCode->qr_code }}</code>
                                                </td>
                                                <td>{{ $qrCode->qr_title ?? '未設定' }}</td>
                                                <td>{{ $qrCode->qr_artist_name ?? '未設定' }}</td>
                                                <td>{{ Str::limit($qrCode->qr_description ?? '未設定', 50) }}</td>
                                                <td>
                                                    @if($qrCode->qr_points > 0)
                                                        <span class="badge badge-success">{{ $qrCode->qr_points }}pt</span>
                                                    @else
                                                        <span class="text-muted">0pt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_expires_at)
                                                        {{ $qrCode->qr_expires_at->format('Y年m月d日 H:i') }}
                                                    @else
                                                        <span class="text-muted">無期限</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_is_active)
                                                        <span class="badge badge-success">アクティブ</span>
                                                    @else
                                                        <span class="badge badge-warning">非アクティブ</span>
                                                    @endif
                                                    @if($qrCode->qr_is_liveevent)
                                                        <span class="badge badge-primary ml-1">ライブ</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_options_enabled)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> 有効
                                                        </span>
                                                        @if($qrCode->qr_avatar_expressions)
                                                            <br><small class="text-muted">{{ count($qrCode->qr_avatar_expressions) }}種類</small>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-times"></i> 無効
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $qrCode->qr_created_at->format('Y年m月d日 H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.qrcodes.show', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> 詳細
                                                        </a>
                                                        <a href="{{ route('admin.qrcodes.edit', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> 編集
                                                        </a>
                                                        <a href="{{ route('admin.qrcodes.duplicate', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-warning" 
                                                           onclick="return confirm('このQRコードを複製しますか？')"
                                                           title="QRコードを複製">
                                                            <i class="fas fa-copy"></i> 複製
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="11" class="text-center">リアルイベントQRコードが登録されていません。</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ゲームグッズ（仮）タブ -->
                        <div class="tab-pane fade" id="goods-content" role="tabpanel" aria-labelledby="goods-tab">
                            <div class="mt-3">
                                <!-- 統計情報 -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-gift"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">ゲームグッズ（仮）総数</span>
                                                <span class="info-box-number">{{ $goodsQrCodes->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">アクティブ</span>
                                                <span class="info-box-number">{{ $goodsQrCodes->where('qr_is_active', true)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-pause"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">非アクティブ</span>
                                                <span class="info-box-number">{{ $goodsQrCodes->where('qr_is_active', false)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">総ポイント</span>
                                                <span class="info-box-number">{{ $goodsQrCodes->sum('qr_points') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ゲームグッズ一覧テーブル -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>QRコード</th>
                                                <th>アイテム名</th>
                                                <th>説明</th>
                                                <th>ポイント</th>
                                                <th>複数回使用</th>
                                                <th>有効期限</th>
                                                <th>状態</th>
                                                <th>オプション</th>
                                                <th>作成日</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($goodsQrCodes as $qrCode)
                                            <tr>
                                                <td>{{ $qrCode->id }}</td>
                                                <td>
                                                    <code>{{ $qrCode->qr_code }}</code>
                                                </td>
                                                <td>{{ $qrCode->qr_title ?? '未設定' }}</td>
                                                <td>{{ Str::limit($qrCode->qr_description ?? '未設定', 50) }}</td>
                                                <td>
                                                    @if($qrCode->qr_points > 0)
                                                        <span class="badge badge-success">{{ $qrCode->qr_points }}pt</span>
                                                    @else
                                                        <span class="text-muted">0pt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_is_multiple)
                                                        <span class="badge badge-info">複数回</span>
                                                    @else
                                                        <span class="badge badge-secondary">1回のみ</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_expires_at)
                                                        {{ $qrCode->qr_expires_at->format('Y年m月d日 H:i') }}
                                                    @else
                                                        <span class="text-muted">無期限</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_is_active)
                                                        <span class="badge badge-success">アクティブ</span>
                                                    @else
                                                        <span class="badge badge-warning">非アクティブ</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qrCode->qr_options_enabled)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> 有効
                                                        </span>
                                                        @if($qrCode->qr_avatar_expressions)
                                                            <br><small class="text-muted">{{ count($qrCode->qr_avatar_expressions) }}種類</small>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-times"></i> 無効
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $qrCode->qr_created_at->format('Y年m月d日 H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.qrcodes.show', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> 詳細
                                                        </a>
                                                        <a href="{{ route('admin.qrcodes.edit', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> 編集
                                                        </a>
                                                        <a href="{{ route('admin.qrcodes.duplicate', $qrCode->id) }}" 
                                                           class="btn btn-sm btn-warning" 
                                                           onclick="return confirm('このQRコードを複製しますか？')"
                                                           title="QRコードを複製">
                                                            <i class="fas fa-copy"></i> 複製
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center">ゲームグッズ（仮）QRコードが登録されていません。</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// タブ切り替え時の処理
document.addEventListener('DOMContentLoaded', function() {
    // jQueryが利用可能かチェック
    if (typeof $ !== 'undefined') {
        // URLパラメータでタブを指定
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        
        if (activeTab) {
            $('#' + activeTab + '-tab').tab('show');
        }
        
        // タブ切り替え時にURLを更新
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('id').replace('-tab', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', target);
            window.history.replaceState({}, '', url);
        });
    } else {
        console.warn('jQuery is not available');
    }
});
</script>
@endsection
