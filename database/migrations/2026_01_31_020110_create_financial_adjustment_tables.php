<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Penyesuaian (Koreksi Tagihan)
        // Berfungsi seperti Credit Note / Debit Note
        Schema::create('keuangan_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tagihan_id')->constrained('tagihan_mahasiswas')->cascadeOnDelete();

            // TIPE: 'POTONGAN' (Kurangi Tagihan), 'DENDA' (Tambah Tagihan), 'BEASISWA' (Bayarkan Tagihan)
            $table->string('jenis_adjustment', 20);
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();

            // Siapa yang melakukan koreksi (Audit)
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 2. Tabel Saldo Mahasiswa (Dompet/Deposit)
        Schema::create('keuangan_saldos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();

            // Saldo saat ini
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamps();
        });

        // 3. Tabel Mutasi Saldo (History Dompet)
        Schema::create('keuangan_saldo_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('saldo_id')->constrained('keuangan_saldos');

            // 'IN' (Masuk dari kelebihan bayar), 'OUT' (Dipakai bayar tagihan lain / Refund)
            $table->enum('tipe', ['IN', 'OUT']);
            $table->decimal('nominal', 15, 2);
            $table->string('referensi_id')->nullable(); // ID Tagihan atau ID Refund
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_saldo_transactions');
        Schema::dropIfExists('keuangan_saldos');
        Schema::dropIfExists('keuangan_adjustments');
    }
};
