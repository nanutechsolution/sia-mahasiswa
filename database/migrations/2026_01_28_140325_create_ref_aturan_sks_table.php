<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_aturan_sks', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_ips', 4, 2); // Contoh: 3.00
            $table->decimal('max_ips', 4, 2); // Contoh: 4.00
            $table->integer('max_sks');       // Contoh: 24
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_aturan_sks');
    }
};