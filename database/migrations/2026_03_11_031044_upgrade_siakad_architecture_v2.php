<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Master Ruang
        Schema::create('ref_ruang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruang', 20)->unique();
            $table->string('nama_ruang', 100);
            $table->integer('kapasitas')->default(40);
            $table->boolean('is_active')->default(true);
        });

        // Update Jadwal Kuliah (Hapus dosen_id tunggal & ruang varchar, ganti relasi ruang)
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
            $table->dropColumn('ruang');
            $table->foreignId('ruang_id')->nullable()->after('jam_selesai')->constrained('ref_ruang');
        });

        // 2. Relasi Mahasiswa -> Kurikulum
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->foreignId('kurikulum_id')->nullable()->after('prodi_id')
                ->constrained('master_kurikulums')->restrictOnDelete();
        });

        // 3. Team Teaching (Jadwal Dosen Pivot)
        Schema::create('jadwal_kuliah_dosen', function (Blueprint $table) {
            $table->id();
            $table->char('jadwal_kuliah_id', 36);
            $table->char('dosen_id', 36);
            $table->boolean('is_koordinator')->default(false);
            $table->integer('rencana_tatap_muka')->default(14);
            $table->timestamps();

            $table->foreign('jadwal_kuliah_id')->references('id')->on('jadwal_kuliah')->cascadeOnDelete();
            $table->foreign('dosen_id')->references('id')->on('trx_dosen')->restrictOnDelete();
        });

        // 4. Perbaikan Prasyarat (Many-to-Many Pivot)
        Schema::table('kurikulum_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['prasyarat_mk_id']);
            $table->dropColumn(['prasyarat_mk_id', 'min_nilai_prasyarat']);
        });

        Schema::create('kurikulum_mk_prasyarat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_mk_id')->constrained('kurikulum_mata_kuliah')->cascadeOnDelete();
            $table->foreignId('prasyarat_mk_id')->constrained('master_mata_kuliahs')->cascadeOnDelete();
            $table->string('min_nilai_huruf', 2)->default('D');
        });

        // 5. Harden KRS Detail
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->foreignId('mata_kuliah_id')->nullable()->after('jadwal_kuliah_id')
                ->constrained('master_mata_kuliahs')->nullOnDelete();
            $table->boolean('is_locked')->default(false)->after('is_published');
            // Mencegah SKS Ganda di semester yg sama
            $table->unique(['krs_id', 'mata_kuliah_id'], 'krs_detail_prevent_double_mk');
        });

        // 6. Transkrip Materialized View
        Schema::create('akademik_transkrip', function (Blueprint $table) {
            $table->id();
            $table->char('mahasiswa_id', 36);
            $table->foreignId('mata_kuliah_id')->constrained('master_mata_kuliahs')->restrictOnDelete();
            $table->foreignId('krs_detail_id')->constrained('krs_detail')->restrictOnDelete();
            $table->integer('sks_diakui');
            $table->decimal('nilai_angka_final', 5, 2);
            $table->string('nilai_huruf_final', 2);
            $table->decimal('nilai_indeks_final', 3, 2);
            $table->boolean('is_konversi')->default(false);
            $table->timestamps();

            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->cascadeOnDelete();
            $table->unique(['mahasiswa_id', 'mata_kuliah_id'], 'unik_transkrip_mhs_mk');
        });
    }

    public function down(): void
    {
        // Tulis logika rollback (drop tables & reverse columns) jika diperlukan
    }
};
