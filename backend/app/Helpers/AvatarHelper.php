<?php

namespace App\Helpers;

class AvatarHelper
{
    /**
     * 利用可能なアバターの一覧を取得
     *
     * @return array
     */
    public static function getAvailableAvatars()
    {
        $avatars = config('avatars.avatars');
        return array_filter($avatars, function ($avatar) {
            return $avatar['available'] ?? true;
        });
    }

    /**
     * アバターの名前を取得
     *
     * @param string $avatarId
     * @return string
     */
    public static function getAvatarName($avatarId)
    {
        $avatars = config('avatars.avatars');
        return $avatars[$avatarId]['name'] ?? $avatarId;
    }

    /**
     * アバターの説明を取得
     *
     * @param string $avatarId
     * @return string
     */
    public static function getAvatarDescription($avatarId)
    {
        $avatars = config('avatars.avatars');
        return $avatars[$avatarId]['description'] ?? '';
    }

    /**
     * アバターの画像パスを取得
     *
     * @param string $avatarId
     * @return string
     */
    public static function getAvatarImagePath($avatarId)
    {
        $avatars = config('avatars.avatars');
        $imagePath = config('avatars.image_path');
        $extension = config('avatars.image_extension');
        
        if (isset($avatars[$avatarId])) {
            return $imagePath . $avatars[$avatarId]['image'];
        }
        
        // アバターが見つからない場合は、IDから直接パスを生成
        return $imagePath . $avatarId . $extension;
    }

    /**
     * アバターが存在するかチェック
     *
     * @param string $avatarId
     * @return bool
     */
    public static function avatarExists($avatarId)
    {
        $avatars = config('avatars.avatars');
        return isset($avatars[$avatarId]) && ($avatars[$avatarId]['available'] ?? true);
    }

    /**
     * デフォルトアバターIDを取得
     *
     * @return string
     */
    public static function getDefaultAvatar()
    {
        return config('avatars.default', 'car001');
    }

    /**
     * アバター選択用のオプション配列を取得
     *
     * @return array
     */
    public static function getAvatarOptions()
    {
        $avatars = self::getAvailableAvatars();
        $options = [];
        
        foreach ($avatars as $id => $avatar) {
            $options[$id] = $avatar['name'];
        }
        
        return $options;
    }
}



