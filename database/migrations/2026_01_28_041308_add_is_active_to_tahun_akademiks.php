<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ref_tahun_akademik', function (Blueprint $table) {
            // $table->boolean('is_active')->default(false)->after('nama_tahun');
            $table->date('tgl_mulai_krs')->nullable(); // Penting untuk validasi tanggal
            $table->date('tgl_selesai_krs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_tahun_akademik', function (Blueprint $table) {
            $table->dropColumn([ 'tgl_mulai_krs', 'tgl_selesai_krs']);
        });
    }
};
