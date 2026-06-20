<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Aviona Sync

Aviona Sync adalah aplikasi manajemen jadwal dan tugas akademik dengan integrasi Google Classroom, Google Calendar, dan pengingat otomatis via Telegram Bot.

## Fitur Utama

- **Tugas & Deadline**: Kelola tugas dari lokal, Google Classroom, dan Google Calendar
- **Jadwal Kuliah**: Jadwal kuliah mingguan berulang yang otomatis
- **Google Classroom**: Sinkronisasi kursus, coursework, dan status pengumpulan
- **Google Calendar**: Impor acara, ekspor tugas & jadwal kuliah
- **Pengingat Telegram**: Pesan kontekstual sesuai kategori kegiatan
- **Agenda Harian**: Ringkasan aktivitas dikirim setiap jam 05:00
- **Analitik Progres**: Statistik penyelesaian tugas dan tren mingguan
- **Pengaturan**: Kustomisasi notifikasi, jam senyap, format pesan

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

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-app-name.up.railway.app/auth/google/callback

TELEGRAM_BOT_TOKEN=your-botfather-token
TELEGRAM_BOT_USERNAME=your_bot_username
TELEGRAM_WEBHOOK_SECRET=your-random-webhook-secret
```

### Google API Setup

1. Buka [Google Cloud Console](https://console.cloud.google.com)
2. Buat project baru atau pilih project yang sudah ada
3. Aktifkan **Google Classroom API** dan **Google Calendar API**
4. Buat OAuth 2.0 credentials
5. Set authorized redirect URI: `https://your-app-url/auth/google/callback`
6. Tambahkan scopes:
   - `https://www.googleapis.com/auth/classroom.courses.readonly`
   - `https://www.googleapis.com/auth/classroom.coursework.me.readonly`
   - `https://www.googleapis.com/auth/classroom.student-submissions.me.readonly`
   - `https://www.googleapis.com/auth/calendar`
7. Publish app ke production agar tidak perlu approval manual

### Build & Start Command

Railway otomatis menggunakan Nixpacks untuk mendeteksi PHP + Node.js.

### Worker Service

Tambahkan service kedua di Railway dengan command:

```
php artisan schedule:work
```

### Queue Worker

Tambahkan service ketiga untuk queue worker:

```
php artisan queue:work --queue=telegram,default --tries=3 --timeout=30
```

### Scheduler

Scheduler mengantrekan:
- Pengingat H-3, H-1, dan hitung mundur 3 jam (setiap menit)
- Agenda harian jam 05:00 WIB (sekali sehari)
- Sinkronisasi Google Classroom & Calendar (setiap 15 menit)

### Webhook Telegram

Setelah deployment pertama, daftarkan webhook Telegram:

```
php artisan telegram:set-webhook
```

## Setup Lokal

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

## Testing

```bash
php artisan test
npm run build
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
