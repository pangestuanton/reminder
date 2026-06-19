# Route Plan - Aviona Sync

## 1. Route Principles

1. Use named routes.
2. Group protected routes under `auth` middleware.
3. Keep route names consistent and readable.
4. Use resource routes for schedule CRUD.
5. Do not expose schedule data without ownership checks.
6. All visible route responses must use Indonesian UI copy.

---

## 2. Guest Routes

| Method | URI | Name | Controller | Purpose |
|---|---|---|---|---|
| GET | `/` | `home` | Redirect | Redirect guest to login or auth user to dashboard |
| GET | `/login` | `login` | `LoginController@create` | Show login page |
| POST | `/login` | `login.store` | `LoginController@store` | Authenticate user |
| GET | `/register` | `register` | `RegisterController@create` | Show registration page |
| POST | `/register` | `register.store` | `RegisterController@store` | Create account |

---

## 3. Authenticated Routes

| Method | URI | Name | Controller | Purpose |
|---|---|---|---|---|
| POST | `/logout` | `logout` | `LogoutController@destroy` | Logout user |
| GET | `/dashboard` | `dashboard` | `DashboardController@index` | Show dashboard |
| GET | `/profile` | `profile.edit` | `ProfileController@edit` | Edit profile |
| PUT | `/profile` | `profile.update` | `ProfileController@update` | Update profile |

---

## 4. Schedule Resource Routes

| Method | URI | Name | Controller | Purpose |
|---|---|---|---|---|
| GET | `/jadwal-kegiatan` | `jadwal-kegiatan.index` | `JadwalKegiatanController@index` | List schedules |
| GET | `/jadwal-kegiatan/create` | `jadwal-kegiatan.create` | `JadwalKegiatanController@create` | Create form |
| POST | `/jadwal-kegiatan` | `jadwal-kegiatan.store` | `JadwalKegiatanController@store` | Store schedule |
| GET | `/jadwal-kegiatan/{jadwalKegiatan}` | `jadwal-kegiatan.show` | `JadwalKegiatanController@show` | Detail page |
| GET | `/jadwal-kegiatan/{jadwalKegiatan}/edit` | `jadwal-kegiatan.edit` | `JadwalKegiatanController@edit` | Edit form |
| PUT/PATCH | `/jadwal-kegiatan/{jadwalKegiatan}` | `jadwal-kegiatan.update` | `JadwalKegiatanController@update` | Update schedule |
| DELETE | `/jadwal-kegiatan/{jadwalKegiatan}` | `jadwal-kegiatan.destroy` | `JadwalKegiatanController@destroy` | Delete schedule |
| PATCH | `/jadwal-kegiatan/{jadwalKegiatan}/selesai` | `jadwal-kegiatan.complete` | `JadwalKegiatanController@complete` | Mark as completed |

---

## 5. Suggested `web.php`

```php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\ProfileController;
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
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::patch('/jadwal-kegiatan/{jadwalKegiatan}/selesai', [JadwalKegiatanController::class, 'complete'])
        ->name('jadwal-kegiatan.complete');

    Route::resource('jadwal-kegiatan', JadwalKegiatanController::class);
});
```

---

## 6. Console Schedule

In `routes/console.php` or the relevant Laravel scheduler configuration:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('aviona:send-schedule-reminders')->hourly();
```

Hourly scheduling is recommended so reminders are not missed due to deployment or timezone differences.

---

## 7. Route Naming Rules

Use these route names consistently in Blade:

```txt
dashboard
jadwal-kegiatan.index
jadwal-kegiatan.create
jadwal-kegiatan.store
jadwal-kegiatan.show
jadwal-kegiatan.edit
jadwal-kegiatan.update
jadwal-kegiatan.destroy
jadwal-kegiatan.complete
profile.edit
profile.update
logout
```

---

## 8. Flash Message Examples

Use Indonesian flash messages:

```txt
Jadwal berhasil ditambahkan.
Jadwal berhasil diperbarui.
Jadwal berhasil dihapus.
Jadwal berhasil ditandai selesai.
Kamu tidak memiliki akses ke jadwal tersebut.
```

---

## 9. Route Quality Checklist

- Protected pages use `auth` middleware.
- Guest pages use `guest` middleware.
- All schedule actions use policy authorization.
- Routes are named and used consistently.
- Redirects use Indonesian flash messages.
- No sensitive data appears in URLs.
