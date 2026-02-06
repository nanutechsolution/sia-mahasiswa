<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('krs_detail', function (Blueprint $table) {
            // Jadikan nullable karena Skripsi/MBKM tidak punya jadwal fisik
            $table->uuid('jadwal_kuliah_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert (hati-hati jika sudah ada data null)
        // Schema::table('krs_detail', function (Blueprint $table) {
        //     $table->uuid('jadwal_kuliah_id')->nullable(false)->change();
        // });
    }
};