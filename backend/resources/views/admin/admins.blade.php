@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">管理者管理</h3>
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 新規管理者追加
                    </a>
                </div>
                <div class="card-body">
                    <!-- 統計情報 -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総管理者数</span>
                                    <span class="info-box-number">{{ $totalAdmins }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">認証済み</span>
                                    <span class="info-box-number">{{ $activeAdmins }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">未認証</span>
                                    <span class="info-box-number">{{ $inactiveAdmins }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 管理者一覧テーブル -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>管理者名</th>
                                    <th>電話番号</th>
                                    <th>メールアドレス</th>
                                    <th>認証状況</th>
                                    <th>作成日</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>{{ $admin->admin_name }}</td>
                                    <td>{{ $admin->admin_phone }}</td>
                                    <td>{{ $admin->admin_email ?? '未設定' }}</td>
                                    <td>
                                        @if($admin->admin_is_verified)
                                            <span class="badge badge-success">認証済み</span>
                                        @else
                                            <span class="badge badge-warning">未認証</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->admin_created_at ? $admin->admin_created_at->format('Y年m月d日 H:i') : '未設定' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.admins.edit', $admin->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> 編集
                                            </a>
                                            @if($admin->id !== auth()->guard('admin')->id())
                                            <form action="{{ route('admin.admins.destroy', $admin->id) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('管理者「{{ $admin->admin_name }}」を削除しますか？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> 削除
                                                </button>
                                            </form>
                                            @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-user"></i> 自分
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">管理者が登録されていません。</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ページネーション -->
                    <div class="d-flex justify-content-center">
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
