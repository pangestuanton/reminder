<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->string('source')->default('local')->after('prioritas');
            $table->string('source_id')->nullable()->after('source');
            $table->boolean('synced_to_calendar')->default(false)->after('source_id');
            $table->timestamp('completed_at')->nullable()->after('synced_to_calendar');
            $table->timestamp('deadline_at')->nullable()->after('waktu_pelaksanaan');
            $table->boolean('is_all_day')->default(false)->after('deadline_at');
            $table->string('calendar_event_id')->nullable()->after('is_all_day');
            $table->string('course_name')->nullable()->after('calendar_event_id');
            $table->boolean('reminder_h3')->default(true)->after('course_name');
            $table->boolean('reminder_h1')->default(true)->after('reminder_h3');
            $table->boolean('reminder_3h')->default(true)->after('reminder_h1');

            $table->unique(['source', 'source_id'], 'unique_jadwal_source');
            $table->index(['user_id', 'source', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kegiatans', function (Blueprint $table) {
            $table->dropIndex('unique_jadwal_source');
            $table->dropIndex(['user_id', 'source', 'status']);
            $table->dropColumn([
                'source',
                'source_id',
                'synced_to_calendar',
                'completed_at',
                'deadline_at',
                'is_all_day',
                'calendar_event_id',
                'course_name',
                'reminder_h3',
                'reminder_h1',
                'reminder_3h',
            ]);
        });
    }
};
