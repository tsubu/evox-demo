<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class BZSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bz = Artist::where('artist_name', 'B\'z')->first();
        
        if (!$bz) {
            $this->command->error('B\'zが見つかりません。');
            return;
        }

        // 既存のB'zの楽曲を削除
        Song::where('artist_id', $bz->id)->delete();
        $this->command->info('既存のB\'zの楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => 'だからその手を離して', 'year' => 1988, 'color' => '#CC0000', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'BE THERE', 'year' => 1989, 'color' => '#DD0000', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => '太陽のKomachi Angel', 'year' => 1989, 'color' => '#EE0000', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Easy Come, Easy Go!', 'year' => 1990, 'color' => '#FF0000', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛しい人よGood Night...', 'year' => 1990, 'color' => '#FF1100', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'LADY NAVIGATION', 'year' => 1990, 'color' => '#FF2200', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'ALONE', 'year' => 1991, 'color' => '#FF3300', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'BLOWIN\'', 'year' => 1991, 'color' => '#FF4400', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'ZERO', 'year' => 1992, 'color' => '#FF5500', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '愛のままにわがままに 僕は君だけを傷つけない', 'year' => 1992, 'color' => '#FF6600', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '裸足の女神', 'year' => 1993, 'color' => '#FF7700', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Don\'t Leave Me', 'year' => 1993, 'color' => '#FF8800', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'MOTEL', 'year' => 1994, 'color' => '#FF9900', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ねがい', 'year' => 1994, 'color' => '#FFAA00', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'love me, I love you', 'year' => 1995, 'color' => '#FFBB00', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'LOVE PHANTOM', 'year' => 1995, 'color' => '#FFCC00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'ミエナイチカラ 〜INVISIBLE ONE〜', 'year' => 1996, 'color' => '#FFDD00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'MOVE', 'year' => 1996, 'color' => '#FFEE00', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Calling', 'year' => 1997, 'color' => '#FFFF00', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'ギリギリchop', 'year' => 1999, 'color' => '#FFFF11', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '今夜月の見える丘に', 'year' => 1999, 'color' => '#FFFF22', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'May', 'year' => 2000, 'color' => '#FFFF33', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'juice', 'year' => 2000, 'color' => '#FFFF44', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RING', 'year' => 2000, 'color' => '#FFFF55', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'ultra soul', 'year' => 2001, 'color' => '#FFFF66', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'GOLD', 'year' => 2001, 'color' => '#FFFF77', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '野性のENERGY', 'year' => 2002, 'color' => '#FFFF88', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'IT\'S SHOWTIME!!', 'year' => 2002, 'color' => '#FFFF99', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'ARIGATO', 'year' => 2003, 'color' => '#FFFFAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'BANZAI', 'year' => 2003, 'color' => '#FFFFBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛のバクダン', 'year' => 2004, 'color' => '#FFFFCC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'OCEAN', 'year' => 2005, 'color' => '#FFFFDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '衝動', 'year' => 2006, 'color' => '#FFFFEE', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ゆるぎないものひとつ', 'year' => 2006, 'color' => '#FFFFFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'SPLASH!', 'year' => 2007, 'color' => '#FFEEDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '永遠の翼', 'year' => 2007, 'color' => '#FFDDCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'BURN -フメツノフェイス-', 'year' => 2008, 'color' => '#FFCCBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'MY LONELY TOWN', 'year' => 2009, 'color' => '#FFBBAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'イチブトゼンブ', 'year' => 2009, 'color' => '#FFAA99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'さよなら傷だらけの日々よ', 'year' => 2010, 'color' => '#FF9988', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Don\'t Wanna Lie', 'year' => 2011, 'color' => '#FF8877', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'GO FOR IT, BABY -キオクの山脈-', 'year' => 2011, 'color' => '#FF7766', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2012, 'color' => '#FF6655', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2012, 'color' => '#FF5544', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2013, 'color' => '#FF4433', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2013, 'color' => '#FF3322', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2014, 'color' => '#FF2211', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2014, 'color' => '#FF1100', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2015, 'color' => '#FF0000', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2015, 'color' => '#FF1111', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2016, 'color' => '#FF2222', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2016, 'color' => '#FF3333', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2017, 'color' => '#FF4444', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2017, 'color' => '#FF5555', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2018, 'color' => '#FF6666', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2018, 'color' => '#FF7777', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2019, 'color' => '#FF8888', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2019, 'color' => '#FF9999', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2020, 'color' => '#FFAAAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2020, 'color' => '#FFBBBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2021, 'color' => '#FFCCCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2021, 'color' => '#FFDDDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2022, 'color' => '#FFEEEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2022, 'color' => '#FFFFFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2023, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2023, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '有頂天', 'year' => 2024, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'RED', 'year' => 2024, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Q&A', 'year' => 2025, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Uchouten', 'year' => 2025, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
        ];

        foreach ($songs as $songData) {
            Song::create([
                'artist_id' => $bz->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ')',
                'song_description' => 'B\'zの楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('B\'zの楽曲を' . count($songs) . '曲登録しました。');
    }
}
