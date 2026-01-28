<?php

// database/migrations/xxxx_xx_xx_000008_create_master_akademik_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. LIBRARY MATA KULIAH (Bank Data)
        Schema::create('master_mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('ref_prodi'); // Pemilik MK
            
            $table->string('kode_mk', 20); // Kode Internal (misal: TI-101)
            $table->string('nama_mk', 200);
            $table->integer('sks_default')->default(3); // Acuan default
            
            // Jenis MK: Wajib Nasional, Wajib Prodi, Pilihan
            $table->char('jenis_mk', 1)->default('A'); 
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['prodi_id', 'kode_mk']); // Kode unik per prodi
        });

        // 2. HEADER KURIKULUM
        Schema::create('master_kurikulums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('ref_prodi');
            $table->string('nama_kurikulum', 100); // "Kurikulum 2024 KKNI"
            $table->integer('tahun_mulai'); // 2024
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. PIVOT (ISI KURIKULUM)
        // Inilah yang menghubungkan MK ke Kurikulum + Paket Semester
        Schema::create('kurikulum_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('master_kurikulums')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('master_mata_kuliahs')->restrictOnDelete();
            
            $table->integer('semester_paket'); // Paket Semester 1, 2, dst
            $table->integer('sks_tatap_muka'); // Override SKS default jika perlu
            $table->integer('sks_praktek')->default(0);
            $table->integer('sks_lapangan')->default(0);
            
            // Sifat: Wajib (W) / Pilihan (P)
            $table->char('sifat_mk', 1)->default('W'); 
            
            $table->timestamps();
            
            // Satu MK tidak boleh ganda di satu kurikulum
            $table->unique(['kurikulum_id', 'mata_kuliah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum_mata_kuliah');
        Schema::dropIfExists('master_kurikulums');
        Schema::dropIfExists('master_mata_kuliahs');
    }
};