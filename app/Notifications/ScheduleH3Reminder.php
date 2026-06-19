<?php

namespace App\Notifications;

use App\Models\JadwalKegiatan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleH3Reminder extends Notification
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
            ->subject('Pengingat jadwal H-3: '.$this->jadwalKegiatan->judul)
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Ada jadwal yang akan berlangsung dalam 3 hari.')
            ->line('Judul: '.$this->jadwalKegiatan->judul)
            ->line('Kategori: '.ucfirst($this->jadwalKegiatan->kategori))
            ->line('Waktu: '.$this->jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i'))
            ->line('Silakan cek dan siapkan kebutuhanmu dari sekarang.');
    }
}
