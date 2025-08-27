@extends('admin.layout')

@section('title', 'アーティスト編集')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>アーティスト編集</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 一覧に戻る
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">アーティスト情報編集</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.artists.update', $artist->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="artist_name">アーティスト名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('artist_name') is-invalid @enderror" 
                                           id="artist_name" name="artist_name" value="{{ old('artist_name', $artist->artist_name) }}" required>
                                    @error('artist_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="artist_color">アーティストカラー <span class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('artist_color') is-invalid @enderror" 
                                           id="artist_color" name="artist_color" value="{{ old('artist_color', $artist->artist_color) }}" required>
                                    @error('artist_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="artist_description">説明</label>
                            <textarea class="form-control @error('artist_description') is-invalid @enderror" 
                                      id="artist_description" name="artist_description" rows="3">{{ old('artist_description', $artist->artist_description) }}</textarea>
                            @error('artist_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="artist_pattern">パターン <span class="text-danger">*</span></label>
                                    <select class="form-control @error('artist_pattern') is-invalid @enderror" 
                                            id="artist_pattern" name="artist_pattern" required>
                                        <option value="solid" {{ old('artist_pattern', $artist->artist_pattern) == 'solid' ? 'selected' : '' }}>単色</option>
                                        <option value="blink" {{ old('artist_pattern', $artist->artist_pattern) == 'blink' ? 'selected' : '' }}>点滅</option>
                                        <option value="fade" {{ old('artist_pattern', $artist->artist_pattern) == 'fade' ? 'selected' : '' }}>フェード</option>
                                        <option value="wave" {{ old('artist_pattern', $artist->artist_pattern) == 'wave' ? 'selected' : '' }}>波紋</option>
                                        <option value="rainbow" {{ old('artist_pattern', $artist->artist_pattern) == 'rainbow' ? 'selected' : '' }}>虹色</option>
                                        <option value="pulse" {{ old('artist_pattern', $artist->artist_pattern) == 'pulse' ? 'selected' : '' }}>パルス</option>
                                    </select>
                                    @error('artist_pattern')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="artist_brightness">明度 (1-100) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('artist_brightness') is-invalid @enderror" 
                                           id="artist_brightness" name="artist_brightness" 
                                           value="{{ old('artist_brightness', $artist->artist_brightness) }}" min="1" max="100" required>
                                    @error('artist_brightness')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="artist_bpm">BPM (60-200) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('artist_bpm') is-invalid @enderror" 
                                           id="artist_bpm" name="artist_bpm" 
                                           value="{{ old('artist_bpm', $artist->artist_bpm) }}" min="60" max="200" required>
                                    @error('artist_bpm')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="artist_intensity">強度 <span class="text-danger">*</span></label>
                                    <select class="form-control @error('artist_intensity') is-invalid @enderror" 
                                            id="artist_intensity" name="artist_intensity" required>
                                        <option value="low" {{ old('artist_intensity', $artist->artist_intensity) == 'low' ? 'selected' : '' }}>低</option>
                                        <option value="medium" {{ old('artist_intensity', $artist->artist_intensity) == 'medium' ? 'selected' : '' }}>中</option>
                                        <option value="high" {{ old('artist_intensity', $artist->artist_intensity) == 'high' ? 'selected' : '' }}>高</option>
                                    </select>
                                    @error('artist_intensity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="artist_color_scheme">カラースキーム <span class="text-danger">*</span></label>
                                    <select class="form-control @error('artist_color_scheme') is-invalid @enderror" 
                                            id="artist_color_scheme" name="artist_color_scheme" required>
                                        <option value="artist" {{ old('artist_color_scheme', $artist->artist_color_scheme) == 'artist' ? 'selected' : '' }}>アーティスト色</option>
                                        <option value="rainbow" {{ old('artist_color_scheme', $artist->artist_color_scheme) == 'rainbow' ? 'selected' : '' }}>虹色</option>
                                        <option value="monochrome" {{ old('artist_color_scheme', $artist->artist_color_scheme) == 'monochrome' ? 'selected' : '' }}>モノクロ</option>
                                    </select>
                                    @error('artist_color_scheme')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="artist_is_active" name="artist_is_active" value="1" 
                                               {{ old('artist_is_active', $artist->artist_is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="artist_is_active">アクティブ</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 更新
                            </button>
                            <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> キャンセル
                            </a>
                        </div>
                    </form>
                    
                    <!-- 削除フォーム -->
                    <hr>
                    <div class="form-group">
                        <h5 class="text-danger">危険な操作</h5>
                        <p class="text-muted">このアーティストを削除すると、関連する楽曲もすべて削除されます。この操作は取り消せません。</p>
                        <form action="{{ route('admin.artists.destroy', $artist->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('本当にこのアーティストを削除しますか？\n\nこの操作により、関連する楽曲もすべて削除されます。\nこの操作は取り消せません。')">
                                <i class="fas fa-trash"></i> アーティストを削除
                            </button>
                        </form>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
