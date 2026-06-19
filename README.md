<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Aviona Sync

Aviona Sync adalah aplikasi manajemen jadwal kegiatan dengan integrasi Google Login, pengingat via email dan WhatsApp (Fonnte).

## Deployment ke Railway

### Persiapan

1. Buat akun di [Railway](https://railway.app)
2. Buat project baru dan connect ke GitHub repo ini
3. Tambahkan service **PostgreSQL** di Railway dashboard

### Environment Variables

Set variabel berikut di Railway dashboard (Settings > Variables):

```
APP_NAME="Aviona Sync"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=pgsql
DB_HOST=your-railway-db-host
DB_PORT=5432
DB_DATABASE=your-railway-db-name
DB_USERNAME=your-railway-db-username
DB_PASSWORD=your-railway-db-password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-app-name.up.railway.app/auth/google/callback

FONNTE_ENABLED=true
FONNTE_TOKEN=your-fonnte-token
FONNTE_BASE_URL=https://api.fonnte.com
FONNTE_TEST_TARGET=your-whatsapp-number
```

> **Tip:** Di Railway, variabel `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` otomatis tersedia jika kamu pakai PostgreSQL plugin Railway. Gunakan `${{PostgreSQL.DATABASE_URL}}` atau set manual.

### Build & Start Command

Railway otomatis menggunakan Nixpacks untuk mendeteksi PHP + Node.js. Build process:

1. `composer install --no-dev --optimize-autoloader`
2. `npm install && npm run build`
3. Start: `php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public`

### Worker Service (Scheduler)

Tambahkan service kedua di Railway dengan command:

```
php artisan schedule:work
```

Ini menjalankan scheduler untuk mengirim pengingat H-3 dan H-1 via email + WhatsApp.

### Setup Lokal

```bash
git clone https://github.com/pangestuanton/reminder.git
cd reminder
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
