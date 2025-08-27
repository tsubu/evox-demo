<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    /**
     * 国一覧を取得
     */
    public function getCountries(): JsonResponse
    {
        $countries = [
            ['code' => 'JP', 'name' => '日本', 'phone_code' => '+81'],
            ['code' => 'TW', 'name' => '台湾', 'phone_code' => '+886'],
            ['code' => 'HK', 'name' => '香港', 'phone_code' => '+852'],
            ['code' => 'US', 'name' => 'アメリカ', 'phone_code' => '+1'],
            ['code' => 'FR', 'name' => 'フランス', 'phone_code' => '+33'],
            ['code' => 'DE', 'name' => 'ドイツ', 'phone_code' => '+49'],
            ['code' => 'CN', 'name' => '中国', 'phone_code' => '+86'],
            ['code' => 'KR', 'name' => '韓国', 'phone_code' => '+82'],
            ['code' => 'MO', 'name' => 'マカオ', 'phone_code' => '+853'],
            ['code' => 'SG', 'name' => 'シンガポール', 'phone_code' => '+65'],
            ['code' => 'MY', 'name' => 'マレーシア', 'phone_code' => '+60'],
            ['code' => 'TH', 'name' => 'タイ', 'phone_code' => '+66'],
            ['code' => 'VN', 'name' => 'ベトナム', 'phone_code' => '+84'],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'countries' => $countries
            ]
        ]);
    }

    /**
     * IPから国を検出
     */
    public function detectCountry(Request $request): JsonResponse
    {
        // 仮の実装（デフォルトで日本を返す）
        $country = [
            'code' => 'JP',
            'name' => '日本',
            'phone_code' => '+81'
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'country' => $country
            ]
        ]);
    }
}
