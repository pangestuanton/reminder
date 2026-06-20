<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->boolean('is_informational')->default(false)->after('reminder_3h');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->dropColumn('is_informational');
        });
    }
};
