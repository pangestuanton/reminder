<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'telegram_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'tone',
        'detail_level',
        'category_preferences',
        'reminder_h3_enabled',
        'reminder_h1_enabled',
        'reminder_3h_enabled',
        'reminder_overdue_enabled',
        'reminder_max_per_day',
    ];

    protected $casts = [
        'telegram_enabled' => 'boolean',
        'reminder_h3_enabled' => 'boolean',
        'reminder_h1_enabled' => 'boolean',
        'reminder_3h_enabled' => 'boolean',
        'reminder_overdue_enabled' => 'boolean',
        'category_preferences' => 'array',
        'reminder_max_per_day' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCategoryEnabled(string $category): bool
    {
        $prefs = $this->category_preferences ?? [];

        return ! isset($prefs[$category]) || $prefs[$category] === true;
    }

    public function isDuringQuietHours(): bool
    {
        $now = now()->timezone(config('app.timezone', 'Asia/Jakarta'));
        $current = $now->format('H:i');
        $start = $this->quiet_hours_start;
        $end = $this->quiet_hours_end;

        if ($start <= $end) {
            return $current >= $start && $current <= $end;
        }

        return $current >= $start || $current <= $end;
    }
}
