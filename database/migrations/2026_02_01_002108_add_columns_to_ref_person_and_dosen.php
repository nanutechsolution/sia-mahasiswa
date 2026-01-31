<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom tempat_lahir di ref_person
        if (Schema::hasTable('ref_person')) {
            Schema::table('ref_person', function (Blueprint $table) {
                if (!Schema::hasColumn('ref_person', 'tempat_lahir')) {
                    $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
                }
            });
        }

        // 2. Perpanjang kolom NIDN & NUPTK di trx_dosen (Anti Truncated Error)
        if (Schema::hasTable('trx_dosen')) {
            Schema::table('trx_dosen', function (Blueprint $table) {
                // Ubah panjang menjadi 50 agar aman dari data kotor/panjang
                $table->string('nidn', 50)->nullable()->change();
                $table->string('nuptk', 50)->nullable()->change();
                
                // Tambah data_tambahan jika belum ada (untuk backup)
                if (!Schema::hasColumn('trx_dosen', 'data_tambahan')) {
                    $table->json('data_tambahan')->nullable()->after('is_active');
                }
            });
        }
    }

    public function down(): void
    {
        // Revert changes if necessary
        Schema::table('ref_person', function (Blueprint $table) {
            $table->dropColumn('tempat_lahir');
        });
    }
};