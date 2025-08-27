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
        Schema::create('qr_liveevents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('qrevent_user_id');
            $table->bigInteger('qrevent_qr_code_id');
            $table->text('qrevent_artist_name');
            $table->string('qrevent_nickname')->nullable();
            $table->string('qrevent_avatar_choice')->nullable();
            $table->string('qrevent_uniquecode', 255)->unique();
            $table->timestamp('qrevent_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_liveevents');
    }
};
