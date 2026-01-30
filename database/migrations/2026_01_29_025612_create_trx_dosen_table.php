<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('ref_person');
            $table->foreignId('prodi_id')->constrained('ref_prodi');
            $table->string('nidn', 20)->nullable()->unique();
            $table->string('nip', 30)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_dosen');
    }
};
