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
        Schema::create('trx_person_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('ref_person')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('ref_jabatan');
            $table->foreignId('fakultas_id')->nullable()->constrained('ref_fakultas');
            $table->foreignId('prodi_id')->nullable()->constrained('ref_prodi');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_person_jabatan');
    }
};
