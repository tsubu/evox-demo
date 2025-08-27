@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">管理者編集</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.admins') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_name">管理者名 <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('admin_name') is-invalid @enderror" 
                                           id="admin_name" 
                                           name="admin_name" 
                                           value="{{ old('admin_name', $admin->admin_name) }}" 
                                           required>
                                    @error('admin_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_phone">電話番号 <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('admin_phone') is-invalid @enderror" 
                                           id="admin_phone" 
                                           name="admin_phone" 
                                           value="{{ old('admin_phone', $admin->admin_phone) }}" 
                                           placeholder="例: +818090330374"
                                           required>
                                    @error('admin_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_email">メールアドレス</label>
                                    <input type="email" 
                                           class="form-control @error('admin_email') is-invalid @enderror" 
                                           id="admin_email" 
                                           name="admin_email" 
                                           value="{{ old('admin_email', $admin->admin_email) }}" 
                                           placeholder="例: admin@example.com">
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password">新しいパスワード</label>
                                    <input type="password" 
                                           class="form-control @error('admin_password') is-invalid @enderror" 
                                           id="admin_password" 
                                           name="admin_password" 
                                           minlength="8">
                                    <small class="form-text text-muted">変更する場合のみ入力してください（8文字以上）。</small>
                                    @error('admin_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password_confirmation">新しいパスワード（確認）</label>
                                    <input type="password" 
                                           class="form-control @error('admin_password_confirmation') is-invalid @enderror" 
                                           id="admin_password_confirmation" 
                                           name="admin_password_confirmation" 
                                           minlength="8">
                                    @error('admin_password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 更新
                                    </button>
                                    <a href="{{ route('admin.admins') }}" class="btn btn-secondary">
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
    // パスワード確認のリアルタイムチェック
    const password = document.getElementById('admin_password');
    const confirmPassword = document.getElementById('admin_password_confirmation');
    
    function checkPasswordMatch() {
        if (password.value && confirmPassword.value) {
            if (password.value === confirmPassword.value) {
                confirmPassword.setCustomValidity('');
            } else {
                confirmPassword.setCustomValidity('パスワードが一致しません。');
            }
        }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // 既存の電話番号から国別コードを分離
    const phoneInput = document.getElementById('admin_phone');
    const countryCodeSelect = document.getElementById('country_code');
    
    if (phoneInput.value) {
        const phoneValue = phoneInput.value;
        
        // 国別コードを検出
        const countryCodes = ['+81', '+1', '+44', '+86', '+82', '+33', '+49', '+39', '+34', '+31'];
        let detectedCode = '+81'; // デフォルト
        
        for (const code of countryCodes) {
            if (phoneValue.startsWith(code)) {
                detectedCode = code;
                break;
            }
        }
        
        // 国別コードを設定
        countryCodeSelect.value = detectedCode;
        
        // 電話番号から国別コードを除去
        const phoneWithoutCode = phoneValue.replace(detectedCode, '');
        phoneInput.value = phoneWithoutCode;
    }
});
</script>
@endsection
