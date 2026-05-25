<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom bukti_surat untuk fitur Izin/Sakit
     * dan menjadikan qr_code_id nullable agar record Izin/Sakit
     * bisa disimpan tanpa referensi ke QR Code.
     */
    public function up(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->string('bukti_surat')->nullable()->after('status_image');
            $table->foreignId('qr_code_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->dropColumn('bukti_surat');
            $table->foreignId('qr_code_id')->nullable(false)->change();
        });
    }
};
