<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleClassroomSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_classroom_course_work_id',
        'external_id',
        'state',
        'late',
        'draft_url',
        'alternate_link',
        'synced_at',
    ];

    protected $casts = [
        'late' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courseWork(): BelongsTo
    {
        return $this->belongsTo(GoogleClassroomCourseWork::class, 'google_classroom_course_work_id');
    }

    public function isTurnedIn(): bool
    {
        return $this->state === 'TURNED_IN';
    }

    public function isReturned(): bool
    {
        return $this->state === 'RETURNED';
    }
}
