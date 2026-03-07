<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi ini diperbarui untuk memastikan perubahan tipe data 
     * tokenable_id dari BigInt ke VARCHAR(36) (UUID) pada server Live.
     */
    public function up(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            // 1. Kosongkan tabel (mencegah data korup)
            DB::table('personal_access_tokens')->truncate();

            // 2. Jalankan perintah ALTER secara mentah (Raw SQL)
            // Mengubah tipe data tokenable_id agar bisa menampung UUID dari SIAKAD
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id VARCHAR(36) NOT NULL');
            
            // 3. Perbaikan: Hanya aktifkan AUTO_INCREMENT tanpa mendefinisikan PRIMARY KEY ulang
            // Ini untuk menghindari error "Multiple primary key defined"
            DB::statement('ALTER TABLE personal_access_tokens MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            // Rollback: Matikan auto increment dan kembalikan tipe data ke BigInt jika diperlukan
            DB::statement('ALTER TABLE personal_access_tokens MODIFY id BIGINT UNSIGNED');
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED NOT NULL');
        }
    }
};