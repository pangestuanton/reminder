<?php

namespace App\Services;

use App\Jobs\SendTelegramReminder;
use App\Models\JadwalKegiatan;
use Illuminate\Support\Collection;

class ReminderService
{
    public function sendDueReminders(): array
    {
        $h3 = $this->dispatchForType(3, 'h3');
        $h1 = $this->dispatchForType(1, 'h1');
        $countdown = $this->dispatchCountdownReminders();

        return [
            'h3_telegram' => $h3,
            'h1_telegram' => $h1,
            'countdown_telegram' => $countdown,
            'total' => $h3 + $h1 + $countdown,
        ];
    }

    public function dispatchForType(int $days, string $type): int
    {
        $schedules = $this->getSchedulesForReminder($days, $type);

        foreach ($schedules as $schedule) {
            SendTelegramReminder::dispatch($schedule, $type);
        }

        return $schedules->count();
    }

    public function dispatchCountdownReminders(): int
    {
        $schedules = JadwalKegiatan::query()
            ->with('user')
            ->whereHas('user', fn ($query) => $query->whereNotNull('telegram_chat_id'))
            ->where('status', 'pending')
            ->whereBetween('waktu_pelaksanaan', [now(), now()->addHours(3)])
            ->get();

        $count = 0;

        foreach ($schedules as $schedule) {
            $minutesRemaining = (int) now()->diffInMinutes($schedule->waktu_pelaksanaan, false);

            if ($minutesRemaining <= 0 || $minutesRemaining > 180) {
                continue;
            }

            $slot = (int) ceil($minutesRemaining / 30);
            $type = "3h_slot_{$slot}";

            if ($schedule->reminderLogs()
                ->where('reminder_type', $type)
                ->where('channel', 'telegram')
                ->exists()) {
                continue;
            }

            SendTelegramReminder::dispatch($schedule, $type, $minutesRemaining);
            $count++;
        }

        return $count;
    }

    public function getSchedulesForReminder(int $days, string $type): Collection
    {
        $start = now()->startOfDay()->addDays($days);
        $end = now()->endOfDay()->addDays($days);

        return JadwalKegiatan::query()
            ->with('user')
            ->whereHas('user', fn ($query) => $query->whereNotNull('telegram_chat_id'))
            ->where('status', 'pending')
            ->whereBetween('waktu_pelaksanaan', [$start, $end])
            ->whereDoesntHave('reminderLogs', function ($query) use ($type) {
                $query->where('reminder_type', $type)
                    ->where('channel', 'telegram');
            })
            ->get();
    }
}
