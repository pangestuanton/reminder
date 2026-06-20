<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\DailyAgendaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class SendDailyAgendaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public int $uniqueFor = 86400;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public User $user,
    ) {
        $this->onQueue('telegram');
    }

    public function uniqueId(): string
    {
        $date = now()->timezone('Asia/Jakarta')->toDateString();

        return "daily_agenda:{$this->user->id}:{$date}";
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(DailyAgendaService $agendaService): void
    {
        if (! $agendaService->shouldSend($this->user)) {
            return;
        }

        $message = $agendaService->buildAgendaMessage($this->user);

        if ($message === null) {
            return;
        }

        try {
            $telegramMessage = TelegramMessage::create()
                ->content($message)
                ->options(['parse_mode' => 'Markdown'])
                ->button('Dashboard', route('dashboard'));

            $this->user->notifyNow(new \App\Notifications\DailyAgendaNotification($message));

            $agendaService->logSent($this->user);

            Log::info('Daily agenda sent', [
                'user_id' => $this->user->id,
                'date' => now()->timezone('Asia/Jakarta')->toDateString(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Daily agenda send failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendDailyAgendaJob failed', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
