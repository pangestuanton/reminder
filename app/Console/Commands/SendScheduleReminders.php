<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendScheduleReminders extends Command
{
    protected $signature = 'aviona:send-schedule-reminders';

    protected $description = 'Mengirim pengingat jadwal H-3 dan H-1';

    public function handle(ReminderService $reminderService): int
    {
        $result = $reminderService->sendDueReminders();

        $this->info('Pengingat terkirim: '.$result['total']);
        $this->line('H-3 Email: '.$result['h3_mail']);
        $this->line('H-1 Email: '.$result['h1_mail']);
        $this->line('H-3 WhatsApp: '.$result['h3_whatsapp']);
        $this->line('H-1 WhatsApp: '.$result['h1_whatsapp']);

        return self::SUCCESS;
    }
}
