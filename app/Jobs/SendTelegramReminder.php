<?php

namespace App\Jobs;

use App\Models\JadwalKegiatan;
use App\Models\ReminderLog;
use App\Notifications\ScheduleTelegramReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTelegramReminder implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public int $uniqueFor = 86400;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public JadwalKegiatan $schedule,
        public string $reminderType,
        public ?int $minutesRemaining = null,
    ) {
        $this->onQueue('telegram');
    }

    public function uniqueId(): string
    {
        return $this->schedule->getKey().':'.$this->reminderType.':telegram';
    }

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function handle(): void
    {
        $schedule = $this->schedule->fresh(['user']);

        if (! $schedule || $schedule->status !== 'pending' || blank($schedule->user?->telegram_chat_id)) {
            return;
        }

        $alreadySent = ReminderLog::query()
            ->where('jadwal_kegiatan_id', $schedule->id)
            ->where('reminder_type', $this->reminderType)
            ->where('channel', 'telegram')
            ->exists();

        if ($alreadySent) {
            return;
        }

        $schedule->user->notifyNow(new ScheduleTelegramReminder(
            $schedule,
            $this->reminderType,
            $this->minutesRemaining,
        ));

        ReminderLog::create([
            'user_id' => $schedule->user_id,
            'jadwal_kegiatan_id' => $schedule->id,
            'reminder_type' => $this->reminderType,
            'channel' => 'telegram',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        ReminderLog::query()->updateOrCreate([
            'jadwal_kegiatan_id' => $this->schedule->id,
            'reminder_type' => $this->reminderType,
            'channel' => 'telegram',
        ], [
            'user_id' => $this->schedule->user_id,
            'status' => 'failed',
            'sent_at' => null,
            'failed_at' => now(),
        ]);

        Log::warning('Pengingat Telegram gagal setelah seluruh percobaan.', [
            'jadwal_kegiatan_id' => $this->schedule->id,
            'reminder_type' => $this->reminderType,
        ]);
    }
}
