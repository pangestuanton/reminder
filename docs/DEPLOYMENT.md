# Deployment Guide - Aviona Sync

## 1. Deployment Target

Aviona Sync is designed for:

- Laravel application hosting on Railway.
- PostgreSQL database on Supabase.
- Vite asset build during deployment.
- Laravel Scheduler through Railway worker or cron strategy.

---

## 2. Required Environment Variables

Add these variables in Railway:

```env
APP_NAME="Aviona Sync"
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_KEY
APP_DEBUG=false
APP_URL=https://your-railway-domain.up.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=your-supabase-host
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@aviona-sync.app
MAIL_FROM_NAME="Aviona Sync"
```

Do not commit real values.

---

## 3. Local Production Build Check

Before deployment:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan test
```

---

## 4. Railway Build Commands

Recommended build command:

```bash
composer install --no-dev --optimize-autoloader && npm install && npm run build
```

Recommended start command:

```bash
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

For production-grade serving, configure the platform according to the chosen Railway Laravel template.

---

## 5. Database Migration

Run migrations on deployment:

```bash
php artisan migrate --force
```

Optional seed for demo only:

```bash
php artisan db:seed --force
```

Do not run demo seeders in real production unless intended.

---

## 6. Scheduler Strategy

The reminder command must run regularly:

```bash
php artisan aviona:send-schedule-reminders
```

Recommended scheduler entry:

```php
Schedule::command('aviona:send-schedule-reminders')->hourly();
```

Deployment options:

1. Railway cron job that runs the command hourly.
2. Railway worker process running Laravel scheduler.
3. External cron service calling a protected command runner, if configured safely.

---

## 7. Queue Strategy

For MVP, `database` queue is acceptable.

If using queued notifications:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Production should include a queue worker if notifications are queued.

---

## 8. Supabase Notes

Use the PostgreSQL connection string from Supabase project settings.

Make sure:

- SSL requirements are handled if needed.
- Database password is stored only in Railway variables.
- Migrations run successfully.
- Timezone behavior is tested for schedule reminders.

---

## 9. Production Checklist

- `APP_DEBUG=false`
- `APP_KEY` generated
- `APP_URL` correct
- Supabase connection works
- Migrations run
- Assets build
- Login works
- CRUD works
- Reminder command works
- Scheduler configured
- Mail configuration works
- No secrets committed
- Indonesian UI copy verified

---

## 10. Rollback Plan

If deployment fails:

1. Check Railway logs.
2. Check database credentials.
3. Check failed migration.
4. Roll back recent migration if needed:

```bash
php artisan migrate:rollback --force
```

5. Redeploy previous working commit.
