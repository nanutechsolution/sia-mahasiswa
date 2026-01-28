<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // HEADER KRS
        Schema::create('krs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mahasiswa_id')->constrained('mahasiswas');
            $table->foreignId('tahun_akademik_id')->constrained('ref_tahun_akademik');
            
            $table->dateTime('tgl_krs')->useCurrent();
            
            // Status: DRAFT, AJUKAN, DISETUJUI, DITOLAK, KUNCI
            $table->string('status_krs', 20)->default('DRAFT'); 
            $table->foreignUuid('dosen_wali_id')->nullable()->constrained('dosens');
            
            $table->timestamps();
            
            // Satu mahasiswa satu KRS per semester
            $table->unique(['mahasiswa_id', 'tahun_akademik_id']);
        });

        // DETAIL KRS (Isi MK yang diambil) & NILAI
        Schema::create('krs_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('krs_id')->constrained('krs')->cascadeOnDelete();
            
            // Mengambil Jadwal (yang didalamnya sudah ada MK & Dosen)
            $table->foreignUuid('jadwal_kuliah_id')->constrained('jadwal_kuliah');
            
            // Status Ambil: B=Baru, U=Ulang
            $table->char('status_ambil', 1)->default('B');
            
            // === AREA PENILAIAN ===
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_uts', 5, 2)->default(0);
            $table->decimal('nilai_uas', 5, 2)->default(0);
            
            // Nilai Akhir
            $table->decimal('nilai_angka', 5, 2)->default(0); // 0 - 100
            $table->string('nilai_huruf', 2)->nullable(); // A, B+, C
            $table->decimal('nilai_indeks', 3, 2)->default(0); // 4.00, 3.50
            
            $table->boolean('is_published')->default(false); // Nilai sudah dirilis ke MHS?
            
            $table->timestamps();
            // Tidak boleh ambil jadwal yang sama 2x
            $table->unique(['krs_id', 'jadwal_kuliah_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_detail');
        Schema::dropIfExists('krs');
    }
};