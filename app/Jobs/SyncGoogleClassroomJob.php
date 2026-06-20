<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleClassroomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGoogleClassroomJob implements ShouldQueue
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
        return 'sync_classroom:'.$this->user->id;
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(GoogleClassroomService $classroomService): void
    {
        if (! $this->user->hasClassroomAccess()) {
            return;
        }

        $courseResult = $classroomService->syncCourses($this->user);

        if (isset($courseResult['error'])) {
            Log::warning('Classroom course sync failed', [
                'user_id' => $this->user->id,
                'error' => $courseResult['error'],
            ]);

            return;
        }

        $workResult = $classroomService->syncCourseWork($this->user);

        Log::info('Classroom sync completed', [
            'user_id' => $this->user->id,
            'courses' => $courseResult['courses'],
            'course_works' => $workResult['course_works'] ?? 0,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncGoogleClassroomJob failed', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
