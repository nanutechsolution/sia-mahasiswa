<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            // Menambahkan konteks akademik: Jadwal ini dibuka berdasarkan kurikulum mana?
            $table->foreignId('kurikulum_id')->nullable()->after('tahun_akademik_id')
                  ->constrained('master_kurikulums')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
            $table->dropColumn('kurikulum_id');
        });
    }
};