<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Team;
use App\Models\User;
use App\Services\GoogleAds\GoogleAdsPdfGenerator;
use App\Services\GoogleAds\GoogleAdsReportService;
use Carbon\CarbonInterface;
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
        public readonly CarbonInterface $month,
        public readonly ?int $userId = null,
    ) {}

    public function handle(
        GoogleAdsReportService $reportService,
        GoogleAdsPdfGenerator $pdfGenerator,
    ): void {
        $team = Team::query()->findOrFail($this->teamId);

        $data = $reportService->generateReportData($team, $this->month);
        $filename = $pdfGenerator->generate($data);

        $this->notifyUser($filename);
    }

    private function notifyUser(string $filename): void
    {
        $user = $this->userId !== null ? User::query()->find($this->userId) : null;

        if (! $user instanceof User) {
            return;
        }

        Notification::make()
            ->title(__('Google Ads riport elkészült'))
            ->body(__('A havi riport letölthető.'))
            ->success()
            ->actions([
                Action::make('view')
                    ->label(__('Megtekintés'))
                    ->url(route('pdf.show', ['filename' => $filename]))
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($user);
    }
}
