@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if(request('type') == 'event')
                            <i class="fas fa-music"></i> 新規リアルイベントQRコード作成
                        @elseif(request('type') == 'goods')
                            <i class="fas fa-gift"></i> 新規ゲームグッズ（仮）QRコード作成
                        @else
                            新規QRコード作成
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
                            <a class="nav-link {{ request('type') == 'event' ? 'active' : '' }}" 
                               id="event-tab" data-toggle="tab" href="#event-form" role="tab" aria-controls="event-form" aria-selected="{{ request('type') == 'event' ? 'true' : 'false' }}">
                                <i class="fas fa-music"></i> リアルイベント
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('type') == 'goods' ? 'active' : '' }}" 
                               id="goods-tab" data-toggle="tab" href="#goods-form" role="tab" aria-controls="goods-form" aria-selected="{{ request('type') == 'goods' ? 'true' : 'false' }}">
                                <i class="fas fa-gift"></i> ゲームグッズ（仮）
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="qrTypeTabContent">
                        <!-- リアルイベントフォーム -->
                        <div class="tab-pane fade {{ request('type') == 'event' ? 'show active' : '' }}" id="event-form" role="tabpanel" aria-labelledby="event-tab">
                            <form action="{{ route('admin.qrcodes.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="qr_type" value="event">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_code_event">QRコード <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control @error('qr_code') is-invalid @enderror" 
                                               id="qr_code_event" 
                                               name="qr_code" 
                                               value="{{ old('qr_code') }}" 
                                               required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateQrCode('qr_code_event')">
                                                <i class="fas fa-magic"></i> 自動生成
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">QRコードの文字列を入力するか、自動生成ボタンを使用してください。</small>
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
                                           value="{{ old('qr_title') }}" 
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
                                           value="{{ old('qr_artist_name') }}" 
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
                                           value="{{ old('qr_points', 0) }}" 
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
                                           value="{{ old('qr_expires_at') }}">
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
                                              placeholder="イベントの詳細説明を入力してください">{{ old('qr_description') }}</textarea>
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
                                              placeholder="イベントの詳細内容、参加方法などを入力してください">{{ old('qr_contents') }}</textarea>
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
                                               {{ old('qr_is_active', true) ? 'checked' : '' }}>
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
                                                       {{ old('qr_options_enabled') ? 'checked' : '' }}
                                                       onchange="toggleOptionsSection('event')">
                                                <label class="custom-control-label" for="qr_options_enabled_event">
                                                    オプション機能を有効にする
                                                </label>
                                            </div>
                                        </div>

                                        <div id="options-section-event" class="{{ old('qr_options_enabled') ? '' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>アバター表情オプション（最大5種類）</label>
                                                        <div id="avatar-expressions-event">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_avatar_expressions[]" placeholder="例: 笑顔" value="{{ old('qr_avatar_expressions.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
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
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_avatar_actions[]" placeholder="例: ダンス" value="{{ old('qr_avatar_actions.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
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
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_background_colors[]" placeholder="例: 青空" value="{{ old('qr_background_colors.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
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
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_effects[]" placeholder="例: キラキラ" value="{{ old('qr_effects.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
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
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_sounds[]" placeholder="例: ファンファーレ" value="{{ old('qr_sounds.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
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
                                        <i class="fas fa-music"></i> リアルイベントQRコードを作成
                                    </button>
                                    <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ゲームグッズ（仮）フォーム -->
                <div class="tab-pane fade {{ request('type') == 'goods' ? 'show active' : '' }}" id="goods-form" role="tabpanel" aria-labelledby="goods-tab">
                    <form action="{{ route('admin.qrcodes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="qr_type" value="goods">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qr_code_goods">QRコード <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control @error('qr_code') is-invalid @enderror" 
                                               id="qr_code_goods" 
                                               name="qr_code" 
                                               value="{{ old('qr_code') }}" 
                                               required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateQrCode('qr_code_goods')">
                                                <i class="fas fa-magic"></i> 自動生成
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">QRコードの文字列を入力するか、自動生成ボタンを使用してください。</small>
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
                                           value="{{ old('qr_title') }}" 
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
                                           value="{{ old('qr_points', 0) }}" 
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
                                           value="{{ old('qr_expires_at') }}">
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
                                              placeholder="アイテムの詳細説明を入力してください">{{ old('qr_description') }}</textarea>
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
                                              placeholder="アイテムの詳細内容、使用方法などを入力してください">{{ old('qr_contents') }}</textarea>
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
                                               {{ old('qr_is_active', true) ? 'checked' : '' }}>
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
                                               {{ old('qr_is_multiple') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="qr_is_multiple_goods">
                                            複数回使用可能
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                       id="qr_options_enabled_goods" 
                                                       name="qr_options_enabled" 
                                                       value="1" 
                                                       {{ old('qr_options_enabled') ? 'checked' : '' }}
                                                       onchange="toggleOptionsSection('goods')">
                                                <label class="custom-control-label" for="qr_options_enabled_goods">
                                                    オプション機能を有効にする
                                                </label>
                                            </div>
                                        </div>

                                        <div id="options-section-goods" class="{{ old('qr_options_enabled') ? '' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>アバター表情オプション（最大5種類）</label>
                                                        <div id="avatar-expressions-goods">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_avatar_expressions[]" placeholder="例: 笑顔" value="{{ old('qr_avatar_expressions.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('avatar-expressions-goods', 'qr_avatar_expressions[]', 'アバター表情')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>アバター行動オプション（最大5種類）</label>
                                                        <div id="avatar-actions-goods">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_avatar_actions[]" placeholder="例: ダンス" value="{{ old('qr_avatar_actions.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('avatar-actions-goods', 'qr_avatar_actions[]', 'アバター行動')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>背景色オプション（最大5種類）</label>
                                                        <div id="background-colors-goods">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_background_colors[]" placeholder="例: 青空" value="{{ old('qr_background_colors.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('background-colors-goods', 'qr_background_colors[]', '背景色')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>エフェクトオプション（最大5種類）</label>
                                                        <div id="effects-goods">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_effects[]" placeholder="例: キラキラ" value="{{ old('qr_effects.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('effects-goods', 'qr_effects[]', 'エフェクト')">
                                                            <i class="fas fa-plus"></i> 追加
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>サウンドオプション（最大5種類）</label>
                                                        <div id="sounds-goods">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="qr_sounds[]" placeholder="例: ファンファーレ" value="{{ old('qr_sounds.0') }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('sounds-goods', 'qr_sounds[]', 'サウンド')">
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-gift"></i> ゲームグッズ（仮）QRコードを作成
                                    </button>
                                    <a href="{{ route('admin.qrcodes') }}" class="btn btn-secondary">
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
function generateQrCode(elementId) {
    // QRコードを自動生成
    fetch('{{ route("admin.qrcodes.generate") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(elementId).value = data.qr_code;
        } else {
            alert('QRコードの生成に失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('QRコードの生成中にエラーが発生しました。');
    });
}

// オプション機能の表示/非表示切り替え
function toggleOptionsSection(type) {
    const checkbox = document.getElementById(`qr_options_enabled_${type}`);
    const optionsSection = document.getElementById(`options-section-${type}`);
    
    if (checkbox.checked) {
        optionsSection.classList.remove('d-none');
    } else {
        optionsSection.classList.add('d-none');
    }
}

// オプション項目の追加
function addOption(containerId, name, label) {
    const container = document.getElementById(containerId);
    const inputs = container.querySelectorAll('input[name="' + name + '"]');
    
    if (inputs.length >= 5) {
        alert(`${label}は最大5種類まで設定できます。`);
        return;
    }
    
    const newInput = document.createElement('div');
    newInput.className = 'input-group mb-2';
    newInput.innerHTML = `
        <input type="text" class="form-control" name="${name}" placeholder="例: ${label}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">削除</button>
        </div>
    `;
    
    container.appendChild(newInput);
}

// オプション項目の削除
function removeOption(button) {
    const inputGroup = button.closest('.input-group');
    inputGroup.remove();
}

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
                $('#goods-form .custom-control-input').prop('checked', false);
                $('#options-section-goods').addClass('d-none');
            } else if (target === 'goods') {
                // ゲームグッズタブが選択された場合
                $('#event-form input, #event-form textarea').val('');
                $('#event-form .custom-control-input').prop('checked', false);
                $('#options-section-event').addClass('d-none');
            }
        });
    } else {
        console.warn('jQuery is not available');
    }
});
</script>
@endsection
