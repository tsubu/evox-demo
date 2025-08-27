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
        Schema::create('qr_themes', function (Blueprint $table) {
            $table->id();
            $table->string('theme_name', 100)->unique();
            $table->string('theme_description', 255)->nullable();
            $table->boolean('theme_is_active')->default(true);
            $table->json('theme_expressions')->nullable(); // テーマ別表情オプション
            $table->json('theme_actions')->nullable(); // テーマ別行動オプション
            $table->json('theme_backgrounds')->nullable(); // テーマ別背景オプション
            $table->json('theme_effects')->nullable(); // テーマ別エフェクトオプション
            $table->json('theme_sounds')->nullable(); // テーマ別サウンドオプション
            $table->string('theme_color', 7)->default('#3B82F6'); // テーマカラー（HEX）
            $table->string('theme_icon', 50)->nullable(); // テーマアイコン
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_themes');
    }
};
