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
        // Drop foreign keys first to avoid constraint errors
        Schema::table('absens', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });

        Schema::dropIfExists('shifts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->enum('shift_name', ['Pagi', 'Siang', 'Malam']);
            $table->time('in_time');
            $table->time('out_time');
            $table->timestamps();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('absens', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
