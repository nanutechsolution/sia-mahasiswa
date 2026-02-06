<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Sesi Pertemuan
        Schema::create('perkuliahan_sesi', function (Blueprint $table) {
            // UUID sangat disarankan untuk data yang tumbuh cepat agar tidak mentok di Integer limit
            $table->uuid('id')->primary(); 
            $table->char('jadwal_kuliah_id', 36);
            
            $table->integer('pertemuan_ke');
            
            // Waktu rencana vs realisasi (penting untuk audit kinerja dosen)
            $table->dateTime('waktu_mulai_rencana');
            $table->dateTime('waktu_mulai_realisasi')->nullable();
            $table->dateTime('waktu_selesai_realisasi')->nullable();
            
            $table->text('materi_kuliah')->nullable();
            $table->text('catatan_dosen')->nullable();
            
            // Token dinamis untuk QR Code (berubah tiap 10 detik di level aplikasi)
            $table->string('token_sesi', 10)->nullable()->index();
            
            // Metode: QR, GPS, FACE_RECOG, MANUAL
            $table->string('metode_validasi', 20)->default('QR');
            
            $table->enum('status_sesi', ['terjadwal', 'dibuka', 'selesai', 'dibatalkan'])->default('terjadwal')->index();
            
            $table->timestamps();
            
            // Indexing untuk pencarian cepat riwayat
            $table->foreign('jadwal_kuliah_id')->references('id')->on('jadwal_kuliah')->onDelete('cascade');
            $table->index(['jadwal_kuliah_id', 'pertemuan_ke']);
        });

        // 2. Tabel Detail Absensi Mahasiswa (Big Data Ready)
        Schema::create('perkuliahan_absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('perkuliahan_sesi_id');
            $table->unsignedBigInteger('krs_detail_id');
            
            // Status lebih lengkap: Hadir, Ijin, Sakit, Alpha, Terlambat
            $table->char('status_kehadiran', 1)->default('A')->index(); 
            
            // Waktu presisi
            $table->dateTime('waktu_check_in')->nullable();
            
            // KUNCI MASA DEPAN: Kolom JSON
            // Bisa simpan: {"ip": "192.168.1.1", "lat": -6.200, "long": 106.816, "device": "iPhone 15", "face_match_score": 98.5}
            // Tidak perlu ubah tabel jika teknologi absensi berubah.
            $table->json('bukti_validasi')->nullable();
            
            // Audit Trail (Jika diubah manual oleh dosen/admin)
            $table->boolean('is_manual_update')->default(false);
            $table->string('modified_by_user_id', 36)->nullable(); // ID User yang mengubah
            $table->string('alasan_perubahan')->nullable(); // Kenapa diubah? (misal: "Sistem Error")

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('perkuliahan_sesi_id')->references('id')->on('perkuliahan_sesi')->onDelete('cascade');
            $table->foreign('krs_detail_id')->references('id')->on('krs_detail')->onDelete('cascade');
            
            // Composite Index untuk performa query dashboard mahasiswa
            // Agar saat load halaman "Riwayat Absen" tidak lemot meski data jutaan
            $table->index(['krs_detail_id', 'status_kehadiran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perkuliahan_absensi');
        Schema::dropIfExists('perkuliahan_sesi');
    }
};