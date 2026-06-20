<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('telegram_enabled')->default(true);
            $table->string('quiet_hours_start')->default('22:00');
            $table->string('quiet_hours_end')->default('06:00');
            $table->string('tone')->default('friendly');
            $table->string('detail_level')->default('normal');
            $table->json('category_preferences')->nullable();
            $table->boolean('reminder_h3_enabled')->default(true);
            $table->boolean('reminder_h1_enabled')->default(true);
            $table->boolean('reminder_3h_enabled')->default(true);
            $table->boolean('reminder_overdue_enabled')->default(true);
            $table->integer('reminder_max_per_day')->default(20);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
