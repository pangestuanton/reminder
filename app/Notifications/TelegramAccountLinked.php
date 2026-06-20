<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramAccountLinked extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content("<b>Telegram berhasil terhubung</b>\n\nPengingat jadwal Aviona Sync akan dikirim ke percakapan ini.")
            ->options(['parse_mode' => 'HTML']);
    }
}
