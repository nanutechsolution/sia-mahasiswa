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
        Schema::table('master_mata_kuliahs', function (Blueprint $table) {
            $table->integer('sks_tatap_muka')->default(0)->after('sks_default');
            $table->integer('sks_praktek')->default(0)->after('sks_tatap_muka');
            $table->integer('sks_lapangan')->default(0)->after('sks_praktek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_mata_kuliahs', function (Blueprint $table) {
            $table->dropColumn(['sks_tatap_muka', 'sks_praktek', 'sks_lapangan']);
        });
    }
};
