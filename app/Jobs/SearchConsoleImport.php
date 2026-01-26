<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\GlobalSetting;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Settings;
use App\Models\Team;
use App\Services\GoogleClientFactory;
use Filament\Notifications\Notification;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final class SearchConsoleImport implements ShouldQueue
{
    use Batchable;
    use Queueable;

    private const int ROW_LIMIT = 25000;

    private const int DATE_RANGE_DAYS = 90;

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

        $globalSettings = GlobalSetting::instance();
        $serviceAccount = $globalSettings->getServiceAccount();

        if (! $serviceAccount) {
            $this->failWithNotification(
                'Google Service Account credentials not configured.',
                'Please configure the credentials in Settings first.',
            );

            return;
        }

        $settings = Settings::query()
            ->where('team_id', $this->teamId)
            ->first();

        if (! $settings || ! $settings->site_url) {
            $this->failWithNotification(
                'Site URL not configured.',
                'Please configure the Site URL in Settings first.',
            );

            return;
        }

        $client = GoogleClientFactory::make(
            'https://www.googleapis.com/auth/webmasters.readonly',
            $globalSettings->google_service_account,
        );
        $service = new SearchConsole($client);

        $this->importSearchQueries($service, $settings->site_url);
        $this->importSearchPages($service, $settings->site_url);

        Notification::make()
            ->title('Search Console import completed successfully.')
            ->body('All Search Console data has been imported and KPIs have been synced.')
            ->success()
            ->send();
    }

    private function importSearchQueries(SearchConsole $service, string $siteUrl): void
    {
        $this->importSearchData(
            $service,
            $siteUrl,
            dimensions: ['date', 'query', 'country', 'device'],
            modelClass: SearchQuery::class,
            keyFieldName: 'query',
        );
    }

    private function importSearchPages(SearchConsole $service, string $siteUrl): void
    {
        $this->importSearchData(
            $service,
            $siteUrl,
            dimensions: ['date', 'page', 'country', 'device'],
            modelClass: SearchPage::class,
            keyFieldName: 'page_url',
        );
    }

    /**
     * Import search data from Google Search Console API.
     *
     * @param  array<string>  $dimensions
     * @param  class-string<Model>  $modelClass
     */
    private function importSearchData(
        SearchConsole $service,
        string $siteUrl,
        array $dimensions,
        string $modelClass,
        string $keyFieldName,
    ): void {
        $request = $this->createSearchRequest($dimensions);
        $response = $service->searchanalytics->query($siteUrl, $request);

        if (! $response->getRows()) {
            return;
        }

        $this->processRows(
            collect($response->getRows()),
            $modelClass,
            $keyFieldName,
        );
    }

    /**
     * Process and upsert rows from the API response.
     *
     * @param  Collection<int, object>  $rows
     * @param  class-string<Model>  $modelClass
     */
    private function processRows(Collection $rows, string $modelClass, string $keyFieldName): void
    {
        $rows->groupBy(fn ($row): string => implode('|', array_slice($row->getKeys(), 0, 4)))
            ->each(function (Collection $groupedRows) use ($modelClass, $keyFieldName): void {
                $first = $groupedRows->first();
                $keys = $first->getKeys();

                $values = $this->calculateAggregatedValues($groupedRows);

                $this->upsertRecord(
                    $modelClass,
                    $keyFieldName,
                    $keys,
                    $values,
                );
            });
    }

    /**
     * Calculate aggregated values from grouped rows.
     *
     * @param  Collection<int, object>  $rows
     * @return array{team_id: int, impressions: int, clicks: int, ctr: float, position: float}
     */
    private function calculateAggregatedValues(Collection $rows): array
    {
        return [
            'team_id' => $this->teamId,
            'impressions' => $rows->sum(fn ($r): int => (int) $r->getImpressions()),
            'clicks' => $rows->sum(fn ($r): int => (int) $r->getClicks()),
            'ctr' => $rows->avg(fn ($r): float => $r->getCtr() * 100),
            'position' => $rows->avg(fn ($r): float => $r->getPosition()),
        ];
    }

    /**
     * Upsert a record in the database.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string>  $keys
     * @param  array<string, mixed>  $values
     */
    private function upsertRecord(string $modelClass, string $keyFieldName, array $keys, array $values): void
    {
        $record = $modelClass::query()
            ->withoutGlobalScope('team')
            ->where('team_id', $this->teamId)
            ->whereDate('date', $keys[0])
            ->where($keyFieldName, $keys[1])
            ->where('country', $keys[2])
            ->where('device', $keys[3])
            ->first();

        if ($record) {
            $record->update($values);

            return;
        }

        $modelClass::query()->create([
            'date' => $keys[0],
            $keyFieldName => $keys[1],
            'country' => $keys[2],
            'device' => $keys[3],
            ...$values,
        ]);
    }

    /**
     * @param  array<string>  $dimensions
     */
    private function createSearchRequest(array $dimensions): SearchAnalyticsQueryRequest
    {
        $request = new SearchAnalyticsQueryRequest();
        $request->setStartDate(Date::now()->subDays(self::DATE_RANGE_DAYS)->format('Y-m-d'));
        $request->setEndDate(Date::now()->format('Y-m-d'));
        $request->setDimensions($dimensions);
        $request->setRowLimit(self::ROW_LIMIT);

        return $request;
    }

    private function failWithNotification(string $title, string $body): void
    {
        Notification::make()->title($title)->body($body)->danger()->send();
        $this->fail($title);
    }
}
