<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tabel yang sudah ada agar bersih saat dijalankan ulang (sesuai permintaan)
        // Urutan drop harus dari tabel anak (foreign key) ke tabel induk
        Schema::dropIfExists('krs_detail_nilai');
        Schema::dropIfExists('kurikulum_komponen_nilai');
        Schema::dropIfExists('ref_komponen_nilai');

        // 1. Master Komponen (Aktif, Tugas, UTS, UAS, Quiz, dsb)
        Schema::create('ref_komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_komponen'); // Contoh: Aktif, Tugas
            $table->string('slug')->unique(); // contoh: aktif, tugas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Setting Bobot per Kurikulum (Agar tiap prodi boleh beza bobot)
        Schema::create('kurikulum_komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('master_kurikulums')->cascadeOnDelete();
            $table->foreignId('komponen_id')->constrained('ref_komponen_nilai');
            $table->decimal('bobot_persen', 5, 2); // Contoh: 15.00 (bermaksud 15%)
            $table->timestamps();
        });

        // 3. Simpan Nilai Mahasiswa secara Dinamik
        Schema::create('krs_detail_nilai', function (Blueprint $table) {
            $table->id();
            // Menggunakan foreignId agar kompatibel dengan tipe ID standar di krs_detail
            $table->foreignId('krs_detail_id')->constrained('krs_detail')->cascadeOnDelete();
            $table->foreignId('komponen_id')->constrained('ref_komponen_nilai');
            $table->decimal('nilai_angka', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_detail_nilai');
        Schema::dropIfExists('kurikulum_komponen_nilai');
        Schema::dropIfExists('ref_komponen_nilai');
    }
};