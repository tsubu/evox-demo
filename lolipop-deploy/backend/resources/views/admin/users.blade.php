@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ユーザー管理</h3>
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
                                    <span class="info-box-text">認証済み</span>
                                    <span class="info-box-number">{{ $activeUsers }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">未認証</span>
                                    <span class="info-box-number">{{ $incompleteUsers }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ユーザー一覧テーブル -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>名前</th>
                                    <th>メールアドレス</th>
                                    <th>電話番号</th>
                                    <th>認証状況</th>
                                    <th>登録日</th>
                                    <th>詳細</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name ?? '未設定' }}</td>
                                    <td>{{ $user->email ?? '未設定' }}</td>
                                    <td>{{ $user->phone ?? '未設定' }}</td>
                                    <td>
                                        @if($user->isVerified())
                                            <span class="badge badge-success">認証済み</span>
                                        @else
                                            <span class="badge badge-warning">未認証</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y年m月d日 H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> 詳細
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> 編集
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('ユーザー「{{ $user->name ?? 'ID: ' . $user->id }}」を削除しますか？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> 削除
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="toggleBlacklist({{ $user->id }}, '{{ $user->name ?? 'ID: ' . $user->id }}')">
                                                <i class="fas fa-ban"></i> BKL
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">ユーザーが登録されていません。</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ページネーション -->
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBlacklist(userId, userName) {
    if (confirm('ユーザー「' + userName + '」をブラックリストに追加しますか？')) {
        // ブラックリスト機能の実装
        // ここでAPIを呼び出してブラックリスト状態を切り替える
        fetch('/admin/users/' + userId + '/blacklist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('ブラックリスト状態を更新しました。');
                location.reload();
            } else {
                alert('エラーが発生しました: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。');
        });
    }
}
</script>
@endsection
