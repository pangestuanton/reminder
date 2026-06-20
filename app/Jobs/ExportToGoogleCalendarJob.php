<?php

namespace App\Jobs;

use App\Models\CollegeSchedule;
use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportToGoogleCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public int $uniqueFor = 600;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public User $user,
        public string $exportableType,
        public int $exportableId,
    ) {
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return "export_cal:{$this->user->id}:{$this->exportableType}:{$this->exportableId}";
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

        if ($this->exportableType === 'jadwal') {
            $jadwal = JadwalKegiatan::where('user_id', $this->user->id)
                ->where('id', $this->exportableId)
                ->first();

            if ($jadwal) {
                $calendarService->exportTask($this->user, $jadwal);
            }
        } elseif ($this->exportableType === 'college_schedule') {
            $schedule = CollegeSchedule::where('user_id', $this->user->id)
                ->where('id', $this->exportableId)
                ->first();

            if ($schedule) {
                $calendarService->exportCollegeSchedule($this->user, $schedule);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExportToGoogleCalendarJob failed', [
            'user_id' => $this->user->id,
            'exportable_type' => $this->exportableType,
            'exportable_id' => $this->exportableId,
            'error' => $exception->getMessage(),
        ]);
    }
}
