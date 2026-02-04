<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Standar Mutu (PPEPP - Penetapan)
        Schema::create('lpm_standars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_standar')->unique(); // Cth: STD-AKD-01
            $table->string('nama_standar');
            $table->enum('kategori', ['AKADEMIK', 'NON-AKADEMIK']);
            $table->text('pernyataan_standar');
            $table->integer('target_pencapaian')->default(100);
            $table->string('satuan')->default('%');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Indikator Kinerja Utama & Tambahan (IKU/IKT)
        Schema::create('lpm_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_id')->constrained('lpm_standars')->cascadeOnDelete();
            $table->string('nama_indikator');
            $table->string('slug')->unique();
            $table->decimal('bobot', 5, 2)->default(0);
            $table->string('sumber_data_siakad')->nullable(); // Mapping ke tabel/field di siakad
            $table->timestamps();
        });

        // 3. Repositori Dokumen Mutu (Digital Archive)
        Schema::create('lpm_dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->enum('jenis', ['KEBIJAKAN', 'MANUAL', 'STANDAR', 'FORMULIR']);
            $table->string('file_path');
            $table->string('versi')->default('1.0');
            $table->date('tgl_berlaku');
            $table->timestamps();
        });

        // 4. Audit Mutu Internal (AMI) - Sesi Audit
        Schema::create('lpm_ami_periodes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode'); // Cth: AMI Tahun 2025/2026
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->enum('status', ['DRAFT', 'ON-GOING', 'FINISHED'])->default('DRAFT');
            $table->timestamps();
        });

        // 5. Temuan Audit (AMI Findings)
        Schema::create('lpm_ami_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('lpm_ami_periodes');
            $table->foreignId('prodi_id')->constrained('ref_prodi');
            $table->foreignId('standar_id')->constrained('lpm_standars');
            $table->string('auditor_name');
            $table->enum('klasifikasi', ['OB', 'KTS_MINOR', 'KTS_MAYOR']); // Observasi / Ketidaksesuaian
            $table->text('deskripsi_temuan');
            $table->text('akar_masalah')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->date('deadline_perbaikan')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpm_ami_findings');
        Schema::dropIfExists('lpm_ami_periodes');
        Schema::dropIfExists('lpm_dokumens');
        Schema::dropIfExists('lpm_indikators');
        Schema::dropIfExists('lpm_standars');
    }
};