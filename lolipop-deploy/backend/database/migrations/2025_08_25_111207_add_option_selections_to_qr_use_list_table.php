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
        Schema::table('qr_use_list', function (Blueprint $table) {
            // ユーザーが選択したオプション
            $table->string('qruse_selected_expression')->nullable()->comment('選択されたアバター表情');
            $table->string('qruse_selected_action')->nullable()->comment('選択されたアバター行動');
            $table->string('qruse_selected_background')->nullable()->comment('選択された背景色');
            $table->string('qruse_selected_effect')->nullable()->comment('選択されたエフェクト');
            $table->string('qruse_selected_sound')->nullable()->comment('選択されたサウンド');
            
            // オプション選択のタイムスタンプ
            $table->timestamp('qruse_options_selected_at')->nullable()->comment('オプション選択時刻');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_use_list', function (Blueprint $table) {
            $table->dropColumn([
                'qruse_selected_expression',
                'qruse_selected_action',
                'qruse_selected_background', 
                'qruse_selected_effect',
                'qruse_selected_sound',
                'qruse_options_selected_at'
            ]);
        });
    }
};
