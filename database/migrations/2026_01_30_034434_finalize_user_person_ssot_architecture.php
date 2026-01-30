<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS -> PERSON
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'person_id')) {
                $table->foreignId('person_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ref_person')
                    ->nullOnDelete();
            }
        });

        // TRX_DOSEN: lepas dari autentikasi
        if (Schema::hasColumn('trx_dosen', 'user_id')) {
            Schema::table('trx_dosen', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {}
            });

            Schema::table('trx_dosen', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        // MAHASISWAS: user_id -> person_id
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'person_id')) {
                $table->foreignId('person_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ref_person')
                    ->cascadeOnDelete();
            }
        });

        if (Schema::hasColumn('mahasiswas', 'user_id')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {}
            });

            Schema::table('mahasiswas', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'person_id')) {
                $table->dropForeign(['person_id']);
                $table->dropColumn('person_id');
            }
        });
    }
};
