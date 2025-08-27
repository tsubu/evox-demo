@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ユーザー情報編集</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">名前</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" 
                                           placeholder="名前を入力">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">メールアドレス</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           placeholder="メールアドレスを入力">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">電話番号</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           placeholder="電話番号を入力">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nickname">ニックネーム</label>
                                    <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                                           id="nickname" name="nickname" value="{{ old('nickname', $user->nickname) }}" 
                                           placeholder="ニックネームを入力">
                                    @error('nickname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="avatar_choice">アバター選択</label>
                                    <select class="form-control @error('avatar_choice') is-invalid @enderror" 
                                            id="avatar_choice" name="avatar_choice">
                                        <option value="">選択してください</option>
                                        @foreach($avatarOptions as $avatarId => $avatarName)
                                            <option value="{{ $avatarId }}" {{ old('avatar_choice', $user->avatar_choice) == $avatarId ? 'selected' : '' }}>
                                                {{ $avatarName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('avatar_choice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="birthday">誕生日</label>
                                    <input type="date" class="form-control @error('birthday') is-invalid @enderror" 
                                           id="birthday" name="birthday" 
                                           value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}">
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>SMS認証状況</h4>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>注意:</strong> SMS認証状況を変更すると、ユーザーのログイン権限に影響します。
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMS認証状況</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="sms_verified" name="sms_verified" 
                                               value="1" {{ $user->isSmsVerified() ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sms_verified">
                                            @if($user->isSmsVerified())
                                                SMS認証済み
                                            @else
                                                未認証
                                            @endif
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        SMS認証の状態を切り替えます（基本認証方式）
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>注意:</strong> パスワードの変更はこの画面ではできません。ユーザーが自分でパスワードリセットを行う必要があります。
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 更新
                                    </button>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // メール認証状況の変更を監視
    const emailVerifiedCheckbox = document.getElementById('email_verified');
    const emailVerifiedLabel = emailVerifiedCheckbox.nextElementSibling;
    
    emailVerifiedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            emailVerifiedLabel.textContent = '認証済み（' + new Date().toLocaleString('ja-JP') + '）';
        } else {
            emailVerifiedLabel.textContent = '未認証';
        }
    });

    // SMS認証状況の変更を監視
    const smsVerifiedCheckbox = document.getElementById('sms_verified');
    const smsVerifiedLabel = smsVerifiedCheckbox.nextElementSibling;
    
    smsVerifiedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            smsVerifiedLabel.textContent = 'SMS認証済み';
        } else {
            smsVerifiedLabel.textContent = '未認証';
        }
    });
});
</script>
@endsection
