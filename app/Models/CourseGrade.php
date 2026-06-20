<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'semester',
        'mata_kuliah',
        'sks',
        'nilai',
    ];

    protected $casts = [
        'semester' => 'integer',
        'sks' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGradePointAttribute(): float
    {
        return match (strtoupper($this->nilai)) {
            'A' => 4.0,
            'AB' => 3.5,
            'B' => 3.0,
            'BC' => 2.5,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }
}
