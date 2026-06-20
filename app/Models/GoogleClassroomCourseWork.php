<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoogleClassroomCourseWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_classroom_course_id',
        'external_id',
        'title',
        'description',
        'due_date',
        'due_time_only',
        'max_points',
        'work_type',
        'status',
        'alternate_link',
        'materials',
        'synced_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'materials' => 'array',
        'synced_at' => 'datetime',
        'max_points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(GoogleClassroomCourse::class, 'google_classroom_course_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(GoogleClassroomSubmission::class, 'google_classroom_course_work_id');
    }

    public function userSubmission(): HasOne
    {
        return $this->hasOne(GoogleClassroomSubmission::class, 'google_classroom_course_work_id')
            ->where('user_id', $this->user_id);
    }

    public function jadwalKegiatan(): HasOne
    {
        return $this->hasOne(JadwalKegiatan::class)
            ->where('source', 'classroom');
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function isSubmitted(): bool
    {
        $submission = $this->userSubmission;

        return $submission && in_array($submission->state, ['TURNED_IN', 'RETURNED'], true);
    }

    public function scopeNotSubmitted($query)
    {
        return $query->whereDoesntHave('submissions', function ($q) {
            $q->whereIn('state', ['TURNED_IN', 'RETURNED']);
        });
    }
}
