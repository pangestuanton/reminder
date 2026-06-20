<?php

namespace App\Notifications;

use App\Models\JadwalKegiatan;
use App\Services\TelegramMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class ScheduleTelegramReminder extends Notification
{
    use Queueable;

    public function __construct(
        public readonly JadwalKegiatan $schedule,
        public readonly string $reminderType,
        public readonly ?int $minutesRemaining = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        $messageService = app(TelegramMessageService::class);

        $content = $messageService->buildReminderMessage(
            $this->schedule,
            $this->reminderType,
            $this->minutesRemaining,
        );

        return TelegramMessage::create()
            ->content($content)
            ->options(['parse_mode' => 'HTML'])
            ->button('Lihat Tugas', route('jadwal-kegiatan.show', $this->schedule));
    }
}
