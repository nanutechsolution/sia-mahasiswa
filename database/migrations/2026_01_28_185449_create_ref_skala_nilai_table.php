<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('ref_skala_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('huruf', 2); // Contoh: A, B+, C
            $table->decimal('bobot_indeks', 3, 2); // Contoh: 4.00, 3.50
            $table->decimal('nilai_min', 5, 2); // Batas bawah nilai angka (Contoh: 80.00)
            $table->decimal('nilai_max', 5, 2); // Batas atas nilai angka (Contoh: 100.00)
            $table->boolean('is_lulus')->default(true); // Status kelulusan untuk syarat prasyarat
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_skala_nilai');
    }
};