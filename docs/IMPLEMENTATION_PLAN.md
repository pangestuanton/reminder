# Implementation Plan - Aviona Sync

## 1. Development Strategy

Build the application in stable phases. Each phase should produce a working state before moving to the next phase.

Do not build everything randomly. Follow the order below.

---

## Phase 1 - Project Setup

Goal: Prepare the Laravel foundation.

Checklist:

- Create Laravel project.
- Configure `.env` and `.env.example`.
- Configure PostgreSQL connection.
- Install frontend dependencies.
- Configure Tailwind CSS.
- Configure Alpine.js.
- Create base layouts.
- Create reusable Blade components.

Done when:

- App runs locally.
- Tailwind styles compile.
- A test page displays the design system correctly.

---

## Phase 2 - Authentication

Goal: Allow users to register, login, and logout.

Checklist:

- Create auth controllers.
- Create login page.
- Create register page.
- Add validation.
- Add Indonesian validation messages.
- Protect dashboard route.
- Add logout action.

Done when:

- User can register.
- User can login.
- User can logout.
- Guest cannot access dashboard.

---

## Phase 3 - Database and Models

Goal: Create schedule and reminder data structures.

Checklist:

- Create `jadwal_kegiatans` migration.
- Create `reminder_logs` migration.
- Create `JadwalKegiatan` model.
- Create `ReminderLog` model.
- Define relationships.
- Add casts and fillable fields.
- Add factories and seeders.

Done when:

- Migrations run successfully.
- Demo data can be seeded.
- Relationships work.

---

## Phase 4 - Schedule CRUD

Goal: Build schedule management.

Checklist:

- Create `JadwalKegiatanController`.
- Create Form Requests.
- Create policy.
- Create index page.
- Create create page.
- Create edit page.
- Create show page.
- Add delete action.
- Add complete action.
- Add Indonesian flash messages.

Done when:

- User can create, view, update, delete, and complete schedules.
- User cannot access another user's schedules.

---

## Phase 5 - Search and Filters

Goal: Make schedule list easy to explore.

Checklist:

- Add keyword search.
- Add category filter.
- Add status filter.
- Add priority filter.
- Add date sorting.
- Add empty state.
- Preserve filter values after search.

Done when:

- Filters can be combined.
- Empty results display friendly Indonesian copy.

---

## Phase 6 - Dashboard

Goal: Build the central overview page.

Checklist:

- Create `DashboardStatsService`.
- Show total schedules.
- Show pending schedules.
- Show completed schedules.
- Show urgent schedules.
- Show nearest schedule.
- Show countdown text.
- Add responsive card grid.

Done when:

- Dashboard clearly shows what is urgent and what is next.

---

## Phase 7 - Reminder Engine

Goal: Send automatic H-3 and H-1 reminders.

Checklist:

- Create `ReminderService`.
- Create `SendScheduleReminders` command.
- Create `ScheduleH3Reminder` notification.
- Create `ScheduleH1Reminder` notification.
- Use `reminder_logs` to prevent duplicates.
- Schedule command hourly.
- Add tests for duplicate prevention.

Done when:

- Command sends correct reminders.
- Duplicate reminders are prevented.
- Completed and cancelled schedules are ignored.

---

## Phase 8 - UI Polish

Goal: Make the app feel smooth and professional.

Checklist:

- Review every page against `DESIGN.md`.
- Ensure all UI copy is Indonesian.
- Improve empty states.
- Improve mobile layout.
- Add confirmation modals.
- Add consistent badges and buttons.
- Add alert component.

Done when:

- The app feels consistent, clean, and mobile-friendly.

---

## Phase 9 - Testing and Quality

Goal: Reduce bugs before deployment.

Checklist:

- Add auth tests.
- Add CRUD tests.
- Add policy tests.
- Add dashboard service tests.
- Add reminder service tests.
- Run `php artisan test`.
- Run `npm run build`.

Done when:

- All critical tests pass.
- Production assets build successfully.

---

## Phase 10 - Deployment

Goal: Deploy to Railway with Supabase PostgreSQL.

Checklist:

- Prepare Railway project.
- Add environment variables.
- Connect Supabase PostgreSQL.
- Run migrations.
- Configure scheduler worker or cron strategy.
- Build frontend assets.
- Verify production login and CRUD.

Done when:

- Production app is accessible.
- Database works.
- Scheduler runs.
- No debug mode in production.

---

## Final MVP Checklist

- Authentication works.
- Dashboard works.
- Schedule CRUD works.
- Search and filters work.
- H-3 and H-1 reminders work.
- UI is Indonesian.
- Design follows the iOS-inspired system.
- Tests cover critical flows.
- App is deployable to Railway.
