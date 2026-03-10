<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menambahkan fitur Soft Deletes, Audit Login, dan Indexing
     * untuk memperkuat integritas data identitas.
     * Menggunakan pengecekan kolom dan indeks untuk menghindari error "Duplicate key/column".
     */
    public function up(): void
    {
        // 1. Update Tabel ref_person (Biodata)
        Schema::table('ref_person', function (Blueprint $table) {
            // PERBAIKAN: Menggunakan Schema::getIndexes() untuk deteksi indeks (Laravel 10/11 native)
            $indexes = Schema::getIndexes('ref_person');
            $indexExists = collect($indexes)->contains('name', 'ref_person_nik_unique');
            
            if (!$indexExists) {
                $table->string('nik')->unique()->nullable()->change();
            } else {
                // Jika indeks sudah ada, kita hanya pastikan kolomnya nullable
                $table->string('nik')->nullable()->change();
            }
            
            if (!Schema::hasColumn('ref_person', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. Update Tabel users (Akun)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 3. Update Tabel mahasiswas (Akademik Mhs)
        Schema::table('mahasiswas', function (Blueprint $table) {
            $indexes = Schema::getIndexes('mahasiswas');
            $indexExists = collect($indexes)->contains('name', 'idx_mhs_nim');
            
            if (!$indexExists) {
                $table->index('nim', 'idx_mhs_nim');
            }

            if (!Schema::hasColumn('mahasiswas', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 4. Update Tabel trx_dosen (Akademik Dosen)
        Schema::table('trx_dosen', function (Blueprint $table) {
            $indexes = Schema::getIndexes('trx_dosen');
            $indexExists = collect($indexes)->contains('name', 'idx_dosen_nidn');

            if (!$indexExists) {
                $table->index('nidn', 'idx_dosen_nidn');
            }

            if (!Schema::hasColumn('trx_dosen', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Membatalkan perubahan (Rollback)
     */
    public function down(): void
    {
        Schema::table('trx_dosen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'last_login_ip']);
            $table->dropSoftDeletes();
        });

        Schema::table('ref_person', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};