@extends('admin.layout')

@section('title', '楽曲管理')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ $artist->artist_name }} - 楽曲管理</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> アーティスト一覧に戻る
            </a>
            <a href="{{ route('admin.songs.create', $artist->id) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> 新規楽曲追加
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $artist->artist_name }}の楽曲一覧</h3>
                </div>
                <div class="card-body">
                    @if($artist->songs->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>楽曲名</th>
                                    <th>説明</th>
                                    <th>色</th>
                                    <th>パターン</th>
                                    <th>BPM</th>
                                    <th>ステータス</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($artist->songs as $song)
                                <tr>
                                    <td>{{ $song->id }}</td>
                                    <td>{{ $song->song_name }}</td>
                                    <td>{{ Str::limit($song->song_description, 50) }}</td>
                                    <td>
                                        <div style="width: 30px; height: 20px; background-color: {{ $song->song_color }}; border: 1px solid #ccc;"></div>
                                        {{ $song->song_color }}
                                    </td>
                                    <td>{{ $song->song_pattern }}</td>
                                    <td>{{ $song->song_bpm }}</td>
                                    <td>
                                        @if($song->song_is_active)
                                            <span class="badge badge-success">アクティブ</span>
                                        @else
                                            <span class="badge badge-secondary">非アクティブ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.songs.edit', [$artist->id, $song->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> 編集
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-music fa-3x text-muted mb-3"></i>
                            <p class="text-muted">このアーティストには楽曲が登録されていません。</p>
                            <a href="{{ route('admin.songs.create', $artist->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> 最初の楽曲を追加
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
