<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class FIELDOFVIEWSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fieldofview = Artist::where('artist_name', 'FIELD OF VIEW')->first();
        
        if (!$fieldofview) {
            $this->command->error('FIELD OF VIEWが見つかりません。');
            return;
        }

        // 既存のFIELD OF VIEWの楽曲を削除
        Song::where('artist_id', $fieldofview->id)->delete();
        $this->command->info('既存のFIELD OF VIEWの楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => '突然', 'year' => 1994, 'color' => '#FFE6CC', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'Last Good-bye', 'year' => 1995, 'color' => '#FFF0DD', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1996, 'color' => '#FFFAEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 1996, 'color' => '#FFE6CC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1996, 'color' => '#FFCC99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 1997, 'color' => '#FFB366', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1997, 'color' => '#FF9944', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 1997, 'color' => '#FF8022', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1998, 'color' => '#FF6600', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 1998, 'color' => '#FF7711', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1998, 'color' => '#FF8822', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 1999, 'color' => '#FF9933', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 1999, 'color' => '#FFAA44', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 1999, 'color' => '#FFBB55', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2000, 'color' => '#FFCC66', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2000, 'color' => '#FFDD77', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2000, 'color' => '#FFEE88', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2001, 'color' => '#FFFF99', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2001, 'color' => '#FFEEDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2001, 'color' => '#FFDDCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2002, 'color' => '#FFCCBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2002, 'color' => '#FFBBAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2002, 'color' => '#FFAA99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2003, 'color' => '#FF9988', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2003, 'color' => '#FF8877', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2003, 'color' => '#FF7766', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2004, 'color' => '#FF6655', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2004, 'color' => '#FF5544', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2004, 'color' => '#FF4433', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2005, 'color' => '#FF3322', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2005, 'color' => '#FF2211', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2005, 'color' => '#FF1100', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2006, 'color' => '#FF0000', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2006, 'color' => '#FF1111', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2006, 'color' => '#FF2222', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2007, 'color' => '#FF3333', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2007, 'color' => '#FF4444', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2007, 'color' => '#FF5555', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2008, 'color' => '#FF6666', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2008, 'color' => '#FF7777', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2008, 'color' => '#FF8888', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2009, 'color' => '#FF9999', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2009, 'color' => '#FFAAAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2009, 'color' => '#FFBBBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2010, 'color' => '#FFCCCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2010, 'color' => '#FFDDDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2010, 'color' => '#FFEEEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2011, 'color' => '#FFFFFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2011, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2011, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2012, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2012, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2012, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2013, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2013, 'color' => '#FF8877', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2013, 'color' => '#FF7766', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2014, 'color' => '#FF6655', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2014, 'color' => '#FF5544', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2014, 'color' => '#FF4433', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2015, 'color' => '#FF3322', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2015, 'color' => '#FF2211', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2015, 'color' => '#FF1100', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2016, 'color' => '#FF0000', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2016, 'color' => '#FF1111', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2016, 'color' => '#FF2222', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2017, 'color' => '#FF3333', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2017, 'color' => '#FF4444', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2017, 'color' => '#FF5555', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2018, 'color' => '#FF6666', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2018, 'color' => '#FF7777', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2018, 'color' => '#FF8888', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2019, 'color' => '#FF9999', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2019, 'color' => '#FFAAAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2019, 'color' => '#FFBBBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2020, 'color' => '#FFCCCC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2020, 'color' => '#FFDDDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2020, 'color' => '#FFEEEE', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2021, 'color' => '#FFFFFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2021, 'color' => '#FFEEDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2021, 'color' => '#FFDDCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2022, 'color' => '#FFCCBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2022, 'color' => '#FFBBAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2022, 'color' => '#FFAA99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2023, 'color' => '#FF9988', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2023, 'color' => '#FF8877', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2023, 'color' => '#FF7766', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2024, 'color' => '#FF6655', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2024, 'color' => '#FF5544', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2024, 'color' => '#FF4433', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Last Good-bye', 'year' => 2025, 'color' => '#FF3322', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'DAN DAN 心魅かれてく', 'year' => 2025, 'color' => '#FF2211', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Last Good-bye', 'year' => 2025, 'color' => '#FF1100', 'pattern' => 'fade', 'bpm' => 130],
        ];

        foreach ($songs as $index => $songData) {
            Song::create([
                'artist_id' => $fieldofview->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ') #' . ($index + 1),
                'song_description' => 'FIELD OF VIEWの楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('FIELD OF VIEWの楽曲を' . count($songs) . '曲登録しました。');
    }
}
