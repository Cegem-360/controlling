<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\GoogleAdsSettings;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V18\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V18\GoogleAdsClientBuilder;
use RuntimeException;

final class GoogleAdsClientFactory
{
    /**
     * Create a Google Ads client for the given settings.
     */
    public static function make(GoogleAdsSettings $settings): GoogleAdsClient
    {
        $globalSettings = GlobalSetting::instance();

        if (! $globalSettings->hasGoogleAdsCredentials()) {
            throw new RuntimeException('Google Ads credentials not configured in global settings.');
        }

        if (! $settings->hasValidCredentials()) {
            throw new RuntimeException('Google Ads is not connected or customer ID is not set.');
        }

        // Refresh token if needed
        $oauthService = new GoogleAdsOAuthService();
        $settings = $oauthService->refreshTokenIfNeeded($settings);

        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId($globalSettings->google_ads_client_id)
            ->withClientSecret($globalSettings->google_ads_client_secret)
            ->withRefreshToken($settings->refresh_token)
            ->build();

        return (new GoogleAdsClientBuilder())
            ->withDeveloperToken($globalSettings->google_ads_developer_token)
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId(self::normalizeCustomerId($settings->customer_id))
            ->build();
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
