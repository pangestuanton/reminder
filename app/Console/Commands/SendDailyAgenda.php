<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyAgendaJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendDailyAgenda extends Command
{
    protected $signature = 'aviona:send-daily-agenda';

    protected $description = 'Dispatch daily 05:00 agenda to all active users';

    public function handle(): int
    {
        $users = User::where('daily_agenda_enabled', true)
            ->whereNotNull('telegram_chat_id')
            ->get();

        $dispatched = 0;

        foreach ($users as $user) {
            SendDailyAgendaJob::dispatch($user);
            $dispatched++;
        }

        $this->info("Daily agenda dispatched to {$dispatched} users.");

        return self::SUCCESS;
    }
}
