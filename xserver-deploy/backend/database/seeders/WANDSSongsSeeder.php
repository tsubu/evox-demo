<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class WANDSSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wands = Artist::where('artist_name', 'WANDS')->first();
        
        if (!$wands) {
            $this->command->error('WANDSが見つかりません。');
            return;
        }

        // 既存のWANDSの楽曲を削除
        Song::where('artist_id', $wands->id)->delete();
        $this->command->info('既存のWANDSの楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => '寂しさは秋の色', 'year' => 1991, 'color' => '#E6FFE6', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'ふりむいて抱きしめて', 'year' => 1991, 'color' => '#F0FFF0', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 1992, 'color' => '#FAFFFA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 1992, 'color' => '#E6FFE6', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 1992, 'color' => '#CCFFCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 1994, 'color' => '#B3FFB3', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 1994, 'color' => '#99FF99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 1994, 'color' => '#80FF80', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 1995, 'color' => '#66FF66', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 1995, 'color' => '#4DFF4D', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 1995, 'color' => '#33FF33', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 1995, 'color' => '#1AFF1A', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 1996, 'color' => '#00FF00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 1996, 'color' => '#11FF11', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 1996, 'color' => '#22FF22', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 1997, 'color' => '#33FF33', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 1997, 'color' => '#44FF44', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 1997, 'color' => '#55FF55', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 1998, 'color' => '#66FF66', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 1998, 'color' => '#77FF77', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 1998, 'color' => '#88FF88', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 1999, 'color' => '#99FF99', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 1999, 'color' => '#AAFFAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 1999, 'color' => '#BBFFBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2000, 'color' => '#CCFFCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2000, 'color' => '#DDFFDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2000, 'color' => '#EEFFEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2001, 'color' => '#FFFF00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2001, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2001, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2002, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2002, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2002, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2003, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2003, 'color' => '#FF8877', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2003, 'color' => '#FF7766', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2004, 'color' => '#FF6655', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2004, 'color' => '#FF5544', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2004, 'color' => '#FF4433', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2005, 'color' => '#FF3322', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2005, 'color' => '#FF2211', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2005, 'color' => '#FF1100', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2006, 'color' => '#FF0000', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2006, 'color' => '#FF1111', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2006, 'color' => '#FF2222', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2007, 'color' => '#FF3333', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2007, 'color' => '#FF4444', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2007, 'color' => '#FF5555', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2008, 'color' => '#FF6666', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2008, 'color' => '#FF7777', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2008, 'color' => '#FF8888', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2009, 'color' => '#FF9999', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2009, 'color' => '#FFAAAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2009, 'color' => '#FFBBBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2010, 'color' => '#FFCCCC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2010, 'color' => '#FFDDDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2010, 'color' => '#FFEEEE', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2011, 'color' => '#FFFFFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2011, 'color' => '#FFEEDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2011, 'color' => '#FFDDCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2012, 'color' => '#FFCCBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2012, 'color' => '#FFBBAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2012, 'color' => '#FFAA99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2013, 'color' => '#FF9988', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2013, 'color' => '#FF8877', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2013, 'color' => '#FF7766', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2014, 'color' => '#FF6655', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2014, 'color' => '#FF5544', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2014, 'color' => '#FF4433', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2015, 'color' => '#FF3322', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2015, 'color' => '#FF2211', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2015, 'color' => '#FF1100', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2016, 'color' => '#FF0000', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2016, 'color' => '#FF1111', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2016, 'color' => '#FF2222', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2017, 'color' => '#FF3333', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2017, 'color' => '#FF4444', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2017, 'color' => '#FF5555', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2018, 'color' => '#FF6666', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2018, 'color' => '#FF7777', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2018, 'color' => '#FF8888', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2019, 'color' => '#FF9999', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2019, 'color' => '#FFAAAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2019, 'color' => '#FFBBBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2020, 'color' => '#FFCCCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2020, 'color' => '#FFDDDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2020, 'color' => '#FFEEEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2021, 'color' => '#FFFFFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2021, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2021, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret Night 〜It\'s My Treat〜', 'year' => 2022, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Jumpin\' Jack Boy', 'year' => 2022, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'PIECE OF MY SOUL', 'year' => 2022, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Same Side', 'year' => 2023, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '錆びついたマシンガンで今を撃ち抜こう', 'year' => 2023, 'color' => '#FF8877', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'WORST CRIME〜About a rock star who was a swindler〜', 'year' => 2023, 'color' => '#FF7766', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '寂しさは秋の色', 'year' => 2024, 'color' => '#FF6655', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ふりむいて抱きしめて', 'year' => 2024, 'color' => '#FF5544', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'もっと強く抱きしめたなら', 'year' => 2024, 'color' => '#FF4433', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '時の扉', 'year' => 2025, 'color' => '#FF3322', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛を語るより口づけをかわそう', 'year' => 2025, 'color' => '#FF2211', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '世界が終るまでは…', 'year' => 2025, 'color' => '#FF1100', 'pattern' => 'wave', 'bpm' => 140],
        ];

        foreach ($songs as $songData) {
            Song::create([
                'artist_id' => $wands->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ')',
                'song_description' => 'WANDSの楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('WANDSの楽曲を' . count($songs) . '曲登録しました。');
    }
}
