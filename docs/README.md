# Aviona Sync Documentation Pack

Aviona Sync is a centralized academic deadline, schedule, and reminder management application built with Laravel, Blade, Tailwind CSS, Alpine.js, PostgreSQL, Railway, and Supabase.

> Documentation language: English.  
> Application output language: Indonesian. Every visible UI label, button, validation message, empty state, notification, email, and dashboard copy generated from this documentation must be written in natural Indonesian.

---

## 1. Project Identity

| Item | Description |
|---|---|
| Project Name | Aviona Sync |
| Product Type | Academic schedule, deadline, and reminder management system |
| Main Users | Students, class coordinators, organization members, and academic activity planners |
| Development Style | AI-assisted Laravel monolith with clear documentation and strict design rules |
| Deployment Target | Railway for Laravel backend, Supabase PostgreSQL for database |
| UI Language | Indonesian |
| Documentation Language | English |

---

## 2. Product Summary

Aviona Sync helps users manage academic activities such as lectures, assignments, midterm exams, final exams, organization events, and deadline-based reminders in one clean dashboard. The app focuses on simplicity, fast CRUD workflows, reminder reliability, and a modern iOS-inspired interface.

The product must feel like a personal academic control center: lightweight, clean, reliable, and easy to understand even for users who do not like complicated productivity tools.

---

## 3. Core Capabilities

1. User authentication with registration, login, logout, password hashing, and session protection.
2. Central dashboard with nearest schedules, countdown cards, pending task stats, completed stats, and urgent deadline highlights.
3. Schedule CRUD for academic and organization activities.
4. Reminder engine for H-3 and H-1 notifications using Laravel Scheduler and Laravel Notifications.
5. Status management: pending, completed, cancelled, and overdue.
6. Category filtering and search.
7. Responsive UI optimized for mobile-first usage.
8. Indonesian UI copy with English code and documentation.
9. Production-ready deployment configuration for Railway and Supabase.
10. Safe, maintainable, and AI-friendly project structure.

---

## 4. Recommended Project Structure

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
│   │   ├── Middleware/
│   │   └── Requests/
│   │       ├── StoreJadwalKegiatanRequest.php
│   │       └── UpdateJadwalKegiatanRequest.php
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
│       │   ├── app-logo.blade.php
│       │   ├── badge.blade.php
│       │   ├── button.blade.php
│       │   ├── card.blade.php
│       │   ├── empty-state.blade.php
│       │   ├── input.blade.php
│       │   ├── navbar.blade.php
│       │   └── sidebar.blade.php
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

## 5. Documentation Map

| File | Purpose |
|---|---|
| `PRD.md` | Full product requirements and acceptance criteria |
| `DESIGN.md` | Visual design system and UI/UX rules |
| `AGENTS.md` | Main instruction file for AI coding agents |
| `ARCHITECTURE.md` | Laravel architecture, layers, and responsibilities |
| `PROJECT_STRUCTURE.md` | Detailed folder and file structure |
| `DATABASE.md` | Database schema, indexes, enum strategy, and relationships |
| `ROUTES.md` | Route plan and controller mapping |
| `IMPLEMENTATION_PLAN.md` | Step-by-step development roadmap |
| `TESTING.md` | Test strategy and quality checklist |
| `SECURITY.md` | Security rules and anti-bug checklist |
| `DEPLOYMENT.md` | Railway and Supabase deployment guide |
| `GEMINI.md` | Gemini CLI optimized instruction file |
| `CLAUDE.md` | Claude/Cursor/AI IDE optimized instruction file |

---

## 6. Quick Build Order

1. Create Laravel 11 project.
2. Configure PostgreSQL connection using Supabase credentials.
3. Install and configure Tailwind CSS, Vite, and Alpine.js.
4. Build authentication pages.
5. Create database migrations and Eloquent models.
6. Build schedule CRUD.
7. Build dashboard stats and countdown components.
8. Build reminder command, notifications, and scheduler.
9. Add tests for authentication, CRUD, dashboard, and reminders.
10. Prepare Railway deployment variables and scheduler worker.

---

## 7. Mandatory Language Policy

All documentation, comments, AI instructions, and repository guidance may be in English. However, every end-user-facing output must be Indonesian, including:

- Navigation labels
- Button labels
- Page titles
- Form labels
- Placeholders
- Validation errors
- Success and error alerts
- Empty states
- Email subject and body
- Notification text
- Dashboard stats labels
- Confirmation dialogs

Good examples:

```txt
Tambah Jadwal
Jadwal berhasil ditambahkan.
Belum ada jadwal untuk minggu ini.
Tenggat waktu tinggal 1 hari lagi.
```

Bad examples:

```txt
Add Schedule
Schedule created successfully.
No upcoming schedule.
```

---

## 8. Definition of Done

A feature is considered complete only when:

1. The code follows Laravel conventions.
2. The UI follows `DESIGN.md`.
3. The feature uses Indonesian user-facing copy.
4. Validation uses Form Request classes.
5. Authorization prevents cross-user data access.
6. Tests cover the happy path and key failure cases.
7. No raw SQL is used unless explicitly justified.
8. The feature works on mobile, tablet, and desktop.
9. The implementation does not break existing routes or views.
10. The final result is ready for Railway deployment.
