<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    /**
     * アーティスト一覧表示
     */
    public function index()
    {
        $artists = Artist::with('songs')->orderBy('artist_name')->get();
        return view('admin.artists.index', compact('artists'));
    }

    /**
     * アーティスト作成画面
     */
    public function create()
    {
        return view('admin.artists.create');
    }

    /**
     * アーティスト保存
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'artist_name' => 'required|string|max:255|unique:artists',
            'artist_description' => 'nullable|string',
            'artist_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'artist_pattern' => 'required|string|in:solid,blink,fade,wave,rainbow,pulse',
            'artist_brightness' => 'required|integer|min:1|max:100',
            'artist_bpm' => 'required|integer|min:60|max:200',
            'artist_intensity' => 'required|string|in:low,medium,high',
            'artist_color_scheme' => 'required|string|in:artist,rainbow,monochrome'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Artist::create($request->all());

        return redirect()->route('admin.artists.index')
            ->with('success', 'アーティストが正常に作成されました。');
    }

    /**
     * アーティスト編集画面
     */
    public function edit($id)
    {
        $artist = Artist::findOrFail($id);
        return view('admin.artists.edit', compact('artist'));
    }

    /**
     * アーティスト更新
     */
    public function update(Request $request, $id)
    {
        $artist = Artist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'artist_name' => 'required|string|max:255|unique:artists,artist_name,' . $id,
            'artist_description' => 'nullable|string',
            'artist_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'artist_pattern' => 'required|string|in:solid,blink,fade,wave,rainbow,pulse',
            'artist_brightness' => 'required|integer|min:1|max:100',
            'artist_bpm' => 'required|integer|min:60|max:200',
            'artist_intensity' => 'required|string|in:low,medium,high',
            'artist_color_scheme' => 'required|string|in:artist,rainbow,monochrome'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $artist->update($request->all());

        return redirect()->route('admin.artists.index')
            ->with('success', 'アーティストが正常に更新されました。');
    }

    /**
     * アーティスト削除
     */
    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $artist->delete();

        return redirect()->route('admin.artists.index')
            ->with('success', 'アーティストが正常に削除されました。');
    }

    /**
     * アーティストの楽曲一覧
     */
    public function songs($id)
    {
        $artist = Artist::findOrFail($id);
        $artist->load(['songs' => function($query) {
            $query->orderBy('updated_at', 'desc');
        }]);
        return view('admin.artists.songs', compact('artist'));
    }

    /**
     * 楽曲作成画面
     */
    public function createSong($artistId)
    {
        $artist = Artist::findOrFail($artistId);
        return view('admin.songs.create', compact('artist'));
    }

    /**
     * 楽曲保存
     */
    public function storeSong(Request $request, $artistId)
    {
        $artist = Artist::findOrFail($artistId);

        $validator = Validator::make($request->all(), [
            'song_name' => 'required|string|max:255|unique:songs,song_name,NULL,id,artist_id,' . $artistId,
            'song_description' => 'nullable|string',
            'song_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'song_pattern' => 'required|string|in:solid,blink,fade,wave,rainbow,pulse',
            'song_brightness' => 'required|integer|min:1|max:100',
            'song_bpm' => 'required|integer|min:60|max:200',
            'song_intensity' => 'required|string|in:low,medium,high',
            'song_color_scheme' => 'required|string|in:artist,rainbow,monochrome'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $request->merge(['artist_id' => $artistId]);
        Song::create($request->all());

        return redirect()->route('admin.artists.songs', $artistId)
            ->with('success', '楽曲が正常に作成されました。');
    }

    /**
     * 楽曲編集画面
     */
    public function editSong($artistId, $songId)
    {
        $artist = Artist::findOrFail($artistId);
        $song = Song::where('artist_id', $artistId)->findOrFail($songId);
        return view('admin.songs.edit', compact('artist', 'song'));
    }

    /**
     * 楽曲更新
     */
    public function updateSong(Request $request, $artistId, $songId)
    {
        $song = Song::where('artist_id', $artistId)->findOrFail($songId);

        $validator = Validator::make($request->all(), [
            'song_name' => 'required|string|max:255|unique:songs,song_name,' . $songId . ',id,artist_id,' . $artistId,
            'song_description' => 'nullable|string',
            'song_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'song_pattern' => 'required|string|in:solid,blink,fade,wave,rainbow,pulse',
            'song_brightness' => 'required|integer|min:1|max:100',
            'song_bpm' => 'required|integer|min:60|max:200',
            'song_intensity' => 'required|string|in:low,medium,high',
            'song_color_scheme' => 'required|string|in:artist,rainbow,monochrome'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $song->update($request->all());

        return redirect()->route('admin.artists.songs', $artistId)
            ->with('success', '楽曲が正常に更新されました。');
    }

    /**
     * 楽曲削除
     */
    public function destroySong($artistId, $songId)
    {
        $song = Song::where('artist_id', $artistId)->findOrFail($songId);
        $song->delete();

        return redirect()->route('admin.artists.songs', $artistId)
            ->with('success', '楽曲が正常に削除されました。');
    }

    /**
     * アーティスト一覧をJSONで取得（API用）
     */
    public function apiList()
    {
        $artists = Artist::active()->orderBy('artist_name')->get();
        return response()->json($artists);
    }

    /**
     * アーティストの楽曲一覧をJSONで取得（API用）
     */
    public function apiSongsList($id)
    {
        $artist = Artist::findOrFail($id);
        $songs = $artist->activeSongs()->orderBy('song_name')->get();
        return response()->json($songs);
    }
}
