<?php

namespace App\Console\Commands;

use App\Jobs\SyncGoogleCalendarJob;
use App\Jobs\SyncGoogleClassroomJob;
use App\Models\User;
use Illuminate\Console\Command;

class SyncGoogleIntegrations extends Command
{
    protected $signature = 'aviona:sync-google';

    protected $description = 'Dispatch Google Classroom and Calendar sync for all connected users';

    public function handle(): int
    {
        $users = User::whereHas('googleAccount', function ($query) {
            $query->whereNull('disconnected_at');
        })->get();

        $synced = 0;

        foreach ($users as $user) {
            if ($user->hasClassroomAccess()) {
                SyncGoogleClassroomJob::dispatch($user);
            }
            if ($user->hasCalendarAccess()) {
                SyncGoogleCalendarJob::dispatch($user);
            }
            $synced++;
        }

        $this->info("Google sync dispatched for {$synced} users.");

        return self::SUCCESS;
    }
}
