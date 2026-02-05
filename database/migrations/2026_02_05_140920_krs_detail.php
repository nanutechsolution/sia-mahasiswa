<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('krs_detail', function (Blueprint $table) {
            // Snapshot Data: Mengunci identitas MK saat transaksi (Audit-Proof)
            $table->string('kode_mk_snapshot', 20)->nullable()->after('jadwal_kuliah_id');
            $table->string('nama_mk_snapshot', 150)->nullable()->after('kode_mk_snapshot');
            $table->integer('sks_snapshot')->nullable()->after('nama_mk_snapshot');

            // Marker Ekuivalensi: Jika pengambilan ini adalah hasil penyetaraan
            $table->foreignId('ekuivalensi_id')->nullable()->after('sks_snapshot')
                ->constrained('akademik_ekuivalensi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->dropForeign(['ekuivalensi_id']);
            $table->dropColumn(['kode_mk_snapshot', 'nama_mk_snapshot', 'sks_snapshot', 'ekuivalensi_id']);
        });
    }
};
