<?php

declare(strict_types=1);

use App\DataTransferObjects\GoogleAdsReportData;
use App\Models\GoogleAdsCampaign;
use App\Models\Team;
use App\Services\GoogleAds\GoogleAdsReportService;
use Carbon\Carbon;

it('generates report data for a team', function (): void {
    $team = Team::factory()->create();

    // Create some campaign data
    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Carbon::now()->startOfMonth(),
        'campaign_id' => 'test-campaign-1',
        'campaign_name' => 'Test Campaign',
        'campaign_status' => 'ENABLED',
        'impressions' => 1000,
        'clicks' => 50,
        'cost' => 100.00,
        'avg_cpc' => 2.00,
        'ctr' => 0.05,
        'conversions' => 5,
        'conversion_value' => 500.00,
        'cost_per_conversion' => 20.00,
        'conversion_rate' => 0.10,
    ]);

    $service = new GoogleAdsReportService();
    $data = $service->generateReportData($team, Carbon::now());

    expect($data)->toBeInstanceOf(GoogleAdsReportData::class);
    expect($data->team->id)->toBe($team->id);
    expect($data->kpiSummary['impressions'])->toBe(1000);
    expect($data->kpiSummary['clicks'])->toBe(50);
    expect($data->campaigns)->toHaveCount(1);
    expect($data->campaigns->first()['campaign_name'])->toBe('Test Campaign');
});

it('returns empty collections when no data exists', function (): void {
    $team = Team::factory()->create();

    $service = new GoogleAdsReportService();
    $data = $service->generateReportData($team, Carbon::now());

    expect($data)->toBeInstanceOf(GoogleAdsReportData::class);
    expect($data->kpiSummary['impressions'])->toBe(0);
    expect($data->campaigns)->toBeEmpty();
    expect($data->adGroups)->toBeEmpty();
    expect($data->dailyStats)->toBeEmpty();
});

it('calculates kpi changes correctly', function (): void {
    $team = Team::factory()->create();

    // Create current month data
    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Carbon::now()->startOfMonth(),
        'campaign_id' => 'test-campaign-1',
        'campaign_name' => 'Test Campaign',
        'campaign_status' => 'ENABLED',
        'impressions' => 200,
        'clicks' => 20,
        'cost' => 50.00,
        'avg_cpc' => 2.50,
        'ctr' => 0.10,
        'conversions' => 2,
        'conversion_value' => 100.00,
        'cost_per_conversion' => 25.00,
        'conversion_rate' => 0.10,
    ]);

    // Create previous month data
    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Carbon::now()->subMonth()->startOfMonth(),
        'campaign_id' => 'test-campaign-1',
        'campaign_name' => 'Test Campaign',
        'campaign_status' => 'ENABLED',
        'impressions' => 100,
        'clicks' => 10,
        'cost' => 25.00,
        'avg_cpc' => 2.50,
        'ctr' => 0.10,
        'conversions' => 1,
        'conversion_value' => 50.00,
        'cost_per_conversion' => 25.00,
        'conversion_rate' => 0.10,
    ]);

    $service = new GoogleAdsReportService();
    $data = $service->generateReportData($team, Carbon::now());

    $changes = $data->getKpiChanges();

    // Impressions doubled from 100 to 200, so change should be 100%
    expect($changes['impressions'])->toBe(100.0);
});
