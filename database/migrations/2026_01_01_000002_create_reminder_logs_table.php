<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jadwal_kegiatan_id')->constrained('jadwal_kegiatans')->cascadeOnDelete();
            $table->string('reminder_type');
            $table->string('channel')->default('mail');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(
                ['jadwal_kegiatan_id', 'reminder_type', 'channel'],
                'unique_schedule_reminder_channel'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
