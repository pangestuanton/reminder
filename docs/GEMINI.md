# GEMINI.md - Gemini CLI Instructions for Aviona Sync

You are working inside the Aviona Sync Laravel project.

Follow these rules strictly:

1. Read `AGENTS.md`, `PRD.md`, and `DESIGN.md` before making changes.
2. Build with Laravel 11, Blade, Tailwind CSS, Alpine.js, and PostgreSQL.
3. Keep documentation and code comments in English, but make all visible application output Indonesian.
4. Follow the iOS-inspired design system: `bg-slate-50`, `bg-white`, `rounded-3xl`, soft shadows, blue primary actions, yellow urgent badges.
5. Use Eloquent ORM, Form Requests, Policies, Services, Notifications, and tests.
6. Do not use raw SQL unless clearly justified.
7. Do not hardcode secrets.
8. Do not replace Blade with React or Vue unless explicitly requested.
9. Keep controllers thin and move business logic into services.
10. Use `reminder_logs` for H-3 and H-1 reminder idempotency.

Primary task style:

```txt
Implement the requested feature in Aviona Sync using Laravel best practices. Follow AGENTS.md, PRD.md, DESIGN.md, DATABASE.md, ROUTES.md, and ARCHITECTURE.md. All end-user-facing UI copy must be Indonesian. Keep the code clean, secure, tested, and production-ready for Railway + Supabase.
```

Before finishing any task, run or recommend:

```bash
php artisan test
npm run build
```

If tests cannot be run, state that honestly.
