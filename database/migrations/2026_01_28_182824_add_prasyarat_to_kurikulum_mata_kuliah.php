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
        Schema::table('kurikulum_mata_kuliah', function (Blueprint $table) {
            // Menyimpan ID MK yang menjadi syarat (Nullable)
            // Jika butuh banyak prasyarat, gunakan tabel terpisah (many-to-many), tapi untuk simple use case, 1 kolom cukup.
            $table->foreignId('prasyarat_mk_id')->nullable()->constrained('master_mata_kuliahs');

            // Nilai minimal, misal 'C' atau 'D'
            $table->string('min_nilai_prasyarat', 2)->default('D');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kurikulum_mata_kuliah', function (Blueprint $table) {
            //
        });
    }
};
