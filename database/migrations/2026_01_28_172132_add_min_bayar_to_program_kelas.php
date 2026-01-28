<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ref_program_kelas', function (Blueprint $table) {
            // Default 50% agar aman
            $table->integer('min_pembayaran_persen')->default(50)->after('kode_internal')
                  ->comment('Syarat minimal bayar untuk bisa KRS');
        });
    }

    public function down(): void
    {
        Schema::table('ref_program_kelas', function (Blueprint $table) {
            $table->dropColumn('min_pembayaran_persen');
        });
    }
};