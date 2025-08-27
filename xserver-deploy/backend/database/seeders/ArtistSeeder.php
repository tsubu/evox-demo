<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Song;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリア
        Song::truncate();
        Artist::truncate();

        // B'z
        $bz = Artist::create([
            'artist_name' => 'B\'z',
            'artist_description' => 'B ZONE所属の国民的ロックバンド。稲葉浩志（Vo）、松本孝弘（Gt）の2人組。',
            'artist_color' => '#FF0000',
            'artist_pattern' => 'pulse',
            'artist_brightness' => 90,
            'artist_bpm' => 140,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // B'zの楽曲
        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'ultra soul',
            'song_description' => 'B\'zの代表曲。2001年リリース。',
            'song_color' => '#FF0000',
            'song_pattern' => 'pulse',
            'song_brightness' => 90,
            'song_bpm' => 140,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => '愛のバクダン',
            'song_description' => 'B\'zの楽曲。1992年リリース。',
            'song_color' => '#FF4444',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 135,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'LOVE PHANTOM',
            'song_description' => 'B\'zの楽曲。1995年リリース。',
            'song_color' => '#CC0000',
            'song_pattern' => 'blink',
            'song_brightness' => 88,
            'song_bpm' => 145,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'BAD COMMUNICATION',
            'song_description' => 'B\'zの楽曲。1991年リリース。',
            'song_color' => '#DD0000',
            'song_pattern' => 'pulse',
            'song_brightness' => 92,
            'song_bpm' => 150,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'BE THERE',
            'song_description' => 'B\'zの楽曲。1993年リリース。',
            'song_color' => '#EE0000',
            'song_pattern' => 'wave',
            'song_brightness' => 87,
            'song_bpm' => 138,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'Calling',
            'song_description' => 'B\'zの楽曲。1997年リリース。',
            'song_color' => '#BB0000',
            'song_pattern' => 'blink',
            'song_brightness' => 89,
            'song_bpm' => 142,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'ギリギリchop',
            'song_description' => 'B\'zの楽曲。1999年リリース。',
            'song_color' => '#AA0000',
            'song_pattern' => 'pulse',
            'song_brightness' => 95,
            'song_bpm' => 155,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => '今夜月の見える丘に',
            'song_description' => 'B\'zの楽曲。1999年リリース。',
            'song_color' => '#990000',
            'song_pattern' => 'fade',
            'song_brightness' => 80,
            'song_bpm' => 115,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'May',
            'song_description' => 'B\'zの楽曲。2000年リリース。',
            'song_color' => '#880000',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'juice',
            'song_description' => 'B\'zの楽曲。2000年リリース。',
            'song_color' => '#770000',
            'song_pattern' => 'blink',
            'song_brightness' => 92,
            'song_bpm' => 148,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'RING',
            'song_description' => 'B\'zの楽曲。2000年リリース。',
            'song_color' => '#660000',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 135,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'GOLD',
            'song_description' => 'B\'zの楽曲。2001年リリース。',
            'song_color' => '#550000',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 140,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => '野性のENERGY',
            'song_description' => 'B\'zの楽曲。2002年リリース。',
            'song_color' => '#440000',
            'song_pattern' => 'pulse',
            'song_brightness' => 95,
            'song_bpm' => 150,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'IT\'S SHOWTIME!!',
            'song_description' => 'B\'zの楽曲。2002年リリース。',
            'song_color' => '#330000',
            'song_pattern' => 'blink',
            'song_brightness' => 98,
            'song_bpm' => 160,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $bz->id,
            'song_name' => 'ARIGATO',
            'song_description' => 'B\'zの楽曲。2003年リリース。',
            'song_color' => '#220000',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // T-BOLAN
        $tbolan = Artist::create([
            'artist_name' => 'T-BOLAN',
            'artist_description' => 'B ZONE所属のロックバンド。1991年デビュー。森友嵐士（Vo）、五味孝氏（Gt）、上野博文（Ba）、青木和義（Dr）の4人組。',
            'artist_color' => '#FF6600',
            'artist_pattern' => 'pulse',
            'artist_brightness' => 85,
            'artist_bpm' => 140,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // T-BOLANの楽曲
        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'LOVE & PEACE',
            'song_description' => 'T-BOLANの代表曲。1992年リリース。',
            'song_color' => '#FF6600',
            'song_pattern' => 'pulse',
            'song_brightness' => 85,
            'song_bpm' => 140,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'Bye For Now',
            'song_description' => 'T-BOLANの楽曲。1992年リリース。',
            'song_color' => '#FF8800',
            'song_pattern' => 'wave',
            'song_brightness' => 80,
            'song_bpm' => 135,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => '離したくはない',
            'song_description' => 'T-BOLANの楽曲。1993年リリース。',
            'song_color' => '#FFAA00',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 130,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'じれったい',
            'song_description' => 'T-BOLANの楽曲。1993年リリース。',
            'song_color' => '#FFCC00',
            'song_pattern' => 'blink',
            'song_brightness' => 88,
            'song_bpm' => 145,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'マリア',
            'song_description' => 'T-BOLANの楽曲。1994年リリース。',
            'song_color' => '#FFDD00',
            'song_pattern' => 'pulse',
            'song_brightness' => 85,
            'song_bpm' => 135,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'すなお',
            'song_description' => 'T-BOLANの楽曲。1994年リリース。',
            'song_color' => '#FFEE00',
            'song_pattern' => 'wave',
            'song_brightness' => 80,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'おさえきれない この気持ち',
            'song_description' => 'T-BOLANの楽曲。1994年リリース。',
            'song_color' => '#FFFF00',
            'song_pattern' => 'pulse',
            'song_brightness' => 85,
            'song_bpm' => 135,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'Bye For Now',
            'song_description' => 'T-BOLANの楽曲。1995年リリース。',
            'song_color' => '#FFFF22',
            'song_pattern' => 'blink',
            'song_brightness' => 88,
            'song_bpm' => 140,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'サヨナラから始めよう',
            'song_description' => 'T-BOLANの楽曲。1995年リリース。',
            'song_color' => '#FFFF44',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => '刹那さを消せやしない',
            'song_description' => 'T-BOLANの楽曲。1995年リリース。',
            'song_color' => '#FFFF66',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 130,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'わがままに抱き合えたなら',
            'song_description' => 'T-BOLANの楽曲。1995年リリース。',
            'song_color' => '#FFFF88',
            'song_pattern' => 'pulse',
            'song_brightness' => 87,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => '愛のために 愛の中で',
            'song_description' => 'T-BOLANの楽曲。1996年リリース。',
            'song_color' => '#FFFFAA',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 135,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $tbolan->id,
            'song_name' => 'Be Specific!',
            'song_description' => 'T-BOLANの楽曲。1996年リリース。',
            'song_color' => '#FFFFCC',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 130,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 大黒摩季
        $daikoku = Artist::create([
            'artist_name' => '大黒摩季',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#FF69B4',
            'artist_pattern' => 'fade',
            'artist_brightness' => 85,
            'artist_bpm' => 120,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 大黒摩季の楽曲
        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'あなただけ見つめてる',
            'song_description' => '大黒摩季の代表曲。1993年リリース。',
            'song_color' => '#FF69B4',
            'song_pattern' => 'fade',
            'song_brightness' => 85,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'ら・ら・ら',
            'song_description' => '大黒摩季の楽曲。1994年リリース。',
            'song_color' => '#FF8DA1',
            'song_pattern' => 'wave',
            'song_brightness' => 80,
            'song_bpm' => 115,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'チョット',
            'song_description' => '大黒摩季の楽曲。1992年リリース。',
            'song_color' => '#FF99CC',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'DA・KA・RA',
            'song_description' => '大黒摩季の楽曲。1993年リリース。',
            'song_color' => '#FFB3D9',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '夏が来る',
            'song_description' => '大黒摩季の楽曲。1994年リリース。',
            'song_color' => '#FFCCE6',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 118,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '永遠の夢に向かって',
            'song_description' => '大黒摩季の楽曲。1995年リリース。',
            'song_color' => '#FFE6F2',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 122,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '愛してます',
            'song_description' => '大黒摩季の楽曲。1995年リリース。',
            'song_color' => '#FFF0F8',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'ら・ら・ら',
            'song_description' => '大黒摩季の楽曲。1996年リリース。',
            'song_color' => '#FFFAFF',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '熱くなれ',
            'song_description' => '大黒摩季の楽曲。1996年リリース。',
            'song_color' => '#FFE6F2',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '愛のしるし',
            'song_description' => '大黒摩季の楽曲。1997年リリース。',
            'song_color' => '#FFCCE6',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => 'ら・ら・ら',
            'song_description' => '大黒摩季の楽曲。1997年リリース。',
            'song_color' => '#FFB3D9',
            'song_pattern' => 'pulse',
            'song_brightness' => 87,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '愛してます',
            'song_description' => '大黒摩季の楽曲。1998年リリース。',
            'song_color' => '#FF99CC',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 132,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $daikoku->id,
            'song_name' => '熱くなれ',
            'song_description' => '大黒摩季の楽曲。1998年リリース。',
            'song_color' => '#FF80BF',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 倉木麻衣
        $kuraki = Artist::create([
            'artist_name' => '倉木麻衣',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#9932CC',
            'artist_pattern' => 'wave',
            'artist_brightness' => 88,
            'artist_bpm' => 125,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 倉木麻衣の楽曲
        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Secret of my heart',
            'song_description' => '倉木麻衣の代表曲。2000年リリース。',
            'song_color' => '#9932CC',
            'song_pattern' => 'wave',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Time after time ～花舞う街で～',
            'song_description' => '倉木麻衣の楽曲。2003年リリース。',
            'song_color' => '#B366FF',
            'song_pattern' => 'fade',
            'song_brightness' => 85,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Love, Day After Tomorrow',
            'song_description' => '倉木麻衣の楽曲。1999年リリース。',
            'song_color' => '#CC99FF',
            'song_pattern' => 'pulse',
            'song_brightness' => 90,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Stay by my side',
            'song_description' => '倉木麻衣の楽曲。2000年リリース。',
            'song_color' => '#DDAAFF',
            'song_pattern' => 'blink',
            'song_brightness' => 87,
            'song_bpm' => 122,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Always',
            'song_description' => '倉木麻衣の楽曲。2001年リリース。',
            'song_color' => '#EEBBFF',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Reach for the sky',
            'song_description' => '倉木麻衣の楽曲。2001年リリース。',
            'song_color' => '#FFCCFF',
            'song_pattern' => 'fade',
            'song_brightness' => 88,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Stand Up',
            'song_description' => '倉木麻衣の楽曲。2001年リリース。',
            'song_color' => '#FFDDFF',
            'song_pattern' => 'pulse',
            'song_brightness' => 90,
            'song_bpm' => 135,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Start in my life',
            'song_description' => '倉木麻衣の楽曲。2001年リリース。',
            'song_color' => '#FFEEFF',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'always',
            'song_description' => '倉木麻衣の楽曲。2002年リリース。',
            'song_color' => '#FFFFFF',
            'song_pattern' => 'fade',
            'song_brightness' => 88,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Winter Bells',
            'song_description' => '倉木麻衣の楽曲。2002年リリース。',
            'song_color' => '#F0F0FF',
            'song_pattern' => 'blink',
            'song_brightness' => 92,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Feel fine!',
            'song_description' => '倉木麻衣の楽曲。2002年リリース。',
            'song_color' => '#E0E0FF',
            'song_pattern' => 'pulse',
            'song_brightness' => 87,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Like a star in the night',
            'song_description' => '倉木麻衣の楽曲。2002年リリース。',
            'song_color' => '#D0D0FF',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 122,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $kuraki->id,
            'song_name' => 'Make my day',
            'song_description' => '倉木麻衣の楽曲。2003年リリース。',
            'song_color' => '#C0C0FF',
            'song_pattern' => 'fade',
            'song_brightness' => 88,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // WANDS
        $wands = Artist::create([
            'artist_name' => 'WANDS',
            'artist_description' => 'B ZONE所属のロックバンド。',
            'artist_color' => '#0066CC',
            'artist_pattern' => 'blink',
            'artist_brightness' => 90,
            'artist_bpm' => 130,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // WANDSの楽曲
        Song::create([
            'artist_id' => $wands->id,
            'song_name' => '世界が終るまでは…',
            'song_description' => 'WANDSの代表曲。1994年リリース。',
            'song_color' => '#0066CC',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $wands->id,
            'song_name' => 'もっと強く抱きしめたなら',
            'song_description' => 'WANDSの楽曲。1993年リリース。',
            'song_color' => '#0088FF',
            'song_pattern' => 'pulse',
            'song_brightness' => 85,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $wands->id,
            'song_name' => '時の扉',
            'song_description' => 'WANDSの楽曲。1992年リリース。',
            'song_color' => '#00AAFF',
            'song_pattern' => 'wave',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $wands->id,
            'song_name' => '寂しさは秋の色',
            'song_description' => 'WANDSの楽曲。1993年リリース。',
            'song_color' => '#00CCFF',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $wands->id,
            'song_name' => 'Jumpin\' Jack Boy',
            'song_description' => 'WANDSの楽曲。1994年リリース。',
            'song_color' => '#00EEFF',
            'song_pattern' => 'blink',
            'song_brightness' => 92,
            'song_bpm' => 135,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $wands->id,
            'song_name' => 'Same Side',
            'song_description' => 'WANDSの楽曲。1995年リリース。',
            'song_color' => '#22FFFF',
            'song_pattern' => 'pulse',
            'song_brightness' => 87,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // DEEN
        $deen = Artist::create([
            'artist_name' => 'DEEN',
            'artist_description' => 'B ZONE所属のロックバンド。池森秀一（Vo）、山根公路（Gt）、田川伸治（Ba）、宇津本直紀（Dr）の4人組。',
            'artist_color' => '#00CC66',
            'artist_pattern' => 'wave',
            'artist_brightness' => 85,
            'artist_bpm' => 125,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // DEENの楽曲
        Song::create([
            'artist_id' => $deen->id,
            'song_name' => 'このまま君だけを奪い去りたい',
            'song_description' => 'DEENの代表曲。1993年リリース。',
            'song_color' => '#00CC66',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $deen->id,
            'song_name' => '瞳そらさないで',
            'song_description' => 'DEENの楽曲。1994年リリース。',
            'song_color' => '#00DD77',
            'song_pattern' => 'fade',
            'song_brightness' => 80,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $deen->id,
            'song_name' => '君がいない夏',
            'song_description' => 'DEENの楽曲。1994年リリース。',
            'song_color' => '#00EE88',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $deen->id,
            'song_name' => '未来のために',
            'song_description' => 'DEENの楽曲。1995年リリース。',
            'song_color' => '#00FF99',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 130,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $deen->id,
            'song_name' => '君の心に帰りたい',
            'song_description' => 'DEENの楽曲。1995年リリース。',
            'song_color' => '#22FFAA',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $deen->id,
            'song_name' => '君さえいれば',
            'song_description' => 'DEENの楽曲。1996年リリース。',
            'song_color' => '#44FFBB',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 122,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // FIELD OF VIEW
        $fov = Artist::create([
            'artist_name' => 'FIELD OF VIEW',
            'artist_description' => 'B ZONE所属のロックバンド。',
            'artist_color' => '#FF9900',
            'artist_pattern' => 'pulse',
            'artist_brightness' => 88,
            'artist_bpm' => 135,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // FIELD OF VIEWの楽曲
        Song::create([
            'artist_id' => $fov->id,
            'song_name' => '突然',
            'song_description' => 'FIELD OF VIEWの代表曲。1995年リリース。',
            'song_color' => '#FF9900',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 135,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $fov->id,
            'song_name' => 'DAN DAN 心魅かれてく',
            'song_description' => 'FIELD OF VIEWの楽曲。1996年リリース。',
            'song_color' => '#FFAA22',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 130,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $fov->id,
            'song_name' => 'Last Good-bye',
            'song_description' => 'FIELD OF VIEWの楽曲。1995年リリース。',
            'song_color' => '#FFBB44',
            'song_pattern' => 'blink',
            'song_brightness' => 90,
            'song_bpm' => 138,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $fov->id,
            'song_name' => '君がいたから',
            'song_description' => 'FIELD OF VIEWの楽曲。1996年リリース。',
            'song_color' => '#FFCC66',
            'song_pattern' => 'fade',
            'song_brightness' => 82,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $fov->id,
            'song_name' => '渇いた叫び',
            'song_description' => 'FIELD OF VIEWの楽曲。1996年リリース。',
            'song_color' => '#FFDD88',
            'song_pattern' => 'pulse',
            'song_brightness' => 87,
            'song_bpm' => 132,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        Song::create([
            'artist_id' => $fov->id,
            'song_name' => 'Still',
            'song_description' => 'FIELD OF VIEWの楽曲。1997年リリース。',
            'song_color' => '#FFEEAA',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 128,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 稲葉浩志
        $inaba = Artist::create([
            'artist_name' => '稲葉浩志',
            'artist_description' => 'B ZONE所属のシンガーソングライター。B\'zのボーカリスト。',
            'artist_color' => '#FF3333',
            'artist_pattern' => 'pulse',
            'artist_brightness' => 90,
            'artist_bpm' => 140,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 稲葉浩志の楽曲
        Song::create([
            'artist_id' => $inaba->id,
            'song_name' => 'Wonderland',
            'song_description' => '稲葉浩志の楽曲。',
            'song_color' => '#FF3333',
            'song_pattern' => 'pulse',
            'song_brightness' => 90,
            'song_bpm' => 140,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 松本孝弘
        $matsumoto = Artist::create([
            'artist_name' => '松本孝弘',
            'artist_description' => 'B ZONE所属のギタリスト。B\'zのギタリスト。',
            'artist_color' => '#CC0000',
            'artist_pattern' => 'blink',
            'artist_brightness' => 88,
            'artist_bpm' => 145,
            'artist_intensity' => 'high',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 松本孝弘の楽曲
        Song::create([
            'artist_id' => $matsumoto->id,
            'song_name' => 'TAK MATSUMOTO',
            'song_description' => '松本孝弘の楽曲。',
            'song_color' => '#CC0000',
            'song_pattern' => 'blink',
            'song_brightness' => 88,
            'song_bpm' => 145,
            'song_intensity' => 'high',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 大野愛果
        $ohno = Artist::create([
            'artist_name' => '大野愛果',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#FF99CC',
            'artist_pattern' => 'fade',
            'artist_brightness' => 85,
            'artist_bpm' => 120,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 大野愛果の楽曲
        Song::create([
            'artist_id' => $ohno->id,
            'song_name' => 'Always',
            'song_description' => '大野愛果の楽曲。',
            'song_color' => '#FF99CC',
            'song_pattern' => 'fade',
            'song_brightness' => 85,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 愛内里菜
        $aiuchi = Artist::create([
            'artist_name' => '愛内里菜',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#FF66CC',
            'artist_pattern' => 'wave',
            'artist_brightness' => 88,
            'artist_bpm' => 125,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 愛内里菜の楽曲
        Song::create([
            'artist_id' => $aiuchi->id,
            'song_name' => 'I can\'t stop my love for you',
            'song_description' => '愛内里菜の楽曲。',
            'song_color' => '#FF66CC',
            'song_pattern' => 'wave',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // GARNET CROW
        $garnet = Artist::create([
            'artist_name' => 'GARNET CROW',
            'artist_description' => 'B ZONE所属のロックバンド。',
            'artist_color' => '#9933CC',
            'artist_pattern' => 'wave',
            'artist_brightness' => 85,
            'artist_bpm' => 130,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // GARNET CROWの楽曲
        Song::create([
            'artist_id' => $garnet->id,
            'song_name' => 'Mysterious Eyes',
            'song_description' => 'GARNET CROWの楽曲。',
            'song_color' => '#9933CC',
            'song_pattern' => 'wave',
            'song_brightness' => 85,
            'song_bpm' => 130,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 小松未歩
        $komatsu = Artist::create([
            'artist_name' => '小松未歩',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#CC6699',
            'artist_pattern' => 'fade',
            'artist_brightness' => 85,
            'artist_bpm' => 120,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 小松未歩の楽曲
        Song::create([
            'artist_id' => $komatsu->id,
            'song_name' => '謎',
            'song_description' => '小松未歩の楽曲。',
            'song_color' => '#CC6699',
            'song_pattern' => 'fade',
            'song_brightness' => 85,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // 三枝夕夏 IN db
        $saegusa = Artist::create([
            'artist_name' => '三枝夕夏 IN db',
            'artist_description' => 'B ZONE所属の女性シンガーソングライター。',
            'artist_color' => '#FF99FF',
            'artist_pattern' => 'pulse',
            'artist_brightness' => 88,
            'artist_bpm' => 125,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'artist',
            'artist_is_active' => true
        ]);

        // 三枝夕夏 IN dbの楽曲
        Song::create([
            'artist_id' => $saegusa->id,
            'song_name' => 'It\'s for you',
            'song_description' => '三枝夕夏 IN dbの楽曲。',
            'song_color' => '#FF99FF',
            'song_pattern' => 'pulse',
            'song_brightness' => 88,
            'song_bpm' => 125,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'artist',
            'song_is_active' => true
        ]);

        // その他のB ZONE所属アーティスト（個別登録）
        $artists = [
            ['name' => 'AislE', 'color' => '#FF6B6B', 'pattern' => 'wave'],
            ['name' => '青紀ひかり', 'color' => '#4ECDC4', 'pattern' => 'fade'],
            ['name' => 'AKIHIDE', 'color' => '#45B7D1', 'pattern' => 'pulse'],
            ['name' => '甘い暴力', 'color' => '#96CEB4', 'pattern' => 'blink'],
            ['name' => 'IKURO', 'color' => '#FFEAA7', 'pattern' => 'pulse'],
            ['name' => 'Wisteria', 'color' => '#DDA0DD', 'pattern' => 'wave'],
            ['name' => '奥崎海斗', 'color' => '#98D8C8', 'pattern' => 'fade'],
            ['name' => 'all at once', 'color' => '#F7DC6F', 'pattern' => 'pulse'],
            ['name' => 'Kylie', 'color' => '#BB8FCE', 'pattern' => 'wave'],
            ['name' => '近藤房之助', 'color' => '#85C1E9', 'pattern' => 'blink'],
            ['name' => '今夜、あの街から', 'color' => '#F8C471', 'pattern' => 'fade'],
            ['name' => 'SARD UNDERGROUND', 'color' => '#82E0AA', 'pattern' => 'pulse'],
            ['name' => 'ZARD', 'color' => '#F1948A', 'pattern' => 'wave'],
            ['name' => 'ザ・ブラックキャンディーズ', 'color' => '#C39BD3', 'pattern' => 'blink'],
            ['name' => '‐真天地開闢集団‐ジグザグ', 'color' => '#F7DC6F', 'pattern' => 'pulse'],
            ['name' => 'ZYYGSHINPEI', 'color' => '#85C1E9', 'pattern' => 'wave'],
            ['name' => 'MUSCLE ATTACK', 'color' => '#F8C471', 'pattern' => 'blink'],
            ['name' => '菅井純愛', 'color' => '#82E0AA', 'pattern' => 'fade'],
            ['name' => 'Sensation', 'color' => '#F1948A', 'pattern' => 'pulse'],
            ['name' => 'DAIGO', 'color' => '#C39BD3', 'pattern' => 'wave'],
            ['name' => '高原由妃', 'color' => '#F7DC6F', 'pattern' => 'fade'],
            ['name' => '高山賢人', 'color' => '#85C1E9', 'pattern' => 'pulse'],
            ['name' => 'TMG', 'color' => '#F8C471', 'pattern' => 'blink'],
            ['name' => 'DIMENSION', 'color' => '#82E0AA', 'pattern' => 'wave'],
            ['name' => '新浜レオン', 'color' => '#F1948A', 'pattern' => 'fade'],
            ['name' => '新山詩織', 'color' => '#C39BD3', 'pattern' => 'pulse'],
            ['name' => '寧音', 'color' => '#F7DC6F', 'pattern' => 'wave'],
            ['name' => 'KNOCK OUT MONKEY', 'color' => '#85C1E9', 'pattern' => 'blink'],
            ['name' => 'B.B.クィーンズ', 'color' => '#F8C471', 'pattern' => 'fade'],
            ['name' => 'BREAKERZ', 'color' => '#82E0AA', 'pattern' => 'pulse'],
            ['name' => '未完成', 'color' => '#F1948A', 'pattern' => 'wave'],
            ['name' => 'ブレイブ', 'color' => '#C39BD3', 'pattern' => 'blink'],
            ['name' => '宮川愛李', 'color' => '#F7DC6F', 'pattern' => 'fade'],
            ['name' => 'MADEINRAKU', 'color' => '#85C1E9', 'pattern' => 'pulse'],
            ['name' => 'Ran', 'color' => '#F8C471', 'pattern' => 'wave'],
            ['name' => 'RIMA', 'color' => '#82E0AA', 'pattern' => 'fade'],
            ['name' => 'Rainy。', 'color' => '#F1948A', 'pattern' => 'pulse'],
            ['name' => 'Ryo', 'color' => '#C39BD3', 'pattern' => 'wave'],
        ];

        foreach ($artists as $artistData) {
            $artist = Artist::create([
                'artist_name' => $artistData['name'],
                'artist_description' => 'B ZONE所属のアーティスト。',
                'artist_color' => $artistData['color'],
                'artist_pattern' => $artistData['pattern'],
                'artist_brightness' => 85,
                'artist_bpm' => 120,
                'artist_intensity' => 'medium',
                'artist_color_scheme' => 'artist',
                'artist_is_active' => true
            ]);

            // 各アーティストにサンプル楽曲を追加
            Song::create([
                'artist_id' => $artist->id,
                'song_name' => $artistData['name'] . 'の楽曲',
                'song_description' => $artistData['name'] . 'のサンプル楽曲。',
                'song_color' => $artistData['color'],
                'song_pattern' => $artistData['pattern'],
                'song_brightness' => 85,
                'song_bpm' => 120,
                'song_intensity' => 'medium',
                'song_color_scheme' => 'artist',
                'song_is_active' => true
            ]);
        }

        // その他のB ZONE所属アーティスト
        $otherArtist = Artist::create([
            'artist_name' => 'その他のアーティスト',
            'artist_description' => 'B ZONE所属のその他のアーティスト。',
            'artist_color' => '#999999',
            'artist_pattern' => 'solid',
            'artist_brightness' => 80,
            'artist_bpm' => 120,
            'artist_intensity' => 'medium',
            'artist_color_scheme' => 'monochrome',
            'artist_is_active' => true
        ]);

        Song::create([
            'artist_id' => $otherArtist->id,
            'song_name' => 'サンプル楽曲',
            'song_description' => 'その他のアーティストのサンプル楽曲。',
            'song_color' => '#999999',
            'song_pattern' => 'solid',
            'song_brightness' => 80,
            'song_bpm' => 120,
            'song_intensity' => 'medium',
            'song_color_scheme' => 'monochrome',
            'song_is_active' => true
        ]);

        $this->command->info('B ZONE所属アーティストと楽曲のデータを登録しました。');
    }
}
