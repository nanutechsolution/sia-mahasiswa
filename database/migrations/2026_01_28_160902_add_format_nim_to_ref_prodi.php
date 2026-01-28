<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ref_prodi', function (Blueprint $table) {
            // Contoh Format: {THN}{KODE}{NO:4}
            $table->string('format_nim')->nullable()->after('gelar_lulusan')
                ->comment('Pattern: {THN}=24, {TAHUN}=2024, {KODE}=KodeProdi, {NO:4}=0001');
            
            // Counter untuk urutan (bisa direset per tahun via logic nanti)
            $table->integer('last_nim_seq')->default(0)->after('format_nim');
        });
    }

    public function down(): void
    {
        Schema::table('ref_prodi', function (Blueprint $table) {
            $table->dropColumn(['format_nim', 'last_nim_seq']);
        });
    }
};