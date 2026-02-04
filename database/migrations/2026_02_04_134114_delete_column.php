<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('krs_detail', function (Blueprint $table) {
            $table->dropColumn(['nilai_tugas', 'nilai_uts', 'nilai_uas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('krs_detail', function (Blueprint $table) {
            //
        });
    }
};
