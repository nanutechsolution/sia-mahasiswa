<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_program_kelas', function (Blueprint $table) {
            $table->id(); // BigInt
            
            // Nama display: "Reguler Pagi", "Ekstensi Karyawan"
            $table->string('nama_program', 50); 
            
            // Kode unik internal: "REG", "EKS", "INT"
            // Digunakan di codingan jika butuh logic spesifik
            $table->string('kode_internal', 10)->unique();
            
            // Mapping ke ID Jenis Pendaftaran/Kelas di Neo Feeder (jika ada)
            // Memudahkan sinkronisasi nanti
            $table->string('id_jenis_kelas_feeder')->nullable(); 

            $table->boolean('is_active')->default(true);
            $table->text('deskripsi')->nullable(); // Penjelasan peruntukan kelas
            
            $table->timestamps();
            $table->softDeletes(); // Audit safety
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_program_kelas');
    }
};