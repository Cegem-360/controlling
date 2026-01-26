<?php

declare(strict_types=1);

use App\Jobs\SendScheduledGoogleAdsReportJob;
use App\Mail\GoogleAdsReportMail;
use App\Models\GoogleAdsCampaign;
use App\Models\GoogleAdsSettings;
use App\Models\Team;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
    Mail::fake();
});

it('sends report email to configured recipients', function (): void {
    $team = Team::factory()->create();

    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Date::now()->subMonth()->startOfMonth(),
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

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => ['test@example.com', 'admin@example.com'],
        'email_frequency' => 'monthly',
        'email_day_of_month' => Date::now()->day,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertSent(GoogleAdsReportMail::class, function (GoogleAdsReportMail $mail) use ($team): bool {
        return $mail->team->id === $team->id
            && $mail->hasTo('test@example.com')
            && $mail->hasTo('admin@example.com');
    });
});

it('does not send email when email is disabled', function (): void {
    $team = Team::factory()->create();

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => false,
        'email_recipients' => ['test@example.com'],
        'email_frequency' => 'monthly',
        'email_day_of_month' => Date::now()->day,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertNothingSent();
});

it('does not send email when recipients are empty', function (): void {
    $team = Team::factory()->create();

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => [],
        'email_frequency' => 'monthly',
        'email_day_of_month' => Date::now()->day,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertNothingSent();
});

it('only sends email on correct day for weekly frequency', function (): void {
    $team = Team::factory()->create();

    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Date::now()->subMonth()->startOfMonth(),
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

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => ['test@example.com'],
        'email_frequency' => 'weekly',
        'email_day_of_week' => Date::now()->dayOfWeekIso,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertSent(GoogleAdsReportMail::class);
});

it('does not send email on wrong day for weekly frequency', function (): void {
    $team = Team::factory()->create();

    $wrongDay = Date::now()->dayOfWeekIso === 7 ? 1 : Date::now()->dayOfWeekIso + 1;

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => ['test@example.com'],
        'email_frequency' => 'weekly',
        'email_day_of_week' => $wrongDay,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertNothingSent();
});

it('does not send email on wrong day for monthly frequency', function (): void {
    $team = Team::factory()->create();

    $wrongDay = Date::now()->day === 28 ? 1 : Date::now()->day + 1;

    GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => ['test@example.com'],
        'email_frequency' => 'monthly',
        'email_day_of_month' => $wrongDay,
    ]);

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    Mail::assertNothingSent();
});

it('updates last_email_sent_at after sending', function (): void {
    $team = Team::factory()->create();

    GoogleAdsCampaign::query()->create([
        'team_id' => $team->id,
        'date' => Date::now()->subMonth()->startOfMonth(),
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

    $settings = GoogleAdsSettings::query()->create([
        'team_id' => $team->id,
        'customer_id' => '123-456-7890',
        'is_connected' => true,
        'email_enabled' => true,
        'email_recipients' => ['test@example.com'],
        'email_frequency' => 'monthly',
        'email_day_of_month' => Date::now()->day,
        'last_email_sent_at' => null,
    ]);

    expect($settings->last_email_sent_at)->toBeNull();

    dispatch_sync(new SendScheduledGoogleAdsReportJob());

    $settings->refresh();
    expect($settings->last_email_sent_at)->not->toBeNull();
});

describe('GoogleAdsSettings shouldSendEmailToday', function (): void {
    it('returns false when email is disabled', function (): void {
        $settings = new GoogleAdsSettings([
            'email_enabled' => false,
            'email_recipients' => ['test@example.com'],
            'email_frequency' => 'monthly',
            'email_day_of_month' => Date::now()->day,
        ]);

        expect($settings->shouldSendEmailToday())->toBeFalse();
    });

    it('returns false when recipients are empty', function (): void {
        $settings = new GoogleAdsSettings([
            'email_enabled' => true,
            'email_recipients' => [],
            'email_frequency' => 'monthly',
            'email_day_of_month' => Date::now()->day,
        ]);

        expect($settings->shouldSendEmailToday())->toBeFalse();
    });

    it('returns true on correct day for monthly frequency', function (): void {
        $settings = new GoogleAdsSettings([
            'email_enabled' => true,
            'email_recipients' => ['test@example.com'],
            'email_frequency' => 'monthly',
            'email_day_of_month' => Date::now()->day,
        ]);

        expect($settings->shouldSendEmailToday())->toBeTrue();
    });

    it('returns true on correct day for weekly frequency', function (): void {
        $settings = new GoogleAdsSettings([
            'email_enabled' => true,
            'email_recipients' => ['test@example.com'],
            'email_frequency' => 'weekly',
            'email_day_of_week' => Date::now()->dayOfWeekIso,
        ]);

        expect($settings->shouldSendEmailToday())->toBeTrue();
    });
});

describe('GoogleAdsReportMail', function (): void {
    it('has correct subject', function (): void {
        $team = Team::factory()->create(['name' => 'Test Company']);
        $reportMonth = Date::parse('2026-01-15');

        $mail = new GoogleAdsReportMail(
            team: $team,
            pdfPath: 'pdfs/test.pdf',
            reportMonth: $reportMonth,
        );

        expect($mail->envelope()->subject)->toContain('Google Ads Havi Riport');
        expect($mail->envelope()->subject)->toContain('Test Company');
    });

    it('has pdf attachment', function (): void {
        Storage::disk('public')->put('pdfs/test-report.pdf', 'fake pdf content');

        $team = Team::factory()->create();
        $reportMonth = Date::parse('2026-01-15');

        $mail = new GoogleAdsReportMail(
            team: $team,
            pdfPath: 'pdfs/test-report.pdf',
            reportMonth: $reportMonth,
        );

        $attachments = $mail->attachments();
        expect($attachments)->toHaveCount(1);
    });
});
