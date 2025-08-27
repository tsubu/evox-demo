<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('artists', function (Blueprint $table) {
            // 既存のカラムを確認して、存在しない場合のみ追加
            if (!Schema::hasColumn('artists', 'name_kana')) {
                $table->string('name_kana')->nullable()->comment('アーティスト名（カナ）');
            }
            if (!Schema::hasColumn('artists', 'description')) {
                $table->text('description')->nullable()->comment('アーティスト説明');
            }
            if (!Schema::hasColumn('artists', 'image_path')) {
                $table->string('image_path')->nullable()->comment('アーティスト画像パス');
            }
            if (!Schema::hasColumn('artists', 'is_active')) {
                $table->boolean('is_active')->default(true)->comment('アクティブ状態');
            }
        });
    }

    public function down()
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn(['name_kana', 'description', 'image_path', 'is_active']);
        });
    }
};
