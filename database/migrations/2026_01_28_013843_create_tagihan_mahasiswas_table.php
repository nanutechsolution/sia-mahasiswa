<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID untuk keamanan link pembayaran
            
            $table->foreignUuid('mahasiswa_id')->constrained('mahasiswas');
            $table->foreignId('tahun_akademik_id')->nullable()->constrained('ref_tahun_akademik');
            
            // Snapshot Data (Agar histori aman walau master berubah)
            $table->string('kode_transaksi', 20)->unique(); // INV-202401-0001
            $table->string('deskripsi', 255); // "SPP Semester Ganjil 2024/2025"
            
            // Angka Keuangan
            $table->decimal('total_tagihan', 19, 2);
            $table->decimal('total_bayar', 19, 2)->default(0);
            $table->decimal('sisa_tagihan', 19, 2)->virtualAs('total_tagihan - total_bayar'); // MySQL Virtual Column
            
            // Status
            $table->enum('status_bayar', ['BELUM', 'CICIL', 'LUNAS'])->default('BELUM')->index();
            $table->date('tenggat_waktu')->nullable();
            
            // Rincian Item Tagihan disimpan dalam JSON untuk efisiensi & snapshot audit
            // Contoh: [{"nama": "SPP Tetap", "nominal": 3000000}, {"nama": "Denda", "nominal": 50000}]
            $table->json('rincian_item')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_mahasiswas');
    }
};