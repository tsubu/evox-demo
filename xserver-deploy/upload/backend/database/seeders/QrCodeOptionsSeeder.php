<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QrCode;

class QrCodeOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // デフォルトのオプション選択肢を定義
        $defaultOptions = [
            'qr_avatar_expressions' => [
                '笑顔',
                '怒り',
                '悲しみ',
                '驚き',
                '普通'
            ],
            'qr_avatar_actions' => [
                'ダンス',
                'ジャンプ',
                '手を振る',
                '座る',
                '走る'
            ],
            'qr_background_colors' => [
                '青空',
                '夕日',
                '星空',
                '森',
                '海'
            ],
            'qr_effects' => [
                'キラキラ',
                '虹',
                '花火',
                '雪',
                '雨'
            ],
            'qr_sounds' => [
                'ファンファーレ',
                '拍手',
                '音楽',
                '効果音',
                '無音'
            ]
        ];

        // 既存のQRコードにデフォルトオプションを設定（オプションが未設定の場合のみ）
        $qrCodes = QrCode::whereNull('qr_avatar_expressions')
            ->orWhereNull('qr_avatar_actions')
            ->orWhereNull('qr_background_colors')
            ->orWhereNull('qr_effects')
            ->orWhereNull('qr_sounds')
            ->get();

        foreach ($qrCodes as $qrCode) {
            $updateData = [];
            
            // 各オプションが未設定の場合のみデフォルト値を設定
            if (empty($qrCode->qr_avatar_expressions)) {
                $updateData['qr_avatar_expressions'] = $defaultOptions['qr_avatar_expressions'];
            }
            if (empty($qrCode->qr_avatar_actions)) {
                $updateData['qr_avatar_actions'] = $defaultOptions['qr_avatar_actions'];
            }
            if (empty($qrCode->qr_background_colors)) {
                $updateData['qr_background_colors'] = $defaultOptions['qr_background_colors'];
            }
            if (empty($qrCode->qr_effects)) {
                $updateData['qr_effects'] = $defaultOptions['qr_effects'];
            }
            if (empty($qrCode->qr_sounds)) {
                $updateData['qr_sounds'] = $defaultOptions['qr_sounds'];
            }
            
            if (!empty($updateData)) {
                $qrCode->update($updateData);
            }
        }

        $this->command->info('デフォルトのオプション選択肢を設定しました。');
    }
}
