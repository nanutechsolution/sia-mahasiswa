<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel ANGKATAN (Cohort)
        // Menyimpan metadata per generasi masuk, misal: Batas Masa Studi
        Schema::create('ref_angkatan', function (Blueprint $table) {
            // Kita gunakan tahun sebagai Primary Key karena unik & integer (2024, 2025)
            $table->integer('id_tahun')->primary(); 
            
            // Batas maksimal studi (misal 2024 + 7 tahun = 2031)
            $table->integer('batas_tahun_studi')->nullable();
            
            $table->boolean('is_active_pmb')->default(false); // Sedang buka PMB?
            $table->timestamps();
        });

        // 2. Tabel TAHUN AKADEMIK (Semester)
        // Format ID standar Dikti: YYYY1 (Ganjil), YYYY2 (Genap), YYYY3 (Pendek)
        Schema::create('ref_tahun_akademik', function (Blueprint $table) {
            $table->id(); // Bisa pakai Auto Increment atau input manual ID 20241
            
            $table->string('kode_tahun', 5)->unique(); // "20241"
            $table->string('nama_tahun', 50); // "Ganjil 2024/2025"
            $table->integer('semester')->comment('1=Ganjil, 2=Genap, 3=Pendek');
            
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            // Flag penting untuk sistem: Hanya boleh ada 1 yang TRUE
            $table->boolean('is_active')->default(false);
            
            // Flag untuk KRS: Apakah periode ini boleh KRS?
            $table->boolean('buka_krs')->default(false);
            $table->boolean('buka_input_nilai')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_tahun_akademik');
        Schema::dropIfExists('ref_angkatan');
    }
};