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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('artist_name', 255)->unique();
            $table->text('artist_description')->nullable();
            $table->string('artist_color', 7)->default('#FF0000'); // ヘックスカラー
            $table->string('artist_pattern', 50)->default('solid');
            $table->integer('artist_brightness')->default(100);
            $table->integer('artist_bpm')->default(120);
            $table->string('artist_intensity', 20)->default('medium');
            $table->string('artist_color_scheme', 50)->default('artist');
            $table->boolean('artist_is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
