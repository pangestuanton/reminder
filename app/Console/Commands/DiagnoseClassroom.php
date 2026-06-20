<?php

namespace App\Console\Commands;

use App\Models\GoogleAccount;
use App\Models\GoogleClassroomCourse;
use App\Models\GoogleClassroomCourseWork;
use App\Models\User;
use App\Services\GoogleTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseClassroom extends Command
{
    protected $signature = 'classroom:diagnose {--user=} {--sync}';

    protected $description = 'Diagnose Google Classroom integration and optionally force sync';

    public function handle(GoogleTokenService $tokenService): int
    {
        $this->info('=== CLASSROOM DIAGNOSIS ===');
        $this->newLine();

        // Users
        $users = User::with('googleAccount')->get();
        $this->info("Users: {$users->count()}");
        foreach ($users as $user) {
            $this->line("  [{$user->id}] {$user->name} <{$user->email}>");
        }
        $this->newLine();

        // Google Accounts
        $accounts = GoogleAccount::all();
        $this->info("Google Accounts: {$accounts->count()}");
        foreach ($accounts as $acc) {
            $this->line("  [user_id={$acc->user_id}] {$acc->google_account_email}");
            $this->line("    classroom_connected_at : " . ($acc->classroom_connected_at ?? 'NULL'));
            $this->line("    calendar_connected_at  : " . ($acc->calendar_connected_at ?? 'NULL'));
            $this->line("    disconnected_at        : " . ($acc->disconnected_at ?? 'NULL'));
            $this->line("    token_expires_at       : " . ($acc->token_expires_at ?? 'NULL'));
            $this->line("    has_access_token       : " . (empty($acc->access_token_encrypted) ? 'NO' : 'YES'));
            $this->line("    has_refresh_token      : " . (empty($acc->refresh_token_encrypted) ? 'NO' : 'YES'));
            $this->line("    isClassroomConnected() : " . ($acc->isClassroomConnected() ? 'YES' : 'NO'));

            // Try to get token
            try {
                $token = $tokenService->getAccessToken($acc);
                $this->line("    getAccessToken()       : " . ($token ? 'OK ('.strlen($token).' chars)' : 'FAILED/NULL'));
            } catch (\Throwable $e) {
                $this->line("    getAccessToken() ERROR : " . $e->getMessage());
            }
        }
        $this->newLine();

        // Classroom courses
        $courses = GoogleClassroomCourse::all();
        $this->info("Classroom Courses: {$courses->count()}");
        foreach ($courses as $c) {
            $this->line("  [{$c->id}] {$c->name} (state={$c->course_state}, user_id={$c->user_id})");
        }
        $this->newLine();

        // Course works
        $works = GoogleClassroomCourseWork::all();
        $this->info("Course Works: {$works->count()}");
        foreach ($works->take(10) as $w) {
            $this->line("  [{$w->id}] {$w->title} | due={$w->due_date} | user_id={$w->user_id}");
        }
        if ($works->count() > 10) {
            $this->line("  ... and " . ($works->count() - 10) . " more");
        }
        $this->newLine();

        // Jobs
        $pending = DB::table('jobs')->count();
        $failed  = DB::table('failed_jobs')->count();
        $this->info("Queue: {$pending} pending, {$failed} failed");
        foreach (DB::table('jobs')->get() as $j) {
            $payload = json_decode($j->payload, true);
            $this->line("  [{$j->queue}] " . ($payload['displayName'] ?? '?') . " attempts={$j->attempts}");
        }
        foreach (DB::table('failed_jobs')->orderByDesc('id')->limit(3)->get() as $f) {
            $payload = json_decode($f->payload, true);
            $this->warn("  FAILED [{$f->queue}] " . ($payload['displayName'] ?? '?'));
            $this->warn("    " . substr($f->exception, 0, 300));
        }
        $this->newLine();

        // Force sync if requested
        if ($this->option('sync')) {
            $userId = $this->option('user');
            $targetUsers = $userId ? User::where('id', $userId)->get() : $users;

            foreach ($targetUsers as $user) {
                if (! $user->hasClassroomAccess()) {
                    $this->warn("User [{$user->id}] {$user->name}: Classroom not connected. Skipping.");
                    continue;
                }

                $this->info("Syncing user [{$user->id}] {$user->name}...");
                try {
                    $classroomService = app(\App\Services\GoogleClassroomService::class);

                    $courseResult = $classroomService->syncCourses($user);
                    $this->line("  Courses synced: " . ($courseResult['courses'] ?? 0));
                    if (isset($courseResult['error'])) {
                        $this->error("  Course sync error: " . $courseResult['error']);
                        continue;
                    }

                    $workResult = $classroomService->syncCourseWork($user);
                    $this->line("  CourseWorks synced: " . ($workResult['course_works'] ?? 0));
                    if (isset($workResult['error'])) {
                        $this->error("  CourseWork sync error: " . $workResult['error']);
                    }
                } catch (\Throwable $e) {
                    $this->error("  EXCEPTION: " . $e->getMessage());
                    $this->error("  File: " . $e->getFile() . ':' . $e->getLine());
                }
            }
        }

        $this->newLine();
        $this->info('=== DONE ===');
        return 0;
    }
}
