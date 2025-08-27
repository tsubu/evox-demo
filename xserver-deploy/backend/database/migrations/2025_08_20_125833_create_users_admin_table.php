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
        Schema::create('users_admin', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name');
            $table->string('admin_email')->unique()->nullable();
            $table->string('admin_phone', 20)->unique();
            $table->string('admin_password');
            $table->boolean('admin_dels')->nullable();
            $table->string('admin_remember_token', 100)->nullable();
            $table->boolean('admin_is_verified')->default(false);
            $table->timestamp('admin_verified_at')->nullable();
            $table->timestamp('admin_created_at')->nullable();
            $table->timestamp('admin_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_admin');
    }
};
