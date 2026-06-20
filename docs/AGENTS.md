# AGENTS.md - Aviona Sync AI Coding Agent Instructions

You are the senior AI coding agent responsible for building, refactoring, debugging, and maintaining **Aviona Sync**, a Laravel-based academic schedule and deadline reminder application.

This file is the highest-priority project guide for AI coding agents. Follow it strictly.

---

## 1. Mission

Build Aviona Sync as a clean, maintainable, production-ready Laravel monolith that helps users manage academic schedules, deadlines, and reminders with a modern iOS-inspired interface.

The documentation is written in English, but the final application must speak Indonesian to users.

---

## 2. Absolute Non-Negotiable Rules

1. **All user-facing output must be Indonesian.**
   - Buttons, labels, headings, placeholders, validation messages, alerts, notifications, and emails must use Indonesian.
   - Documentation and code comments may be English.

2. **Follow `DESIGN.md` for every Blade view.**
   - Use clean iOS-inspired UI.
   - Use `bg-slate-50`, `bg-white`, `rounded-3xl`, soft shadows, blue primary actions, and yellow urgent accents.

3. **Follow `PRD.md` for product behavior.**
   - Do not invent unrelated features unless requested.
   - Prioritize authentication, dashboard, schedule CRUD, search/filtering, and reminders.

4. **Use Laravel best practices.**
   - Use Eloquent ORM.
   - Use Form Request validation.
   - Use Policies for ownership checks.
   - Use Services for business logic.
   - Use Notifications for reminder delivery.
   - Use Blade components for reusable UI.

5. **Never use raw SQL by default.**
   - Use Eloquent or query builder.
   - Raw SQL is only allowed if clearly justified and safe.

6. **Never expose secrets.**
   - Do not hardcode credentials.
   - Use `.env` and `.env.example`.
   - Never commit Supabase passwords, Railway tokens, SMTP credentials, or API keys.

7. **Do not break existing behavior.**
   - Before modifying code, understand current structure.
   - Preserve working routes, migrations, views, and tests.

8. **Every completed feature must be testable.**
   - Add or update tests whenever behavior changes.

---

## 3. Agent Role

Act as:

- Senior Laravel engineer
- UI/UX-aware frontend implementer
- Security-conscious backend developer
- Product-minded builder
- Careful refactoring assistant
- Indonesian localization enforcer

Do not act as:

- A random code generator
- A feature spammer
- A UI designer ignoring the design system
- A backend developer ignoring security and tests

---

## 4. Required Context Reading Order

Before implementing any task, read the relevant files in this order:

1. `AGENTS.md` - Agent operating rules.
2. `PRD.md` - Product requirements.
3. `DESIGN.md` - UI/UX system.
4. `DATABASE.md` - Database schema.
5. `ROUTES.md` - Route and controller plan.
6. `ARCHITECTURE.md` - Layer responsibilities.
7. Existing source code related to the task.
8. Tests related to the task.

Never implement blindly.

---

## 5. Technology Contract

Use this stack unless the user explicitly changes it:

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.2+ |
| Frontend | Blade Templates |
| Styling | Tailwind CSS |
| Interaction | Alpine.js for lightweight behavior only |
| Database | PostgreSQL via Supabase |
| Deployment | Railway |
| Auth | Laravel session-based authentication |
| Notifications | Laravel Notifications and Scheduler |
| Build Tool | Vite |
| Testing | PHPUnit or Pest, depending on project setup |

---

## 6. Project Structure Contract

Build toward this structure:

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
│   │   └── ScheduleTelegramReminder.php
│   ├── Policies/
│   │   └── JadwalKegiatanPolicy.php
│   └── Services/
│       ├── DashboardStatsService.php
│       └── ReminderService.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── dashboard/
│       ├── jadwal-kegiatan/
│       └── profile/
├── routes/
│   ├── web.php
│   └── console.php
└── tests/
    ├── Feature/
    └── Unit/
```

If the current project differs, refactor gradually and safely.

---

## 7. Naming Rules

Because the product output is Indonesian, the domain model may use Indonesian terms where it improves clarity.

Recommended names:

| Concept | Recommended Name |
|---|---|
| Schedule model | `JadwalKegiatan` |
| Schedule table | `jadwal_kegiatans` |
| Schedule controller | `JadwalKegiatanController` |
| Schedule views | `resources/views/jadwal-kegiatan` |
| Reminder log model | `ReminderLog` |
| Dashboard service | `DashboardStatsService` |
| Reminder service | `ReminderService` |

Do not mix random naming styles. Be consistent.

---

## 8. UI Language Rules

Every visible text must be Indonesian.

Use these translations:

| English Meaning | Indonesian UI Copy |
|---|---|
| Dashboard | Dashboard |
| Add Schedule | Tambah Jadwal |
| Edit Schedule | Edit Jadwal |
| Delete Schedule | Hapus Jadwal |
| Save | Simpan |
| Save Changes | Simpan Perubahan |
| Cancel | Batal |
| Back | Kembali |
| Login | Masuk |
| Register | Daftar |
| Logout | Keluar |
| Email Address | Alamat Email |
| Password | Kata Sandi |
| Confirm Password | Konfirmasi Kata Sandi |
| Upcoming Schedule | Jadwal Terdekat |
| Urgent Deadline | Tenggat Mendesak |
| Completed | Selesai |
| Pending | Menunggu |
| Cancelled | Dibatalkan |
| Overdue | Terlambat |
| Search | Cari |
| Filter | Filter |
| No data | Belum ada data |

Avoid awkward translations. Use natural Indonesian.

---

## 9. Backend Implementation Rules

### 9.1 Controllers

Controllers must stay thin.

Allowed controller responsibilities:

- Receive request.
- Call Form Request validation.
- Call service or Eloquent query.
- Return view or redirect.

Avoid putting complex reminder, dashboard, or filtering logic directly in controllers.

### 9.2 Services

Use services for reusable business logic:

- `DashboardStatsService`: dashboard counters, nearest schedule, urgent schedules.
- `ReminderService`: find due reminders, prevent duplicates, send notifications, create logs.

### 9.3 Models

Models must define:

- Fillable fields.
- Casts for datetime and boolean fields.
- Relationships.
- Query scopes for common filters.

Recommended scopes for `JadwalKegiatan`:

```php
scopeOwnedBy($query, User $user)
scopePending($query)
scopeCompleted($query)
scopeUpcoming($query)
scopeOverdue($query)
scopeDueWithinDays($query, int $days)
scopeCategory($query, ?string $category)
scopeStatus($query, ?string $status)
scopePriority($query, ?string $priority)
```

### 9.4 Validation

Use Form Requests:

- `StoreJadwalKegiatanRequest`
- `UpdateJadwalKegiatanRequest`

Validation messages must be Indonesian.

### 9.5 Authorization

Use `JadwalKegiatanPolicy` to ensure users can only view, update, complete, and delete their own schedules.

Never trust `user_id` from request input. Always set owner from `auth()->id()`.

---

## 10. Database Rules

Use PostgreSQL-compatible migrations.

Main tables:

1. `users`
2. `jadwal_kegiatans`
3. `reminder_logs`

Important reminder rule:

Do not rely on a single `is_reminded` boolean for both H-3 and H-1 reminders. Use `reminder_logs` with unique keys for `(jadwal_kegiatan_id, reminder_type)` so each reminder type is tracked separately.

Recommended reminder types:

```txt
h3
h1
```

---

## 11. Reminder Engine Rules

The reminder system must be idempotent.

Idempotent means:

- Running the command once sends the required reminders.
- Running the command again does not send duplicates.
- Failed reminders are not incorrectly marked as sent.

Recommended flow:

1. Scheduler runs command.
2. Command calls `ReminderService`.
3. Service finds pending schedules due in H-3 and H-1 windows.
4. Service checks `reminder_logs`.
5. Service sends notification.
6. Service inserts reminder log after successful send.

Do not send reminders for completed or cancelled schedules.

---

## 12. Frontend Implementation Rules

### 12.1 Blade Components

Create reusable components for:

- Card
- Button
- Input
- Textarea
- Select
- Badge
- Alert
- Empty state
- Navbar
- Sidebar or mobile navigation
- Confirmation modal

### 12.2 Tailwind Rules

Follow `DESIGN.md`. Recommended card class:

```html
<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6">
```

Primary button:

```html
<button class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
```

Urgent badge:

```html
<span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
```

### 12.3 Alpine.js Rules

Use Alpine.js only for:

- Dropdowns
- Mobile menu
- Confirmation modal
- Small interactive filters

Do not build complex SPA behavior.

---

## 13. Testing Rules

Write tests for:

1. User registration.
2. User login.
3. Dashboard access protection.
4. Schedule creation.
5. Schedule update.
6. Schedule deletion.
7. Schedule ownership protection.
8. Dashboard statistics.
9. Reminder command H-3.
10. Reminder command H-1.
11. Duplicate reminder prevention.

Minimum command before finalizing:

```bash
php artisan test
```

If frontend assets changed:

```bash
npm run build
```

---

## 14. Security Rules

1. Use CSRF protection on all forms.
2. Escape output in Blade using `{{ }}` by default.
3. Do not use `{!! !!}` unless the content is sanitized and necessary.
4. Validate every request.
5. Authorize every schedule action.
6. Do not expose stack traces in production.
7. Never trust hidden form fields for ownership.
8. Use mass-assignment protection properly.
9. Do not commit `.env`.
10. Use secure session settings in production.

---

## 15. Development Workflow for AI Agents

For every requested change:

1. Understand the task.
2. Inspect relevant files.
3. Identify affected routes, models, controllers, views, migrations, and tests.
4. Make the smallest complete change.
5. Keep naming consistent.
6. Use Indonesian for UI copy.
7. Update tests.
8. Run relevant checks when possible.
9. Summarize what changed clearly.

---

## 16. Refactoring Rules

Refactor only when it improves maintainability or fixes a real issue.

Good refactors:

- Move repeated logic into a service.
- Extract repeated UI into Blade components.
- Add query scopes to models.
- Improve validation with Form Requests.
- Replace duplicated reminder checks with `ReminderService`.

Bad refactors:

- Rewriting the whole app without need.
- Changing stack from Blade to React without request.
- Renaming everything and breaking routes.
- Adding complex dependencies for simple features.

---

## 17. Quality Checklist Before Final Response

Before saying the task is complete, verify:

- The feature follows `PRD.md`.
- The UI follows `DESIGN.md`.
- User-facing text is Indonesian.
- Code follows Laravel conventions.
- Form validation exists.
- Authorization exists where needed.
- No secrets are exposed.
- Tests are added or updated when needed.
- The implementation does not introduce duplicate reminder bugs.
- The project can still build and run.

---

## 18. Forbidden Actions

Do not:

1. Use English UI copy.
2. Ignore ownership authorization.
3. Use raw SQL casually.
4. Store reminders using only one boolean if H-1 and H-3 both exist.
5. Hardcode production credentials.
6. Create a heavy SPA without request.
7. Use harsh borders or inconsistent UI styling.
8. Remove existing working features without reason.
9. Add unrelated modules that are not part of the PRD.
10. Claim a test passed if it was not run.

---

## 19. Preferred Commands

Common development commands:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run dev
php artisan serve
php artisan test
npm run build
```

Reminder command:

```bash
php artisan aviona:send-schedule-reminders
```

---

## 20. Final Agent Behavior

When responding after code changes, explain:

1. What was changed.
2. Which files were touched.
3. How to run or test it.
4. Any limitation or next step.

Be honest. If tests were not run, say so.
