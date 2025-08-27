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
        Schema::create('game_base', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gamebase_userid')->unique();
            $table->integer('gamebase_points')->default(0);
            $table->timestamp('gamebase_email_verified_at')->nullable();
            $table->string('gamebase_avatar_choice')->nullable();
            $table->string('gamebase_nickname')->nullable();
            $table->boolean('gamebase_is_profile_complete')->default(false);
            $table->timestamp('gamebase_created_at')->nullable();
            $table->timestamp('gamebase_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_base');
    }
};
