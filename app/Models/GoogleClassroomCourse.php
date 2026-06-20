<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleClassroomCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_account_id',
        'external_id',
        'name',
        'section',
        'description',
        'room',
        'alternate_link',
        'course_state',
        'synced_at',
    ];

    protected $casts = [
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

    public function courseWorks(): HasMany
    {
        return $this->hasMany(GoogleClassroomCourseWork::class);
    }

    public function scopeActive($query)
    {
        return $query->where('course_state', 'ACTIVE');
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
