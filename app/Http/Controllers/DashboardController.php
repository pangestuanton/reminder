<?php

namespace App\Http\Controllers;

use App\Models\CollegeSchedule;
use App\Models\GoogleCalendarEvent;
use App\Models\NotificationLog;
use App\Services\AnalyticsService;
use App\Services\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $dashboardStatsService,
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $stats = $this->dashboardStatsService->get($user);

        $todayClasses = CollegeSchedule::where('user_id', $user->id)
            ->active()
            ->currentSemester()
            ->forDay(now()->translatedFormat('l'))
            ->orderBy('jam_mulai')
            ->get();

        $todayEvents = GoogleCalendarEvent::where('user_id', $user->id)
            ->forDate(now())
            ->orderBy('start_datetime')
            ->get();

        $analytics = $this->analyticsService->getStats($user);

        $recentNotifications = NotificationLog::where('user_id', $user->id)
            ->orderByDesc('sent_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            ...$stats,
            'todayClasses' => $todayClasses,
            'todayEvents' => $todayEvents,
            'analytics' => $analytics,
            'recentNotifications' => $recentNotifications,
            'countdownText' => $this->dashboardStatsService->countdownText($stats['nearest']),
        ]);
    }
}
