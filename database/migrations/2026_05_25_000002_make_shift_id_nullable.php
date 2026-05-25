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
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->change();
        });

        Schema::table('absens', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable(false)->change();
        });

        Schema::table('absens', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable(false)->change();
        });
    }
};
