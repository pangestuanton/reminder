# Database Design - Aviona Sync

## 1. Database Engine

Aviona Sync uses PostgreSQL through Supabase.

Use Laravel migrations for all schema changes. Do not manually create production tables outside migrations.

---

## 2. Main Tables

1. `users`
2. `jadwal_kegiatans`
3. `reminder_logs`

---

## 3. Table: `users`

Laravel default user table with standard authentication fields.

| Column | Type | Notes |
|---|---|---|
| `id` | big integer | Primary key |
| `name` | string | User full name |
| `email` | string | Unique |
| `email_verified_at` | timestamp nullable | Optional |
| `password` | string | Hashed password |
| `remember_token` | string nullable | Laravel remember token |
| `created_at` | timestamp | Auto-managed |
| `updated_at` | timestamp | Auto-managed |

Relationships:

```php
public function jadwalKegiatans(): HasMany
public function reminderLogs(): HasMany
```

---

## 4. Table: `jadwal_kegiatans`

Stores academic and organization schedules.

| Column | Type | Required | Notes |
|---|---|---:|---|
| `id` | big integer | Yes | Primary key |
| `user_id` | foreign id | Yes | References `users.id`, cascade on delete |
| `judul` | string | Yes | Schedule title |
| `kategori` | string | Yes | `kuliah`, `tugas`, `uts`, `uas`, `organisasi` |
| `waktu_pelaksanaan` | timestamp | Yes | Schedule date and time |
| `lokasi_atau_link` | string nullable | No | Physical location or online link |
| `deskripsi` | text nullable | No | Additional notes |
| `status` | string | Yes | `pending`, `selesai`, `dibatalkan` |
| `prioritas` | string | Yes | `rendah`, `sedang`, `tinggi` |
| `created_at` | timestamp | Yes | Auto-managed |
| `updated_at` | timestamp | Yes | Auto-managed |

Recommended indexes:

```txt
user_id
kategori
status
prioritas
waktu_pelaksanaan
(user_id, status, waktu_pelaksanaan)
(user_id, kategori, status)
```

Recommended Eloquent casts:

```php
protected $casts = [
    'waktu_pelaksanaan' => 'datetime',
];
```

Recommended fillable:

```php
protected $fillable = [
    'user_id',
    'judul',
    'kategori',
    'waktu_pelaksanaan',
    'lokasi_atau_link',
    'deskripsi',
    'status',
    'prioritas',
];
```

---

## 5. Table: `reminder_logs`

Stores reminder sending history and prevents duplicate H-3/H-1 notifications.

| Column | Type | Required | Notes |
|---|---|---:|---|
| `id` | big integer | Yes | Primary key |
| `user_id` | foreign id | Yes | References `users.id`, cascade on delete |
| `jadwal_kegiatan_id` | foreign id | Yes | References `jadwal_kegiatans.id`, cascade on delete |
| `reminder_type` | string | Yes | `h3` or `h1` |
| `channel` | string | Yes | Always `telegram` |
| `status` | string | Yes | `sent` or `failed` |
| `sent_at` | timestamp | No | When reminder was sent |
| `failed_at` | timestamp | No | When all delivery attempts failed |
| `created_at` | timestamp | Yes | Auto-managed |
| `updated_at` | timestamp | Yes | Auto-managed |

Important unique constraint:

```txt
unique(jadwal_kegiatan_id, reminder_type, channel)
```

This prevents duplicate reminders.

---

## 6. Enum Strategy

Laravel migrations may use strings instead of database enum types for easier future changes.

Allowed categories:

```txt
kuliah
tugas
uts
uas
organisasi
```

Allowed statuses:

```txt
pending
selesai
dibatalkan
```

Allowed priorities:

```txt
rendah
sedang
tinggi
```

Allowed reminder types:

```txt
h3
h1
```

---

## 7. Relationship Map

```txt
User
 ├── hasMany JadwalKegiatan
 └── hasMany ReminderLog

JadwalKegiatan
 ├── belongsTo User
 └── hasMany ReminderLog

ReminderLog
 ├── belongsTo User
 └── belongsTo JadwalKegiatan
```

---

## 8. Migration Sketch

### `jadwal_kegiatans`

```php
Schema::create('jadwal_kegiatans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('judul');
    $table->string('kategori');
    $table->dateTime('waktu_pelaksanaan');
    $table->string('lokasi_atau_link')->nullable();
    $table->text('deskripsi')->nullable();
    $table->string('status')->default('pending');
    $table->string('prioritas')->default('sedang');
    $table->timestamps();

    $table->index(['user_id', 'status', 'waktu_pelaksanaan']);
    $table->index(['user_id', 'kategori', 'status']);
});
```

### `reminder_logs`

```php
Schema::create('reminder_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('jadwal_kegiatan_id')->constrained('jadwal_kegiatans')->cascadeOnDelete();
    $table->string('reminder_type');
    $table->string('channel')->default('telegram');
    $table->timestamp('sent_at');
    $table->timestamps();

    $table->unique(['jadwal_kegiatan_id', 'reminder_type', 'channel'], 'unique_schedule_reminder_channel');
});
```

---

## 9. Query Scope Recommendations

For `JadwalKegiatan`:

```php
public function scopeOwnedBy($query, User $user)
public function scopePending($query)
public function scopeCompleted($query)
public function scopeUpcoming($query)
public function scopeOverdue($query)
public function scopeDueWithinDays($query, int $days)
public function scopeCategory($query, ?string $category)
public function scopeStatus($query, ?string $status)
public function scopePriority($query, ?string $priority)
public function scopeSearch($query, ?string $keyword)
```

---

## 10. Data Integrity Rules

1. `user_id` must always be taken from authenticated user context.
2. Deleted users should delete their schedules and reminder logs.
3. Deleted schedules should delete their reminder logs.
4. Duplicate reminder logs must be prevented by database constraint.
5. Past schedules with `pending` status should be shown as overdue in UI.
6. Completed and cancelled schedules should not receive reminders.
