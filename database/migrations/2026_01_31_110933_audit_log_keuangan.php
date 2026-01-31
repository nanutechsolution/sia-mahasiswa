<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit untuk Tagihan (Siapa yang generate/buat?)
        Schema::table('tagihan_mahasiswas', function (Blueprint $table) {
            $table->foreignUuid('created_by')->nullable()->after('status_bayar')
                ->constrained('users')->nullOnDelete()
                ->comment('User ID yang membuat tagihan');
        });

        
    }

    public function down(): void
    {
        Schema::table('tagihan_mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

      
    }
};
