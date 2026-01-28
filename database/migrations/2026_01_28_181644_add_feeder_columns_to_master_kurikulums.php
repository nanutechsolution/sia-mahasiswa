<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kurikulums', function (Blueprint $table) {
            // Kolom sesuai Neo Feeder
            $table->string('id_semester_mulai', 5)->nullable()->after('tahun_mulai')
                  ->comment('Format: 20211. Mapping ke id_semester di Feeder');
            
            $table->integer('jumlah_sks_lulus')->default(144)->after('is_active')
                  ->comment('Total SKS minimal untuk lulus');
                  
            $table->integer('jumlah_sks_wajib')->default(0)->after('jumlah_sks_lulus');
            $table->integer('jumlah_sks_pilihan')->default(0)->after('jumlah_sks_wajib');
        });
    }

    public function down(): void
    {
        Schema::table('master_kurikulums', function (Blueprint $table) {
            $table->dropColumn([
                'id_semester_mulai', 
                'jumlah_sks_lulus', 
                'jumlah_sks_wajib', 
                'jumlah_sks_pilihan'
            ]);
        });
    }
};