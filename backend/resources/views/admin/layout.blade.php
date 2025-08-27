<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EvoX 管理画面</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AdminLTE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    
    <style>
        .content-wrapper {
            background-color: #f4f6f9;
        }
        
        .main-header {
            background-color: #2a5298;
        }
        
        .main-header .navbar-brand {
            color: white;
            font-weight: bold;
        }
        
        .main-header .navbar-nav .nav-link {
            color: white;
        }
        
        .main-header .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        
        .btn-group .btn {
            margin-right: 0.25rem;
        }
        
        .info-box {
            display: block;
            min-height: 80px;
            background: #fff;
            width: 100%;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        
        .info-box-icon {
            border-radius: 0.25rem 0 0 0.25rem;
            display: block;
            float: left;
            height: 80px;
            width: 80px;
            text-align: center;
            font-size: 1.875rem;
            line-height: 80px;
            background: rgba(0,0,0,.2);
        }
        
        .info-box-content {
            padding: 5px 10px;
            margin-left: 80px;
        }
        
        .info-box-text {
            display: block;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .info-box-number {
            display: block;
            font-weight: 700;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link" onclick="return confirm('ログアウトしますか？')">
                            <i class="fas fa-sign-out-alt"></i> ログアウト
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">EvoX 管理画面</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>ダッシュボード</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>ユーザー管理</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.news') }}" class="nav-link {{ request()->routeIs('admin.news*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>ニュース管理</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.qrcodes') }}" class="nav-link {{ request()->routeIs('admin.qrcodes*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-qrcode"></i>
                                <p>QRコード管理</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.stats') }}" class="nav-link {{ request()->routeIs('admin.stats*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>統計情報</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.admins') }}" class="nav-link {{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>管理者管理</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.penlight-control') }}" class="nav-link {{ request()->routeIs('admin.penlight-control*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-lightbulb"></i>
                                <p>ペンライト制御</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.artists.index') }}" class="nav-link {{ request()->routeIs('admin.artists*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-music"></i>
                                <p>アーティスト管理</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                EvoX Admin Panel
            </div>
            <strong>Copyright &copy; 2025</strong> All rights reserved.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS (AdminLTEとの互換性のため) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    @stack('scripts')
</body>
</html>
