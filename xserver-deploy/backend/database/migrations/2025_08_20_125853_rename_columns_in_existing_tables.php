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
        // usersテーブルはLaravelの基本テーブルのため、変更しない
        // 代わりにphoneカラムのみ追加
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });

        // pre_registrationsテーブルのカラム名変更
        Schema::table('pre_registrations', function (Blueprint $table) {
            $table->renameColumn('temp_id', 'prereg_temp_id');
            $table->renameColumn('phone', 'prereg_phone');
            $table->renameColumn('password', 'prereg_password');
            $table->renameColumn('is_verified', 'prereg_is_verified');
            $table->renameColumn('verified_at', 'prereg_verified_at');
            $table->renameColumn('created_at', 'prereg_created_at');
            $table->renameColumn('updated_at', 'prereg_updated_at');
            
            // avatar_choiceカラムを追加（存在しない場合）
            if (!Schema::hasColumn('pre_registrations', 'prereg_avatar_choice')) {
                $table->string('prereg_avatar_choice')->nullable()->after('prereg_verified_at');
            }
        });

        // otp_codesテーブルは既に新しいカラム名で作成されているため、変更しない

        // qr_codesテーブルのカラム名変更と追加
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->renameColumn('code', 'qr_code');
            $table->renameColumn('title', 'qr_title');
            $table->renameColumn('description', 'qr_description');
            $table->renameColumn('points', 'qr_points');
            $table->renameColumn('is_active', 'qr_is_active');
            $table->renameColumn('expires_at', 'qr_expires_at');
            $table->renameColumn('created_at', 'qr_created_at');
            $table->renameColumn('updated_at', 'qr_updated_at');
            
            // 新しいカラムを追加
            $table->boolean('qr_is_liveevent')->default(true)->after('qr_description');
            $table->text('qr_artist_name')->nullable()->after('qr_is_liveevent');
            $table->text('qr_contents')->nullable()->after('qr_artist_name');
            $table->boolean('qr_is_multiple')->default(false)->after('qr_points');
        });

        // claimsテーブルをqr_use_listにリネーム
        Schema::rename('claims', 'qr_use_list');
        Schema::table('qr_use_list', function (Blueprint $table) {
            $table->renameColumn('user_id', 'qruse_user_id');
            $table->renameColumn('qr_code_id', 'qruse_qr_code_id');
            $table->renameColumn('points_earned', 'qruse_points_earned');
            $table->renameColumn('claimed_at', 'qruse_claimed_at');
            $table->renameColumn('created_at', 'qruse_created_at');
            $table->renameColumn('updated_at', 'qruse_updated_at');
        });

        // news_itemsテーブルのカラム名変更
        Schema::table('news_items', function (Blueprint $table) {
            $table->renameColumn('title', 'gamenews_title');
            $table->renameColumn('content', 'gamenews_content');
            $table->renameColumn('image_url', 'gamenews_image_url');
            $table->renameColumn('is_published', 'gamenews_is_published');
            $table->renameColumn('published_at', 'gamenews_published_at');
            $table->renameColumn('created_at', 'gamenews_created_at');
            $table->renameColumn('updated_at', 'gamenews_updated_at');
        });

        // Laravelの基本テーブル（personal_access_tokens, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs）は変更しない
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 元に戻す処理（必要に応じて実装）
    }
};
