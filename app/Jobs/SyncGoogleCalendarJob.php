<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGoogleCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public int $uniqueFor = 300;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public User $user,
    ) {
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return 'sync_calendar:' . $this->user->id;
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(GoogleCalendarService $calendarService): void
    {
        if (! $this->user->hasCalendarAccess()) {
            return;
        }

        $result = $calendarService->syncEvents($this->user);

        Log::info('Calendar sync completed', [
            'user_id' => $this->user->id,
            'events' => $result['events'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncGoogleCalendarJob failed', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
