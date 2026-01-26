<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Team;
use App\Models\User;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class GenerateGoogleAdsReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $teamId,
        public readonly Carbon $month,
        public readonly ?int $userId = null,
    ) {}

    public function handle(
        GoogleAdsReportService $reportService,
        GoogleAdsPdfGenerator $pdfGenerator,
    ): void {
        $team = Team::findOrFail($this->teamId);

        $data = $reportService->generateReportData($team, $this->month);
        $filename = $pdfGenerator->generate($data);

        $this->notifyUser($filename);
    }

    private function notifyUser(string $filename): void
    {
        if ($this->userId === null) {
            return;
        }

        $user = User::find($this->userId);
        if ($user === null) {
            return;
        }

        $pdfUrl = route('pdf.show', ['filename' => $filename]);

        Notification::make()
            ->title(__('Google Ads riport elkészült'))
            ->body(__('A havi riport letölthető.'))
            ->success()
            ->actions([
                Action::make('view')
                    ->label(__('Megtekintés'))
                    ->url($pdfUrl)
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($user);
    }
}
