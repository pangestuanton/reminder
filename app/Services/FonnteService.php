<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FonnteService
{
    public function isEnabled(): bool
    {
        return (bool) config('services.fonnte.enabled') && filled(config('services.fonnte.token'));
    }

    public function sendMessage(string $target, string $message): Response
    {
        return Http::withHeaders([
            'Authorization' => config('services.fonnte.token'),
        ])->asForm()->post($this->sendUrl(), [
            'target' => $this->normalizeNumber($target),
            'message' => $message,
        ]);
    }

    public function messageWasAccepted(Response $response): bool
    {
        if (! $response->successful()) {
            return false;
        }

        return (bool) data_get($response->json(), 'status', false);
    }

    public function normalizeNumber(string $number): string
    {
        $normalized = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($normalized, '0')) {
            return '62'.substr($normalized, 1);
        }

        return $normalized;
    }

    private function sendUrl(): string
    {
        return rtrim((string) config('services.fonnte.base_url'), '/').'/send';
    }
}
