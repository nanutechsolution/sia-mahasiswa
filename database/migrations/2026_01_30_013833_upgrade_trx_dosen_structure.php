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
        // 1. Hapus tabel trx_dosen lama jika ada (Clean Slate)
        Schema::dropIfExists('trx_dosen');
        // 2. Hapus tabel dosens lama jika ada (Clean Slate)
        // Schema::dropIfExists('dosens'); 

        // 2. Buat ulang dengan struktur yang benar (UUID)
        Schema::create('trx_dosen', function (Blueprint $table) {
            // PENTING: Gunakan UUID agar ID lama dari tabel 'dosens' bisa dipindahkan kesini
            // tanpa merusak relasi di tabel jadwal_kuliah atau krs
            $table->uuid('id')->primary(); 
            
            // Relasi ke Biodata Pusat
            $table->foreignId('person_id')->constrained('ref_person')->cascadeOnDelete();
            
            // Relasi Akademik
            $table->foreignId('prodi_id')->constrained('ref_prodi'); // Homebase
            
            $table->string('nidn', 20)->nullable()->unique();
            $table->string('nuptk', 20)->nullable()->unique(); // Tambahan: NUPTK
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_dosen');
    }
};