<?php

namespace App\Services;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WhatsappReminderService
{
    public function __construct(private readonly FonnteService $fonnteService) {}

    public function isEnabled(): bool
    {
        return $this->fonnteService->isEnabled();
    }

    public function canSendTo(User $user): bool
    {
        return $this->isEnabled() && filled($user->whatsapp_number);
    }

    public function send(User $user, JadwalKegiatan $jadwalKegiatan, string $type): bool
    {
        if (! $this->canSendTo($user)) {
            return false;
        }

        try {
            $response = $this->fonnteService->sendMessage(
                $user->whatsapp_number,
                $this->buildMessage($user, $jadwalKegiatan, $type),
            );

            if ($this->fonnteService->messageWasAccepted($response)) {
                return true;
            }

            Log::error('Gagal mengirim pengingat WhatsApp via Fonnte.', [
                'user_id' => $user->id,
                'jadwal_kegiatan_id' => $jadwalKegiatan->id,
                'reminder_type' => $type,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Terjadi kesalahan saat mengirim pengingat WhatsApp via Fonnte.', [
                'user_id' => $user->id,
                'jadwal_kegiatan_id' => $jadwalKegiatan->id,
                'reminder_type' => $type,
                'message' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    protected function buildMessage(User $user, JadwalKegiatan $jadwalKegiatan, string $type): string
    {
        $label = $type === 'h3' ? 'H-3' : 'H-1';

        return "Halo {$user->name},\n\n"
            ."Ini pengingat {$label} dari Aviona Sync.\n"
            ."Judul: {$jadwalKegiatan->judul}\n"
            .'Kategori: '.ucfirst($jadwalKegiatan->kategori)."\n"
            .'Waktu: '.$jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i')."\n\n"
            .'Semoga aktivitasmu lancar ya.';
    }
}
