<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'waktu_pelaksanaan',
        'lokasi_atau_link',
        'deskripsi',
        'status',
        'prioritas',
        'source',
        'source_id',
        'synced_to_calendar',
        'completed_at',
        'deadline_at',
        'is_all_day',
        'calendar_event_id',
        'course_name',
        'reminder_h3',
        'reminder_h1',
        'reminder_3h',
        'is_informational',
    ];

    protected $casts = [
        'waktu_pelaksanaan' => 'datetime',
        'deadline_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_all_day' => 'boolean',
        'synced_to_calendar' => 'boolean',
        'reminder_h3' => 'boolean',
        'reminder_h1' => 'boolean',
        'reminder_3h' => 'boolean',
        'is_informational' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function courseWork(): HasOne
    {
        return $this->hasOne(GoogleClassroomCourseWork::class, 'external_id', 'source_id');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->pending()->where('waktu_pelaksanaan', '>=', now());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->pending()->where('waktu_pelaksanaan', '<', now());
    }

    public function scopeDueWithinDays(Builder $query, int $days): Builder
    {
        return $query->pending()
            ->where('waktu_pelaksanaan', '>=', now())
            ->where('waktu_pelaksanaan', '<=', now()->addDays($days));
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('kategori', $category) : $query;
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopePriority(Builder $query, ?string $priority): Builder
    {
        return $priority ? $query->where('prioritas', $priority) : $query;
    }

    public function scopeSource(Builder $query, ?string $source): Builder
    {
        return $source ? $query->where('source', $source) : $query;
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
                ->orWhere('deskripsi', 'like', "%{$keyword}%")
                ->orWhere('lokasi_atau_link', 'like', "%{$keyword}%")
                ->orWhere('course_name', 'like', "%{$keyword}%");
        });
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->waktu_pelaksanaan->isPast();
    }

    public function daysUntilDue(): int
    {
        return (int) now()->diffInDays($this->waktu_pelaksanaan, absolute: false);
    }

    public function getEffectiveDeadline()
    {
        return $this->deadline_at ?? $this->waktu_pelaksanaan;
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'classroom' => 'Google Classroom',
            'calendar' => 'Google Calendar',
            default => 'Lokal',
        };
    }

    public function isRelevantForAnalytics(): bool
    {
        return ! $this->is_informational;
    }
}
