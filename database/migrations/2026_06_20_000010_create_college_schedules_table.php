<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mata_kuliah');
            $table->string('dosen')->nullable();
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('lokasi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('warna')->default('#3B82F6');
            $table->integer('reminder_minutes')->default(30);
            $table->date('semester_mulai')->nullable();
            $table->date('semester_akhir')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('synced_to_calendar')->default(false);
            $table->string('calendar_event_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active', 'hari']);
            $table->index(['user_id', 'hari', 'jam_mulai', 'jam_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_schedules');
    }
};
