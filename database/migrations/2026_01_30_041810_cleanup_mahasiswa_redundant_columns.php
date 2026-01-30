<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Hapus kolom yang sudah ada di ref_person
            $table->dropColumn([
                'nama_lengkap',
                'nik',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'nomor_hp',
                'email_pribadi'
            ]);
        });
    }

    public function down(): void
    {
        // Rollback (jika perlu) - Tambahkan kembali kolomnya
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('nama_lengkap', 150)->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('nomor_hp', 15)->nullable();
            $table->string('email_pribadi', 100)->nullable();
        });
    }
};