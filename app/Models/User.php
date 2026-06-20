<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'theme_preference',
        'daily_agenda_enabled',
        'daily_agenda_time',
        'daily_agenda_include_overdue',
        'daily_agenda_format',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'telegram_linked_at' => 'datetime',
            'daily_agenda_enabled' => 'boolean',
            'daily_agenda_include_overdue' => 'boolean',
        ];
    }

    public function routeNotificationForTelegram(): ?string
    {
        return $this->telegram_chat_id;
    }

    public function jadwalKegiatans(): HasMany
    {
        return $this->hasMany(JadwalKegiatan::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function googleAccount(): HasOne
    {
        return $this->hasOne(GoogleAccount::class);
    }

    public function collegeSchedules(): HasMany
    {
        return $this->hasMany(CollegeSchedule::class);
    }

    public function googleClassroomCourses(): HasMany
    {
        return $this->hasMany(GoogleClassroomCourse::class);
    }

    public function googleCalendarEvents(): HasMany
    {
        return $this->hasMany(GoogleCalendarEvent::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function courseGrades(): HasMany
    {
        return $this->hasMany(CourseGrade::class);
    }

    public function hasGoogleAccount(): bool
    {
        return $this->googleAccount !== null;
    }

    public function hasClassroomAccess(): bool
    {
        return $this->googleAccount && $this->googleAccount->isClassroomConnected();
    }

    public function hasCalendarAccess(): bool
    {
        return $this->googleAccount && $this->googleAccount->isCalendarConnected();
    }
}
