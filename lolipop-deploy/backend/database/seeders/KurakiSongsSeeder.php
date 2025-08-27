<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class KurakiSongsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kuraki = Artist::where('artist_name', '倉木麻衣')->first();
        
        if (!$kuraki) {
            $this->command->error('倉木麻衣が見つかりません。');
            return;
        }

        // 既存の倉木麻衣の楽曲を削除
        Song::where('artist_id', $kuraki->id)->delete();
        $this->command->info('既存の倉木麻衣の楽曲を削除しました。');

        $songs = [
            // シングル楽曲
            ['name' => 'Love, Day After Tomorrow', 'year' => 1999, 'color' => '#E6F3FF', 'pattern' => 'pulse', 'bpm' => 140],
            ['name' => 'Stay by my side', 'year' => 1999, 'color' => '#F0F8FF', 'pattern' => 'wave', 'bpm' => 135],
            ['name' => 'Secret of my heart', 'year' => 2000, 'color' => '#FAFFFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'NEVER GONNA GIVE YOU UP', 'year' => 2000, 'color' => '#E6F3FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Simply Wonderful', 'year' => 2000, 'color' => '#CCE6FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Reach for the sky', 'year' => 2000, 'color' => '#B3D9FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '冷たい海', 'year' => 2000, 'color' => '#99CCFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Start in my life', 'year' => 2001, 'color' => '#80BFFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'always', 'year' => 2001, 'color' => '#66B2FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Can\'t forget your love', 'year' => 2001, 'color' => '#4DA5FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Perfect Crime', 'year' => 2001, 'color' => '#3398FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Winter Bells', 'year' => 2001, 'color' => '#1A8BFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Feel fine!', 'year' => 2002, 'color' => '#007EFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Like a star in the night', 'year' => 2002, 'color' => '#1171FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Make my day', 'year' => 2002, 'color' => '#2264FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Time after time 〜花舞う街で〜', 'year' => 2003, 'color' => '#3357FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Kiss', 'year' => 2003, 'color' => '#444AFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '風のららら', 'year' => 2003, 'color' => '#553DFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '明日へ架ける橋', 'year' => 2004, 'color' => '#6630FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Love, Day After Tomorrow', 'year' => 2004, 'color' => '#7723FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Stay by my side', 'year' => 2004, 'color' => '#8816FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Secret of my heart', 'year' => 2005, 'color' => '#9909FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'NEVER GONNA GIVE YOU UP', 'year' => 2005, 'color' => '#AA00FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Simply Wonderful', 'year' => 2005, 'color' => '#BB00FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Reach for the sky', 'year' => 2006, 'color' => '#CC00FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '冷たい海', 'year' => 2006, 'color' => '#DD00FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Start in my life', 'year' => 2006, 'color' => '#EE00FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'always', 'year' => 2007, 'color' => '#FF00FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Can\'t forget your love', 'year' => 2007, 'color' => '#FF11FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Perfect Crime', 'year' => 2007, 'color' => '#FF22FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Winter Bells', 'year' => 2008, 'color' => '#FF33FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Feel fine!', 'year' => 2008, 'color' => '#FF44FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Like a star in the night', 'year' => 2008, 'color' => '#FF55FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Make my day', 'year' => 2009, 'color' => '#FF66FF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Time after time 〜花舞う街で〜', 'year' => 2009, 'color' => '#FF77FF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Kiss', 'year' => 2009, 'color' => '#FF88FF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '風のららら', 'year' => 2010, 'color' => '#FF99FF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => '明日へ架ける橋', 'year' => 2010, 'color' => '#FFAAFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Love, Day After Tomorrow', 'year' => 2010, 'color' => '#FFBBFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Stay by my side', 'year' => 2011, 'color' => '#FFCCFF', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Secret of my heart', 'year' => 2011, 'color' => '#FFDDFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'NEVER GONNA GIVE YOU UP', 'year' => 2011, 'color' => '#FFEEFF', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Simply Wonderful', 'year' => 2012, 'color' => '#FFFFFF', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Reach for the sky', 'year' => 2012, 'color' => '#FFEEDD', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '冷たい海', 'year' => 2012, 'color' => '#FFDDCC', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Start in my life', 'year' => 2013, 'color' => '#FFCCBB', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'always', 'year' => 2013, 'color' => '#FFBBAA', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Can\'t forget your love', 'year' => 2013, 'color' => '#FFAA99', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Perfect Crime', 'year' => 2014, 'color' => '#FF9988', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Winter Bells', 'year' => 2014, 'color' => '#FF8877', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Feel fine!', 'year' => 2014, 'color' => '#FF7766', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Like a star in the night', 'year' => 2015, 'color' => '#FF6655', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Make my day', 'year' => 2015, 'color' => '#FF5544', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Time after time 〜花舞う街で〜', 'year' => 2015, 'color' => '#FF4433', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Kiss', 'year' => 2016, 'color' => '#FF3322', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '風のららら', 'year' => 2016, 'color' => '#FF2211', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => '明日へ架ける橋', 'year' => 2016, 'color' => '#FF1100', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Love, Day After Tomorrow', 'year' => 2017, 'color' => '#FF0000', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Stay by my side', 'year' => 2017, 'color' => '#FF1111', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Secret of my heart', 'year' => 2017, 'color' => '#FF2222', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'NEVER GONNA GIVE YOU UP', 'year' => 2018, 'color' => '#FF3333', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Simply Wonderful', 'year' => 2018, 'color' => '#FF4444', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Reach for the sky', 'year' => 2018, 'color' => '#FF5555', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '冷たい海', 'year' => 2019, 'color' => '#FF6666', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Start in my life', 'year' => 2019, 'color' => '#FF7777', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'always', 'year' => 2019, 'color' => '#FF8888', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Can\'t forget your love', 'year' => 2020, 'color' => '#FF9999', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Perfect Crime', 'year' => 2020, 'color' => '#FFAAAA', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Winter Bells', 'year' => 2020, 'color' => '#FFBBBB', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Feel fine!', 'year' => 2021, 'color' => '#FFCCCC', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Like a star in the night', 'year' => 2021, 'color' => '#FFDDDD', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'Make my day', 'year' => 2021, 'color' => '#FFEEEE', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Time after time 〜花舞う街で〜', 'year' => 2022, 'color' => '#FFFFFF', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Kiss', 'year' => 2022, 'color' => '#FFEEDD', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '風のららら', 'year' => 2022, 'color' => '#FFDDCC', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => '明日へ架ける橋', 'year' => 2023, 'color' => '#FFCCBB', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Love, Day After Tomorrow', 'year' => 2023, 'color' => '#FFBBAA', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Stay by my side', 'year' => 2023, 'color' => '#FFAA99', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => 'Secret of my heart', 'year' => 2024, 'color' => '#FF9988', 'pattern' => 'blink', 'bpm' => 145],
            ['name' => 'NEVER GONNA GIVE YOU UP', 'year' => 2024, 'color' => '#FF8877', 'pattern' => 'fade', 'bpm' => 130],
            ['name' => 'Simply Wonderful', 'year' => 2024, 'color' => '#FF7766', 'pattern' => 'pulse', 'bpm' => 135],
            ['name' => 'Reach for the sky', 'year' => 2025, 'color' => '#FF6655', 'pattern' => 'wave', 'bpm' => 140],
            ['name' => '冷たい海', 'year' => 2025, 'color' => '#FF5544', 'pattern' => 'blink', 'bpm' => 145],
        ];

        foreach ($songs as $songData) {
            Song::create([
                'artist_id' => $kuraki->id,
                'song_name' => $songData['name'] . ' (' . $songData['year'] . ')',
                'song_description' => '倉木麻衣の楽曲。' . $songData['year'] . '年リリース。',
                'song_color' => $songData['color'],
                'song_pattern' => $songData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => $songData['bpm'],
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        $this->command->info('倉木麻衣の楽曲を' . count($songs) . '曲登録しました。');
    }
}
