@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> QRコードオプション管理
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> QRコード管理に戻る
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- 統計情報 -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-qrcode"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総QRコード数</span>
                                    <span class="info-box-number">{{ $stats['total_qrcodes'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">オプション有効</span>
                                    <span class="info-box-number">{{ $stats['options_enabled'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-times"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">オプション無効</span>
                                    <span class="info-box-number">{{ $stats['options_disabled'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- タブナビゲーション -->
                    <ul class="nav nav-tabs mb-3" id="optionsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="defaults-tab" data-toggle="tab" href="#defaults" role="tab">
                                <i class="fas fa-list"></i> デフォルト選択肢
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="custom-tab" data-toggle="tab" href="#custom" role="tab">
                                <i class="fas fa-edit"></i> カスタム選択肢
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="bulk-tab" data-toggle="tab" href="#bulk" role="tab">
                                <i class="fas fa-tasks"></i> 一括操作
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="optionsTabContent">
                        <!-- デフォルト選択肢タブ -->
                        <div class="tab-pane fade show active" id="defaults" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>アバター表情</h5>
                                    <ul class="list-group">
                                        @foreach($defaultOptions['qr_avatar_expressions'] as $expression)
                                            <li class="list-group-item">{{ $expression }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>アバター行動</h5>
                                    <ul class="list-group">
                                        @foreach($defaultOptions['qr_avatar_actions'] as $action)
                                            <li class="list-group-item">{{ $action }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <h5>背景色</h5>
                                    <ul class="list-group">
                                        @foreach($defaultOptions['qr_background_colors'] as $color)
                                            <li class="list-group-item">{{ $color }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h5>エフェクト</h5>
                                    <ul class="list-group">
                                        @foreach($defaultOptions['qr_effects'] as $effect)
                                            <li class="list-group-item">{{ $effect }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h5>サウンド</h5>
                                    <ul class="list-group">
                                        @foreach($defaultOptions['qr_sounds'] as $sound)
                                            <li class="list-group-item">{{ $sound }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- カスタム選択肢タブ -->
                        <div class="tab-pane fade" id="custom" role="tabpanel">
                            <form action="{{ route('admin.qrcode-options.update-custom') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>アバター表情（最大10種類）</label>
                                            <div id="custom-avatar-expressions">
                                                @foreach($defaultOptions['qr_avatar_expressions'] as $index => $expression)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="qr_avatar_expressions[]" value="{{ $expression }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomOption('custom-avatar-expressions', 'qr_avatar_expressions[]', 'アバター表情')">
                                                <i class="fas fa-plus"></i> 追加
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>アバター行動（最大10種類）</label>
                                            <div id="custom-avatar-actions">
                                                @foreach($defaultOptions['qr_avatar_actions'] as $index => $action)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="qr_avatar_actions[]" value="{{ $action }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomOption('custom-avatar-actions', 'qr_avatar_actions[]', 'アバター行動')">
                                                <i class="fas fa-plus"></i> 追加
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>背景色（最大10種類）</label>
                                            <div id="custom-background-colors">
                                                @foreach($defaultOptions['qr_background_colors'] as $index => $color)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="qr_background_colors[]" value="{{ $color }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomOption('custom-background-colors', 'qr_background_colors[]', '背景色')">
                                                <i class="fas fa-plus"></i> 追加
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>エフェクト（最大10種類）</label>
                                            <div id="custom-effects">
                                                @foreach($defaultOptions['qr_effects'] as $index => $effect)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="qr_effects[]" value="{{ $effect }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomOption('custom-effects', 'qr_effects[]', 'エフェクト')">
                                                <i class="fas fa-plus"></i> 追加
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>サウンド（最大10種類）</label>
                                            <div id="custom-sounds">
                                                @foreach($defaultOptions['qr_sounds'] as $index => $sound)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="qr_sounds[]" value="{{ $sound }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomOption('custom-sounds', 'qr_sounds[]', 'サウンド')">
                                                <i class="fas fa-plus"></i> 追加
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> カスタム選択肢を保存
                                        </button>
                                        <small class="form-text text-muted">
                                            この操作により、オプションが有効なすべてのQRコードの選択肢が更新されます。
                                        </small>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- 一括操作タブ -->
                        <div class="tab-pane fade" id="bulk" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">デフォルトオプションの適用</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">
                                                選択したQRコードにデフォルトのオプション選択肢を適用します。
                                            </p>
                                            <form action="{{ route('admin.qrcode-options.apply-defaults') }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label>適用対象のQRコードを選択</label>
                                                    <select name="qr_code_ids[]" class="form-control" multiple size="5">
                                                        @foreach(\App\Models\QrCode::all() as $qrCode)
                                                            <option value="{{ $qrCode->id }}">
                                                                {{ $qrCode->qr_code }} - {{ $qrCode->qr_title }}
                                                                @if($qrCode->qr_options_enabled)
                                                                    <span class="text-success">(オプション有効)</span>
                                                                @else
                                                                    <span class="text-warning">(オプション無効)</span>
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> デフォルトオプションを適用
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">オプション機能の一括制御</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">
                                                選択したQRコードのオプション機能を一括で有効化または無効化します。
                                            </p>
                                            <form action="{{ route('admin.qrcode-options.toggle') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="enable">
                                                <div class="form-group">
                                                    <label>対象のQRコードを選択</label>
                                                    <select name="qr_code_ids[]" class="form-control" multiple size="5">
                                                        @foreach(\App\Models\QrCode::all() as $qrCode)
                                                            <option value="{{ $qrCode->id }}">
                                                                {{ $qrCode->qr_code }} - {{ $qrCode->qr_title }}
                                                                @if($qrCode->qr_options_enabled)
                                                                    <span class="text-success">(オプション有効)</span>
                                                                @else
                                                                    <span class="text-warning">(オプション無効)</span>
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-check"></i> 有効化
                                                    </button>
                                                    <button type="button" class="btn btn-warning" onclick="submitToggleForm('disable')">
                                                        <i class="fas fa-times"></i> 無効化
                                                    </button>
                                                </div>
                                            </form>
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
</div>

<script>
// カスタムオプションの追加
function addCustomOption(containerId, name, label) {
    const container = document.getElementById(containerId);
    const inputs = container.querySelectorAll('input[name="' + name + '"]');
    
    if (inputs.length >= 10) {
        alert(`${label}は最大10種類まで設定できます。`);
        return;
    }
    
    const newInput = document.createElement('div');
    newInput.className = 'input-group mb-2';
    newInput.innerHTML = `
        <input type="text" class="form-control" name="${name}" placeholder="新しい${label}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-danger" onclick="removeCustomOption(this)">削除</button>
        </div>
    `;
    
    container.appendChild(newInput);
}

// カスタムオプションの削除
function removeCustomOption(button) {
    const inputGroup = button.closest('.input-group');
    inputGroup.remove();
}

// 一括無効化フォームの送信
function submitToggleForm(action) {
    const form = document.querySelector('form[action="{{ route("admin.qrcode-options.toggle") }}"]');
    const actionInput = form.querySelector('input[name="action"]');
    actionInput.value = action;
    form.submit();
}
</script>
@endsection
