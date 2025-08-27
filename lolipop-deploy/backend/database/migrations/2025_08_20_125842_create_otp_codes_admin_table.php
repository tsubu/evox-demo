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
        Schema::create('otp_codes_admin', function (Blueprint $table) {
            $table->id();
            $table->string('adminopt_temp_id', 13);
            $table->string('adminopt_code', 6);
            $table->timestamp('adminopt_expires_at');
            $table->boolean('adminopt_is_used')->default(false);
            $table->timestamp('adminopt_created_at')->nullable();
            $table->timestamp('adminopt_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes_admin');
    }
};
