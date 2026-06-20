<?php

namespace App\Jobs;

use App\Models\GoogleClassroomCourseWork;
use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoCompleteTaskOnSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public User $user,
        public GoogleClassroomCourseWork $courseWork,
        public string $submissionState,
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        if (! in_array($this->submissionState, ['TURNED_IN', 'RETURNED'], true)) {
            return;
        }

        $jadwal = JadwalKegiatan::where('user_id', $this->user->id)
            ->where('source', 'classroom')
            ->where('source_id', $this->courseWork->external_id)
            ->first();

        if ($jadwal && $jadwal->status !== 'selesai') {
            $jadwal->update([
                'status' => 'selesai',
                'completed_at' => now(),
            ]);

            Log::info('Task auto-completed from Classroom submission', [
                'user_id' => $this->user->id,
                'jadwal_id' => $jadwal->id,
                'state' => $this->submissionState,
            ]);
        }
    }
}
