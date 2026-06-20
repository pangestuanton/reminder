<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('semester');
            $table->string('mata_kuliah');
            $table->integer('sks');
            $table->string('nilai'); // A, AB, B, BC, C, D, E
            $table->timestamps();

            $table->index(['user_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_grades');
    }
};
