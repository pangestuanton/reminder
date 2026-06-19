<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FonnteTest extends Command
{
    protected $signature = 'fonnte:test';

    protected $description = 'Kirim pesan tes WhatsApp melalui Fonnte API';

    public function handle(FonnteService $fonnteService): int
    {
        $token = config('services.fonnte.token');
        $baseUrl = config('services.fonnte.base_url');
        $target = config('services.fonnte.test_target');

        $this->info('Tes WhatsApp Fonnte');

        if (! $fonnteService->isEnabled()) {
            $this->error('FONNTE_ENABLED belum aktif atau FONNTE_TOKEN belum diisi di .env.');

            return static::FAILURE;
        }

        if (blank($token)) {
            $this->error('FONNTE_TOKEN belum diisi di .env.');

            return static::FAILURE;
        }

        if (blank($target)) {
            $this->error('FONNTE_TEST_TARGET belum diisi di .env.');

            return static::FAILURE;
        }

        try {
            $this->line("Target: {$target}");
            $this->line('API: '.rtrim((string) $baseUrl, '/').'/send');
            $this->info('Mengirim pesan tes...');

            $response = $fonnteService->sendMessage(
                $target,
                'Halo, ini pesan tes dari Aviona Sync. Waktu kirim: '.now()->format('Y-m-d H:i:s'),
            );

            if ($fonnteService->messageWasAccepted($response)) {
                $this->info('Pesan WhatsApp berhasil dikirim.');
                $this->line('Respons: '.$response->body());

                return static::SUCCESS;
            }

            $this->error('Pesan WhatsApp gagal diproses oleh Fonnte. HTTP '.$response->status());
            $this->line('Respons: '.$response->body());

            return static::FAILURE;
        } catch (\Throwable $exception) {
            Log::error('Tes Fonnte gagal dijalankan.', [
                'message' => $exception->getMessage(),
            ]);

            $this->error('Pesan WhatsApp gagal dikirim: '.$exception->getMessage());

            return static::FAILURE;
        }
    }
}
