<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('daily_agenda_enabled')->default(true)->after('telegram_linked_at');
            $table->string('daily_agenda_time')->default('05:00')->after('daily_agenda_enabled');
            $table->boolean('daily_agenda_include_overdue')->default(true)->after('daily_agenda_time');
            $table->string('daily_agenda_format')->default('detailed')->after('daily_agenda_include_overdue');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_agenda_enabled',
                'daily_agenda_time',
                'daily_agenda_include_overdue',
                'daily_agenda_format',
            ]);
        });
    }
};
