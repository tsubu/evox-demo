<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penlight_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('プリセット名');
            $table->text('description')->nullable()->comment('プリセット説明');
            $table->integer('order')->default(0)->comment('表示順序');
            
            // 基本設定
            $table->string('pattern')->default('solid')->comment('パターン: solid, blink, fade, wave, pulse, rainbow, strobe');
            $table->string('color')->default('#FF0000')->comment('基本色');
            $table->integer('brightness')->default(100)->comment('明度(0-100)');
            $table->integer('bpm')->default(120)->comment('BPM');
            
            // 詳細設定
            $table->json('color_scheme')->nullable()->comment('カラースキーム設定');
            $table->json('timing')->nullable()->comment('タイミング設定');
            $table->json('effects')->nullable()->comment('エフェクト設定');
            $table->json('audio_sync')->nullable()->comment('音声連動設定');
            
            // 制御設定
            $table->boolean('is_active')->default(true)->comment('アクティブ状態');
            $table->boolean('is_default')->default(false)->comment('デフォルトプリセット');
            $table->timestamp('activated_at')->nullable()->comment('アクティベート日時');
            
            $table->timestamps();
            
            $table->index('song_id');
            $table->index('order');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penlight_presets');
    }
};
