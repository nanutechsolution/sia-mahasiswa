<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah atribut tipe aktivitas di Master MK
        Schema::table('master_mata_kuliahs', function (Blueprint $table) {
            // REGULAR, THESIS, MBKM, CONTINUATION
            $table->string('activity_type', 20)->default('REGULAR')->after('jenis_mk');
        });

        // 2. Tambah Snapshot Tipe Aktivitas di KRS Detail (Audit Trail)
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->string('activity_type_snapshot', 20)->default('REGULAR')->after('sks_snapshot');
        });

        // 3. Tambah Tabel Log Sejarah Akademik (Opsional tapi disarankan untuk 10 thn)
        Schema::create('academic_history_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('mahasiswa_id');
            $table->foreignId('tahun_akademik_id');
            $table->string('previous_mode', 20)->nullable();
            $table->string('new_mode', 20);
            $table->text('trigger_event'); // Misal: "Ambil MK Skripsi"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_history_logs');
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->dropColumn('activity_type_snapshot');
        });
        Schema::table('master_mata_kuliahs', function (Blueprint $table) {
            $table->dropColumn('activity_type');
        });
    }
};