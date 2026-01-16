<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\GoogleAdsDemographic;
use App\Models\GoogleAdsDeviceStat;
use App\Models\GoogleAdsGeographicStat;
use App\Models\GoogleAdsHourlyStat;
use App\Models\GoogleAdsSettings;
use App\Models\Team;
use App\Services\GoogleAdsClientFactory;
use App\Services\GoogleAdsOAuthService;
use Exception;
use Filament\Notifications\Notification;
use Google\Ads\GoogleAds\Lib\V22\GoogleAdsClient;
use Google\Ads\GoogleAds\V22\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V22\Enums\AgeRangeTypeEnum\AgeRangeType;
use Google\Ads\GoogleAds\V22\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V22\Enums\DeviceEnum\Device;
use Google\Ads\GoogleAds\V22\Enums\GenderTypeEnum\GenderType;
use Google\Ads\GoogleAds\V22\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V22\Services\SearchGoogleAdsRequest;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class GoogleAdsImport implements ShouldQueue
{
    use Batchable;
    use Queueable;

    private const DAYS_TO_IMPORT = 90;

    public function __construct(
        public readonly int $teamId,
    ) {}

    public function handle(): void
    {
        $team = Team::query()->find($this->teamId);

        if (! $team) {
            $this->failWithNotification('Team not found.', 'The specified team does not exist.');

            return;
        }

        $oauthService = new GoogleAdsOAuthService();

        if (! $oauthService->hasCredentials()) {
            $this->failWithNotification('Google Ads credentials not configured.', 'Please set GOOGLE_ADS_CLIENT_ID, GOOGLE_ADS_CLIENT_SECRET, and GOOGLE_ADS_DEVELOPER_TOKEN in your .env file.');

            return;
        }

        $settings = GoogleAdsSettings::query()
            ->where('team_id', $this->teamId)
            ->first();

        if (! $settings || ! $settings->hasValidCredentials()) {
            $this->failWithNotification('Google Ads not connected.', 'Please connect your Google Ads account in Settings.');

            return;
        }

        try {
            $client = GoogleAdsClientFactory::make($settings);
            $customerId = str_replace('-', '', $settings->customer_id);

            $this->importCampaigns($client, $customerId);
            $this->importAdGroups($client, $customerId);
            $this->importHourlyStats($client, $customerId);
            $this->importDeviceStats($client, $customerId);
            $this->importDemographics($client, $customerId);
            $this->importGeographicStats($client, $customerId);

            $settings->update(['last_sync_at' => now()]);

            Notification::make()
                ->title('Google Ads import completed successfully.')
                ->body('All Google Ads data has been imported.')
                ->success()
                ->send();
        } catch (Exception $e) {
            $this->failWithNotification('Google Ads import failed.', $e->getMessage());
        }
    }

    /**
     * Get the date range for GAQL queries in 'YYYY-MM-DD' AND 'YYYY-MM-DD' format.
     */
    private function getDateRange(): string
    {
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(self::DAYS_TO_IMPORT)->format('Y-m-d');

        return "'{$startDate}' AND '{$endDate}'";
    }

    private function importCampaigns(GoogleAdsClient $client, string $customerId): void
    {
        $query = '
            SELECT
                segments.date,
                campaign.id,
                campaign.name,
                campaign.status,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.average_cpc,
                metrics.ctr,
                metrics.conversions,
                metrics.conversions_value,
                metrics.cost_per_conversion
            FROM campaign
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
            ORDER BY segments.date DESC
        ';

        $this->executeQuery($client, $customerId, $query, function (GoogleAdsRow $row): void {
            $campaign = $row->getCampaign();
            $metrics = $row->getMetrics();
            $segments = $row->getSegments();

            $clicks = $metrics->getClicks();
            $conversions = $metrics->getConversions();

            GoogleAdsCampaign::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $segments->getDate(),
                        'campaign_id' => (string) $campaign->getId(),
                    ],
                    [
                        'campaign_name' => $campaign->getName(),
                        'campaign_status' => CampaignStatus::name($campaign->getStatus()),
                        'impressions' => $metrics->getImpressions(),
                        'clicks' => $clicks,
                        'cost' => $metrics->getCostMicros() / 1_000_000,
                        'avg_cpc' => $metrics->getAverageCpc() / 1_000_000,
                        'ctr' => $metrics->getCtr() * 100,
                        'conversions' => $conversions,
                        'conversion_value' => $metrics->getConversionsValue(),
                        'cost_per_conversion' => $metrics->getCostPerConversion() / 1_000_000,
                        'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0,
                    ],
                );
        });
    }

    private function importAdGroups(GoogleAdsClient $client, string $customerId): void
    {
        $query = '
            SELECT
                segments.date,
                campaign.id,
                campaign.name,
                ad_group.id,
                ad_group.name,
                ad_group.status,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.average_cpc,
                metrics.ctr,
                metrics.conversions,
                metrics.conversions_value,
                metrics.cost_per_conversion
            FROM ad_group
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
            ORDER BY segments.date DESC
        ';

        $this->executeQuery($client, $customerId, $query, function (GoogleAdsRow $row): void {
            $campaign = $row->getCampaign();
            $adGroup = $row->getAdGroup();
            $metrics = $row->getMetrics();
            $segments = $row->getSegments();

            $clicks = $metrics->getClicks();
            $conversions = $metrics->getConversions();

            GoogleAdsAdGroup::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $segments->getDate(),
                        'ad_group_id' => (string) $adGroup->getId(),
                    ],
                    [
                        'campaign_id' => (string) $campaign->getId(),
                        'campaign_name' => $campaign->getName(),
                        'ad_group_name' => $adGroup->getName(),
                        'ad_group_status' => AdGroupStatus::name($adGroup->getStatus()),
                        'impressions' => $metrics->getImpressions(),
                        'clicks' => $clicks,
                        'cost' => $metrics->getCostMicros() / 1_000_000,
                        'avg_cpc' => $metrics->getAverageCpc() / 1_000_000,
                        'ctr' => $metrics->getCtr() * 100,
                        'conversions' => $conversions,
                        'conversion_value' => $metrics->getConversionsValue(),
                        'cost_per_conversion' => $metrics->getCostPerConversion() / 1_000_000,
                        'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0,
                    ],
                );
        });
    }

    private function importHourlyStats(GoogleAdsClient $client, string $customerId): void
    {
        $query = '
            SELECT
                segments.date,
                segments.hour,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions,
                metrics.ctr,
                metrics.average_cpc
            FROM campaign
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
        ';

        $aggregated = [];

        $this->executeQuery($client, $customerId, $query, function (GoogleAdsRow $row) use (&$aggregated): void {
            $segments = $row->getSegments();
            $metrics = $row->getMetrics();

            $key = $segments->getDate() . '|' . $segments->getHour();

            if (! isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'date' => $segments->getDate(),
                    'hour' => $segments->getHour(),
                    'impressions' => 0,
                    'clicks' => 0,
                    'cost' => 0,
                    'conversions' => 0,
                ];
            }

            $aggregated[$key]['impressions'] += $metrics->getImpressions();
            $aggregated[$key]['clicks'] += $metrics->getClicks();
            $aggregated[$key]['cost'] += $metrics->getCostMicros() / 1_000_000;
            $aggregated[$key]['conversions'] += $metrics->getConversions();
        });

        foreach ($aggregated as $data) {
            $clicks = $data['clicks'];

            GoogleAdsHourlyStat::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $data['date'],
                        'hour' => $data['hour'],
                    ],
                    [
                        'impressions' => $data['impressions'],
                        'clicks' => $clicks,
                        'cost' => $data['cost'],
                        'conversions' => $data['conversions'],
                        'ctr' => $data['impressions'] > 0 ? ($clicks / $data['impressions']) * 100 : 0,
                        'avg_cpc' => $clicks > 0 ? $data['cost'] / $clicks : 0,
                        'conversion_rate' => $clicks > 0 ? ($data['conversions'] / $clicks) * 100 : 0,
                    ],
                );
        }
    }

    private function importDeviceStats(GoogleAdsClient $client, string $customerId): void
    {
        $query = '
            SELECT
                segments.date,
                segments.device,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions,
                metrics.ctr,
                metrics.average_cpc
            FROM campaign
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
        ';

        $aggregated = [];

        $this->executeQuery($client, $customerId, $query, function (GoogleAdsRow $row) use (&$aggregated): void {
            $segments = $row->getSegments();
            $metrics = $row->getMetrics();

            $key = $segments->getDate() . '|' . Device::name($segments->getDevice());

            if (! isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'date' => $segments->getDate(),
                    'device' => Device::name($segments->getDevice()),
                    'impressions' => 0,
                    'clicks' => 0,
                    'cost' => 0,
                    'conversions' => 0,
                ];
            }

            $aggregated[$key]['impressions'] += $metrics->getImpressions();
            $aggregated[$key]['clicks'] += $metrics->getClicks();
            $aggregated[$key]['cost'] += $metrics->getCostMicros() / 1_000_000;
            $aggregated[$key]['conversions'] += $metrics->getConversions();
        });

        foreach ($aggregated as $data) {
            $clicks = $data['clicks'];

            GoogleAdsDeviceStat::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $data['date'],
                        'device' => $data['device'],
                    ],
                    [
                        'impressions' => $data['impressions'],
                        'clicks' => $clicks,
                        'cost' => $data['cost'],
                        'conversions' => $data['conversions'],
                        'ctr' => $data['impressions'] > 0 ? ($clicks / $data['impressions']) * 100 : 0,
                        'avg_cpc' => $clicks > 0 ? $data['cost'] / $clicks : 0,
                        'conversion_rate' => $clicks > 0 ? ($data['conversions'] / $clicks) * 100 : 0,
                    ],
                );
        }
    }

    private function importDemographics(GoogleAdsClient $client, string $customerId): void
    {
        // Gender stats
        $genderQuery = '
            SELECT
                segments.date,
                ad_group_criterion.gender.type,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions
            FROM gender_view
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
        ';

        $this->executeQuery($client, $customerId, $genderQuery, function (GoogleAdsRow $row): void {
            $segments = $row->getSegments();
            $metrics = $row->getMetrics();
            $criterion = $row->getAdGroupCriterion();

            $clicks = $metrics->getClicks();
            $conversions = $metrics->getConversions();

            GoogleAdsDemographic::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $segments->getDate(),
                        'gender' => GenderType::name($criterion->getGender()->getType()),
                        'age_range' => null,
                    ],
                    [
                        'impressions' => $metrics->getImpressions(),
                        'clicks' => $clicks,
                        'cost' => $metrics->getCostMicros() / 1_000_000,
                        'conversions' => $conversions,
                        'ctr' => $metrics->getImpressions() > 0 ? ($clicks / $metrics->getImpressions()) * 100 : 0,
                        'avg_cpc' => $clicks > 0 ? ($metrics->getCostMicros() / 1_000_000) / $clicks : 0,
                        'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0,
                    ],
                );
        });

        // Age stats
        $ageQuery = '
            SELECT
                segments.date,
                ad_group_criterion.age_range.type,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions
            FROM age_range_view
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
        ';

        $this->executeQuery($client, $customerId, $ageQuery, function (GoogleAdsRow $row): void {
            $segments = $row->getSegments();
            $metrics = $row->getMetrics();
            $criterion = $row->getAdGroupCriterion();

            $clicks = $metrics->getClicks();
            $conversions = $metrics->getConversions();

            GoogleAdsDemographic::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $segments->getDate(),
                        'gender' => null,
                        'age_range' => AgeRangeType::name($criterion->getAgeRange()->getType()),
                    ],
                    [
                        'impressions' => $metrics->getImpressions(),
                        'clicks' => $clicks,
                        'cost' => $metrics->getCostMicros() / 1_000_000,
                        'conversions' => $conversions,
                        'ctr' => $metrics->getImpressions() > 0 ? ($clicks / $metrics->getImpressions()) * 100 : 0,
                        'avg_cpc' => $clicks > 0 ? ($metrics->getCostMicros() / 1_000_000) / $clicks : 0,
                        'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0,
                    ],
                );
        });
    }

    private function importGeographicStats(GoogleAdsClient $client, string $customerId): void
    {
        $query = '
            SELECT
                segments.date,
                geographic_view.country_criterion_id,
                geographic_view.location_type,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions
            FROM geographic_view
            WHERE segments.date BETWEEN ' . $this->getDateRange() . '
        ';

        $this->executeQuery($client, $customerId, $query, function (GoogleAdsRow $row): void {
            $segments = $row->getSegments();
            $metrics = $row->getMetrics();
            $geoView = $row->getGeographicView();

            $clicks = $metrics->getClicks();
            $conversions = $metrics->getConversions();

            GoogleAdsGeographicStat::query()
                ->withoutGlobalScope('team')
                ->updateOrCreate(
                    [
                        'team_id' => $this->teamId,
                        'date' => $segments->getDate(),
                        'geo_target_constant' => (string) $geoView->getCountryCriterionId(),
                    ],
                    [
                        'location_name' => $geoView->getLocationType(),
                        'country_code' => 'HU',
                        'impressions' => $metrics->getImpressions(),
                        'clicks' => $clicks,
                        'cost' => $metrics->getCostMicros() / 1_000_000,
                        'conversions' => $conversions,
                        'ctr' => $metrics->getImpressions() > 0 ? ($clicks / $metrics->getImpressions()) * 100 : 0,
                        'avg_cpc' => $clicks > 0 ? ($metrics->getCostMicros() / 1_000_000) / $clicks : 0,
                        'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0,
                    ],
                );
        });
    }

    /**
     * @param  callable(GoogleAdsRow): void  $callback
     */
    private function executeQuery(GoogleAdsClient $client, string $customerId, string $query, callable $callback): void
    {
        $googleAdsService = $client->getGoogleAdsServiceClient();

        $request = new SearchGoogleAdsRequest();
        $request->setCustomerId($customerId);
        $request->setQuery($query);

        $response = $googleAdsService->search($request);

        foreach ($response->iterateAllElements() as $row) {
            $callback($row);
        }
    }

    private function failWithNotification(string $title, string $body): void
    {
        Notification::make()->title($title)->body($body)->danger()->send();
        $this->fail($title);
    }
}
