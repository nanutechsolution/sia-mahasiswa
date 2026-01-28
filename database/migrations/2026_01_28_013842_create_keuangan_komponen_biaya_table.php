<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_komponen_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('nama_komponen', 100); // SPP Tetap, SPP Variabel, Uang Gedung, Wisuda
            
            // Tipe: 
            // - TETAP (Sekali bayar per semester, misal SPP)
            // - SKS (Dikalikan jumlah SKS diambil)
            // - SEKALI (Hanya sekali selama kuliah, misal Gedung/Almamater)
            // - INSIDENTAL (Denda, Cuti, Wisuda)
            $table->enum('tipe_biaya', ['TETAP', 'SKS', 'SEKALI', 'INSIDENTAL']);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_komponen_biaya');
    }
};