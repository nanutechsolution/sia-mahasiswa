<?php

// database/migrations/0001_01_01_000000_create_users_table.php (Edit file default Laravel)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID WAJIB untuk keamanan ID di URL
            
            $table->string('name');
            $table->string('username')->unique(); // Login pakai NIM/NIDN/Username
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Polymorphic Relation: User ini Dosen atau Mahasiswa?
            // type: 'App\Domains\Mahasiswa\Models\Mahasiswa' atau 'Dosen'
            // id: ID dari tabel mahasiswa/dosen
            $table->nullableUuidMorphs('profileable'); 
            
            $table->boolean('is_active')->default(true); // Banned/Active
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel bawaan Laravel (Password Reset & Sessions) - Biarkan default
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};