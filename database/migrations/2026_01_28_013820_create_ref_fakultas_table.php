<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_fakultas', function (Blueprint $table) {
            $table->id();
            
            // Kode unik internal kampus, misal "FT", "FE"
            $table->string('kode_fakultas', 10)->unique();
            $table->string('nama_fakultas', 100);
            
            // Nama Pejabat Dekan (Sering berubah, history-nya nanti di tabel SK, ini current)
            $table->string('nama_dekan')->nullable();
            
            // ID GUID dari Neo Feeder (Wajib simpan untuk sync)
            $table->uuid('id_feeder')->nullable()->index(); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_fakultas');
    }
};