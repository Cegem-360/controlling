<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\GoogleAdsReportMail;
use App\Models\GoogleAdsSettings;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class SendScheduledGoogleAdsReportJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(
        GoogleAdsReportService $reportService,
        GoogleAdsPdfGenerator $pdfGenerator,
    ): void {
        $settings = GoogleAdsSettings::query()
            ->where('email_enabled', true)
            ->whereNotNull('email_recipients')
            ->with('team')
            ->get();

        foreach ($settings as $setting) {
            if (! $setting->shouldSendEmailToday()) {
                continue;
            }

            $this->sendReportEmail($setting, $reportService, $pdfGenerator);
        }
    }

    private function sendReportEmail(
        GoogleAdsSettings $settings,
        GoogleAdsReportService $reportService,
        GoogleAdsPdfGenerator $pdfGenerator,
    ): void {
        $team = $settings->team;
        $reportMonth = now()->subMonth();

        try {
            $reportData = $reportService->generateReportData($team, $reportMonth);
            $filename = $pdfGenerator->generate($reportData);
            $pdfPath = 'pdfs/' . $filename;

            $recipients = $settings->getEmailRecipients();

            Mail::to($recipients)
                ->send(new GoogleAdsReportMail(
                    team: $team,
                    pdfPath: $pdfPath,
                    reportMonth: $reportMonth,
                ));

            $settings->update(['last_email_sent_at' => now()]);

            Log::info('Google Ads report email sent', [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'recipients' => $recipients,
                'report_month' => $reportMonth->format('Y-m'),
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send Google Ads report email', [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
