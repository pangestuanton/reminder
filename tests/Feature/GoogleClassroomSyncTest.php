<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleClassroomSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_users_can_have_tasks_from_the_same_classroom_course_work(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // User A imports coursework
        $taskA = JadwalKegiatan::create([
            'user_id' => $userA->id,
            'judul' => 'Tugas Bersama ASD',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDays(3),
            'source' => 'classroom',
            'source_id' => 'coursework-id-123',
            'status' => 'pending',
            'prioritas' => 'tinggi',
        ]);

        // User B imports the exact same coursework (with identical source & source_id)
        $taskB = JadwalKegiatan::create([
            'user_id' => $userB->id,
            'judul' => 'Tugas Bersama ASD',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDays(3),
            'source' => 'classroom',
            'source_id' => 'coursework-id-123',
            'status' => 'pending',
            'prioritas' => 'tinggi',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $taskA->id,
            'user_id' => $userA->id,
            'source_id' => 'coursework-id-123',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $taskB->id,
            'user_id' => $userB->id,
            'source_id' => 'coursework-id-123',
        ]);
    }

    public function test_tasks_without_due_date_can_be_saved_with_null_waktu_pelaksanaan(): void
    {
        $user = User::factory()->create();

        $task = JadwalKegiatan::create([
            'user_id' => $user->id,
            'judul' => 'Tugas Tanpa Deadline',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => null,
            'source' => 'classroom',
            'source_id' => 'coursework-id-999',
            'status' => 'pending',
            'prioritas' => 'rendah',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $task->id,
            'waktu_pelaksanaan' => null,
        ]);

        $this->assertNull($task->countdown_text);
    }

    public function test_classroom_due_date_and_time_are_converted_from_utc_to_local_timezone(): void
    {
        $oldTimezone = config('app.timezone');
        config(['app.timezone' => 'Asia/Jakarta']);

        $user = User::factory()->create();
        $googleAccount = $user->googleAccount()->create([
            'google_account_email' => 'user@gmail.com',
            'access_token_encrypted' => encrypt('token'),
            'refresh_token_encrypted' => encrypt('refresh'),
            'token_expires_at' => now()->addHour(),
            'classroom_connected_at' => now(),
            'scopes' => [],
        ]);

        $course = \App\Models\GoogleClassroomCourse::create([
            'user_id' => $user->id,
            'google_account_id' => $googleAccount->id,
            'external_id' => 'course-123',
            'name' => 'R2 AGAMA ISLAM 2026',
            'course_state' => 'ACTIVE',
            'synced_at' => now(),
        ]);

        // Mock GoogleTokenService
        $tokenService = $this->mock(\App\Services\GoogleTokenService::class);
        $tokenService->shouldReceive('getAccessToken')->andReturn('mock-access-token');

        // Mock Guzzle Client
        $mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, [], json_encode([
                'courseWork' => [
                    [
                        'id' => 'cw-123',
                        'title' => 'Tugas Agama Islam',
                        'description' => 'Materi UAS',
                        'dueDate' => [
                            'year' => 2026,
                            'month' => 6,
                            'day' => 21,
                        ],
                        'dueTime' => [
                            'hours' => 10,
                            'minutes' => 15,
                        ],
                        'workType' => 'ASSIGNMENT',
                        'state' => 'PUBLISHED',
                        'alternateLink' => 'https://classroom.google.com/c/123/a/456/details',
                    ]
                ]
            ])),
            new \GuzzleHttp\Psr7\Response(200, [], json_encode(['studentSubmissions' => []])),
        ]);

        $handlerStack = \GuzzleHttp\HandlerStack::create($mock);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $service = new \App\Services\GoogleClassroomService($tokenService);
        $service->setHttpClient($client);

        // Run sync
        $result = $service->syncCourseWork($user);

        config(['app.timezone' => $oldTimezone]);

        $this->assertEquals(1, $result['course_works']);

        // Assert database values
        // Asia/Jakarta is UTC+7, so 10:15 UTC becomes 17:15 local
        $dbWork = \App\Models\GoogleClassroomCourseWork::where('external_id', 'cw-123')->firstOrFail();
        $this->assertEquals('2026-06-21', $dbWork->due_date->format('Y-m-d'));
        $this->assertEquals('17:15', $dbWork->due_time_only);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'source' => 'classroom',
            'source_id' => 'cw-123',
            'waktu_pelaksanaan' => '2026-06-21 17:15:00',
        ]);
    }
}
