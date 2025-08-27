<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QrTheme;

class QrThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'theme_name' => 'ロック',
                'theme_description' => 'ロックミュージックに特化したテーマ',
                'theme_color' => '#DC2626',
                'theme_icon' => '🎸',
                'theme_expressions' => ['熱狂', '興奮', '怒り', '集中', '解放'],
                'theme_actions' => ['ヘッドバン', 'ジャンプ', '拳を振り上げる', 'ギターを弾く', '叫ぶ'],
                'theme_backgrounds' => ['ステージ', '観客', 'ライト', '煙', '暗闇'],
                'theme_effects' => ['閃光', '爆発', '雷', '炎', '衝撃波'],
                'theme_sounds' => ['ギターリフ', 'ドラムビート', '歓声', '爆音', 'サイレン'],
            ],
            [
                'theme_name' => 'ポップ',
                'theme_description' => 'ポップミュージックに特化したテーマ',
                'theme_color' => '#EC4899',
                'theme_icon' => '🎤',
                'theme_expressions' => ['笑顔', '可愛い', '元気', '明るい', '楽しい'],
                'theme_actions' => ['ダンス', '手を振る', 'ハートサイン', 'キス', 'ジャンプ'],
                'theme_backgrounds' => ['虹', '花', '星', '雲', '光'],
                'theme_effects' => ['キラキラ', 'ハート', '花びら', '虹', '光の粒'],
                'theme_sounds' => ['ポップチューン', '拍手', '笑い声', '鈴の音', 'ファンファーレ'],
            ],
            [
                'theme_name' => 'クラシック',
                'theme_description' => 'クラシック音楽に特化したテーマ',
                'theme_color' => '#8B5CF6',
                'theme_icon' => '🎻',
                'theme_expressions' => ['優雅', '落ち着いた', '感動', '集中', '美しい'],
                'theme_actions' => ['拍手', '静聴', 'お辞儀', '手を胸に', '目を閉じる'],
                'theme_backgrounds' => ['コンサートホール', '教会', '宮殿', '庭園', '夕日'],
                'theme_effects' => ['光の柱', '音符', 'オーラ', '霧', '星屑'],
                'theme_sounds' => ['オーケストラ', 'バイオリン', 'ピアノ', '静寂', '拍手'],
            ],
            [
                'theme_name' => 'スポーツ',
                'theme_description' => 'スポーツイベントに特化したテーマ',
                'theme_color' => '#059669',
                'theme_icon' => '⚽',
                'theme_expressions' => ['闘志', '集中', '勝利', '悔しさ', '達成感'],
                'theme_actions' => ['応援', 'ハイタッチ', 'ガッツポーズ', '走る', 'ジャンプ'],
                'theme_backgrounds' => ['スタジアム', 'フィールド', '観客席', '空', 'グラウンド'],
                'theme_effects' => ['火花', '風', '土煙', '歓声', '旗'],
                'theme_sounds' => ['応援歌', '笛', '歓声', '拍手', 'ファンファーレ'],
            ],
            [
                'theme_name' => 'アニメ',
                'theme_description' => 'アニメ・ゲームに特化したテーマ',
                'theme_color' => '#3B82F6',
                'theme_icon' => '🎮',
                'theme_expressions' => ['かわいい', 'かっこいい', '驚き', '決意', '笑顔'],
                'theme_actions' => ['ポーズ', '魔法', '変身', '走る', '戦う'],
                'theme_backgrounds' => ['ファンタジー世界', '都市', '森', '空', '異世界'],
                'theme_effects' => ['魔法陣', '光の剣', '炎', '雷', '虹'],
                'theme_sounds' => ['BGM', '効果音', '魔法音', '戦闘音', '勝利音'],
            ],
        ];

        foreach ($themes as $theme) {
            QrTheme::create($theme);
        }
    }
}
