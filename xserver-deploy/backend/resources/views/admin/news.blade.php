@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">ニュース管理</h3>
                    <div>
                        <a href="{{ route('admin.news.add-test-data') }}" class="btn btn-warning me-2" onclick="return confirm('テスト用ニュース記事を6件追加しますか？')">
                            <i class="fas fa-database"></i> テストデータ追加
                        </a>
                        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> 新規作成
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- 統計情報 -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-newspaper"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">総記事数</span>
                                    <span class="info-box-number">{{ $totalNews }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">公開済み</span>
                                    <span class="info-box-number">{{ $publishedNews }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">下書き</span>
                                    <span class="info-box-number">{{ $draftNews }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ニュース一覧テーブル -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>画像</th>
                                    <th>タイトル</th>
                                    <th>内容</th>
                                    <th>ステータス</th>
                                    <th>作成日</th>
                                    <th>公開日</th>
                                    <th>公開開始日時</th>
                                    <th>公開終了日時</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($news as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @if($item->gamenews_image_url)
                                            <img src="{{ $item->gamenews_image_url }}" alt="ニュース画像" class="img-fluid" style="max-width: 80px; max-height: 60px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">画像なし</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->gamenews_title }}</td>
                                    <td>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item->gamenews_content }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->gamenews_is_published)
                                            <span class="badge badge-success">公開</span>
                                        @else
                                            <span class="badge badge-warning">下書き</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->gamenews_created_at->format('Y年m月d日 H:i') }}</td>
                                    <td>{{ $item->gamenews_published_at ? $item->gamenews_published_at->format('Y年m月d日 H:i') : '-' }}</td>
                                    <td>{{ $item->gamenews_publish_start_at ? $item->gamenews_publish_start_at->format('Y年m月d日 H:i') : 'すぐに公開' }}</td>
                                    <td>{{ $item->gamenews_publish_end_at ? $item->gamenews_publish_end_at->format('Y年m月d日 H:i') : '永続公開' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.news.edit', $item->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> 編集
                                            </a>
                                            <form action="{{ route('admin.news.destroy', $item->id) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('このニュース記事を削除しますか？この操作は取り消せません。')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> 削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">ニュース記事が登録されていません。</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ページネーション -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $news->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
