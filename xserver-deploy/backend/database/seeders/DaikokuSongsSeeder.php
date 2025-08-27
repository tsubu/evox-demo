<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class DaikokuSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daikoku = Artist::where('artist_name', '大黒摩季')->first();
        
        if (!$daikoku) {
            $this->command->error('大黒摩季が見つかりません。');
            return;
        }

        // 既存の大黒摩季の楽曲を削除
        Song::where('artist_id', $daikoku->id)->delete();
        $this->command->info('既存の大黒摩季の楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => 'STOP MOTION', 'year' => 1992, 'color' => '#FFE6F2', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'チョット', 'year' => 1992, 'color' => '#FFF0F8', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'DA・KA・RA', 'year' => 1993, 'color' => '#FFFAFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '夏が来る', 'year' => 1993, 'color' => '#FFE6F2', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '永遠の夢に向かって', 'year' => 1995, 'color' => '#FFCCE6', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '愛してます', 'year' => 1995, 'color' => '#FFB3D9', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'ら・ら・ら', 'year' => 1996, 'color' => '#FF99CC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 1996, 'color' => '#FF80BF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 1997, 'color' => '#FF66B2', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 1997, 'color' => '#FF4DA5', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 1998, 'color' => '#FF3398', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 1998, 'color' => '#FF1A8B', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 1999, 'color' => '#FF007E', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 1999, 'color' => '#FF1171', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2000, 'color' => '#FF2264', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2000, 'color' => '#FF3357', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2001, 'color' => '#FF444A', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2001, 'color' => '#FF553D', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2002, 'color' => '#FF6630', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2002, 'color' => '#FF7723', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2003, 'color' => '#FF8816', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2003, 'color' => '#FF9909', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2004, 'color' => '#FFAA00', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2004, 'color' => '#FFBB00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2005, 'color' => '#FFCC00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2005, 'color' => '#FFDD00', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2006, 'color' => '#FFEE00', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2006, 'color' => '#FFFF00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2007, 'color' => '#FFFF11', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2007, 'color' => '#FFFF22', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2008, 'color' => '#FFFF33', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2008, 'color' => '#FFFF44', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2009, 'color' => '#FFFF55', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2009, 'color' => '#FFFF66', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2010, 'color' => '#FFFF77', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2010, 'color' => '#FFFF88', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2011, 'color' => '#FFFF99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2011, 'color' => '#FFFFAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2012, 'color' => '#FFFFBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2012, 'color' => '#FFFFCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2013, 'color' => '#FFFFDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2013, 'color' => '#FFFFEE', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2014, 'color' => '#FFFFFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2014, 'color' => '#FFEEDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2015, 'color' => '#FFDDCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2015, 'color' => '#FFCCBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2016, 'color' => '#FFBBAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2016, 'color' => '#FFAA99', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2017, 'color' => '#FF9988', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2017, 'color' => '#FF8877', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2018, 'color' => '#FF7766', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2018, 'color' => '#FF6655', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2019, 'color' => '#FF5544', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2019, 'color' => '#FF4433', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2020, 'color' => '#FF3322', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2020, 'color' => '#FF2211', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2021, 'color' => '#FF1100', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2021, 'color' => '#FF0000', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2022, 'color' => '#FF1111', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2022, 'color' => '#FF2222', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2023, 'color' => '#FF3333', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2023, 'color' => '#FF4444', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '愛してます', 'year' => 2024, 'color' => '#FF5555', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '熱くなれ', 'year' => 2024, 'color' => '#FF6666', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '愛のしるし', 'year' => 2025, 'color' => '#FF7777', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'ら・ら・ら', 'year' => 2025, 'color' => '#FF8888', 'pattern' => 'wave', 'bpm' => 140],
        ];

        foreach ($songs as $songData) {
            Song::create([
                'artist_id' => $daikoku->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ')',
                'song_description' => '大黒摩季の楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('大黒摩季の楽曲を' . count($songs) . '曲登録しました。');
    }
}
