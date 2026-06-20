<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_account_email',
        'access_token_encrypted',
        'refresh_token_encrypted',
        'token_expires_at',
        'scopes',
        'classroom_connected_at',
        'calendar_connected_at',
        'disconnected_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'classroom_connected_at' => 'datetime',
        'calendar_connected_at' => 'datetime',
        'disconnected_at' => 'datetime',
        'scopes' => 'array',
    ];

    protected $hidden = [
        'access_token_encrypted',
        'refresh_token_encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(GoogleClassroomCourse::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(GoogleCalendarEvent::class);
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at->isPast();
    }

    public function isClassroomConnected(): bool
    {
        return $this->classroom_connected_at !== null && $this->disconnected_at === null;
    }

    public function isCalendarConnected(): bool
    {
        return $this->calendar_connected_at !== null && $this->disconnected_at === null;
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes ?? [], true);
    }
}
