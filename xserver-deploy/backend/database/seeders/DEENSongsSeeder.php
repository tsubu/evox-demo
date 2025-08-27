<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class DEENSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deen = Artist::where('artist_name', 'DEEN')->first();
        
        if (!$deen) {
            $this->command->error('DEENが見つかりません。');
            return;
        }

        // 既存のDEENの楽曲を削除
        Song::where('artist_id', $deen->id)->delete();
        $this->command->info('既存のDEENの楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => 'このまま君だけを奪い去りたい', 'year' => 1993, 'color' => '#E6E6FF', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => '瞳そらさないで', 'year' => 1993, 'color' => '#F0F0FF', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => '夢であるように', 'year' => 1994, 'color' => '#FAFAFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '未来のために', 'year' => 1994, 'color' => '#E6E6FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 1994, 'color' => '#CCCCFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 1995, 'color' => '#B3B3FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 1995, 'color' => '#9999FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 1995, 'color' => '#8080FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 1996, 'color' => '#6666FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 1996, 'color' => '#4D4DFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 1996, 'color' => '#3333FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 1997, 'color' => '#1A1AFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 1997, 'color' => '#0000FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 1997, 'color' => '#1111FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 1998, 'color' => '#2222FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 1998, 'color' => '#3333FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 1998, 'color' => '#4444FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 1999, 'color' => '#5555FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 1999, 'color' => '#6666FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 1999, 'color' => '#7777FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2000, 'color' => '#8888FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2000, 'color' => '#9999FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2000, 'color' => '#AAAAFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2001, 'color' => '#BBBBFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2001, 'color' => '#CCCCFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2001, 'color' => '#DDDDFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2002, 'color' => '#EEEEFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2002, 'color' => '#FFFF00', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2002, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2003, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2003, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2003, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2004, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2004, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2004, 'color' => '#FF8877', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2005, 'color' => '#FF7766', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2005, 'color' => '#FF6655', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2005, 'color' => '#FF5544', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2006, 'color' => '#FF4433', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2006, 'color' => '#FF3322', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2006, 'color' => '#FF2211', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2007, 'color' => '#FF1100', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2007, 'color' => '#FF0000', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2007, 'color' => '#FF1111', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2008, 'color' => '#FF2222', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2008, 'color' => '#FF3333', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2008, 'color' => '#FF4444', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2009, 'color' => '#FF5555', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2009, 'color' => '#FF6666', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2009, 'color' => '#FF7777', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2010, 'color' => '#FF8888', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2010, 'color' => '#FF9999', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2010, 'color' => '#FFAAAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2011, 'color' => '#FFBBBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2011, 'color' => '#FFCCCC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2011, 'color' => '#FFDDDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2012, 'color' => '#FFEEEE', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2012, 'color' => '#FFFFFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2012, 'color' => '#FFEEDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2013, 'color' => '#FFDDCC', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2013, 'color' => '#FFCCBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2013, 'color' => '#FFBBAA', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2014, 'color' => '#FFAA99', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2014, 'color' => '#FF9988', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2014, 'color' => '#FF8877', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2015, 'color' => '#FF7766', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2015, 'color' => '#FF6655', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2015, 'color' => '#FF5544', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2016, 'color' => '#FF4433', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2016, 'color' => '#FF3322', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2016, 'color' => '#FF2211', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2017, 'color' => '#FF1100', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2017, 'color' => '#FF0000', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2017, 'color' => '#FF1111', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2018, 'color' => '#FF2222', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2018, 'color' => '#FF3333', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2018, 'color' => '#FF4444', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2019, 'color' => '#FF5555', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2019, 'color' => '#FF6666', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2019, 'color' => '#FF7777', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2020, 'color' => '#FF8888', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2020, 'color' => '#FF9999', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2020, 'color' => '#FFAAAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2021, 'color' => '#FFBBBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2021, 'color' => '#FFCCCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2021, 'color' => '#FFDDDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2022, 'color' => '#FFEEEE', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2022, 'color' => '#FFFFFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2022, 'color' => '#FFEEDD', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2023, 'color' => '#FFDDCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2023, 'color' => '#FFCCBB', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2023, 'color' => '#FFBBAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2024, 'color' => '#FFAA99', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2024, 'color' => '#FF9988', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '君がいない夏', 'year' => 2024, 'color' => '#FF8877', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Memories', 'year' => 2025, 'color' => '#FF7766', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '君がいない夏', 'year' => 2025, 'color' => '#FF6655', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Memories', 'year' => 2025, 'color' => '#FF5544', 'pattern' => 'wave', 'bpm' => 140],
        ];

        foreach ($songs as $index => $songData) {
            Song::create([
                'artist_id' => $deen->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ') #' . ($index + 1),
                'song_description' => 'DEENの楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('DEENの楽曲を' . count($songs) . '曲登録しました。');
    }
}
