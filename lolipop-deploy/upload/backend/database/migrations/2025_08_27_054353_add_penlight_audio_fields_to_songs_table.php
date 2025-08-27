<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->boolean('penlight_audio_sync')->default(false)->after('song_is_active');
            $table->decimal('penlight_music_intensity', 3, 2)->default(0.80)->after('penlight_audio_sync');
            $table->decimal('penlight_noise_gate_threshold', 5, 2)->default(35.00)->after('penlight_music_intensity');
            $table->integer('penlight_frequency_low')->default(2)->after('penlight_noise_gate_threshold');
            $table->integer('penlight_frequency_high')->default(25)->after('penlight_frequency_low');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn([
                'penlight_audio_sync',
                'penlight_music_intensity',
                'penlight_noise_gate_threshold',
                'penlight_frequency_low',
                'penlight_frequency_high'
            ]);
        });
    }
};
