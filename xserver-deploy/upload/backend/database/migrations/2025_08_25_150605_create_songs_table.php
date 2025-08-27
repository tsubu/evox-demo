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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->string('song_name', 255);
            $table->text('song_description')->nullable();
            $table->string('song_color', 7)->default('#FF0000'); // ヘックスカラー
            $table->string('song_pattern', 50)->default('solid');
            $table->integer('song_brightness')->default(100);
            $table->integer('song_bpm')->default(120);
            $table->string('song_intensity', 20)->default('medium');
            $table->string('song_color_scheme', 50)->default('artist');
            $table->boolean('song_is_active')->default(true);
            $table->timestamps();
            
            // アーティスト内で曲名は一意
            $table->unique(['artist_id', 'song_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
