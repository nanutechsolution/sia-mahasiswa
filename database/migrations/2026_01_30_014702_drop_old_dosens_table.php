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
        // 1. Putuskan relasi dari tabel JADWAL KULIAH
        if (Schema::hasTable('jadwal_kuliah')) {
            Schema::table('jadwal_kuliah', function (Blueprint $table) {
                // Hapus constraint lama yang mengarah ke 'dosens'
                $table->dropForeign(['dosen_id']); 
            });
        }

        // 2. Putuskan relasi dari tabel MAHASISWA (Dosen Wali)
        if (Schema::hasTable('mahasiswas')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                $table->dropForeign(['dosen_wali_id']);
            });
        }

        // 3. Putuskan relasi dari tabel KRS (jika ada Dosen Wali snapshot)
        if (Schema::hasTable('krs') && Schema::hasColumn('krs', 'dosen_wali_id')) {
            Schema::table('krs', function (Blueprint $table) {
                // Cek nama constraint dulu biasanya krs_dosen_wali_id_foreign
                // Kita gunakan array syntax agar Laravel otomatis mencari namanya
                try {
                    $table->dropForeign(['dosen_wali_id']);
                } catch (\Exception $e) {
                    // Ignore jika constraint tidak ditemukan
                }
            });
        }

        // 4. HAPUS TABEL LAMA (Sekarang sudah aman)
        Schema::dropIfExists('dosens');

        // 5. SAMBUNGKAN KEMBALI KE TABEL BARU (TRX_DOSEN)
        // Karena ID-nya sama (hasil migrasi), datanya tetap valid.
        
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->foreign('dosen_id')
                  ->references('id')
                  ->on('trx_dosen')
                  ->onDelete('restrict'); // Jangan hapus dosen jika ada jadwal
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->foreign('dosen_wali_id')
                  ->references('id')
                  ->on('trx_dosen')
                  ->nullOnDelete();
        });

        if (Schema::hasTable('krs') && Schema::hasColumn('krs', 'dosen_wali_id')) {
            Schema::table('krs', function (Blueprint $table) {
                $table->foreign('dosen_wali_id')
                      ->references('id')
                      ->on('trx_dosen')
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback karena tabel lama sudah dihapus dan data sudah dipindah.
    }
};