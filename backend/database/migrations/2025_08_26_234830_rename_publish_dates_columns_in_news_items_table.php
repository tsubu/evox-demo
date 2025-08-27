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
        Schema::table('news_items', function (Blueprint $table) {
            $table->renameColumn('publish_start_at', 'gamenews_publish_start_at');
            $table->renameColumn('publish_end_at', 'gamenews_publish_end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_items', function (Blueprint $table) {
            $table->renameColumn('gamenews_publish_start_at', 'publish_start_at');
            $table->renameColumn('gamenews_publish_end_at', 'publish_end_at');
        });
    }
};
