<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_classroom_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('google_classroom_course_work_id')->constrained('google_classroom_course_works')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('state')->default('NEW');
            $table->boolean('late')->default(false);
            $table->string('draft_url')->nullable();
            $table->string('alternate_link')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['user_id', 'external_id']);
            $table->index(['google_classroom_course_work_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_classroom_submissions');
    }
};
