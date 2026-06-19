<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\ProfileController;
use App\Models\JadwalKegiatan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

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

    Route::get('/api/trigger-reminders', function () {
        Artisan::call('aviona:send-schedule-reminders');

        return response()->json(['message' => 'Reminders processed']);
    });
});
