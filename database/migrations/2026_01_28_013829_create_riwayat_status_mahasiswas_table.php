<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status_mahasiswas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignUuid('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('ref_tahun_akademik');
            
            // Status: A=Aktif, C=Cuti, D=DO, L=Lulus, K=Keluar, N=Non-Aktif (Mangkir)
            $table->char('status_kuliah', 1)->default('A')->index(); 
            
            // IPS & IPK saat semester tersebut (Snapshot data)
            $table->decimal('ips', 4, 2)->default(0);
            $table->decimal('ipk', 4, 2)->default(0);
            $table->integer('sks_semester')->default(0);
            $table->integer('sks_total')->default(0);
            
            // Nomor SK (Jika Cuti/Yudisium/DO)
            $table->string('nomor_sk')->nullable();
            
            $table->timestamps();
            
            // Satu mahasiswa hanya punya 1 status per semester
            $table->unique(['mahasiswa_id', 'tahun_akademik_id'], 'unique_status_per_semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_mahasiswas');
    }
};