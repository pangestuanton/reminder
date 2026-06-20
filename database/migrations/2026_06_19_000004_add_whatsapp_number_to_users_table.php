<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * This no-op migration is retained because deployed databases may already
     * contain its name in the migration history. The legacy field is removed
     * by the Telegram migration that follows it.
     */
    public function up(): void {}

    public function down(): void {}
};
