<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID karena sering diakses publik/API
            
            // Konteks Waktu
            $table->foreignId('tahun_akademik_id')->constrained('ref_tahun_akademik');
            
            // Konten Kelas
            $table->foreignId('mata_kuliah_id')->constrained('master_mata_kuliahs');
            $table->foreignUuid('dosen_id')->constrained('dosens'); // Dosen Pengampu Utama
            $table->string('nama_kelas', 10); // A, B, C, Pagi, Malam
            
            // Waktu & Tempat
            $table->string('hari', 10)->nullable(); // Senin, Selasa
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('ruang', 50)->nullable(); // Bisa relasi ke tabel ruang jika ada
            
            $table->integer('kuota_kelas')->default(40);
            $table->integer('isi_kelas')->default(0); // Cache count mahasiswa KRS
            
            // === LOGIC PROGRAM KELAS ===
            // Jika NULL = Open Class (Semua bisa ambil)
            // Jika Terisi = Restricted Class (Hanya program kelas tertentu)
            $table->foreignId('id_program_kelas_allow')
                  ->nullable()
                  ->constrained('ref_program_kelas')
                  ->comment('Jika diisi, jadwal ini HANYA terlihat oleh mahasiswa program kelas tsb');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kuliah');
    }
};