@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">統計情報</h3>
                </div>
                <div class="card-body">
                    <!-- 統計情報 -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総ユーザー数</span>
                                    <span class="info-box-number">{{ $totalUsers }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">プロフィール完了</span>
                                    <span class="info-box-number">{{ $activeUsers }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-qrcode"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総QRコード数</span>
                                    <span class="info-box-number">{{ $totalQrCodes }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-newspaper"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">ニュース記事数</span>
                                    <span class="info-box-number">{{ $totalNews }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">今日の新規登録</span>
                                    <span class="info-box-number">{{ $todayRegistrations }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-mobile-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">今日のQRスキャン</span>
                                    <span class="info-box-number">{{ $todayQrScans }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- チャート -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">登録推移（過去30日）</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="registrationChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">キャラクター選択分布</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="characterChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QRコード使用状況 -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">QRコード使用状況（上位10件）</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>QRコード</th>
                                                    <th>説明</th>
                                                    <th>使用回数</th>
                                                    <th>割合</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($qrCodeUsage as $qrCode)
                                                    <tr>
                                                        <td>{{ $qrCode->qr_code }}</td>
                                                        <td>{{ $qrCode->qr_description }}</td>
                                                        <td>{{ $qrCode->qr_use_list_count }}</td>
                                                        <td>
                                                            @if($totalQrCodes > 0)
                                                                {{ round(($qrCode->qr_use_list_count / $totalQrCodes) * 100, 1) }}%
                                                            @else
                                                                0%
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- キャラクター選択統計 -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">キャラクター選択統計</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>キャラクター</th>
                                                    <th>選択回数</th>
                                                    <th>割合</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($characterStats as $character)
                                                    <tr>
                                                        <td>{{ $character->character_name }}</td>
                                                        <td>{{ $character->count }}</td>
                                                        <td>
                                                            @if($totalUsers > 0)
                                                                {{ round(($character->count / $totalUsers) * 100, 1) }}%
                                                            @else
                                                                0%
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 登録推移チャート
    const registrationCtx = document.getElementById('registrationChart').getContext('2d');
    new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['registration']['labels']),
            datasets: [{
                label: '新規登録数',
                data: @json($chartData['registration']['data']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // キャラクター選択チャート
    const characterCtx = document.getElementById('characterChart').getContext('2d');
    new Chart(characterCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['character']['labels']),
            datasets: [{
                data: @json($chartData['character']['data']),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
