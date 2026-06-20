# Security Guidelines - Aviona Sync

## 1. Security Goals

Aviona Sync stores user schedules and academic deadlines. The app must protect user accounts, prevent unauthorized access, and avoid exposing sensitive configuration.

---

## 2. Authentication Security

1. Use Laravel password hashing.
2. Use session-based authentication.
3. Protect authenticated routes with `auth` middleware.
4. Redirect guests to login.
5. Do not store plain text passwords.
6. Do not reveal whether an email exists during login beyond normal validation behavior.

---

## 3. Authorization Security

Every schedule belongs to a user. Users must never access schedules that belong to other users.

Required:

- `JadwalKegiatanPolicy`
- Ownership checks in controller actions
- Tests for cross-user access prevention

Never trust `user_id` from request input.

Correct pattern:

```php
$data = $request->validated();
$data['user_id'] = $request->user()->id;
```

Bad pattern:

```php
JadwalKegiatan::create($request->all());
```

---

## 4. Request Validation

Use Form Requests for create and update actions.

Validation must check:

- Required fields.
- Allowed categories.
- Allowed statuses.
- Allowed priorities.
- Valid date/time.
- Maximum string lengths.
- Nullable fields.

Validation messages must be Indonesian.

---

## 5. CSRF Protection

All forms must use CSRF protection:

```blade
@csrf
```

For update and delete methods:

```blade
@method('PUT')
@method('DELETE')
```

---

## 6. XSS Prevention

Use escaped Blade output by default:

```blade
{{ $jadwal->judul }}
```

Avoid raw output:

```blade
{!! $jadwal->deskripsi !!}
```

Only use raw output when content is sanitized and absolutely necessary.

---

## 7. Mass Assignment Protection

Models must define `$fillable` and avoid unsafe mass assignment.

Do not include fields that users should not control unless they are set server-side.

---

## 8. Environment Security

Never commit `.env`.

Use `.env.example` with placeholder values only.

Required production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.com
```

Secrets must be stored in Railway environment variables.

---

## 9. Database Security

1. Use migrations, not manual production edits.
2. Use parameterized Eloquent queries.
3. Avoid raw SQL.
4. Add indexes for performance.
5. Use cascading deletes intentionally.
6. Do not expose database credentials.

---

## 10. Reminder Security

1. Only send reminders for schedules owned by the target user.
2. Do not leak one user's schedule to another user.
3. Use `reminder_logs` to prevent duplicate reminders.
4. Do not mark reminders as sent before successful notification dispatch.

---

## 11. Deployment Security

Before production deployment:

- `APP_DEBUG=false`
- Strong `APP_KEY`
- HTTPS URL configured
- Database credentials in Railway variables
- Telegram bot token and webhook secret in Railway variables
- No test credentials in repository
- No stack traces exposed to users

---

## 12. Security Checklist

- Auth routes are protected properly.
- Schedule ownership policy exists.
- Form Requests validate input.
- Blade output is escaped.
- CSRF exists in all forms.
- `.env` is not committed.
- Production debug is disabled.
- No raw SQL is used casually.
- Reminder logs prevent duplicates.
- Cross-user access tests exist.
