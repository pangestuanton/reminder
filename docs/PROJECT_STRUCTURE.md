# Project Structure - Aviona Sync

## 1. Target Structure

The project should follow this structure to stay maintainable, AI-friendly, and Laravel-conventional.

```txt
aviona-sync/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SendScheduleReminders.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── LogoutController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── JadwalKegiatanController.php
│   │   │   └── ProfileController.php
│   │   ├── Requests/
│   │   │   ├── StoreJadwalKegiatanRequest.php
│   │   │   └── UpdateJadwalKegiatanRequest.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── JadwalKegiatan.php
│   │   └── ReminderLog.php
│   ├── Notifications/
│   │   ├── ScheduleH1Reminder.php
│   │   └── ScheduleH3Reminder.php
│   ├── Policies/
│   │   └── JadwalKegiatanPolicy.php
│   ├── Providers/
│   └── Services/
│       ├── DashboardStatsService.php
│       └── ReminderService.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── JadwalKegiatanFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_01_01_000001_create_jadwal_kegiatans_table.php
│   │   └── 2026_01_01_000002_create_reminder_logs_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── DemoJadwalKegiatanSeeder.php
├── docs/
│   ├── PRD.md
│   ├── DESIGN.md
│   ├── AGENTS.md
│   ├── ARCHITECTURE.md
│   ├── DATABASE.md
│   ├── ROUTES.md
│   ├── TESTING.md
│   ├── SECURITY.md
│   └── DEPLOYMENT.md
├── public/
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── guest.blade.php
│       ├── components/
│       │   ├── alert.blade.php
│       │   ├── app-logo.blade.php
│       │   ├── badge.blade.php
│       │   ├── button.blade.php
│       │   ├── card.blade.php
│       │   ├── empty-state.blade.php
│       │   ├── input.blade.php
│       │   ├── modal.blade.php
│       │   ├── select.blade.php
│       │   ├── textarea.blade.php
│       │   └── validation-error.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── jadwal-kegiatan/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       └── profile/
│           └── edit.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── DashboardTest.php
│   │   ├── JadwalKegiatanCrudTest.php
│   │   ├── JadwalKegiatanPolicyTest.php
│   │   └── ReminderCommandTest.php
│   └── Unit/
│       ├── DashboardStatsServiceTest.php
│       └── ReminderServiceTest.php
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js
```

---

## 2. Folder Responsibilities

### `app/Console/Commands`

Contains scheduled commands, especially the reminder sender.

### `app/Http/Controllers`

Contains controllers. Keep controllers thin and readable.

### `app/Http/Requests`

Contains validation logic and Indonesian validation messages.

### `app/Models`

Contains Eloquent models and relationships.

### `app/Notifications`

Contains Laravel Notification classes for reminder delivery.

### `app/Policies`

Contains authorization logic.

### `app/Services`

Contains reusable business logic that should not live in controllers.

### `resources/views/components`

Contains reusable Blade UI components that follow `DESIGN.md`.

### `resources/views/jadwal-kegiatan`

Contains schedule CRUD pages.

### `tests`

Contains feature and unit tests.

---

## 3. View Structure Standard

Each page should follow this pattern:

```blade
<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Judul Halaman</h1>
                <p class="mt-1 text-sm text-slate-500">Deskripsi singkat halaman.</p>
            </div>
        </div>

        <x-card>
            <!-- Content -->
        </x-card>
    </div>
</x-app-layout>
```

---

## 4. Component Naming Standard

Use kebab-case for Blade components:

```txt
<x-card>
<x-button>
<x-input>
<x-select>
<x-textarea>
<x-badge>
<x-alert>
<x-empty-state>
<x-validation-error>
```

---

## 5. Test Naming Standard

Use descriptive test names:

```php
public function test_authenticated_user_can_create_schedule(): void
public function test_user_cannot_update_other_users_schedule(): void
public function test_h1_reminder_is_not_sent_twice(): void
```

---

## 6. Structure Quality Checklist

- Business logic is not duplicated across controllers.
- Repeated UI is extracted into components.
- Database schema is documented.
- Routes are named consistently.
- Tests are grouped by feature.
- User-facing copy is Indonesian.
