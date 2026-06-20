<?php

namespace App\Services;

use App\Models\GoogleAccount;
use App\Models\GoogleClassroomCourse;
use App\Models\GoogleClassroomCourseWork;
use App\Models\GoogleClassroomSubmission;
use App\Models\JadwalKegiatan;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleClassroomService
{
    protected Client $httpClient;
    protected GoogleTokenService $tokenService;

    public function __construct(GoogleTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->httpClient = new Client(['verify' => false]);
    }

    public function syncCourses(User $user): array
    {
        $account = $user->googleAccount;
        if (! $account || ! $account->isClassroomConnected()) {
            return ['courses' => 0, 'error' => 'Classroom not connected'];
        }

        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return ['courses' => 0, 'error' => 'Token refresh failed'];
        }

        try {
            $courses = $this->fetchCourses($token);
            $synced = 0;

            foreach ($courses as $courseData) {
                if (($courseData['courseState'] ?? 'ACTIVE') !== 'ACTIVE') {
                    continue;
                }

                GoogleClassroomCourse::updateOrCreate(
                    ['user_id' => $user->id, 'external_id' => $courseData['id']],
                    [
                        'google_account_id' => $account->id,
                        'name' => $courseData['name'] ?? '',
                        'section' => $courseData['section'] ?? null,
                        'description' => $courseData['description'] ?? null,
                        'room' => $courseData['room'] ?? null,
                        'alternate_link' => $courseData['alternateLink'] ?? null,
                        'course_state' => $courseData['courseState'] ?? 'ACTIVE',
                        'synced_at' => now(),
                    ]
                );
                $synced++;
            }

            $account->update(['classroom_connected_at' => $account->classroom_connected_at ?? now()]);

            return ['courses' => $synced];
        } catch (\Throwable $e) {
            Log::error('Classroom sync failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return ['courses' => 0, 'error' => $e->getMessage()];
        }
    }

    public function syncCourseWork(User $user): array
    {
        $account = $user->googleAccount;
        if (! $account) {
            return ['course_works' => 0, 'error' => 'No Google account'];
        }

        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return ['course_works' => 0, 'error' => 'Token refresh failed'];
        }

        $courses = GoogleClassroomCourse::where('user_id', $user->id)->active()->get();
        $synced = 0;

        Log::info('Starting courseWork sync', [
            'user_id'      => $user->id,
            'course_count' => $courses->count(),
        ]);

        foreach ($courses as $course) {
            $works = [];
            try {
                $works = $this->fetchCourseWork($token, $course->external_id);
            } catch (\Throwable $e) {
                Log::warning('CourseWork fetch failed for course', [
                    'user_id'           => $user->id,
                    'course'            => $course->name,
                    'course_external_id'=> $course->external_id,
                    'error'             => $e->getMessage(),
                ]);
                continue;
            }

            Log::info('CourseWork fetched for course', [
                'user_id'    => $user->id,
                'course'     => $course->name,
                'course_id'  => $course->external_id,
                'work_count' => count($works),
            ]);

            // Process each work item independently so one failure doesn't skip the rest
            foreach ($works as $workData) {
                try {
                    $courseWork = GoogleClassroomCourseWork::updateOrCreate(
                        ['user_id' => $user->id, 'external_id' => $workData['id']],
                        [
                            'google_classroom_course_id' => $course->id,
                            'title'         => $workData['title'] ?? '',
                            'description'   => $workData['description'] ?? null,
                            'due_date'      => $this->parseDueDate($workData['dueDate'] ?? null),
                            'due_time_only' => $this->parseDueTime($workData['dueTime'] ?? null),
                            'max_points'    => $workData['maxPoints'] ?? null,
                            'work_type'     => $workData['workType'] ?? 'ASSIGNMENT',
                            'status'        => $workData['state'] ?? 'PUBLISHED',
                            'alternate_link'=> $workData['alternateLink'] ?? null,
                            'materials'     => $this->extractMaterials($workData['materials'] ?? []),
                            'synced_at'     => now(),
                        ]
                    );

                    $this->syncSubmissions($courseWork, $user);
                    $this->importCourseWorkAsTask($user, $courseWork, $course);
                    $synced++;
                } catch (\Throwable $e) {
                    Log::warning('CourseWork item sync failed', [
                        'user_id'    => $user->id,
                        'course'     => $course->name,
                        'work_title' => $workData['title'] ?? '?',
                        'error'      => $e->getMessage(),
                    ]);
                }
            }
        }

        Log::info('CourseWork sync complete', [
            'user_id'     => $user->id,
            'total_synced'=> $synced,
        ]);

        return ['course_works' => $synced];
    }

    public function syncSubmissions(GoogleClassroomCourseWork $courseWork, User $user): void
    {
        try {
            $response = $this->httpClient->get(
                "https://classroom.googleapis.com/v1/courses/{$courseWork->course->external_id}/courseWork/{$courseWork->external_id}/studentSubmissions",
                ['headers' => ['Authorization' => 'Bearer ' . $this->getAccessTokenForUser($user)]]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            foreach (($data['studentSubmissions'] ?? []) as $sub) {
                GoogleClassroomSubmission::updateOrCreate(
                    ['user_id' => $user->id, 'external_id' => $sub['id']],
                    [
                        'google_classroom_course_work_id' => $courseWork->id,
                        'state' => $sub['state'] ?? 'NEW',
                        'late' => $sub['late'] ?? false,
                        'draft_url' => $sub['draft']['sharedDriveId'] ?? null,
                        'alternate_link' => $sub['alternateLink'] ?? null,
                        'synced_at' => now(),
                    ]
                );

                $this->updateTaskFromSubmission($user, $courseWork, $sub['state'] ?? 'NEW');
            }
        } catch (\Throwable $e) {
            Log::warning('Submission sync failed', [
                'user_id' => $user->id,
                'course_work_id' => $courseWork->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function importCourseWorkAsTask(User $user, GoogleClassroomCourseWork $courseWork, GoogleClassroomCourse $course): void
    {
        $dueDateTime = $courseWork->due_date
            ? $courseWork->due_date->copy()->setTimeFromTimeString($courseWork->due_time_only ?? '23:59')
            : now()->addDays(7);

        $kategori = match ($courseWork->work_type) {
            'QUIZ' => 'uts',
            'TEST' => 'uas',
            default => 'tugas',
        };

        JadwalKegiatan::updateOrCreate(
            [
                'user_id' => $user->id,
                'source' => 'classroom',
                'source_id' => $courseWork->external_id,
            ],
            [
                'judul' => $courseWork->title,
                'kategori' => $kategori,
                'waktu_pelaksanaan' => $dueDateTime,
                'lokasi_atau_link' => $courseWork->alternate_link,
                'deskripsi' => $courseWork->description,
                'status' => $courseWork->isSubmitted() ? 'selesai' : 'pending',
                'prioritas' => $this->calculatePriority($dueDateTime),
                'course_name' => $course->name,
                'deadline_at' => $dueDateTime,
            ]
        );
    }

    protected function updateTaskFromSubmission(User $user, GoogleClassroomCourseWork $courseWork, string $state): void
    {
        $jadwal = JadwalKegiatan::where('user_id', $user->id)
            ->where('source', 'classroom')
            ->where('source_id', $courseWork->external_id)
            ->first();

        if (! $jadwal) {
            return;
        }

        if (in_array($state, ['TURNED_IN', 'RETURNED'], true)) {
            $jadwal->update([
                'status' => 'selesai',
                'completed_at' => now(),
            ]);
        }
    }

    protected function calculatePriority(Carbon $dueDateTime): string
    {
        $daysUntil = now()->diffInDays($dueDateTime, false);

        if ($daysUntil < 0) {
            return 'tinggi';
        }
        if ($daysUntil <= 3) {
            return 'tinggi';
        }
        if ($daysUntil <= 7) {
            return 'sedang';
        }

        return 'rendah';
    }

    protected function extractMaterials(array $materials): array
    {
        $result = [];

        foreach ($materials as $material) {
            if (isset($material['driveFile']['id'])) {
                $result[] = [
                    'type' => 'driveFile',
                    'id' => $material['driveFile']['id'],
                    'title' => $material['driveFile']['title'] ?? null,
                ];
            } elseif (isset($material['link']['url'])) {
                $result[] = [
                    'type' => 'link',
                    'url' => $material['link']['url'],
                    'title' => $material['link']['title'] ?? null,
                ];
            } elseif (isset($material['youTubeVideo']['id'])) {
                $result[] = [
                    'type' => 'youtube',
                    'id' => $material['youTubeVideo']['id'],
                    'title' => $material['youTubeVideo']['title'] ?? null,
                ];
            } elseif (isset($material['form'])) {
                $result[] = [
                    'type' => 'form',
                    'id' => $material['form']['formId'] ?? null,
                    'title' => $material['form']['title'] ?? null,
                ];
            }
        }

        return $result;
    }

    protected function parseDueDate(?array $dueDate): ?Carbon
    {
        if (! $dueDate || ! isset($dueDate['year'])) {
            return null;
        }

        return Carbon::createFromDate(
            $dueDate['year'],
            $dueDate['month'] ?? 1,
            $dueDate['day'] ?? 1
        );
    }

    protected function parseDueTime(?array $dueTime): ?string
    {
        if (! $dueTime || ! isset($dueTime['hours'])) {
            return null;
        }

        return sprintf('%02d:%02d', $dueTime['hours'], $dueTime['minutes'] ?? 0);
    }

    protected function fetchCourses(string $token): array
    {
        $courses = [];
        $pageToken = null;

        do {
            $params = ['pageSize' => 50];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response = $this->httpClient->get(
                'https://classroom.googleapis.com/v1/courses',
                [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                    'query' => $params,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);
            $courses = array_merge($courses, $data['courses'] ?? []);
            $pageToken = $data['nextPageToken'] ?? null;
        } while ($pageToken);

        return $courses;
    }

    protected function fetchCourseWork(string $token, string $courseId): array
    {
        $works = [];
        $pageToken = null;

        do {
            $params = ['pageSize' => 50];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response = $this->httpClient->get(
                "https://classroom.googleapis.com/v1/courses/{$courseId}/courseWork",
                [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                    'query' => $params,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);
            $works = array_merge($works, $data['courseWork'] ?? []);
            $pageToken = $data['nextPageToken'] ?? null;
        } while ($pageToken);

        return $works;
    }

    protected function getAccessTokenForUser(User $user): string
    {
        $account = $user->googleAccount;

        return $this->tokenService->getAccessToken($account) ?? '';
    }

    public function getMaterialsLinks(?array $materials): array
    {
        $links = [];

        foreach ($materials ?? [] as $material) {
            if ($material['type'] === 'link') {
                $links[] = $material['url'];
            } elseif ($material['type'] === 'youtube') {
                $links[] = "https://youtube.com/watch?v={$material['id']}";
            }
        }

        return $links;
    }

    public function disconnect(User $user): void
    {
        $account = $user->googleAccount;
        if (! $account) {
            return;
        }

        $account->update([
            'classroom_connected_at' => null,
        ]);

        GoogleClassroomCourse::where('user_id', $user->id)->delete();
    }
}
