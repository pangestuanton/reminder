<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = $this->analyticsService->getStats(
            $user,
            $request->input('date_from'),
            $request->input('date_to'),
            $request->input('course'),
            $request->input('category'),
            $request->input('source'),
        );

        $courses = $this->analyticsService->getUserCourses($user);

        return view('analytics.index', [
            ...$stats,
            'courses' => $courses,
            'filters' => $request->only(['date_from', 'date_to', 'course', 'category', 'source']),
        ]);
    }
}
