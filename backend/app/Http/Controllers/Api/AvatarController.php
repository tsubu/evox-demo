<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\AvatarHelper;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    /**
     * 利用可能なアバターの一覧を取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $avatars = AvatarHelper::getAvailableAvatars();
        
        $avatarList = [];
        foreach ($avatars as $id => $avatar) {
            $avatarList[] = [
                'id' => $id,
                'name' => $avatar['name'],
                'description' => $avatar['description'],
                'image' => AvatarHelper::getAvatarImagePath($id),
            ];
        }
        
        return response()->json([
            'success' => true,
            'avatars' => $avatarList,
            'default' => AvatarHelper::getDefaultAvatar(),
        ]);
    }

    /**
     * 特定のアバター情報を取得
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if (!AvatarHelper::avatarExists($id)) {
            return response()->json([
                'success' => false,
                'message' => 'アバターが見つかりません。'
            ], 404);
        }
        
        $avatars = config('avatars.avatars');
        $avatar = $avatars[$id];
        
        return response()->json([
            'success' => true,
            'avatar' => [
                'id' => $id,
                'name' => $avatar['name'],
                'description' => $avatar['description'],
                'image' => AvatarHelper::getAvatarImagePath($id),
            ]
        ]);
    }
}



