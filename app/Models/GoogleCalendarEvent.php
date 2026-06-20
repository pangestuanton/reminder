<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_account_id',
        'external_id',
        'calendar_id',
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
        'is_all_day',
        'start_date',
        'end_date',
        'recurring_event_id',
        'html_link',
        'source_label',
        'synced_at',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_all_day' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function googleAccount(): BelongsTo
    {
        return $this->belongsTo(GoogleAccount::class);
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->where('start_datetime', '>=', $date->copy()->startOfDay())
                ->where('start_datetime', '<=', $date->copy()->endOfDay());
        })->orWhere(function ($q) use ($date) {
            $q->where('is_all_day', true)
                ->where('start_date', '<=', $date->toDateString())
                ->where('end_date', '>=', $date->toDateString());
        });
    }
}
