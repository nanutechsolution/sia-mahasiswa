<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID agar URL profil aman
            
            // Relasi ke User Login (One-to-One)
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Identitas Akademik
            $table->string('nim', 20)->unique();
            $table->string('nama_lengkap', 150);
            $table->integer('angkatan_id'); // Foreign key manual ke ref_angkatan.id_tahun
            
            // RELASI CORE: Mengikat mahasiswa ke Prodi & Program Kelas
            $table->foreignId('prodi_id')->constrained('ref_prodi')->restrictOnDelete();
            $table->foreignId('program_kelas_id')->constrained('ref_program_kelas')->restrictOnDelete();
            
            // Biodata Pribadi (Sesuai Feeder)
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nik', 16)->nullable()->index(); // Index untuk pencarian cepat
            $table->string('nomor_hp', 15)->nullable();
            $table->string('email_pribadi', 100)->nullable();
            
            // Data Fleksibel (Nama Ibu, Ayah, Alamat, Hobi, Ukuran Jaket, dll)
            // JSON Column agar tidak perlu alter table jika dikti minta data aneh-aneh
            $table->json('data_tambahan')->nullable(); 
            
            // Feeder Sync
            $table->uuid('id_pd_feeder')->nullable()->index(); // ID Peserta Didik di Feeder
            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraint manual untuk angkatan (karena primary key-nya integer custom)
            $table->foreign('angkatan_id')->references('id_tahun')->on('ref_angkatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};