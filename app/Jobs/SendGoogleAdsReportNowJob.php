<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\GoogleAdsReportMail;
use App\Models\Team;
use App\Models\User;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class SendGoogleAdsReportNowJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @param  array<int, string>  $recipients
     */
    public function __construct(
        public readonly int $teamId,
        public readonly CarbonInterface $month,
        public readonly array $recipients,
        public readonly ?int $userId = null,
    ) {}

    public function handle(
        GoogleAdsReportService $reportService,
        GoogleAdsPdfGenerator $pdfGenerator,
    ): void {
        $team = Team::query()->findOrFail($this->teamId);

        try {
            $reportData = $reportService->generateReportData($team, $this->month);
            $filename = $pdfGenerator->generate($reportData);
            $pdfPath = 'pdfs/' . $filename;

            Mail::to($this->recipients)
                ->send(new GoogleAdsReportMail(
                    team: $team,
                    pdfPath: $pdfPath,
                    reportMonth: $this->month,
                ));

            Log::info('Google Ads report email sent immediately', [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'recipients' => $this->recipients,
                'report_month' => $this->month->format('Y-m'),
            ]);

            $this->notifyUserSuccess($filename);
        } catch (Throwable $e) {
            Log::error('Failed to send Google Ads report email immediately', [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'recipients' => $this->recipients,
                'error' => $e->getMessage(),
            ]);

            $this->notifyUserError($e->getMessage());

            throw $e;
        }
    }

    private function notifyUserSuccess(string $filename): void
    {
        $user = $this->userId !== null ? User::query()->find($this->userId) : null;

        if (! $user instanceof User) {
            return;
        }

        Notification::make()
            ->title(__('Email sent successfully'))
            ->body(__('The Google Ads report has been sent to the specified recipients.'))
            ->success()
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->url(route('pdf.show', ['filename' => $filename]), true)
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($user);
    }

    private function notifyUserError(string $error): void
    {
        $user = $this->userId !== null ? User::query()->find($this->userId) : null;

        if (! $user instanceof User) {
            return;
        }

        Notification::make()
            ->title(__('Email sending failed'))
            ->body(__('An error occurred while sending the email: :error', ['error' => $error]))
            ->danger()
            ->sendToDatabase($user);
    }
}
