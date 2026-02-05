<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akademik_ekuivalensi', function (Blueprint $table) {
            $table->id();
            
            // Konteks Prodi (Karena ekuivalensi biasanya kebijakan internal Prodi)
            $table->foreignId('prodi_id')->constrained('ref_prodi')->cascadeOnDelete();

            // MK ASAL: MK yang ada di Kurikulum Lama mahasiswa (yang ingin diakui di transkrip)
            $table->foreignId('mk_asal_id')->constrained('master_mata_kuliahs');
            
            // MK TUJUAN: MK yang ditawarkan di Jadwal/Kurikulum Baru (yang secara fisik diikuti mahasiswa)
            $table->foreignId('mk_tujuan_id')->constrained('master_mata_kuliahs');

            $table->string('nomor_sk')->nullable(); // Dasar hukum penyetaraan
            $table->text('keterangan')->nullable(); // Misal: "Penyetaraan Kurikulum 2017 ke MBKM 2024"
            
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indeks unik agar tidak ada duplikasi pemetaan yang sama
            $table->unique(['mk_asal_id', 'mk_tujuan_id'], 'unique_ekuivalensi_pair');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_ekuivalensi');
    }
};