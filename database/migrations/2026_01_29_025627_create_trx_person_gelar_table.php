<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trx_person_gelar', function (Blueprint $table) {

            $table->id();
            $table->foreignId('person_id')->constrained('ref_person')->cascadeOnDelete();
            $table->foreignId('gelar_id')->constrained('ref_gelar')->cascadeOnDelete();

            $table->unsignedTinyInteger('urutan')->default(1); // urutan tampil gelar

            $table->timestamps();

            $table->unique(['person_id', 'gelar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_person_gelar');
    }
};
