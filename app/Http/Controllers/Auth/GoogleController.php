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

class GoogleController extends Controller
{
    protected function driver()
    {
        return Socialite::driver('google')
            ->stateless()
            ->setHttpClient(new Client(['verify' => false]));
    }

    public function redirect(): RedirectResponse
    {
        return $this->driver()->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
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
            Log::error('Google login gagal', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }
    }
}
