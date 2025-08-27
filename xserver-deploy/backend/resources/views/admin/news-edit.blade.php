@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ニュース記事編集</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>記事ID:</strong> {{ $news->id }} | 
                        <strong>作成日:</strong> {{ $news->gamenews_created_at ? $news->gamenews_created_at->format('Y-m-d H:i:s') : 'N/A' }} | 
                        <strong>更新日:</strong> {{ $news->gamenews_updated_at ? $news->gamenews_updated_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </div>

                    <form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="gamenews_title">タイトル *</label>
                            <input type="text" id="gamenews_title" name="gamenews_title" class="form-control" value="{{ old('gamenews_title', $news->gamenews_title) }}" required>
                            @error('gamenews_title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gamenews_content">内容 *</label>
                            <textarea id="gamenews_content" name="gamenews_content" class="form-control" rows="10" required>{{ old('gamenews_content', $news->gamenews_content) }}</textarea>
                            @error('gamenews_content')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gamenews_image">バナー画像</label>
                            <div id="drop-zone" class="border-3 border-dashed border-gray-400 rounded-lg p-8 text-center cursor-pointer transition-all duration-300" style="min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(45deg, #f8fafc 25%, transparent 25%), linear-gradient(-45deg, #f8fafc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f8fafc 75%), linear-gradient(-45deg, transparent 75%, #f8fafc 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;">
                                <div id="drop-zone-content">
                                    <div class="upload-icon mb-4">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                    </div>
                                    <h5 class="text-gray-700 font-weight-bold mb-2">画像をアップロード</h5>
                                    <p class="text-gray-600 mb-3">ここに画像をドラッグ&ドロップしてください</p>
                                    <div class="upload-hint mb-3">
                                        <span class="badge badge-light mr-2">JPEG</span>
                                        <span class="badge badge-light mr-2">PNG</span>
                                        <span class="badge badge-light mr-2">JPG</span>
                                        <span class="badge badge-light">GIF</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3">または</p>
                                    <button type="button" id="browse-btn" class="btn btn-primary">
                                        <i class="fas fa-folder-open mr-2"></i>ファイルを選択
                                    </button>
                                    <input type="file" id="gamenews_image" name="gamenews_image" class="form-control-file" accept="image/*" style="display: none;">
                                </div>
                                <div id="file-info" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-image mr-2"></i>
                                        <span id="file-name" class="font-weight-bold"></span>
                                        <br>
                                        <small id="file-size" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">JPEG, PNG, JPG, GIF形式、最大2MB</small>
                            @error('gamenews_image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($news->gamenews_image_url)
                        <div class="form-group">
                            <label>現在の画像</label>
                            <div class="mt-2">
                                <img src="{{ $news->gamenews_image_url }}" alt="現在の画像" class="img-fluid" style="max-width: 300px; max-height: 200px;">
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <div id="image-preview" class="mt-3" style="display: none;">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">新しい画像のプレビュー</h6>
                                        <button type="button" id="remove-image" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash mr-1"></i>削除
                                        </button>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="preview-img" src="" alt="プレビュー" class="img-fluid rounded" style="max-width: 100%; max-height: 300px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="gamenews_is_published" name="gamenews_is_published" class="custom-control-input" value="1" {{ old('gamenews_is_published', $news->gamenews_is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="gamenews_is_published">公開する</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gamenews_publish_start_at">公開開始日時</label>
                            <input type="datetime-local" id="gamenews_publish_start_at" name="gamenews_publish_start_at" class="form-control" value="{{ old('gamenews_publish_start_at', $news->gamenews_publish_start_at ? $news->gamenews_publish_start_at->format('Y-m-d\TH:i') : '') }}">
                            @error('gamenews_publish_start_at')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gamenews_publish_end_at">公開終了日時</label>
                            <input type="datetime-local" id="gamenews_publish_end_at" name="gamenews_publish_end_at" class="form-control" value="{{ old('gamenews_publish_end_at', $news->gamenews_publish_end_at ? $news->gamenews_publish_end_at->format('Y-m-d\TH:i') : '') }}">
                            @error('gamenews_publish_end_at')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">更新</button>
                            <a href="{{ route('admin.news') }}" class="btn btn-secondary">キャンセル</a>
                            <button type="button" class="btn btn-danger" onclick="deleteNews()">削除</button>
                        </div>
                    </form>

                    <!-- 削除用の独立したフォーム -->
                    <form id="delete-form" method="POST" action="{{ route('admin.news.destroy', $news->id) }}" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('gamenews_image');
const browseBtn = document.getElementById('browse-btn');
const dropZoneContent = document.getElementById('drop-zone-content');
const fileInfo = document.getElementById('file-info');
const fileName = document.getElementById('file-name');
const fileSize = document.getElementById('file-size');
const preview = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');

// ファイル選択ボタンのクリックイベント
browseBtn.addEventListener('click', function() {
    fileInput.click();
});

// ファイル入力の変更イベント
fileInput.addEventListener('change', function(e) {
    handleFile(e.target.files[0]);
});

// ドラッグ&ドロップイベント
dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = '#3B82F6';
    dropZone.style.borderWidth = '3px';
    dropZone.style.backgroundColor = '#EFF6FF';
    dropZone.style.transform = 'scale(1.02)';
    dropZone.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.3)';
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = '#9CA3AF';
    dropZone.style.borderWidth = '3px';
    dropZone.style.backgroundColor = 'transparent';
    dropZone.style.transform = 'scale(1)';
    dropZone.style.boxShadow = 'none';
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = '#9CA3AF';
    dropZone.style.borderWidth = '3px';
    dropZone.style.backgroundColor = 'transparent';
    dropZone.style.transform = 'scale(1)';
    dropZone.style.boxShadow = 'none';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
});

// ファイル処理関数
function handleFile(file) {
    if (!file) return;
    
    // ファイルタイプチェック
    if (!file.type.startsWith('image/')) {
        alert('画像ファイルを選択してください。');
        return;
    }
    
    // ファイルサイズチェック（2MB）
    if (file.size > 2 * 1024 * 1024) {
        alert('ファイルサイズは2MB以下にしてください。');
        return;
    }
    
    // ファイル情報を表示
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    dropZoneContent.style.display = 'none';
    fileInfo.style.display = 'block';
    
    // プレビュー表示
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        preview.style.display = 'block';
    }
    reader.readAsDataURL(file);
    
    // ファイル入力を更新
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;
}

// ファイルサイズフォーマット関数
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// 削除ボタンのイベント
document.getElementById('remove-image').addEventListener('click', function() {
    // ファイル入力をクリア
    fileInput.value = '';
    
    // プレビューを非表示
    preview.style.display = 'none';
    
    // ドロップゾーンをリセット
    dropZoneContent.style.display = 'block';
    fileInfo.style.display = 'none';
    
    // ドロップゾーンのスタイルをリセット
    dropZone.style.borderColor = '#9CA3AF';
    dropZone.style.borderWidth = '3px';
    dropZone.style.backgroundColor = 'transparent';
    dropZone.style.transform = 'scale(1)';
    dropZone.style.boxShadow = 'none';
});

// ドロップゾーンをクリックしてファイル選択
dropZone.addEventListener('click', function(e) {
    if (e.target === dropZone || e.target.closest('#drop-zone-content')) {
        fileInput.click();
    }
});

// ニュース削除関数
function deleteNews() {
    if (confirm('このニュース記事を削除しますか？')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
