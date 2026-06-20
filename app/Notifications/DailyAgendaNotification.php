<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class DailyAgendaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content($this->message)
            ->options(['parse_mode' => 'Markdown'])
            ->button('Buka Dashboard', route('dashboard'));
    }
}
