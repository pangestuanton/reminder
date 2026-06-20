<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GoogleClassroomService;
use App\Services\GoogleTokenService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class PatchClassroomToken extends Command
{
    protected $signature = 'classroom:patch-token {user_id? : User ID to patch (default: first user)}';

    protected $description = 'Patch existing Google token to add Classroom scopes and force sync (use after scope bug fix)';

    public function handle(GoogleClassroomService $classroomService, GoogleTokenService $tokenService): int
    {
        $userId = $this->argument('user_id');
        $user = $userId ? User::findOrFail($userId) : User::first();

        if (! $user) {
            $this->error('No user found.');

            return 1;
        }

        $account = $user->googleAccount;
        if (! $account) {
            $this->error("User [{$user->id}] has no Google account.");

            return 1;
        }

        $this->info("User: [{$user->id}] {$user->name}");
        $this->info('Current scopes: '.implode(', ', $account->scopes ?? []));

        // Add classroom scopes to the stored scope list
        $classroomScopes = [
            'https://www.googleapis.com/auth/classroom.courses.readonly',
            'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
            'https://www.googleapis.com/auth/classroom.student-submissions.me.readonly',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ];

        $existingScopes = $account->scopes ?? [];
        $mergedScopes = array_values(array_unique(array_merge($existingScopes, $classroomScopes)));
        $account->update(['scopes' => $mergedScopes]);
        $this->info('Updated scopes: '.implode(', ', $mergedScopes));

        // Get current access token
        $token = null;
        try {
            $token = $tokenService->getAccessToken($account);
        } catch (\Throwable $e) {
            $this->error('Cannot get access token: '.$e->getMessage());
        }

        if (! $token) {
            $this->warn('No valid access token. User must reconnect Google Classroom manually.');
            $this->warn("Go to /integrations and click 'Hubungkan Classroom'.");

            return 0;
        }

        // Test if current token has classroom scope
        $http = new Client(['verify' => false, 'timeout' => 10]);
        try {
            $resp = $http->get('https://classroom.googleapis.com/v1/courses', [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'query' => ['pageSize' => 1],
            ]);
            $this->info('✓ Current token HAS Classroom access!');
        } catch (\Throwable $e) {
            $this->warn('✗ Current token does NOT have Classroom access: '.$e->getMessage());
            $this->warn('User MUST go to /integrations → Putuskan → Hubungkan Classroom again.');
            $this->warn('The fix is deployed — next time they connect it will merge scopes properly.');

            return 0;
        }

        // Mark classroom as connected
        $account->update(['classroom_connected_at' => now()]);
        $this->info('✓ classroom_connected_at set');

        // Force sync
        $this->info('Syncing courses...');
        $courseResult = $classroomService->syncCourses($user);
        $this->info('  Courses: '.($courseResult['courses'] ?? 0));
        if (isset($courseResult['error'])) {
            $this->error('  Error: '.$courseResult['error']);

            return 1;
        }

        $this->info('Syncing course works...');
        $workResult = $classroomService->syncCourseWork($user);
        $this->info('  CourseWorks: '.($workResult['course_works'] ?? 0));

        $this->newLine();
        $this->info('✓ Done! Check /classroom to see your tasks.');

        return 0;
    }
}
