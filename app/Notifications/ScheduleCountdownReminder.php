<?php

namespace App\Notifications;

use App\Models\JadwalKegiatan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleCountdownReminder extends Notification
{
    use Queueable;

    public function __construct(
        private readonly JadwalKegiatan $jadwalKegiatan,
        private readonly int $minutesRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutes = $this->minutesRemaining;
        if ($minutes < 0) {
            $minutes = 0;
        }

        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $remMins = $minutes % 60;
            $timeString = $hours . ' jam' . ($remMins > 0 ? ' ' . $remMins . ' menit' : '');
        } else {
            $timeString = $minutes . ' menit';
        }

        return (new MailMessage)
            ->subject('Pengingat mendekati pelaksanaan: '.$this->jadwalKegiatan->judul)
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Jadwal berikut akan berlangsung dalam '.$timeString.'.')
            ->line('Judul: '.$this->jadwalKegiatan->judul)
            ->line('Kategori: '.ucfirst($this->jadwalKegiatan->kategori))
            ->line('Waktu: '.$this->jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i'))
            ->line('Segera bersiap untuk mengikuti kegiatan.');
    }
}
