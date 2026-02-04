<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Target IKU per Tahun/Periode
        Schema::create('lpm_iku_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('lpm_indikators')->cascadeOnDelete();
            $table->integer('tahun');
            $table->decimal('target_nilai', 10, 2);
            $table->decimal('capaian_nilai', 10, 2)->default(0);
            $table->text('analisis_kendala')->nullable();
            $table->text('tindakan_koreksi')->nullable();
            $table->timestamps();
        });

        // 2. Kelompok Pertanyaan Kuisioner (Pedagogik, Profesional, dll)
        Schema::create('lpm_kuisioner_kelompok', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok');
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Bank Pertanyaan Kuisioner
        Schema::create('lpm_kuisioner_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('lpm_kuisioner_kelompok')->cascadeOnDelete();
            $table->text('bunyi_pertanyaan');
            $table->enum('tipe_skala', ['1-4', '1-5'])->default('1-4');
            $table->boolean('is_required')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpm_kuisioner_pertanyaan');
        Schema::dropIfExists('lpm_kuisioner_kelompok');
        Schema::dropIfExists('lpm_iku_targets');
    }
};