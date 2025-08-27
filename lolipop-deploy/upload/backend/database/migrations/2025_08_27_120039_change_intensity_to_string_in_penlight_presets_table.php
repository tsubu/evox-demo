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
        Schema::table('penlight_presets', function (Blueprint $table) {
            // intensityフィールドをinteger型からstring型に変更
            $table->string('intensity', 20)->default('medium')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penlight_presets', function (Blueprint $table) {
            // intensityフィールドをstring型からinteger型に戻す
            $table->integer('intensity')->default(50)->change();
        });
    }
};
