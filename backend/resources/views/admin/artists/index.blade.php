@extends('admin.layout')

@section('title', 'アーティスト管理')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>アーティスト管理</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.artists.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> 新規アーティスト追加
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
                    <h3 class="card-title">B ZONE所属アーティスト一覧</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>アーティスト名</th>
                                <th>説明</th>
                                <th>色</th>
                                <th>パターン</th>
                                <th>BPM</th>
                                <th>楽曲数</th>
                                <th>ステータス</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($artists as $artist)
                            <tr>
                                <td>{{ $artist->id }}</td>
                                <td>{{ $artist->artist_name }}</td>
                                <td>{{ Str::limit($artist->artist_description, 50) }}</td>
                                <td>
                                    <div style="width: 30px; height: 20px; background-color: {{ $artist->artist_color }}; border: 1px solid #ccc;"></div>
                                    {{ $artist->artist_color }}
                                </td>
                                <td>{{ $artist->artist_pattern }}</td>
                                <td>{{ $artist->artist_bpm }}</td>
                                <td>{{ $artist->songs->count() }}</td>
                                <td>
                                    @if($artist->artist_is_active)
                                        <span class="badge badge-success">アクティブ</span>
                                    @else
                                        <span class="badge badge-secondary">非アクティブ</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.artists.edit', $artist->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> 編集
                                        </a>
                                        <a href="{{ route('admin.artists.songs', $artist->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-music"></i> 楽曲管理
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
