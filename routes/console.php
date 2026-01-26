<?php

declare(strict_types=1);

use App\Jobs\SendScheduledGoogleAdsReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendScheduledGoogleAdsReportJob())
    ->dailyAt('08:00')
    ->timezone('Europe/Budapest')
    ->name('google-ads-report-email')
    ->withoutOverlapping();
