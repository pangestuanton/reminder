# Architecture Guide - Aviona Sync

## 1. Architecture Style

Aviona Sync uses a Laravel full-stack monolith architecture. The backend, frontend rendering, authentication, business logic, and scheduled commands live in one Laravel application.

This architecture is selected because the MVP prioritizes speed, simplicity, maintainability, and deployment ease.

---

## 2. Main Layers

```txt
Browser
  ↓
Routes
  ↓
Controllers
  ↓
Form Requests / Policies
  ↓
Services
  ↓
Models / Eloquent ORM
  ↓
PostgreSQL
```

---

## 3. Layer Responsibilities

### 3.1 Routes

Routes define URL entry points and connect requests to controllers.

Routes must be grouped by middleware:

- Guest routes for login and registration.
- Authenticated routes for dashboard, schedules, and profile.

---

### 3.2 Controllers

Controllers coordinate request handling.

Controllers should:

- Receive validated requests.
- Call services or models.
- Return views.
- Redirect with Indonesian flash messages.

Controllers should not contain complex reminder calculations or dashboard aggregation logic.

---

### 3.3 Form Requests

Form Requests handle validation and validation messages.

Required Form Requests:

- `StoreJadwalKegiatanRequest`
- `UpdateJadwalKegiatanRequest`

Validation messages must be Indonesian.

---

### 3.4 Policies

Policies enforce ownership.

`JadwalKegiatanPolicy` must prevent users from viewing, editing, completing, or deleting schedules that do not belong to them.

---

### 3.5 Services

Services contain reusable business logic.

Recommended services:

| Service | Responsibility |
|---|---|
| `DashboardStatsService` | Dashboard counters, nearest schedule, urgent schedule list |
| `ReminderService` | H-3/H-1 reminder selection, sending, and logging |

---

### 3.6 Models

Models represent database entities and relationships.

Required models:

- `User`
- `JadwalKegiatan`
- `ReminderLog`

Models must define relationships, casts, fillable fields, and useful query scopes.

---

### 3.7 Notifications

Notifications send reminder content.

Required notifications:

- `ScheduleTelegramReminder`

Every notification message must be Indonesian.

---

### 3.8 Views

Views are Blade templates styled with Tailwind CSS.

Views must:

- Use reusable Blade components.
- Follow `DESIGN.md`.
- Use Indonesian UI text.
- Be mobile-first.

---

## 4. Request Lifecycle Example: Create Schedule

```txt
POST /jadwal-kegiatan
  ↓
JadwalKegiatanController@store
  ↓
StoreJadwalKegiatanRequest validates input
  ↓
Controller sets user_id from authenticated user
  ↓
JadwalKegiatan::create(...)
  ↓
Redirect to schedule index with Indonesian success message
```

---

## 5. Reminder Lifecycle

```txt
Laravel Scheduler
  ↓
SendScheduleReminders command
  ↓
ReminderService
  ↓
Find pending schedules due in H-3 or H-1 window
  ↓
Check reminder_logs for duplicates
  ↓
Send notification
  ↓
Create reminder_logs record
```

---

## 6. Dashboard Data Flow

```txt
DashboardController@index
  ↓
DashboardStatsService
  ↓
Eloquent queries scoped to authenticated user
  ↓
Dashboard view receives stats and schedule lists
```

---

## 7. Important Architectural Decisions

### 7.1 Why Monolith?

The product does not need a separated API and frontend for MVP. A Laravel monolith is faster to develop, easier to deploy, and easier to maintain for a student-focused academic scheduling app.

### 7.2 Why Blade?

Blade is enough for the required pages and keeps complexity low. Alpine.js can handle small interactions without turning the app into a SPA.

### 7.3 Why Reminder Logs?

H-3 and H-1 reminders are separate events. A single `is_reminded` boolean cannot reliably track both. A `reminder_logs` table prevents duplicate reminders and supports audit history.

---

## 8. Project Boundaries

Do not add these unless requested:

- React/Vue SPA
- Public REST API
- Real-time chat
- Payment system
- Complex role management
- AI extraction pipeline
- Multi-tenant organization system

---

## 9. Architecture Quality Checklist

Before implementation is considered complete:

- Controllers are not bloated.
- Services contain business logic.
- Models contain relationships and scopes.
- Policies protect ownership.
- Form Requests handle validation.
- Notifications are idempotent through logs.
- Blade views are component-based.
- UI text is Indonesian.
