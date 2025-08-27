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
        Schema::table('qr_codes', function (Blueprint $table) {
            // アバター表情オプション（5種類）
            $table->json('qr_avatar_expressions')->nullable()->comment('アバター表情オプション（JSON配列）');
            
            // アバター行動オプション（5種類）
            $table->json('qr_avatar_actions')->nullable()->comment('アバター行動オプション（JSON配列）');
            
            // 背景色オプション（5種類）
            $table->json('qr_background_colors')->nullable()->comment('背景色オプション（JSON配列）');
            
            // エフェクトオプション（5種類）
            $table->json('qr_effects')->nullable()->comment('エフェクトオプション（JSON配列）');
            
            // サウンドオプション（5種類）
            $table->json('qr_sounds')->nullable()->comment('サウンドオプション（JSON配列）');
            
            // オプション機能の有効/無効フラグ
            $table->boolean('qr_options_enabled')->default(false)->comment('オプション機能の有効/無効');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn([
                'qr_avatar_expressions',
                'qr_avatar_actions', 
                'qr_background_colors',
                'qr_effects',
                'qr_sounds',
                'qr_options_enabled'
            ]);
        });
    }
};
