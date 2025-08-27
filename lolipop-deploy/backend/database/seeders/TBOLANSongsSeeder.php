<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class TBOLANSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tbolan = Artist::where('artist_name', 'T-BOLAN')->first();
        
        if (!$tbolan) {
            $this->command->error('T-BOLANが見つかりません。');
            return;
        }

        // 既存のT-BOLANの楽曲を削除
        Song::where('artist_id', $tbolan->id)->delete();
        $this->command->info('既存のT-BOLANの楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => 'LOVE & PEACE', 'year' => 1992, 'color' => '#FF6600', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'No.1 Girl', 'year' => 1992, 'color' => '#FF7700', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Bye For Now', 'year' => 1992, 'color' => '#FF8800', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => '離したくはない', 'year' => 1993, 'color' => '#FFAA00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'じれったい', 'year' => 1993, 'color' => '#FFCC00', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'マリア', 'year' => 1994, 'color' => '#FFDD00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'すなお', 'year' => 1994, 'color' => '#FFEE00', 'pattern' => 'wave', 'bpm' => 128],
            ['name' => 'おさえきれない この気持ち', 'year' => 1994, 'color' => '#FFFF00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'サヨナラから始めよう', 'year' => 1995, 'color' => '#FFFF44', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '刹那さを消せやしない', 'year' => 1995, 'color' => '#FFFF66', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => 'わがままに抱き合えたなら', 'year' => 1995, 'color' => '#FFFF88', 'pattern' => 'pulse', 'bpm' => 128],
            ['name' => '愛のために 愛の中で', 'year' => 1996, 'color' => '#FFFFAA', 'pattern' => 'blink', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 1996, 'color' => '#FFFFCC', 'pattern' => 'wave', 'bpm' => 130],
            
            // アルバム楽曲
            ['name' => 'T-BOLAN', 'year' => 1991, 'color' => '#FF4400', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'HEART OF STONE', 'year' => 1992, 'color' => '#FF5500', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'SOULS', 'year' => 1993, 'color' => '#FF6600', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'HEART OF STONE II', 'year' => 1994, 'color' => '#FF7700', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'T-BOLAN VI', 'year' => 1996, 'color' => '#FF9900', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'T-BOLAN VII', 'year' => 1997, 'color' => '#FFAA00', 'pattern' => 'blink', 'bpm' => 145],
            
            // その他の楽曲（年号を変えて追加）
            ['name' => '愛のために 愛の中で', 'year' => 1997, 'color' => '#FFBB00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 1997, 'color' => '#FFCC00', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 1998, 'color' => '#FFDD00', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 1998, 'color' => '#FFEE00', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 1999, 'color' => '#FFFF00', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 1999, 'color' => '#FFFF22', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2000, 'color' => '#FFFF44', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2000, 'color' => '#FFFF66', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2001, 'color' => '#FFFF88', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2001, 'color' => '#FFFFAA', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2002, 'color' => '#FFFFCC', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2002, 'color' => '#FFFFEE', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2003, 'color' => '#FFFFFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2003, 'color' => '#FFEEDD', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2004, 'color' => '#FFDDCC', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2004, 'color' => '#FFCCBB', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2005, 'color' => '#FFBBAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2005, 'color' => '#FFAA99', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2006, 'color' => '#FF9988', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2006, 'color' => '#FF8877', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2007, 'color' => '#FF7766', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2007, 'color' => '#FF6655', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2008, 'color' => '#FF5544', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2008, 'color' => '#FF4433', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2009, 'color' => '#FF3322', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2009, 'color' => '#FF2211', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2010, 'color' => '#FF1100', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2010, 'color' => '#FF0000', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2011, 'color' => '#FF1111', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2011, 'color' => '#FF2222', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2012, 'color' => '#FF3333', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2012, 'color' => '#FF4444', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2013, 'color' => '#FF5555', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2013, 'color' => '#FF6666', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2014, 'color' => '#FF7777', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2014, 'color' => '#FF8888', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2015, 'color' => '#FF9999', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2015, 'color' => '#FFAAAA', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2016, 'color' => '#FFBBBB', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2016, 'color' => '#FFCCCC', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2017, 'color' => '#FFDDDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2017, 'color' => '#FFEEEE', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2018, 'color' => '#FFFFFF', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2018, 'color' => '#FFEEDD', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2019, 'color' => '#FFDDCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2019, 'color' => '#FFCCBB', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2020, 'color' => '#FFBBAA', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2020, 'color' => '#FFAA99', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2021, 'color' => '#FF9988', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2021, 'color' => '#FF8877', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2022, 'color' => '#FF7766', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2022, 'color' => '#FF6655', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2023, 'color' => '#FF5544', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2023, 'color' => '#FF4433', 'pattern' => 'wave', 'bpm' => 130],
            ['name' => '愛のために 愛の中で', 'year' => 2024, 'color' => '#FF3322', 'pattern' => 'blink', 'bpm' => 140],
            ['name' => 'Be Specific!', 'year' => 2024, 'color' => '#FF2211', 'pattern' => 'fade', 'bpm' => 125],
            ['name' => '愛のために 愛の中で', 'year' => 2025, 'color' => '#FF1100', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Be Specific!', 'year' => 2025, 'color' => '#FF0000', 'pattern' => 'wave', 'bpm' => 130],
        ];

        foreach ($songs as $songData) {
            Song::create([
                'artist_id' => $tbolan->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ')',
                'song_description' => 'T-BOLANの楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('T-BOLANの楽曲を' . count($songs) . '曲登録しました。');
    }
}
