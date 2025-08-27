<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            // 既存のカラムを確認して、存在しない場合のみ追加
            if (!Schema::hasColumn('songs', 'title_kana')) {
                $table->string('title_kana')->nullable()->comment('楽曲タイトル（カナ）');
            }
            if (!Schema::hasColumn('songs', 'description')) {
                $table->text('description')->nullable()->comment('楽曲説明');
            }
            if (!Schema::hasColumn('songs', 'image_path')) {
                $table->string('image_path')->nullable()->comment('楽曲画像パス');
            }
            if (!Schema::hasColumn('songs', 'key')) {
                $table->string('key')->nullable()->comment('調');
            }
            if (!Schema::hasColumn('songs', 'duration')) {
                $table->integer('duration')->nullable()->comment('楽曲時間（秒）');
            }
            if (!Schema::hasColumn('songs', 'is_active')) {
                $table->boolean('is_active')->default(true)->comment('アクティブ状態');
            }
        });
    }

    public function down()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['title_kana', 'description', 'image_path', 'key', 'duration', 'is_active']);
        });
    }
};
