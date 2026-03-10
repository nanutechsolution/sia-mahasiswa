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
        // ==========================================
        // 1. MODUL KONTRAK KULIAH (BOBOT DOSEN)
        // ==========================================
        // Tabel ini memungkinkan dosen mengubah bobot % (Tugas, UTS, UAS) 
        // secara spesifik untuk kelas yang diajarnya tanpa mengubah master kurikulum.
        Schema::create('jadwal_komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->char('jadwal_kuliah_id', 36);
            $table->foreignId('komponen_id')->constrained('ref_komponen_nilai')->cascadeOnDelete();
            $table->decimal('bobot_persen', 5, 2);
            $table->timestamps();

            $table->foreign('jadwal_kuliah_id')->references('id')->on('jadwal_kuliah')->cascadeOnDelete();
            $table->unique(['jadwal_kuliah_id', 'komponen_id'], 'jkn_jadwal_komponen_unique');
        });


        // ==========================================
        // 2. MODUL UJIAN (UTS / UAS)
        // ==========================================

        // A. Header Penjadwalan Ujian
        Schema::create('jadwal_ujians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('jadwal_kuliah_id', 36);
            $table->enum('jenis_ujian', ['UTS', 'UAS', 'SUSULAN', 'LAINNYA']);
            $table->date('tanggal_ujian');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->foreignId('ruang_id')->nullable()->constrained('ref_ruang')->nullOnDelete();
            $table->string('metode_ujian', 50)->default('TERTULIS'); // TERTULIS, CBT, PRAKTEK, TAKE_HOME
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('jadwal_kuliah_id')->references('id')->on('jadwal_kuliah')->cascadeOnDelete();
        });

        // B. Pengawas Ujian (Relasi ke Person, karena staf BAAK juga bisa jadi pengawas)
        Schema::create('jadwal_ujian_pengawas', function (Blueprint $table) {
            $table->id();
            $table->char('jadwal_ujian_id', 36);
            $table->foreignId('person_id')->constrained('ref_person')->cascadeOnDelete();
            $table->string('peran', 20)->default('PENGAWAS'); // UTAMA, PENDAMPING
            $table->timestamps();

            $table->foreign('jadwal_ujian_id')->references('id')->on('jadwal_ujians')->cascadeOnDelete();
            $table->unique(['jadwal_ujian_id', 'person_id'], 'jup_ujian_person_unique');
        });

        // C. Peserta & Presensi Ujian (Untuk Cetak Berita Acara Ujian / BAP)
        Schema::create('jadwal_ujian_pesertas', function (Blueprint $table) {
            $table->id();
            $table->char('jadwal_ujian_id', 36);
            $table->foreignId('krs_detail_id')->constrained('krs_detail')->cascadeOnDelete();
            $table->char('status_kehadiran', 1)->default('A'); // H=Hadir, I=Izin, S=Sakit, A=Alpha
            $table->string('nomor_kursi', 10)->nullable();
            $table->dateTime('waktu_check_in')->nullable();
            $table->text('catatan_pelanggaran')->nullable(); // Jika mahasiswa mencontek
            $table->timestamps();

            $table->foreign('jadwal_ujian_id')->references('id')->on('jadwal_ujians')->cascadeOnDelete();
            $table->unique(['jadwal_ujian_id', 'krs_detail_id'], 'jup_ujian_krsd_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujian_pesertas');
        Schema::dropIfExists('jadwal_ujian_pengawas');
        Schema::dropIfExists('jadwal_ujians');
        Schema::dropIfExists('jadwal_komponen_nilai');
    }
};
