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
        Schema::table('master_kurikulums', function (Blueprint $table) {
            // [FIX] Tambahkan ->nullable() agar data lama yang kosong tidak error
            $table->string('id_semester_mulai', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kurikulums', function (Blueprint $table) {
            // Kembalikan ke 5 karakter
            $table->string('id_semester_mulai', 5)->nullable()->change();
        });
    }
};