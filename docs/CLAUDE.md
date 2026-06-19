# CLAUDE.md - AI IDE Instructions for Aviona Sync

## Project Context

Aviona Sync is a Laravel 11 academic schedule and reminder management app. It uses Blade, Tailwind CSS, Alpine.js, PostgreSQL, Railway, and Supabase.

The documentation is English. The final application UI must be Indonesian.

---

## Required Behavior

When editing this project:

1. Follow `AGENTS.md` as the main authority.
2. Follow `PRD.md` for product scope.
3. Follow `DESIGN.md` for every UI decision.
4. Follow `DATABASE.md` for schema and relationships.
5. Follow `ROUTES.md` for routing conventions.
6. Follow `ARCHITECTURE.md` for layer responsibilities.

---

## Coding Standards

- Use Laravel conventions.
- Use Eloquent ORM.
- Use Form Requests for validation.
- Use Policies for ownership authorization.
- Use Services for business logic.
- Use Notifications for reminders.
- Use Blade components for reusable UI.
- Use Tailwind CSS for styling.
- Use Alpine.js only for lightweight interactions.
- Add or update tests for behavior changes.

---

## UI Rules

All UI copy must be Indonesian.

Use this visual direction:

- Clean iOS-inspired interface.
- `bg-slate-50` app background.
- `bg-white` cards.
- `rounded-3xl` cards.
- Soft shadows.
- Blue primary buttons.
- Yellow urgent accents.
- Mobile-first layouts.

---

## Reminder Rule

Do not use a single `is_reminded` boolean for both H-3 and H-1 reminder logic.

Use `reminder_logs` with unique tracking by:

```txt
jadwal_kegiatan_id + reminder_type + channel
```

This prevents duplicate reminders and supports both H-3 and H-1.

---

## Safety Rules

Never:

- Hardcode secrets.
- Commit `.env`.
- Use English UI copy.
- Allow users to access other users' schedules.
- Add unrelated features.
- Convert the app into a SPA without request.
- Claim tests passed when they were not run.

---

## Final Response Format After Changes

Summarize:

1. Files changed.
2. Main behavior implemented.
3. How to test.
4. Any limitation.
