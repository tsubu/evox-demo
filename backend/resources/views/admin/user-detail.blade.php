@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ユーザー詳細</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>基本情報</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ユーザーID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>名前</th>
                                    <td>{{ $user->name ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>メールアドレス</th>
                                    <td>{{ $user->email ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>電話番号</th>
                                    <td>{{ $user->phone ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>認証状況</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @if($user->isVerified())
                                                    <span class="badge badge-success">認証済み</span>
                                                    @if($user->email_verified_at)
                                                        <small class="text-muted d-block">
                                                            メール認証日時: {{ \Carbon\Carbon::parse($user->email_verified_at)->format('Y年m月d日 H:i:s') }}
                                                        </small>
                                                    @endif
                                                    @if($user->isSmsVerified())
                                                        <small class="text-muted d-block">
                                                            SMS認証済み
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">未認証</span>
                                                @endif
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-info" 
                                                        onclick="toggleSmsVerification({{ $user->id }}, '{{ $user->name ?? 'ID: ' . $user->id }}')">
                                                    <i class="fas fa-mobile-alt"></i> SMS認証切替
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>登録日時</th>
                                    <td>{{ $user->created_at->format('Y年m月d日 H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>最終更新</th>
                                    <td>{{ $user->updated_at->format('Y年m月d日 H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>アカウント情報</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">アカウント状態</th>
                                    <td>
                                        @if($user->isVerified())
                                            <span class="badge badge-success">アクティブ</span>
                                        @else
                                            <span class="badge badge-warning">未認証</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>最終ログイン</th>
                                    <td>
                                        <span class="text-muted">記録なし</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h4>追加情報</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ニックネーム</th>
                                    <td>{{ $user->nickname ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>アバター選択</th>
                                    <td>
                                        @if($user->avatar_choice)
                                            {{ \App\Helpers\AvatarHelper::getAvatarName($user->avatar_choice) }}
                                        @else
                                            未設定
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>誕生日</th>
                                    <td>{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y年m月d日') : '未設定' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>操作</h4>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> ユーザー一覧に戻る
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> 編集
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('ユーザー「{{ $user->name ?? 'ID: ' . $user->id }}」を削除しますか？この操作は取り消せません。')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> ユーザーを削除
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

function toggleVerification(userId, userName) {
    if (confirm('ユーザー「' + userName + '」のメール認証状態を変更しますか？')) {
        fetch('/admin/users/' + userId + '/toggle-verification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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

function toggleSmsVerification(userId, userName) {
    if (confirm('ユーザー「' + userName + '」のSMS認証状態を変更しますか？')) {
        fetch('/admin/users/' + userId + '/toggle-sms-verification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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
