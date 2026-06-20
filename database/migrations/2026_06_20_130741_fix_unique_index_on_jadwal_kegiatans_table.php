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
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->dropUnique('unique_jadwal_source');
            $table->unique(['user_id', 'source', 'source_id'], 'unique_jadwal_source_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->dropUnique('unique_jadwal_source_user');
            $table->unique(['source', 'source_id'], 'unique_jadwal_source');
        });
    }
};
