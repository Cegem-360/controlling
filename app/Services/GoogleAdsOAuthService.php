<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleAdsSettings;
use App\Models\Team;
use Exception;
use Google\Client;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class GoogleAdsOAuthService
{
    private const SCOPES = ['https://www.googleapis.com/auth/adwords'];

    /**
     * Get the OAuth authorization URL.
     */
    public function getAuthorizationUrl(Team $team): string
    {
        $client = $this->createOAuthClient();
        $client->setState((string) $team->id);

        return $client->createAuthUrl();
    }

    /**
     * Handle the OAuth callback and store tokens.
     */
    public function handleCallback(string $code, int $teamId): GoogleAdsSettings
    {
        $client = $this->createOAuthClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            Log::error('Google Ads OAuth error', $token);
            throw new RuntimeException('OAuth error: ' . ($token['error_description'] ?? $token['error']));
        }

        $settings = GoogleAdsSettings::query()->updateOrCreate(
            ['team_id' => $teamId],
            [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_expires_at' => now()->addSeconds($token['expires_in']),
                'is_connected' => true,
            ],
        );

        return $settings;
    }

    /**
     * Refresh the access token if needed.
     */
    public function refreshTokenIfNeeded(GoogleAdsSettings $settings): GoogleAdsSettings
    {
        if (! $settings->isTokenExpired()) {
            return $settings;
        }

        if (! $settings->refresh_token) {
            throw new RuntimeException('No refresh token available. Please reconnect your Google Ads account.');
        }

        $client = $this->createOAuthClient();
        $client->fetchAccessTokenWithRefreshToken($settings->refresh_token);
        $token = $client->getAccessToken();

        if (isset($token['error'])) {
            Log::error('Google Ads token refresh error', $token);
            $settings->update(['is_connected' => false]);
            throw new RuntimeException('Token refresh failed: ' . ($token['error_description'] ?? $token['error']));
        }

        $settings->update([
            'access_token' => $token['access_token'],
            'token_expires_at' => now()->addSeconds($token['expires_in']),
        ]);

        return $settings->fresh();
    }

    /**
     * Disconnect the Google Ads account.
     */
    public function disconnect(Team $team): void
    {
        $settings = $team->googleAdsSettings;

        if ($settings) {
            // Optionally revoke the token
            if ($settings->access_token) {
                try {
                    $client = $this->createOAuthClient();
                    $client->revokeToken($settings->access_token);
                } catch (Exception $e) {
                    Log::warning('Failed to revoke Google Ads token', ['error' => $e->getMessage()]);
                }
            }

            $settings->update([
                'access_token' => null,
                'refresh_token' => null,
                'token_expires_at' => null,
                'is_connected' => false,
            ]);
        }
    }

    /**
     * Check if Google Ads credentials are configured.
     */
    public function hasCredentials(): bool
    {
        return filled(config('services.google_ads.client_id'))
            && filled(config('services.google_ads.client_secret'))
            && filled(config('services.google_ads.developer_token'));
    }

    /**
     * Create a configured OAuth client.
     */
    private function createOAuthClient(): Client
    {
        if (! $this->hasCredentials()) {
            throw new RuntimeException('Google Ads OAuth credentials are not configured. Please set GOOGLE_ADS_CLIENT_ID, GOOGLE_ADS_CLIENT_SECRET, and GOOGLE_ADS_DEVELOPER_TOKEN in your .env file.');
        }

        $client = new Client();
        $client->setClientId(config('services.google_ads.client_id'));
        $client->setClientSecret(config('services.google_ads.client_secret'));
        $client->setRedirectUri($this->getRedirectUri());
        $client->setScopes(self::SCOPES);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }

    /**
     * Get the OAuth redirect URI.
     */
    private function getRedirectUri(): string
    {
        $configuredUri = config('services.google_ads.redirect_uri');

        if ($configuredUri) {
            return $configuredUri;
        }

        return route('google-ads.oauth.callback');
    }
}
