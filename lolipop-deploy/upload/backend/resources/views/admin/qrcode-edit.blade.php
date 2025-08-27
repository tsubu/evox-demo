@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($qrCode->qr_is_liveevent)
                            <i class="fas fa-music"></i> リアルイベントQRコード編集
                        @else
                            <i class="fas fa-gift"></i> ゲームグッズ（仮）QRコード編集
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
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

                    <!-- 機能選択タブ -->
                    <ul class="nav nav-tabs mb-3" id="qrTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $qrCode->qr_is_liveevent ? 'active' : '' }}" 
                               id="event-tab" data-toggle="tab" href="#event-form" role="tab" aria-controls="event-form" aria-selected="{{ $qrCode->qr_is_liveevent ? 'true' : 'false' }}">
                                <i class="fas fa-music"></i> リアルイベント
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ !$qrCode->qr_is_liveevent ? 'active' : '' }}" 
                               id="goods-tab" data-toggle="tab" href="#goods-form" role="tab" aria-controls="goods-form" aria-selected="{{ !$qrCode->qr_is_liveevent ? 'true' : 'false' }}">
                                <i class="fas fa-gift"></i> ゲームグッズ（仮）
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="qrTypeTabContent">
                        <!-- リアルイベントフォーム -->
                        <div class="tab-pane fade {{ $qrCode->qr_is_liveevent ? 'show active' : '' }}" id="event-form" role="tabpanel" aria-labelledby="event-tab">
                            <form action="{{ route('admin.qrcodes.update', $qrCode->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="qr_type" value="event">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_code_event">QRコード <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('qr_code') is-invalid @enderror" 
                                           id="qr_code_event" 
                                           name="qr_code" 
                                           value="{{ old('qr_code', $qrCode->qr_code) }}" 
                                           required>
                                    @error('qr_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_title_event">イベント名 <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('qr_title') is-invalid @enderror" 
                                           id="qr_title_event" 
                                           name="qr_title" 
                                           value="{{ old('qr_title', $qrCode->qr_title) }}" 
                                           placeholder="例: サマーフェス2024">
                                    @error('qr_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qr_artist_name_event">アーティスト名</label>
                                    <input type="text" 
                                           class="form-control @error('qr_artist_name') is-invalid @enderror" 
                                           id="qr_artist_name_event" 
                                           name="qr_artist_name" 
                                           value="{{ old('qr_artist_name', $qrCode->qr_artist_name) }}" 
                                           placeholder="例: アーティスト名">
                                    @error('qr_artist_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qr_points_event">付与ポイント</label>
                                    <input type="number" 
                                           class="form-control @error('qr_points') is-invalid @enderror" 
                                           id="qr_points_event" 
                                           name="qr_points" 
                                           value="{{ old('qr_points', $qrCode->qr_points) }}" 
                                           min="0" 
                                           placeholder="0">
                                    @error('qr_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qr_expires_at_event">イベント日時</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('qr_expires_at') is-invalid @enderror" 
                                           id="qr_expires_at_event" 
                                           name="qr_expires_at" 
                                           value="{{ old('qr_expires_at', $qrCode->qr_expires_at ? $qrCode->qr_expires_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('qr_expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="qr_description_event">イベント説明 <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('qr_description') is-invalid @enderror" 
                                              id="qr_description_event" 
                                              name="qr_description" 
                                              rows="3" 
                                              placeholder="イベントの詳細説明を入力してください">{{ old('qr_description', $qrCode->qr_description) }}</textarea>
                                    @error('qr_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="qr_contents_event">イベント内容</label>
                                    <textarea class="form-control @error('qr_contents') is-invalid @enderror" 
                                              id="qr_contents_event" 
                                              name="qr_contents" 
                                              rows="4" 
                                              placeholder="イベントの詳細内容、参加方法などを入力してください">{{ old('qr_contents', $qrCode->qr_contents) }}</textarea>
                                    @error('qr_contents')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="qr_is_active_event" 
                                               name="qr_is_active" 
                                               value="1" 
                                               {{ old('qr_is_active', $qrCode->qr_is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="qr_is_active_event">
                                            アクティブ
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ライブイベントフラグ（非表示） -->
                        <input type="hidden" name="qr_is_liveevent" value="1">
                        <!-- 複数回参加フラグ（非表示・常にfalse） -->
                        <input type="hidden" name="qr_is_multiple" value="0">

                        <!-- オプション設定セクション -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-cog"></i> オプション設定
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="qr_options_enabled_event" 
                                                       name="qr_options_enabled" 
                                                       value="1" 
                                                       {{ old('qr_options_enabled', $qrCode->qr_options_enabled) ? 'checked' : '' }}
                                                       onchange="toggleOptionsSection('event')">
                                                <label class="custom-control-label" for="qr_options_enabled_event">
                                                    オプション機能を有効にする
                                                </label>
                                            </div>
                                        </div>

                                        <div id="options-section-event" class="{{ old('qr_options_enabled', $qrCode->qr_options_enabled) ? '' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>アバター表情オプション（最大5種類）</label>
                                                        <div id="avatar-expressions-event">
                                                            @if($qrCode->qr_avatar_expressions)
                                                                @foreach($qrCode->qr_avatar_expressions as $index => $expression)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" name="qr_avatar_expressions[]" value="{{ $expression }}" placeholder="例: 笑顔">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" name="qr_avatar_expressions[]" placeholder="例: 笑顔">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('avatar-expressions-event', 'qr_avatar_expressions[]', 'アバター表情')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>アバター行動オプション（最大5種類）</label>
                                                        <div id="avatar-actions-event">
                                                            @if($qrCode->qr_avatar_actions)
                                                                @foreach($qrCode->qr_avatar_actions as $index => $action)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" name="qr_avatar_actions[]" value="{{ $action }}" placeholder="例: ダンス">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" name="qr_avatar_actions[]" placeholder="例: ダンス">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('avatar-actions-event', 'qr_avatar_actions[]', 'アバター行動')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>背景色オプション（最大5種類）</label>
                                                        <div id="background-colors-event">
                                                            @if($qrCode->qr_background_colors)
                                                                @foreach($qrCode->qr_background_colors as $index => $color)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" name="qr_background_colors[]" value="{{ $color }}" placeholder="例: 青空">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" name="qr_background_colors[]" placeholder="例: 青空">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('background-colors-event', 'qr_background_colors[]', '背景色')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>エフェクトオプション（最大5種類）</label>
                                                        <div id="effects-event">
                                                            @if($qrCode->qr_effects)
                                                                @foreach($qrCode->qr_effects as $index => $effect)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" name="qr_effects[]" value="{{ $effect }}" placeholder="例: キラキラ">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" name="qr_effects[]" placeholder="例: キラキラ">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('effects-event', 'qr_effects[]', 'エフェクト')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>サウンドオプション（最大5種類）</label>
                                                        <div id="sounds-event">
                                                            @if($qrCode->qr_sounds)
                                                                @foreach($qrCode->qr_sounds as $index => $sound)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" name="qr_sounds[]" value="{{ $sound }}" placeholder="例: ファンファーレ">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" name="qr_sounds[]" placeholder="例: ファンファーレ">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('sounds-event', 'qr_sounds[]', 'サウンド')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-music"></i> リアルイベントQRコードを更新
                                    </button>
                                    <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- 削除フォーム（独立） -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 危険な操作
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        QRコード「<strong>{{ $qrCode->qr_code }}</strong>」を削除します。この操作は取り消せません。
                                    </p>
                                    <form action="{{ route('admin.qrcodes.destroy', $qrCode->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('QRコード「{{ $qrCode->qr_code }}」を削除しますか？\n\nこの操作は取り消せません。')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> QRコードを削除
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ゲームグッズ（仮）フォーム -->
                <div class="tab-pane fade {{ !$qrCode->qr_is_liveevent ? 'show active' : '' }}" id="goods-form" role="tabpanel" aria-labelledby="goods-tab">
                    <form action="{{ route('admin.qrcodes.update', $qrCode->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="qr_type" value="goods">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_code_goods">QRコード <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('qr_code') is-invalid @enderror" 
                                           id="qr_code_goods" 
                                           name="qr_code" 
                                           value="{{ old('qr_code', $qrCode->qr_code) }}" 
                                           required>
                                    @error('qr_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_title_goods">アイテム名 <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('qr_title') is-invalid @enderror" 
                                           id="qr_title_goods" 
                                           name="qr_title" 
                                           value="{{ old('qr_title', $qrCode->qr_title) }}" 
                                           placeholder="例: レアアバター、限定アイテム">
                                    @error('qr_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_points_goods">付与ポイント</label>
                                    <input type="number" 
                                           class="form-control @error('qr_points') is-invalid @enderror" 
                                           id="qr_points_goods" 
                                           name="qr_points" 
                                           value="{{ old('qr_points', $qrCode->qr_points) }}" 
                                           min="0" 
                                           placeholder="0">
                                    @error('qr_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_expires_at_goods">有効期限</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('qr_expires_at') is-invalid @enderror" 
                                           id="qr_expires_at_goods" 
                                           name="qr_expires_at" 
                                           value="{{ old('qr_expires_at', $qrCode->qr_expires_at ? $qrCode->qr_expires_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('qr_expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="qr_description_goods">アイテム説明 <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('qr_description') is-invalid @enderror" 
                                              id="qr_description_goods" 
                                              name="qr_description" 
                                              rows="3" 
                                              placeholder="アイテムの詳細説明を入力してください">{{ old('qr_description', $qrCode->qr_description) }}</textarea>
                                    @error('qr_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="qr_contents_goods">アイテム内容</label>
                                    <textarea class="form-control @error('qr_contents') is-invalid @enderror" 
                                              id="qr_contents_goods" 
                                              name="qr_contents" 
                                              rows="4" 
                                              placeholder="アイテムの詳細内容、使用方法などを入力してください">{{ old('qr_contents', $qrCode->qr_contents) }}</textarea>
                                    @error('qr_contents')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="qr_is_active_goods" 
                                               name="qr_is_active" 
                                               value="1" 
                                               {{ old('qr_is_active', $qrCode->qr_is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="qr_is_active_goods">
                                            アクティブ
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="qr_is_multiple_goods" 
                                               name="qr_is_multiple" 
                                               value="1" 
                                               {{ old('qr_is_multiple', $qrCode->qr_is_multiple) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="qr_is_multiple_goods">
                                            複数回使用可能
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-gift"></i> ゲームグッズ（仮）QRコードを更新
                                    </button>
                                    <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- 削除フォーム（独立） -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 危険な操作
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        QRコード「<strong>{{ $qrCode->qr_code }}</strong>」を削除します。この操作は取り消せません。
                                    </p>
                                    <form action="{{ route('admin.qrcodes.destroy', $qrCode->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('QRコード「{{ $qrCode->qr_code }}」を削除しますか？\n\nこの操作は取り消せません。')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> QRコードを削除
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
</div>

<script>
// タブ切り替え時の処理
document.addEventListener('DOMContentLoaded', function() {
    // jQueryが利用可能かチェック
    if (typeof $ !== 'undefined') {
        // タブ切り替え時にフォームの値をクリア
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('id').replace('-tab', '');
            if (target === 'event') {
                // リアルイベントタブが選択された場合
                $('#goods-form input, #goods-form textarea').val('');
            } else if (target === 'goods') {
                // ゲームグッズタブが選択された場合
                $('#event-form input, #event-form textarea').val('');
            }
        });
    } else {
        console.warn('jQuery is not available');
    }
});
</script>
@endsection
