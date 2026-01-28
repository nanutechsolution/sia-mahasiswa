<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_prodi', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Fakultas. Restrict: Jangan hapus fakultas jika masih ada prodi.
            $table->foreignId('fakultas_id')
                  ->constrained('ref_fakultas')
                  ->restrictOnDelete();
            
            // Kode Dikti (misal: 55201 untuk Informatika)
            $table->string('kode_prodi_dikti', 10)->nullable()->index();
            
            // Kode Internal Kampus (misal: TI, SI, AK)
            $table->string('kode_prodi_internal', 10)->unique();
            
            $table->string('nama_prodi', 100);
            
            // S1, D3, S2 (Penting untuk validasi batas studi)
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'S3', 'PROFESI']);
            
            $table->string('gelar_lulusan', 50)->nullable(); // S.Kom, S.E.
            
            // GUID Feeder
            $table->uuid('id_feeder')->nullable()->index();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_prodi');
    }
};