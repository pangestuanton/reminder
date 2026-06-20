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
});
