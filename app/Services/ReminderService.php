<?php

namespace App\Services;

use App\Models\JadwalKegiatan;
use App\Models\ReminderLog;
use App\Notifications\ScheduleH1Reminder;
use App\Notifications\ScheduleH3Reminder;
use Illuminate\Support\Collection;

class ReminderService
{
    public function __construct(private readonly WhatsappReminderService $whatsappReminderService) {}

    public function sendDueReminders(): array
    {
        $h3Mail = $this->sendMailForType(3, 'h3');
        $h1Mail = $this->sendMailForType(1, 'h1');
        $h3Whatsapp = $this->sendWhatsappForType(3, 'h3');
        $h1Whatsapp = $this->sendWhatsappForType(1, 'h1');

        return [
            'h3_mail' => $h3Mail,
            'h1_mail' => $h1Mail,
            'h3_whatsapp' => $h3Whatsapp,
            'h1_whatsapp' => $h1Whatsapp,
            'total' => $h3Mail + $h1Mail + $h3Whatsapp + $h1Whatsapp,
        ];
    }

    public function sendMailForType(int $days, string $type): int
    {
        $schedules = $this->getSchedulesForReminder($days, $type, 'mail');
        $count = 0;

        foreach ($schedules as $schedule) {
            $notification = $type === 'h3'
                ? new ScheduleH3Reminder($schedule)
                : new ScheduleH1Reminder($schedule);

            $schedule->user->notify($notification);

            ReminderLog::create([
                'user_id' => $schedule->user_id,
                'jadwal_kegiatan_id' => $schedule->id,
                'reminder_type' => $type,
                'channel' => 'mail',
                'sent_at' => now(),
            ]);

            $count++;
        }

        return $count;
    }

    public function sendWhatsappForType(int $days, string $type): int
    {
        $schedules = $this->getSchedulesForReminder($days, $type, 'whatsapp');
        $count = 0;

        foreach ($schedules as $schedule) {
            if (! $this->whatsappReminderService->send($schedule->user, $schedule, $type)) {
                continue;
            }

            ReminderLog::create([
                'user_id' => $schedule->user_id,
                'jadwal_kegiatan_id' => $schedule->id,
                'reminder_type' => $type,
                'channel' => 'whatsapp',
                'sent_at' => now(),
            ]);

            $count++;
        }

        return $count;
    }

    public function getSchedulesForReminder(int $days, string $type, string $channel): Collection
    {
        $start = now()->startOfDay()->addDays($days);
        $end = now()->endOfDay()->addDays($days);

        return JadwalKegiatan::query()
            ->with('user')
            ->where('status', 'pending')
            ->whereBetween('waktu_pelaksanaan', [$start, $end])
            ->whereDoesntHave('reminderLogs', function ($query) use ($type, $channel) {
                $query->where('reminder_type', $type)->where('channel', $channel);
            })
            ->get();
    }
}
