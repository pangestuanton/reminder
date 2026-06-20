<?php

namespace App\Http\Controllers;

use App\Jobs\SyncGoogleClassroomJob;
use App\Jobs\SyncGoogleCalendarJob;
use App\Models\GoogleAccount;
use App\Services\GoogleTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Illuminate\Contracts\View\View;

class IntegrationsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $googleAccount = $user->googleAccount;

        return view('integrations.index', [
            'googleAccount' => $googleAccount,
            'hasClassroom' => $user->hasClassroomAccess(),
            'hasCalendar' => $user->hasCalendarAccess(),
        ]);
    }

    public function connectGoogle(Request $request, string $service): RedirectResponse
    {
        $serviceScopes = match ($service) {
            'classroom' => [
                'https://www.googleapis.com/auth/classroom.courses.readonly',
                'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
                'https://www.googleapis.com/auth/classroom.student-submissions.me.readonly',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ],
            'calendar' => [
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ],
            default => [
                'https://www.googleapis.com/auth/classroom.courses.readonly',
                'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
                'https://www.googleapis.com/auth/classroom.student-submissions.me.readonly',
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ],
        };

        // Merge with any scopes the user has already granted, so connecting
        // a second service doesn't revoke access to the first one.
        $user = $request->user();
        $existingScopes = $user->googleAccount?->scopes ?? [];
        $scopes = array_values(array_unique(array_merge($existingScopes, $serviceScopes)));

        session([
            'google_integration_service'        => $service,
            'google_integration_scopes'         => $scopes,
        ]);

        return Socialite::driver('google')
            ->scopes($scopes)
            ->with([
                'access_type' => 'offline',
                'prompt'      => 'consent',
            ])
            ->redirectUrl(route('google.callback'))
            ->redirect();
    }

    public function disconnectGoogle(Request $request, GoogleTokenService $tokenService): RedirectResponse
    {
        $user = $request->user();
        $account = $user->googleAccount;

        if ($account) {
            $tokenService->disconnect($account);
        }

        return back()->with('success', 'Google berhasil diputuskan.');
    }
}
