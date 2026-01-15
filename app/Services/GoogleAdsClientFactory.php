<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleAdsSettings;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V22\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V22\GoogleAdsClientBuilder;
use RuntimeException;

final class GoogleAdsClientFactory
{
    /**
     * Create a Google Ads client for the given settings.
     */
    public static function make(GoogleAdsSettings $settings): GoogleAdsClient
    {
        $oauthService = new GoogleAdsOAuthService();

        if (! $oauthService->hasCredentials()) {
            throw new RuntimeException('Google Ads credentials not configured. Please set GOOGLE_ADS_CLIENT_ID, GOOGLE_ADS_CLIENT_SECRET, and GOOGLE_ADS_DEVELOPER_TOKEN in your .env file.');
        }

        if (! $settings->hasValidCredentials()) {
            throw new RuntimeException('Google Ads is not connected or customer ID is not set.');
        }

        // Refresh token if needed
        $settings = $oauthService->refreshTokenIfNeeded($settings);

        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId(config('services.google_ads.client_id'))
            ->withClientSecret(config('services.google_ads.client_secret'))
            ->withRefreshToken($settings->refresh_token)
            ->build();

        $builder = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(config('services.google_ads.developer_token'))
            ->withOAuth2Credential($oAuth2Credential);

        // Use manager_customer_id as login-customer-id for MCC accounts
        if ($settings->manager_customer_id !== null) {
            $builder->withLoginCustomerId((int) self::normalizeCustomerId($settings->manager_customer_id));
        }

        return $builder->build();
    }

    /**
     * Normalize customer ID by removing dashes.
     */
    private static function normalizeCustomerId(?string $customerId): string
    {
        if ($customerId === null) {
            throw new RuntimeException('Customer ID is not set.');
        }

        return str_replace('-', '', $customerId);
    }
}
