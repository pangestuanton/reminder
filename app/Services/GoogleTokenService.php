<?php

namespace App\Services;

use App\Models\GoogleAccount;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class GoogleTokenService
{
    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['verify' => false]);
    }

    public function getAccessToken(GoogleAccount $account): ?string
    {
        if ($account->isTokenExpired()) {
            $refreshed = $this->refreshToken($account);
            if (! $refreshed) {
                return null;
            }
            $account->refresh();
        }

        return Crypt::decryptString($account->access_token_encrypted);
    }

    public function refreshToken(GoogleAccount $account): bool
    {
        try {
            $response = $this->httpClient->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'refresh_token' => Crypt::decryptString($account->refresh_token_encrypted),
                    'grant_type' => 'refresh_token',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $account->update([
                'access_token_encrypted' => Crypt::encryptString($data['access_token']),
                'token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Google token refresh failed', [
                'user_id' => $account->user_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function storeTokens(User $user, array $tokenData, array $scopes): GoogleAccount
    {
        $accessToken = $tokenData['access_token'] ?? $tokenData->getToken();
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $expiresIn = $tokenData['expires_in'] ?? 3600;
        $email = $tokenData['email'] ?? null;

        if (is_object($tokenData)) {
            $accessToken = $tokenData->getToken();
            $refreshToken = $tokenData->refreshToken;
            $expiresIn = $tokenData->expiresIn ?? 3600;
        }

        return GoogleAccount::updateOrCreate(
            ['user_id' => $user->id],
            [
                'google_account_email' => $email ?? $user->email,
                'access_token_encrypted' => Crypt::encryptString($accessToken),
                'refresh_token_encrypted' => Crypt::encryptString($refreshToken),
                'token_expires_at' => now()->addSeconds($expiresIn),
                'scopes' => $scopes,
                'disconnected_at' => null,
            ]
        );
    }

    public function disconnect(GoogleAccount $account): void
    {
        $account->update([
            'disconnected_at' => now(),
            'classroom_connected_at' => null,
            'calendar_connected_at' => null,
        ]);
    }

    public function revokeAccess(GoogleAccount $account): bool
    {
        try {
            $token = $this->getAccessToken($account);
            if ($token) {
                $this->httpClient->get('https://oauth2.googleapis.com/revoke', [
                    'query' => ['token' => $token],
                ]);
            }

            $this->disconnect($account);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Google token revocation failed', [
                'user_id' => $account->user_id,
                'error' => $e->getMessage(),
            ]);
            $this->disconnect($account);

            return false;
        }
    }
}
