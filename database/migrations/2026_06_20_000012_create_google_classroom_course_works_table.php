<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_classroom_course_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('google_classroom_course_id')->constrained('google_classroom_courses')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->time('due_time_only')->nullable();
            $table->integer('max_points')->nullable();
            $table->string('work_type')->default('ASSIGNMENT');
            $table->string('status')->default('PUBLISHED');
            $table->string('alternate_link')->nullable();
            $table->json('materials')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['user_id', 'external_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_classroom_course_works');
    }
};
