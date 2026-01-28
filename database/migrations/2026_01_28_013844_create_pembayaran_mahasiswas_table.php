<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('tagihan_id')->constrained('tagihan_mahasiswas');
            
            $table->decimal('nominal_bayar', 19, 2);
            $table->dateTime('tanggal_bayar');
            
            // Metode: MANUAL (Transfer Bank), GATEWAY (Midtrans/Xendit), TUNAI (Di Loket)
            $table->string('metode_pembayaran', 20)->default('MANUAL');
            
            // Bukti (Jika Manual)
            $table->string('bukti_bayar_path')->nullable(); // Path file upload
            $table->string('keterangan_pengirim')->nullable(); // "A.n. Budi Santoso"
            
            // Verifikasi (Audit Trail)
            $table->enum('status_verifikasi', ['PENDING', 'VALID', 'INVALID'])->default('PENDING')->index();
            $table->foreignUuid('verified_by')->nullable()->constrained('users'); // Siapa admin yang klik valid?
            $table->dateTime('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable(); // Alasan jika ditolak
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_mahasiswas');
    }
};