<?php

namespace App\Notifications;

use App\Models\JadwalKegiatan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleH1Reminder extends Notification
{
    use Queueable;

    public function __construct(private readonly JadwalKegiatan $jadwalKegiatan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengingat jadwal H-1: '.$this->jadwalKegiatan->judul)
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Jadwal berikut akan berlangsung besok.')
            ->line('Judul: '.$this->jadwalKegiatan->judul)
            ->line('Kategori: '.ucfirst($this->jadwalKegiatan->kategori))
            ->line('Waktu: '.$this->jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i'))
            ->line('Pastikan semua persiapanmu sudah siap.');
    }
}
