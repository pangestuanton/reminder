# Testing Strategy - Aviona Sync

## 1. Testing Goals

Testing must prove that the application is safe, reliable, and resistant to common bugs.

The most important areas are:

1. Authentication.
2. Schedule CRUD.
3. Schedule ownership protection.
4. Dashboard statistics.
5. Reminder idempotency.
6. Indonesian validation and feedback behavior.

---

## 2. Recommended Test Types

| Test Type | Purpose |
|---|---|
| Feature tests | Validate user flows through HTTP requests |
| Unit tests | Validate services and isolated logic |
| Policy tests | Validate data ownership rules |
| Command tests | Validate scheduled reminder behavior |

---

## 3. Feature Test Checklist

### Authentication

- User can register.
- User can login.
- User can logout.
- Guest cannot access dashboard.
- Invalid login shows validation error.

### Schedule CRUD

- Authenticated user can create schedule.
- Authenticated user can view own schedule.
- Authenticated user can update own schedule.
- Authenticated user can delete own schedule.
- Authenticated user can mark schedule as completed.
- Guest cannot access schedule pages.

### Ownership

- User cannot view another user's schedule.
- User cannot update another user's schedule.
- User cannot delete another user's schedule.
- User cannot complete another user's schedule.

### Filters

- User can search schedules by keyword.
- User can filter by category.
- User can filter by status.
- User can filter by priority.
- User can sort by nearest date.

---

## 4. Unit Test Checklist

### DashboardStatsService

- Counts pending schedules correctly.
- Counts completed schedules correctly.
- Finds nearest schedule correctly.
- Finds urgent schedules due within 7 days.
- Ignores other users' schedules.

### ReminderService

- Finds H-3 schedules correctly.
- Finds H-1 schedules correctly.
- Ignores completed schedules.
- Ignores cancelled schedules.
- Does not send duplicate reminders.
- Creates reminder log after sending.

---

## 5. Command Test Checklist

Command:

```bash
php artisan aviona:send-schedule-reminders
```

Test cases:

1. Sends H-3 reminder for pending schedule due in three days.
2. Sends H-1 reminder for pending schedule due tomorrow.
3. Does not send duplicate H-3 reminders.
4. Does not send duplicate H-1 reminders.
5. Does not send reminders for completed schedule.
6. Does not send reminders for cancelled schedule.

---

## 6. Example Test Names

```php
public function test_guest_cannot_access_dashboard(): void
public function test_authenticated_user_can_create_schedule(): void
public function test_user_cannot_update_another_users_schedule(): void
public function test_dashboard_shows_nearest_schedule(): void
public function test_h3_reminder_is_sent_once(): void
public function test_h1_reminder_is_not_sent_twice(): void
```

---

## 7. Manual QA Checklist

Before deployment, manually verify:

- Register page works.
- Login page works.
- Logout works.
- Dashboard cards look clean on mobile.
- Add schedule form is easy to use.
- Edit schedule works.
- Delete confirmation works.
- Empty state appears when no schedule exists.
- Filters work.
- Reminder command runs without error.
- All visible text is Indonesian.

---

## 8. Commands

Run tests:

```bash
php artisan test
```

Run a specific test file:

```bash
php artisan test tests/Feature/JadwalKegiatanCrudTest.php
```

Build assets:

```bash
npm run build
```

Run reminder command:

```bash
php artisan aviona:send-schedule-reminders
```

---

## 9. Testing Definition of Done

A feature is not complete until:

- Critical tests are added or updated.
- Existing tests still pass.
- Manual UI check is done.
- Indonesian UI copy is verified.
- Authorization behavior is verified.
