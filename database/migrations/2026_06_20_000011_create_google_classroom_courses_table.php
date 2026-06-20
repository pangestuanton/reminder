<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_classroom_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('google_account_id')->constrained('google_accounts')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('name');
            $table->string('section')->nullable();
            $table->text('description')->nullable();
            $table->string('room')->nullable();
            $table->string('alternate_link')->nullable();
            $table->string('course_state')->default('ACTIVE');
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['user_id', 'external_id']);
            $table->index(['user_id', 'course_state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_classroom_courses');
    }
};
