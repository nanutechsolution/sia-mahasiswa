<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ref_person', 'tempat_lahir')) {
            Schema::table('ref_person', function (Blueprint $table) {
                $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ref_person', function (Blueprint $table) {
            $table->dropColumn('tempat_lahir');
        });
    }
};