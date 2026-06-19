# Product Requirements Document - Aviona Sync

## 1. Product Identity

**Project Name:** Aviona Sync  
**Product Type:** Academic schedule, deadline, and reminder management system  
**Platform:** Web application  
**Primary Stack:** Laravel 11, PHP 8.2+, Blade, Tailwind CSS, Alpine.js, PostgreSQL  
**Deployment Target:** Railway for Laravel, Supabase for PostgreSQL  
**Documentation Language:** English  
**Application Output Language:** Indonesian

---

## 2. Product Vision

Aviona Sync is a simple, clean, and reliable academic planning application that helps students manage lectures, assignments, exams, organization activities, and deadlines in one centralized dashboard.

The app must reduce forgotten deadlines by combining structured schedule management, countdown visibility, urgent status indicators, and automatic H-3 and H-1 reminders.

---

## 3. Problem Statement

Students often manage academic schedules across scattered sources such as chat groups, notes, calendars, screenshots, and informal reminders. This creates repeated problems:

1. Deadlines are forgotten because they are not centralized.
2. Important exam or assignment dates are mixed with ordinary chat messages.
3. Students do not always know which task is urgent.
4. Manual reminders are inconsistent.
5. Existing productivity apps often feel too complex for daily academic use.

Aviona Sync solves this by providing a focused academic deadline system with clean UI, quick CRUD actions, and reminder automation.

---

## 4. Goals

1. Provide a centralized place to manage academic schedules and deadlines.
2. Display urgent activities clearly using countdown and status indicators.
3. Send automatic reminders before important schedules.
4. Keep the interface clean, modern, mobile-friendly, and easy to understand.
5. Use Laravel best practices so the project is maintainable and production-ready.
6. Ensure all user-facing output is written in natural Indonesian.

---

## 5. Non-Goals

1. The MVP does not need a complex team collaboration system.
2. The MVP does not need a public API for third-party apps.
3. The MVP does not need real-time chat.
4. The MVP does not need payment features.
5. The MVP does not need AI schedule extraction from screenshots.
6. The MVP does not need native mobile apps.

---

## 6. Target Users

### 6.1 Primary User: Student

Needs:

- Add assignment deadlines quickly.
- See upcoming schedules without confusion.
- Receive reminder notifications.
- Mark tasks as completed.

### 6.2 Secondary User: Class Coordinator

Needs:

- Track important class schedules.
- Manage academic events from multiple categories.
- Keep data organized and easy to share.

### 6.3 Secondary User: Organization Member

Needs:

- Add organization meetings or deadlines.
- Separate organization agenda from lecture agenda.
- Avoid missing activity deadlines.

---

## 7. Core Features

### 7.1 Authentication

Users must be able to:

1. Register using name, email, and password.
2. Login using email and password.
3. Logout securely.
4. Access protected pages only after login.
5. See validation messages in Indonesian.

Acceptance criteria:

- Passwords are hashed.
- Session-based authentication is used.
- Guest users cannot access dashboard or schedule pages.
- Auth forms use the design system from `DESIGN.md`.

---

### 7.2 Dashboard

The dashboard must show:

1. Greeting text in Indonesian.
2. Total pending schedules.
3. Total completed schedules.
4. Total schedules due within 7 days.
5. Nearest schedule card with countdown.
6. Urgent schedule list.
7. Empty state when no schedules exist.
8. Quick action button to add a schedule.

Acceptance criteria:

- Upcoming schedules are sorted by nearest date.
- Past pending schedules are visually marked as overdue.
- Urgent schedules use yellow accent styling.
- Dashboard is responsive.

---

### 7.3 Schedule Management CRUD

Users must be able to create, read, update, delete, filter, and complete schedules.

Required fields:

| Field | Required | Notes |
|---|---:|---|
| `judul` | Yes | Schedule title |
| `kategori` | Yes | `kuliah`, `tugas`, `uts`, `uas`, `organisasi` |
| `waktu_pelaksanaan` | Yes | Date and time |
| `lokasi_atau_link` | No | Room, address, or online link |
| `deskripsi` | No | Additional notes |
| `status` | Yes | `pending`, `selesai`, `dibatalkan` |
| `prioritas` | Yes | `rendah`, `sedang`, `tinggi` |

Acceptance criteria:

- Only the owner can access and modify their schedules.
- Create and update actions use Form Request validation.
- Delete uses confirmation modal or confirmation page.
- Success and error messages are in Indonesian.
- The list page supports search, category filter, status filter, and date sorting.

---

### 7.4 Reminder Engine

The app must send reminders for schedules:

1. H-3 reminder: three days before `waktu_pelaksanaan`.
2. H-1 reminder: one day before `waktu_pelaksanaan`.
3. No duplicate reminder for the same schedule and reminder type.
4. Reminders are logged in `reminder_logs`.
5. Reminder command is scheduled using Laravel Scheduler.

Recommended implementation:

- Artisan command: `php artisan aviona:send-schedule-reminders`
- Service: `ReminderService`
- Notifications:
  - `ScheduleH3Reminder`
  - `ScheduleH1Reminder`
- Tracking table: `reminder_logs`

Acceptance criteria:

- Running the command twice must not send duplicate reminders.
- Completed and cancelled schedules must not receive reminders.
- Reminder copy must be Indonesian.
- Reminder logic must be covered by tests.

---

### 7.5 Search and Filters

The schedule index must support:

1. Keyword search by title, description, location, or link.
2. Category filter.
3. Status filter.
4. Priority filter.
5. Date range filter.
6. Sort by nearest date, newest, oldest, and priority.

Acceptance criteria:

- Filters can be combined.
- Empty results show a friendly Indonesian empty state.
- Query logic should use Eloquent query builder, not raw SQL.

---

### 7.6 Indonesian Localization

All end-user-facing text must be Indonesian.

Examples:

```txt
Dashboard
Tambah Jadwal
Jadwal Kuliah
Tugas Mendekati Tenggat
Belum ada jadwal yang ditambahkan.
Jadwal berhasil diperbarui.
```

Acceptance criteria:

- No English UI copy appears in pages, notifications, or validation messages.
- Use natural student-friendly Indonesian, not stiff machine translation.
- Documentation can remain English.

---

## 8. User Flows

### 8.1 Add Schedule Flow

1. User opens dashboard.
2. User clicks `Tambah Jadwal`.
3. User fills title, category, date/time, optional location/link, description, and priority.
4. User submits form.
5. System validates data.
6. System saves schedule.
7. User is redirected to schedule detail or index page.
8. System shows `Jadwal berhasil ditambahkan.`

---

### 8.2 Complete Schedule Flow

1. User opens schedule list.
2. User clicks `Tandai Selesai`.
3. System updates status to `selesai`.
4. System shows success alert.
5. Dashboard stats update automatically.

---

### 8.3 Reminder Flow

1. Laravel Scheduler triggers reminder command.
2. Command finds pending schedules due in 3 days and 1 day.
3. System checks `reminder_logs` to prevent duplicates.
4. System sends notification.
5. System stores log with schedule ID, user ID, reminder type, and sent timestamp.

---

## 9. Data Model Overview

Main tables:

1. `users`
2. `jadwal_kegiatans`
3. `reminder_logs`

Relationships:

- One user has many `jadwal_kegiatans`.
- One `jadwal_kegiatan` has many `reminder_logs`.
- One user has many `reminder_logs`.

Detailed schema is defined in `DATABASE.md`.

---

## 10. Route Overview

Main route groups:

1. Guest routes: login and registration.
2. Authenticated routes: dashboard, schedules, profile, logout.
3. Resource routes for `jadwal-kegiatan`.
4. Console route for scheduler.

Detailed route plan is defined in `ROUTES.md`.

---

## 11. UI/UX Requirements

The interface must follow `DESIGN.md` strictly:

1. iOS-inspired clean layout.
2. Soft cards.
3. Large border radius.
4. Minimal borders.
5. Abundant whitespace.
6. Blue primary actions.
7. Yellow urgent indicators.
8. Mobile-first responsive layout.
9. Clear hierarchy and readable spacing.

---

## 12. Technical Requirements

1. Use Laravel 11 conventions.
2. Use PHP 8.2+.
3. Use PostgreSQL.
4. Use Eloquent ORM for database operations.
5. Use Form Requests for validation.
6. Use Policies for authorization.
7. Use Services for dashboard stats and reminder logic.
8. Use Notifications for reminder delivery.
9. Use Blade components for reusable UI.
10. Use Tailwind CSS for styling.
11. Use Alpine.js only for lightweight interactions.
12. Avoid raw SQL unless impossible and clearly justified.
13. Do not expose secret keys in code.

---

## 13. Project Structure Requirement

The implementation must follow this structure:

```txt
aviona-sync/
├── app/
│   ├── Console/Commands/SendScheduleReminders.php
│   ├── Http/Controllers/Auth/
│   ├── Http/Controllers/DashboardController.php
│   ├── Http/Controllers/JadwalKegiatanController.php
│   ├── Http/Requests/StoreJadwalKegiatanRequest.php
│   ├── Http/Requests/UpdateJadwalKegiatanRequest.php
│   ├── Models/JadwalKegiatan.php
│   ├── Models/ReminderLog.php
│   ├── Notifications/ScheduleH1Reminder.php
│   ├── Notifications/ScheduleH3Reminder.php
│   ├── Policies/JadwalKegiatanPolicy.php
│   └── Services/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
├── routes/
│   ├── web.php
│   └── console.php
└── tests/
```

---

## 14. Success Metrics

1. User can add a schedule in less than one minute.
2. Dashboard clearly shows the nearest deadline.
3. H-3 and H-1 reminders are sent without duplication.
4. All pages work properly on mobile.
5. CRUD operations are covered by feature tests.
6. The project can be deployed to Railway using Supabase PostgreSQL.

---

## 15. MVP Scope

MVP must include:

1. Authentication.
2. Dashboard.
3. Schedule CRUD.
4. Search and filters.
5. Reminder command.
6. Notification logging.
7. Indonesian UI copy.
8. Responsive Tailwind UI.
9. Basic tests.
10. Railway deployment readiness.

---

## 16. Future Enhancements

1. Calendar view.
2. Export to Google Calendar.
3. WhatsApp or Telegram reminders.
4. AI schedule extraction from screenshots or chat messages.
5. Multi-class shared schedule boards.
6. Role-based admin panel.
7. Progressive Web App support.
8. Dark mode.
