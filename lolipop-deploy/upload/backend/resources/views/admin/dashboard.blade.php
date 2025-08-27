@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ダッシュボード</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> ダッシュボードへようこそ</h5>
                        EvoX管理画面で、ユーザー、ニュース、QRコードなどの管理を行えます。
                    </div>

                    <!-- 統計情報 -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総ユーザー数</span>
                                    <span class="info-box-number">{{ $userCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">今日の新規登録</span>
                                    <span class="info-box-number">{{ $todayRegistrations ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-qrcode"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総QRコード数</span>
                                    <span class="info-box-number">{{ $qrCodeCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-newspaper"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">ニュース記事数</span>
                                    <span class="info-box-number">{{ $newsCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 機能カード -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users"></i> ユーザー管理
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>ユーザーの一覧表示、詳細確認、管理を行います。</p>
                                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> ユーザー一覧
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-newspaper"></i> ニュース管理
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>ニュース記事の作成、編集、削除を行います。</p>
                                    <a href="{{ route('admin.news') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> ニュース管理
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-qrcode"></i> QRコード管理
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>QRコードの作成、編集、削除を行います。</p>
                                    <a href="{{ route('admin.qrcodes') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> QRコード管理
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar"></i> 統計情報
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>システムの統計情報を確認します。</p>
                                    <a href="{{ route('admin.stats') }}" class="btn btn-primary">
                                        <i class="fas fa-chart-line"></i> 統計情報
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-shield"></i> 管理者管理
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>管理者アカウントの追加、編集、削除を行います。</p>
                                    <a href="{{ route('admin.admins') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> 管理者管理
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-lightbulb"></i> ペンライト制御
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>イベント会場のペンライトをサーバーから制御します。</p>
                                    <a href="{{ route('admin.penlight-control') }}" class="btn btn-primary">
                                        <i class="fas fa-cog"></i> ペンライト制御
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-music"></i> アーティスト管理
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>B ZONE所属アーティストと楽曲の管理を行います。</p>
                                    <a href="{{ route('admin.artists.index') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> アーティスト管理
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
