<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('google_account_id')->constrained('google_accounts')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('calendar_id')->default('primary');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('recurring_event_id')->nullable();
            $table->string('html_link')->nullable();
            $table->string('source_label')->default('calendar');
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['user_id', 'external_id']);
            $table->index(['user_id', 'start_datetime']);
            $table->index(['user_id', 'recurring_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_calendar_events');
    }
};
