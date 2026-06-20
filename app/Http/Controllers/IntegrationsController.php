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
        $scopes = match ($service) {
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

        $sessionKey = 'google_integration_service';
        $sessionKeyScopes = 'google_integration_scopes';
        session([$sessionKey => $service, $sessionKeyScopes => $scopes]);

        return Socialite::driver('google')
            ->stateless()
            ->scopes($scopes)
            ->redirectUrl(route('integrations.google.callback'))
            ->redirect();
    }

    public function googleCallback(Request $request, GoogleTokenService $tokenService): RedirectResponse
    {
        try {
            $service = session('google_integration_service', 'all');
            $scopes = session('google_integration_scopes', []);
            $user = $request->user();

            $socialiteUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl(route('integrations.google.callback'))
                ->user();

            $account = $tokenService->storeTokens($user, [
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'expires_in' => $socialiteUser->expiresIn,
                'email' => $socialiteUser->getEmail(),
            ], $scopes);

            if (in_array('classroom', [$service, 'all'], true)) {
                $account->update(['classroom_connected_at' => now()]);
                SyncGoogleClassroomJob::dispatch($user);
            }

            if (in_array('calendar', [$service, 'all'], true)) {
                $account->update(['calendar_connected_at' => now()]);
                SyncGoogleCalendarJob::dispatch($user);
            }

            session()->forget(['google_integration_service', 'google_integration_scopes']);

            return redirect()->route('integrations.index')
                ->with('success', 'Google berhasil dihubungkan. Sinkronisasi sedang berjalan.');
        } catch (\Throwable $e) {
            Log::error('Google integration callback failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('integrations.index')
                ->with('error', 'Gagal menghubungkan Google. Silakan coba lagi.');
        }
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
