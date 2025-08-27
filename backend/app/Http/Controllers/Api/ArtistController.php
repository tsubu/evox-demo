<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ActiveArtist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    /**
     * アーティスト一覧を取得
     */
    public function index()
    {
        try {
            $artists = Artist::select('id', 'artist_name as name', 'artist_description as description')
                ->orderBy('artist_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $artists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'アーティスト一覧の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 現在アクティブなアーティストを取得
     */
    public function getActiveArtist()
    {
        try {
            $activeArtist = ActiveArtist::getCurrentActive();
            
            if (!$activeArtist) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => null,
                        'name' => 'T-BOLAN',
                        'description' => 'デフォルトアーティスト'
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activeArtist->artist_id,
                    'name' => $activeArtist->artist_name,
                    'description' => $activeArtist->artist->artist_description ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'アクティブアーティストの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * アーティストをアクティブ化
     */
    public function activateArtist(Request $request)
    {
        try {
            $request->validate([
                'artist_id' => 'required|integer|exists:artists,id'
            ]);

            $artist = Artist::findOrFail($request->artist_id);
            
            // 既存のアクティブアーティストを非アクティブ化
            ActiveArtist::deactivateArtist();
            
            // 新しいアーティストをアクティブ化
            ActiveArtist::activateArtist($artist->id, $artist->artist_name);
            
            return response()->json([
                'success' => true,
                'message' => "アーティスト「{$artist->artist_name}」をアクティブ化しました",
                'data' => [
                    'id' => $artist->id,
                    'name' => $artist->artist_name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'アーティストのアクティブ化に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * アーティストを非アクティブ化
     */
    public function deactivateArtist()
    {
        try {
            ActiveArtist::deactivateArtist();
            
            return response()->json([
                'success' => true,
                'message' => 'アーティストを非アクティブ化しました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'アーティストの非アクティブ化に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
