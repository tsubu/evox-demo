<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = NewsItem::orderBy('gamenews_created_at', 'desc')->paginate(20);
        $totalNews = NewsItem::count();
        $publishedNews = NewsItem::published()->count();
        $draftNews = NewsItem::where('gamenews_is_published', false)->count();

        return view('admin.news', compact('news', 'totalNews', 'publishedNews', 'draftNews'));
    }

    public function create()
    {
        return view('admin.news-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gamenews_title' => 'required|string|max:255',
            'gamenews_content' => 'required|string',
            'gamenews_is_published' => 'boolean',
            'gamenews_publish_start_at' => 'nullable|date',
            'gamenews_publish_end_at' => 'nullable|date|after:gamenews_publish_start_at',
            'gamenews_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageUrl = null;
        if ($request->hasFile('gamenews_image')) {
            $image = $request->file('gamenews_image');
            $imageName = 'news_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public/news', $imageName);
            $imageUrl = Storage::url($imagePath);
        }

        NewsItem::create([
            'gamenews_title' => $request->gamenews_title,
            'gamenews_content' => $request->gamenews_content,
            'gamenews_image_url' => $imageUrl,
            'gamenews_is_published' => $request->has('gamenews_is_published'),
            'gamenews_published_at' => $request->has('gamenews_is_published') ? now() : null,
            'gamenews_publish_start_at' => $request->gamenews_publish_start_at ? \Carbon\Carbon::parse($request->gamenews_publish_start_at) : null,
            'gamenews_publish_end_at' => $request->gamenews_publish_end_at ? \Carbon\Carbon::parse($request->gamenews_publish_end_at) : null
        ]);

        return redirect()->route('admin.news')->with('success', 'ニュース記事を作成しました。');
    }

    public function edit($id)
    {
        $news = NewsItem::findOrFail($id);
        return view('admin.news-edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'gamenews_title' => 'required|string|max:255',
            'gamenews_content' => 'required|string',
            'gamenews_is_published' => 'boolean',
            'gamenews_publish_start_at' => 'nullable|date',
            'gamenews_publish_end_at' => 'nullable|date|after:gamenews_publish_start_at',
            'gamenews_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $news = NewsItem::findOrFail($id);
        
        $imageUrl = $news->gamenews_image_url;
        if ($request->hasFile('gamenews_image')) {
            // 古い画像を削除
            if ($news->gamenews_image_url) {
                $oldImagePath = str_replace('/storage/', 'public/', $news->gamenews_image_url);
                Storage::delete($oldImagePath);
            }
            
            // 新しい画像をアップロード
            $image = $request->file('gamenews_image');
            $imageName = 'news_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public/news', $imageName);
            $imageUrl = Storage::url($imagePath);
        }

        $news->update([
            'gamenews_title' => $request->gamenews_title,
            'gamenews_content' => $request->gamenews_content,
            'gamenews_image_url' => $imageUrl,
            'gamenews_is_published' => $request->has('gamenews_is_published'),
            'gamenews_published_at' => $request->has('gamenews_is_published') && !$news->gamenews_is_published ? now() : $news->gamenews_published_at,
            'gamenews_publish_start_at' => $request->gamenews_publish_start_at ? \Carbon\Carbon::parse($request->gamenews_publish_start_at) : null,
            'gamenews_publish_end_at' => $request->gamenews_publish_end_at ? \Carbon\Carbon::parse($request->gamenews_publish_end_at) : null
        ]);

        return redirect()->route('admin.news')->with('success', 'ニュース記事を更新しました。');
    }

    public function destroy($id)
    {
        $news = NewsItem::findOrFail($id);
        $news->delete();

        return redirect()->route('admin.news')->with('success', 'ニュース記事を削除しました。');
    }

    public function addTestData()
    {
        $testNews = [
            [
                'gamenews_title' => 'EvoXアプリ正式リリースのお知らせ',
                'gamenews_content' => 'EvoXアプリが正式にリリースされました。新しい機能や改善されたユーザーインターフェースをお楽しみください。今後も継続的にアップデートを行い、より良いサービスを提供してまいります。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now(),
                'gamenews_publish_start_at' => now(),
                'gamenews_publish_end_at' => null
            ],
            [
                'gamenews_title' => 'メンテナンスのお知らせ（2024年1月15日）',
                'gamenews_content' => '2024年1月15日（月）の午前2時から4時まで、システムメンテナンスを実施いたします。この間はサービスをご利用いただけません。ご不便をおかけしますが、ご理解いただけますようお願いいたします。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now()->subDays(2),
                'gamenews_publish_start_at' => now()->subDays(2),
                'gamenews_publish_end_at' => now()->addDays(30)
            ],
            [
                'gamenews_title' => '新機能「QRコード連携」追加のお知らせ',
                'gamenews_content' => 'EvoXアプリに新機能「QRコード連携」が追加されました。この機能により、イベント会場でのQRコードスキャンがより簡単になり、リアルタイムでの情報共有が可能になります。ぜひお試しください。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now()->subDays(5),
                'gamenews_publish_start_at' => now()->subDays(5),
                'gamenews_publish_end_at' => null
            ],
            [
                'gamenews_title' => 'ユーザー登録機能の改善について',
                'gamenews_content' => 'ユーザー登録機能を改善いたしました。SMS認証による安全な登録プロセスを導入し、より簡単で安全なアカウント作成が可能になりました。既存のユーザーの皆様には影響ありません。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now()->subDays(7),
                'gamenews_publish_start_at' => now()->subDays(7),
                'gamenews_publish_end_at' => now()->addDays(60)
            ],
            [
                'gamenews_title' => 'プライバシーポリシーの更新について',
                'gamenews_content' => 'プライバシーポリシーを更新いたしました。新しい機能に対応し、より詳細な個人情報の取り扱いについて明記いたしました。ご利用前に必ずご確認ください。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now()->subDays(10),
                'gamenews_publish_start_at' => now()->subDays(10),
                'gamenews_publish_end_at' => null
            ],
            [
                'gamenews_title' => 'EvoXアプリの今後の開発予定',
                'gamenews_content' => 'EvoXアプリの今後の開発予定をお知らせいたします。今後、AI機能の強化、ソーシャル機能の追加、パフォーマンスの改善などを予定しております。ユーザーの皆様からのご意見もお待ちしております。',
                'gamenews_is_published' => true,
                'gamenews_published_at' => now()->subDays(12),
                'gamenews_publish_start_at' => now()->subDays(12),
                'gamenews_publish_end_at' => now()->addDays(90)
            ]
        ];

        foreach ($testNews as $news) {
            NewsItem::create($news);
        }

        return redirect()->route('admin.news')->with('success', 'テスト用ニュース記事を6件追加しました。');
    }
}
