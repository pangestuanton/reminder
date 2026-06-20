<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollegeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mata_kuliah',
        'dosen',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'catatan',
        'warna',
        'reminder_minutes',
        'semester_mulai',
        'semester_akhir',
        'is_active',
        'synced_to_calendar',
        'calendar_event_id',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'semester_mulai' => 'date',
        'semester_akhir' => 'date',
        'is_active' => 'boolean',
        'synced_to_calendar' => 'boolean',
        'reminder_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, string $day)
    {
        return $query->where('hari', $day);
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeCurrentSemester($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('semester_mulai')
                ->orWhere('semester_mulai', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('semester_akhir')
                ->orWhere('semester_akhir', '>=', $today);
        });
    }

    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->semester_mulai && $this->semester_mulai->gt($today)) {
            return false;
        }

        if ($this->semester_akhir && $this->semester_akhir->lt($today)) {
            return false;
        }

        return true;
    }

    public function isOverdue(): bool
    {
        return false;
    }
}
