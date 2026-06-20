<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

use App\Services\GoogleTokenService;
use App\Jobs\SyncGoogleClassroomJob;
use App\Jobs\SyncGoogleCalendarJob;

class GoogleController extends Controller
{
    protected function driver()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('google.callback'))
            ->setHttpClient(new Client(['verify' => false]));
    }

    public function redirect(): RedirectResponse
    {
        return $this->driver()
            ->scopes(['openid', 'email', 'profile'])
            ->with(['access_type' => 'offline', 'prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(GoogleTokenService $tokenService): RedirectResponse
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $service = session('google_integration_service', 'all');
                $scopes = session('google_integration_scopes', []);

                $googleUser = $this->driver()->user();

                $account = $tokenService->storeTokens($user, [
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'expires_in' => $googleUser->expiresIn,
                    'email' => $googleUser->getEmail(),
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
            }

            $googleUser = $this->driver()->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'name' => $user->name ?: ($googleUser->getName() ?? 'Pengguna Google'),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Berhasil masuk dengan Google.');
        } catch (\Throwable $e) {
            Log::error('Google login/integration gagal', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (Auth::check()) {
                return redirect()->route('integrations.index')
                    ->with('error', 'Gagal menghubungkan Google. Silakan coba lagi.');
            }

            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }
    }
}
