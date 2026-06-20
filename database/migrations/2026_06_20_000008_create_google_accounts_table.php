<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('google_account_email');
            $table->text('access_token_encrypted');
            $table->text('refresh_token_encrypted');
            $table->timestamp('token_expires_at');
            $table->json('scopes')->nullable();
            $table->timestamp('classroom_connected_at')->nullable();
            $table->timestamp('calendar_connected_at')->nullable();
            $table->timestamp('disconnected_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('google_account_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_accounts');
    }
};
