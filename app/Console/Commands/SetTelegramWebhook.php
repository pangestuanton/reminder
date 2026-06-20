<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';

    protected $description = 'Mendaftarkan webhook bot Telegram untuk penautan akun';

    public function handle(): int
    {
        $token = (string) config('services.telegram-bot-api.token');
        $secret = (string) config('services.telegram.webhook_secret');
        $webhookUrl = route('telegram.webhook');

        if ($token === '' || $secret === '') {
            $this->error('TELEGRAM_BOT_TOKEN dan TELEGRAM_WEBHOOK_SECRET wajib dikonfigurasi.');

            return self::FAILURE;
        }

        if (! preg_match('/^[A-Za-z0-9_-]{16,256}$/', $secret)) {
            $this->error('TELEGRAM_WEBHOOK_SECRET harus 16-256 karakter: huruf, angka, garis bawah, atau tanda hubung.');

            return self::FAILURE;
        }

        if (! str_starts_with($webhookUrl, 'https://')) {
            $this->error('APP_URL harus menggunakan HTTPS agar webhook diterima Telegram.');

            return self::FAILURE;
        }

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $webhookUrl,
                    'secret_token' => $secret,
                    'allowed_updates' => json_encode(['message'], JSON_THROW_ON_ERROR),
                ]);
        } catch (Throwable) {
            $this->error('Tidak dapat menghubungi Telegram Bot API.');

            return self::FAILURE;
        }

        if (! $response->successful() || ! $response->json('ok')) {
            $this->error('Telegram menolak konfigurasi webhook. Periksa token, URL, dan secret.');

            return self::FAILURE;
        }

        $this->info('Webhook Telegram berhasil didaftarkan: '.$webhookUrl);

        return self::SUCCESS;
    }
}
