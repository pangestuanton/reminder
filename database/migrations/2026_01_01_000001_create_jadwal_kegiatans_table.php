<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->string('kategori');
            $table->dateTime('waktu_pelaksanaan');
            $table->string('lokasi_atau_link')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('pending');
            $table->string('prioritas')->default('sedang');
            $table->timestamps();

            $table->index(['user_id', 'status', 'waktu_pelaksanaan']);
            $table->index(['user_id', 'kategori', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kegiatans');
    }
};
