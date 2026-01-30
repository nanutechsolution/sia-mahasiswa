<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ref_fakultas', 'nama_dekan')) {
            Schema::table('ref_fakultas', function (Blueprint $table) {
                $table->dropColumn('nama_dekan');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ref_fakultas', function (Blueprint $table) {
            $table->string('nama_dekan')->nullable();
        });
    }
};