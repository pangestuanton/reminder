<?php

namespace App\Notifications;

use App\Models\JadwalKegiatan;
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
        $when = $this->schedule->waktu_pelaksanaan
            ->timezone(config('app.timezone'))
            ->translatedFormat('l, d F Y - H.i');

        $content = implode("\n", [
            '<b>'.e($this->headline()).'</b>',
            '',
            '<b>Judul:</b> '.e($this->schedule->judul),
            '<b>Kategori:</b> '.e(ucfirst($this->schedule->kategori)),
            '<b>Waktu:</b> '.e($when),
            '<b>Lokasi/Link:</b> '.e($this->schedule->lokasi_atau_link ?: '-'),
            '',
            e($this->closing()),
        ]);

        return TelegramMessage::create()
            ->content($content)
            ->options(['parse_mode' => 'HTML'])
            ->button('Lihat jadwal', route('jadwal-kegiatan.show', $this->schedule));
    }

    private function headline(): string
    {
        return match ($this->reminderType) {
            'h3' => 'Pengingat: jadwal berlangsung 3 hari lagi',
            'h1' => 'Pengingat: jadwal berlangsung besok',
            default => 'Pengingat: jadwal segera dimulai',
        };
    }

    private function closing(): string
    {
        if ($this->minutesRemaining === null) {
            return 'Pastikan semua kebutuhanmu sudah siap.';
        }

        $minutes = max(0, $this->minutesRemaining);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        $duration = $hours > 0
            ? $hours.' jam'.($remainingMinutes > 0 ? ' '.$remainingMinutes.' menit' : '')
            : $remainingMinutes.' menit';

        return 'Kegiatan dimulai dalam '.$duration.'. Segera bersiap.';
    }
}
