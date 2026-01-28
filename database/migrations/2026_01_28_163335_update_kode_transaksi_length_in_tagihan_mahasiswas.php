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
        Schema::table('tagihan_mahasiswas', function (Blueprint $table) {
            // Perbesar dari 20 menjadi 60 karakter agar aman untuk NIM panjang
            $table->string('kode_transaksi', 60)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_mahasiswas', function (Blueprint $table) {
            // Kembalikan ke 20 (Hati-hati, data bisa terpotong jika rollback)
            $table->string('kode_transaksi', 20)->change();
        });
    }
};