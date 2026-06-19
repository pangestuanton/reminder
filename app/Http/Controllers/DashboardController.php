<?php

namespace App\Http\Controllers;

use App\Services\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $dashboardStatsService) {}

    public function index(): View
    {
        $stats = $this->dashboardStatsService->get(auth()->user());

        return view('dashboard.index', [
            ...$stats,
            'countdownText' => $this->dashboardStatsService->countdownText($stats['nearest']),
        ]);
    }
}
