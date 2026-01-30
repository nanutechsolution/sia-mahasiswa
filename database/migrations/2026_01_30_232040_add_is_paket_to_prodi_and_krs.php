<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Kebijakan default per prodi
        Schema::table('ref_prodi', function (Blueprint $blueprint) {
            $blueprint->boolean('is_paket')->default(true)->after('nama_prodi');
        });

        // Snapshot saat mahasiswa melakukan KRS
        Schema::table('krs', function (Blueprint $blueprint) {
            $blueprint->boolean('is_paket_snapshot')->nullable()->after('status_krs');
        });
    }

    public function down()
    {
        Schema::table('ref_prodi', function ($b) { $b->dropColumn('is_paket'); });
        Schema::table('krs', function ($b) { $b->dropColumn('is_paket_snapshot'); });
    }
};