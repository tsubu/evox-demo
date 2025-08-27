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
        Schema::table('penlight_presets', function (Blueprint $table) {
            $table->integer('intensity')->default(50)->after('bpm');
            $table->decimal('music_intensity', 5, 2)->default(0.50)->after('audio_sync');
            $table->decimal('noise_gate_threshold', 5, 2)->default(0.10)->after('music_intensity');
            $table->integer('frequency_low')->default(60)->after('noise_gate_threshold');
            $table->integer('frequency_high')->default(2000)->after('frequency_low');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penlight_presets', function (Blueprint $table) {
            $table->dropColumn([
                'intensity',
                'music_intensity',
                'noise_gate_threshold',
                'frequency_low',
                'frequency_high'
            ]);
        });
    }
};
