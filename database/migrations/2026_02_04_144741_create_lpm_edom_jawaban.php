<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menyimpan jawaban mahasiswa per butir pertanyaan per mata kuliah
        Schema::create('lpm_edom_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->constrained('krs_detail')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('lpm_kuisioner_pertanyaan');
            $table->integer('skor'); // 1-4 atau 1-5
            $table->timestamps();
        });

        // Flag untuk menandai pengisian per mata kuliah selesai
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->boolean('is_edom_filled')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpm_edom_jawaban');
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->dropColumn('is_edom_filled');
        });
    }
};