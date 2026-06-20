<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->string('channel')->default('telegram')->change();
            $table->timestamp('sent_at')->nullable()->change();
            $table->string('status', 20)->default('sent')->after('channel');
            $table->timestamp('failed_at')->nullable()->after('sent_at');
        });

        DB::table('reminder_logs')
            ->whereIn('channel', ['mail', 'whatsapp'])
            ->delete();

        if (! Schema::hasColumn('users', 'telegram_chat_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telegram_chat_id', 32)->nullable()->unique();
                $table->timestamp('telegram_linked_at')->nullable();
            });
        }

        if (Schema::hasColumn('users', 'whatsapp_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('whatsapp_number');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'failed_at']);
        });

        if (Schema::hasColumn('users', 'telegram_chat_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['telegram_chat_id']);
                $table->dropColumn(['telegram_chat_id', 'telegram_linked_at']);
            });
        }
    }
};
