<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trx_dosen', function (Blueprint $table) {
            // TETAP, LB (Luar Biasa), PRAKTISI
            $table->string('jenis_dosen', 20)->default('TETAP')->after('prodi_id');
            
            // Nama kampus asal / perusahaan (jika Dosen Luar/Praktisi)
            $table->string('asal_institusi')->nullable()->after('jenis_dosen'); 
        });
    }

    public function down(): void
    {
        Schema::table('trx_dosen', function (Blueprint $table) {
            $table->dropColumn(['jenis_dosen', 'asal_institusi']);
        });
    }
};