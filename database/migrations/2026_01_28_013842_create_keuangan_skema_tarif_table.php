<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. HEADER SKEMA (Aturan Matriks)
        Schema::create('keuangan_skema_tarif', function (Blueprint $table) {
            $table->id();
            $table->string('nama_skema', 150); // "Tarif TI Reguler Angkatan 2024"
            
            // KUNCI MATRIKS 3 DIMENSI
            // Tarif ini berlaku khusus untuk kombinasi ini:
            $table->integer('angkatan_id'); // Foreign key ke ref_angkatan.id_tahun
            $table->foreignId('prodi_id')->constrained('ref_prodi');
            $table->foreignId('program_kelas_id')->constrained('ref_program_kelas');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Constraint: Tidak boleh ada 2 skema aktif untuk kombinasi yang sama
            $table->unique(['angkatan_id', 'prodi_id', 'program_kelas_id'], 'unique_skema_tarif');
            
            $table->foreign('angkatan_id')->references('id_tahun')->on('ref_angkatan');
        });

        // 2. DETAIL TARIF (Nominal Rupiah)
        Schema::create('keuangan_detail_tarif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_tarif_id')->constrained('keuangan_skema_tarif')->cascadeOnDelete();
            $table->foreignId('komponen_biaya_id')->constrained('keuangan_komponen_biaya');
            
            // Nominal presisi tinggi (19 digit, 2 desimal)
            $table->decimal('nominal', 19, 2)->default(0); 
            
            // Opsional: Jika biaya berbeda per semester (misal smt 1 mahal, smt 2 murah)
            // Jika null, berarti berlaku untuk semua semester
            $table->integer('berlaku_semester')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_detail_tarif');
        Schema::dropIfExists('keuangan_skema_tarif');
    }
};