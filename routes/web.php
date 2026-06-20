<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CollegeScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TelegramLinkController;
use App\Models\JadwalKegiatan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/telegram', [TelegramLinkController::class, 'store'])->name('profile.telegram.store');
    Route::delete('/profile/telegram', [TelegramLinkController::class, 'destroy'])->name('profile.telegram.destroy');

    Route::get('/jadwal-kegiatan', [JadwalKegiatanController::class, 'index'])->name('jadwal-kegiatan.index');

    Route::get('/jadwal-kegiatan/create', [JadwalKegiatanController::class, 'create'])
        ->can('create', JadwalKegiatan::class)
        ->name('jadwal-kegiatan.create');

    Route::post('/jadwal-kegiatan', [JadwalKegiatanController::class, 'store'])
        ->can('create', JadwalKegiatan::class)
        ->name('jadwal-kegiatan.store');

    Route::get('/jadwal-kegiatan/{jadwalKegiatan}', [JadwalKegiatanController::class, 'show'])
        ->can('view', 'jadwalKegiatan')
        ->name('jadwal-kegiatan.show');

    Route::get('/jadwal-kegiatan/{jadwalKegiatan}/edit', [JadwalKegiatanController::class, 'edit'])
        ->can('update', 'jadwalKegiatan')
        ->name('jadwal-kegiatan.edit');

    Route::put('/jadwal-kegiatan/{jadwalKegiatan}', [JadwalKegiatanController::class, 'update'])
        ->can('update', 'jadwalKegiatan')
        ->name('jadwal-kegiatan.update');

    Route::delete('/jadwal-kegiatan/{jadwalKegiatan}', [JadwalKegiatanController::class, 'destroy'])
        ->can('delete', 'jadwalKegiatan')
        ->name('jadwal-kegiatan.destroy');

    Route::patch('/jadwal-kegiatan/{jadwalKegiatan}/selesai', [JadwalKegiatanController::class, 'complete'])
        ->can('complete', 'jadwalKegiatan')
        ->name('jadwal-kegiatan.complete');

    Route::get('/jadkul', [CollegeScheduleController::class, 'index'])->name('college-schedule.index');
    Route::get('/jadkul/create', [CollegeScheduleController::class, 'create'])->name('college-schedule.create');
    Route::post('/jadkul', [CollegeScheduleController::class, 'store'])->name('college-schedule.store');
    Route::get('/jadkul/{collegeSchedule}', [CollegeScheduleController::class, 'show'])->name('college-schedule.show');
    Route::get('/jadkul/{collegeSchedule}/edit', [CollegeScheduleController::class, 'edit'])->name('college-schedule.edit');
    Route::put('/jadkul/{collegeSchedule}', [CollegeScheduleController::class, 'update'])->name('college-schedule.update');
    Route::delete('/jadkul/{collegeSchedule}', [CollegeScheduleController::class, 'destroy'])->name('college-schedule.destroy');
    Route::patch('/jadkul/{collegeSchedule}/toggle', [CollegeScheduleController::class, 'toggle'])->name('college-schedule.toggle');

    Route::get('/classroom', [ClassroomController::class, 'index'])->name('classroom.index');
    Route::get('/classroom/{course}', [ClassroomController::class, 'show'])->name('classroom.show');
    Route::post('/classroom/sync', [ClassroomController::class, 'sync'])->name('classroom.sync');
    Route::post('/classroom/disconnect', [ClassroomController::class, 'disconnect'])->name('classroom.disconnect');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/sync', [CalendarController::class, 'sync'])->name('calendar.sync');
    Route::post('/calendar/export/{jadwalKegiatan}', [CalendarController::class, 'exportTask'])->name('calendar.export-task');
    Route::post('/calendar/disconnect', [CalendarController::class, 'disconnect'])->name('calendar.disconnect');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/integrations', [IntegrationsController::class, 'index'])->name('integrations.index');
    Route::get('/integrations/google/{service}', [IntegrationsController::class, 'connectGoogle'])->name('integrations.google.connect');
    Route::post('/integrations/google/disconnect', [IntegrationsController::class, 'disconnectGoogle'])->name('integrations.google.disconnect');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotification'])->name('settings.notifications.update');
    Route::put('/settings/agenda', [SettingsController::class, 'updateAgenda'])->name('settings.agenda.update');

    // Temporary debug route — remove after investigation
    Route::get('/debug/classroom', function (\App\Services\GoogleTokenService $tokenService) {
        $user = auth()->user();
        $account = $user->googleAccount;
        $output = [];

        $output['user'] = [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'google_id' => $user->google_id,
        ];

        if ($account) {
            $token = null;
            $tokenError = null;
            try {
                $token = $tokenService->getAccessToken($account);
            } catch (\Throwable $e) {
                $tokenError = $e->getMessage();
            }

            $output['google_account'] = [
                'id'                    => $account->id,
                'email'                 => $account->google_account_email,
                'classroom_connected_at'=> (string) $account->classroom_connected_at,
                'calendar_connected_at' => (string) $account->calendar_connected_at,
                'disconnected_at'       => (string) $account->disconnected_at,
                'token_expires_at'      => (string) $account->token_expires_at,
                'token_is_expired'      => $account->isTokenExpired(),
                'has_access_token'      => ! empty($account->access_token_encrypted),
                'has_refresh_token'     => ! empty($account->refresh_token_encrypted),
                'isClassroomConnected'  => $account->isClassroomConnected(),
                'isCalendarConnected'   => $account->isCalendarConnected(),
                'access_token_result'   => $token ? 'OK ('.strlen($token).' chars)' : 'FAILED',
                'token_error'           => $tokenError,
                'scopes'                => $account->scopes,
            ];

            // Try a live Classroom API call
            if ($token) {
                try {
                    $http = new \GuzzleHttp\Client(['verify' => false, 'timeout' => 10]);
                    $resp = $http->get('https://classroom.googleapis.com/v1/courses', [
                        'headers' => ['Authorization' => 'Bearer ' . $token],
                        'query'   => ['pageSize' => 5],
                    ]);
                    $data = json_decode($resp->getBody()->getContents(), true);
                    $output['live_api_courses'] = $data['courses'] ?? [];
                    $output['live_api_error'] = null;
                } catch (\Throwable $e) {
                    $output['live_api_courses'] = [];
                    $output['live_api_error'] = $e->getMessage();
                }
            }
        } else {
            $output['google_account'] = null;
        }

        $output['db_courses'] = \App\Models\GoogleClassroomCourse::ownedBy($user)->get()->toArray();
        $output['db_course_works_count'] = \App\Models\GoogleClassroomCourseWork::ownedBy($user)->count();
        $output['db_course_works'] = \App\Models\GoogleClassroomCourseWork::ownedBy($user)->orderBy('due_date')->limit(20)->get()->toArray();
        $output['pending_jobs'] = \Illuminate\Support\Facades\DB::table('jobs')->count();
        $output['failed_jobs'] = \Illuminate\Support\Facades\DB::table('failed_jobs')
            ->orderByDesc('id')->limit(5)
            ->get()
            ->map(fn($j) => [
                'queue'     => $j->queue,
                'job'       => json_decode($j->payload, true)['displayName'] ?? '?',
                'exception' => substr($j->exception, 0, 500),
            ])->toArray();

        return response()->json($output, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('debug.classroom');

    // Force-sync classroom synchronously (no queue) — for Railway debugging
    Route::get('/debug/classroom/sync', function (\App\Services\GoogleClassroomService $classroomService) {
        $user = auth()->user();
        $output = ['user_id' => $user->id, 'steps' => []];

        if (! $user->hasClassroomAccess()) {
            $output['error'] = 'Classroom not connected. classroom_connected_at is null or disconnected.';
            return response()->json($output, 200, [], JSON_PRETTY_PRINT);
        }

        // Step 1: sync courses
        $courseResult = $classroomService->syncCourses($user);
        $output['steps'][] = ['sync_courses' => $courseResult];

        if (isset($courseResult['error'])) {
            $output['error'] = $courseResult['error'];
            return response()->json($output, 200, [], JSON_PRETTY_PRINT);
        }

        // Step 2: sync course works
        $workResult = $classroomService->syncCourseWork($user);
        $output['steps'][] = ['sync_course_works' => $workResult];

        $output['db_courses_count']      = \App\Models\GoogleClassroomCourse::ownedBy($user)->count();
        $output['db_course_works_count'] = \App\Models\GoogleClassroomCourseWork::ownedBy($user)->count();
        $output['message'] = 'Sync complete. Refresh /classroom to see your tasks.';

        return response()->json($output, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    })->name('debug.classroom.sync');
});
