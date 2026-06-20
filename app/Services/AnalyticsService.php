<?php

namespace App\Services;

use App\Models\JadwalKegiatan;
use App\Models\User;

class AnalyticsService
{
    public function getStats(User $user, ?string $dateFrom = null, ?string $dateTo = null, ?string $course = null, ?string $category = null, ?string $source = null): array
    {
        $query = JadwalKegiatan::query()
            ->ownedBy($user)
            ->where('is_informational', false);

        if ($dateFrom) {
            $query->where('waktu_pelaksanaan', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('waktu_pelaksanaan', '<=', $dateTo);
        }

        if ($course) {
            $query->where('course_name', $course);
        }

        if ($category) {
            $query->category($category);
        }

        if ($source) {
            $query->source($source);
        }

        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', 'selesai')->count();
        $inProgress = (clone $query)->where('status', 'pending')->where('waktu_pelaksanaan', '>=', now())->count();
        $overdue = (clone $query)->where('status', 'pending')->where('waktu_pelaksanaan', '<', now())->count();

        $upcoming = (clone $query)
            ->where('status', 'pending')
            ->where('waktu_pelaksanaan', '>=', now())
            ->orderBy('waktu_pelaksanaan')
            ->limit(10)
            ->get();

        $percentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        $weeklyTrend = $this->getWeeklyTrend($user);

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'overdue' => $overdue,
            'upcoming_count' => $upcoming->count(),
            'percentage' => $percentage,
            'upcoming' => $upcoming,
            'weekly_trend' => $weeklyTrend,
        ];
    }

    protected function getWeeklyTrend(User $user): array
    {
        $trend = [];
        $weekLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $weekLabels[$date->dayOfWeek];

            $completed = JadwalKegiatan::where('user_id', $user->id)
                ->where('status', 'selesai')
                ->whereDate('completed_at', $date->toDateString())
                ->count();

            $created = JadwalKegiatan::where('user_id', $user->id)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $trend[] = [
                'day' => $dayName,
                'date' => $date->toDateString(),
                'completed' => $completed,
                'created' => $created,
            ];
        }

        return $trend;
    }

    public function getUserCourses(User $user): array
    {
        return JadwalKegiatan::where('user_id', $user->id)
            ->whereNotNull('course_name')
            ->where('course_name', '!=', '')
            ->distinct()
            ->pluck('course_name')
            ->toArray();
    }
}
