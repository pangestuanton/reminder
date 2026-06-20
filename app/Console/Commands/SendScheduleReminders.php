<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendScheduleReminders extends Command
{
    protected $signature = 'aviona:send-schedule-reminders';

    protected $description = 'Mengantrekan pengingat jadwal melalui Telegram';

    public function handle(ReminderService $reminderService): int
    {
        $result = $reminderService->sendDueReminders();

        $this->info('Pengingat Telegram diantrekan: '.$result['total']);
        $this->line('H-3: '.$result['h3_telegram']);
        $this->line('H-1: '.$result['h1_telegram']);
        $this->line('Hitung mundur 3 jam: '.$result['countdown_telegram']);

        return self::SUCCESS;
    }
}
