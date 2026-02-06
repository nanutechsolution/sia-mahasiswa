<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel log untuk mencatat histori perubahan nilai yang sudah sah (published)
        Schema::create('akademik_grade_revision_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->constrained('krs_detail')->cascadeOnDelete();
            
            // Snapshot Perubahan
            $table->decimal('old_nilai_angka', 5, 2);
            $table->string('old_nilai_huruf', 2);
            $table->decimal('new_nilai_angka', 5, 2);
            $table->string('new_nilai_huruf', 2);
            
            $table->text('alasan_perbaikan');
            $table->string('nomor_sk_perbaikan')->nullable(); // Jika ada dasar hukumnya
            
            $table->foreignUuid('executed_by')->constrained('users'); // Admin/Pejabat yang mengizinkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_grade_revision_logs');
    }
};