<?php

declare(strict_types=1);

namespace App\Livewire\Pages\GoogleAds;

use App\Enums\KpiGoalType;
use App\Enums\KpiValueType;
use App\Filament\Pages\Actions\GenerateGoogleAdsReportAction;
use App\Filament\Pages\Actions\SendGoogleAdsReportNowAction;
use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\GoogleAdsDemographic;
use App\Models\GoogleAdsDeviceStat;
use App\Models\GoogleAdsGeographicStat;
use App\Models\GoogleAdsHourlyStat;
use App\Models\Kpi;
use App\Models\Team;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Dashboard extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Team $team = null;

    public string $dateRangeType = 'last_month';

    /** @var array<string, mixed> */
    public array $stats = [];

    /** @var array<string, mixed> */
    public array $previousStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $campaigns = [];

    /** @var array<int, array<string, mixed>> */
    public array $adGroups = [];

    /** @var array<int, array<string, mixed>> */
    public array $dailyStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $hourlyStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $deviceStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $genderStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $ageStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $geographicStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $monthlyStats = [];

    /** @var array<int, array<string, mixed>> */
    public array $previousYearMonthlyStats = [];

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->dateRangeType = Session::get('google_ads_date_range', 'last_month');
        $this->loadGoogleAdsData();
    }

    public function setDateRange(string $type): void
    {
        $this->dateRangeType = $type;
        session(['google_ads_date_range' => $type]);
        $this->loadGoogleAdsData();
    }

    /**
     * @return array{start: CarbonInterface, end: CarbonInterface}
     */
    public function getDateRange(): array
    {
        return match ($this->dateRangeType) {
            '7_days' => [
                'start' => now()->subDays(7)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            '28_days' => [
                'start' => now()->subDays(28)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_month' => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            'this_month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfDay(),
            ],
            '3_months' => [
                'start' => now()->subMonths(3)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            default => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
        };
    }

    /**
     * @return array{start: CarbonInterface, end: CarbonInterface}
     */
    public function getPreviousDateRange(): array
    {
        $current = $this->getDateRange();
        $daysDiff = $current['start']->diffInDays($current['end']);

        return [
            'start' => $current['start']->copy()->subDays($daysDiff + 1),
            'end' => $current['start']->copy()->subDay(),
        ];
    }

    public function getDateRangeLabel(): string
    {
        $range = $this->getDateRange();

        return $range['start']->format('Y. M j.') . ' - ' . $range['end']->format('Y. M j.');
    }

    public function render(): View
    {
        return view('livewire.pages.google-ads.dashboard');
    }

    public function setKpiGoalAction(): Action
    {
        $campaignOptions = collect($this->campaigns)->mapWithKeys(fn (array $campaign): array => [
            $campaign['campaign_id'] => "{$campaign['campaign_name']} (Clicks: {$campaign['clicks']})",
        ])->toArray();

        $adGroupOptions = collect($this->adGroups)->mapWithKeys(fn (array $adGroup): array => [
            $adGroup['ad_group_name'] => "{$adGroup['campaign_name']} → {$adGroup['ad_group_name']} (Clicks: {$adGroup['clicks']})",
        ])->toArray();

        return Action::make('setKpiGoal')
            ->label(__('Set KPI Goal'))
            ->icon('heroicon-o-chart-bar')
            ->slideOver()
            ->stickyModalFooter()
            ->schema([
                Select::make('source_type')
                    ->label(__('Source Type'))
                    ->options([
                        'campaign' => __('Campaign'),
                        'ad_group' => __('Ad Group'),
                    ])
                    ->default('campaign')
                    ->required()
                    ->live()
                    ->helperText(__('Choose whether to track a campaign or an ad group')),

                Select::make('campaign_id')
                    ->label(__('Select Campaign'))
                    ->options($campaignOptions)
                    ->required(fn ($get): bool => $get('source_type') === 'campaign')
                    ->visible(fn ($get): bool => $get('source_type') === 'campaign')
                    ->searchable()
                    ->preload()
                    ->helperText(__('Choose a campaign from your Google Ads data')),

                Select::make('ad_group_name')
                    ->label(__('Select Ad Group'))
                    ->options($adGroupOptions)
                    ->required(fn ($get): bool => $get('source_type') === 'ad_group')
                    ->visible(fn ($get): bool => $get('source_type') === 'ad_group')
                    ->searchable()
                    ->preload()
                    ->helperText(__('Choose an ad group from your Google Ads data')),

                Select::make('metric_type')
                    ->label(__('Select Metric'))
                    ->options([
                        'impressions' => __('Impressions'),
                        'clicks' => __('Clicks'),
                        'cost' => __('Cost'),
                        'conversions' => __('Conversions'),
                        'ctr' => __('CTR (%)'),
                        'avg_cpc' => __('Avg. CPC'),
                        'conversion_rate' => __('Conversion Rate (%)'),
                        'cost_per_conversion' => __('Cost per Conversion'),
                    ])
                    ->required()
                    ->native(false)
                    ->helperText(__('Choose which metric to track')),

                DatePicker::make('from_date')
                    ->live()
                    ->label(__('Start Date'))
                    ->required()
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->default(now())
                    ->maxDate(fn ($get) => $get('target_date'))
                    ->helperText(__('When to start tracking this KPI')),

                DatePicker::make('target_date')
                    ->live()
                    ->label(__('Target Date'))
                    ->required()
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->minDate(fn ($get) => $get('from_date') ?? now())
                    ->helperText(__('When you want to achieve the target')),

                DatePicker::make('comparison_start_date')
                    ->live()
                    ->label(__('Comparison Start Date'))
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->maxDate(fn ($get) => $get('comparison_end_date'))
                    ->helperText(__('Start date for comparison period')),

                DatePicker::make('comparison_end_date')
                    ->live()
                    ->label(__('Comparison End Date'))
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->minDate(fn ($get) => $get('comparison_start_date'))
                    ->helperText(__('End date for comparison period')),

                Select::make('goal_type')
                    ->label(__('Goal Type'))
                    ->options([
                        KpiGoalType::Increase->value => __('Increase'),
                        KpiGoalType::Decrease->value => __('Decrease'),
                    ])
                    ->required()
                    ->native(false),

                Select::make('value_type')
                    ->label(__('Value Type'))
                    ->options([
                        KpiValueType::Percentage->value => __('Percentage (%)'),
                        KpiValueType::Fixed->value => __('Fixed Number'),
                    ])
                    ->required()
                    ->native(false)
                    ->live(),

                TextInput::make('target_value')
                    ->label(fn ($get): string => $get('value_type') === KpiValueType::Percentage->value ? __('Target Percentage (%)') : __('Target Value'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix(fn ($get): ?string => $get('value_type') === KpiValueType::Percentage->value ? '%' : null),
            ])
            ->action(function (array $data): void {
                $this->saveKpiGoal($data);
            })
            ->closeModalByClickingAway(false);
    }

    public function generateReportAction(): Action
    {
        return GenerateGoogleAdsReportAction::make($this->team);
    }

    public function sendReportNowAction(): Action
    {
        return SendGoogleAdsReportNowAction::make($this->team);
    }

    /**
     * Calculate percentage change between current and previous values.
     */
    public function getPercentageChange(string $key): float
    {
        $current = (float) ($this->stats[$key] ?? 0);
        $previous = (float) ($this->previousStats[$key] ?? 0);

        if (abs($previous) < 0.0001) {
            return $current > 0.0001 ? 100.0 : 0.0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveKpiGoal(array $data): void
    {
        $sourceType = $data['source_type'];
        $metricType = $data['metric_type'];

        if ($sourceType === 'campaign') {
            $campaignId = $data['campaign_id'];
            $campaignData = collect($this->campaigns)->firstWhere('campaign_id', $campaignId);
            $sourceName = $campaignData['campaign_name'] ?? $campaignId;
            $sourceValue = $campaignId;
        } else {
            $adGroupName = $data['ad_group_name'];
            $adGroupData = collect($this->adGroups)->firstWhere('ad_group_name', $adGroupName);
            $sourceName = $adGroupData ? "{$adGroupData['campaign_name']} → {$adGroupName}" : $adGroupName;
            $sourceValue = $adGroupName;
        }

        $kpiCode = 'google_ads_' . $sourceType . '_' . str_replace(['/', ' ', '.', '?', '→'], ['_', '_', '_', '_', '_'], (string) $sourceValue) . '_' . $metricType;

        $format = match ($metricType) {
            'ctr', 'conversion_rate' => 'percentage',
            'cost', 'avg_cpc', 'cost_per_conversion' => 'number',
            'impressions', 'clicks', 'conversions' => 'number',
            default => 'number',
        };

        $kpi = Kpi::query()->updateOrCreate(
            [
                'code' => $kpiCode,
                'team_id' => $this->team->id,
            ],
            [
                'name' => "{$sourceName} - {$metricType}",
                'description' => __('Track :metric for :type :name', [
                    'metric' => $metricType,
                    'type' => $sourceType === 'campaign' ? __('campaign') : __('ad group'),
                    'name' => $sourceName,
                ]),
                'data_source' => 'google_ads',
                'source_type' => $sourceType,
                'category' => 'conversion',
                'format' => $format,
                'page_path' => $sourceValue,
                'metric_type' => $metricType,
                'from_date' => $data['from_date'] ?? now(),
                'target_date' => $data['target_date'],
                'comparison_start_date' => $data['comparison_start_date'] ?? null,
                'comparison_end_date' => $data['comparison_end_date'] ?? null,
                'goal_type' => $data['goal_type'],
                'value_type' => $data['value_type'],
                'target_value' => $data['target_value'],
                'is_active' => true,
            ],
        );

        Notification::make()
            ->title(__('KPI Goal Set Successfully'))
            ->success()
            ->body(__('Your goal for **:name** has been saved.', ['name' => $kpi->name]))
            ->actions([
                Action::make('view')
                    ->label(__('View KPI'))
                    ->url(route('kpis.show', $kpi)),
            ])
            ->send();
    }

    private function loadGoogleAdsData(): void
    {
        $dateRange = $this->getDateRange();
        $previousRange = $this->getPreviousDateRange();

        $this->loadStats($dateRange, $previousRange);
        $this->loadCampaigns($dateRange);
        $this->loadAdGroups($dateRange);
        $this->loadDailyStats($dateRange);
        $this->loadHourlyStats($dateRange);
        $this->loadDeviceStats($dateRange);
        $this->loadDemographicStats($dateRange);
        $this->loadGeographicStats($dateRange);
        $this->loadMonthlyStats();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $previousRange
     */
    private function loadStats(array $dateRange, array $previousRange): void
    {
        $this->stats = $this->calculateStats($dateRange);
        $this->previousStats = $this->calculateStats($previousRange);
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     * @return array<string, mixed>
     */
    private function calculateStats(array $dateRange): array
    {
        $data = GoogleAdsCampaign::query()
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks,
                SUM(cost) as total_cost,
                SUM(conversions) as total_conversions
            ')
            ->first();

        $totalImpressions = (int) ($data->total_impressions ?? 0);
        $totalClicks = (int) ($data->total_clicks ?? 0);
        $totalCost = (float) ($data->total_cost ?? 0);
        $totalConversions = (float) ($data->total_conversions ?? 0);

        return [
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'total_cost' => $totalCost,
            'total_conversions' => $totalConversions,
            'avg_ctr' => $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0,
            'avg_cpc' => $totalClicks > 0 ? $totalCost / $totalClicks : 0,
            'cost_per_conversion' => $totalConversions > 0 ? $totalCost / $totalConversions : 0,
            'conversion_rate' => $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0,
        ];
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadCampaigns(array $dateRange): void
    {
        $this->campaigns = GoogleAdsCampaign::query()
            ->select(
                'campaign_id',
                'campaign_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('campaign_id', 'campaign_name')
            ->orderByDesc('total_impressions')
            ->limit(20)
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'campaign_id' => $item->campaign_id,
                'campaign_name' => $item->campaign_name,
            ]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadAdGroups(array $dateRange): void
    {
        $this->adGroups = GoogleAdsAdGroup::query()
            ->select(
                'campaign_name',
                'ad_group_id',
                'ad_group_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('campaign_name', 'ad_group_id', 'ad_group_name')
            ->orderByDesc('total_impressions')
            ->limit(30)
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'campaign_name' => $item->campaign_name,
                'ad_group_name' => $item->ad_group_name,
            ]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadDailyStats(array $dateRange): void
    {
        $this->dailyStats = GoogleAdsCampaign::query()
            ->select(
                'date',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'date' => $item->date->format('Y-m-d'),
                'date_formatted' => $item->date->translatedFormat('Y. M j.'),
                'day_name' => $item->date->translatedFormat('l'),
            ]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadHourlyStats(array $dateRange): void
    {
        $this->hourlyStats = GoogleAdsHourlyStat::query()
            ->select(
                'hour',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, ['hour' => $item->hour]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadDeviceStats(array $dateRange): void
    {
        $this->deviceStats = GoogleAdsDeviceStat::query()
            ->select(
                'device',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('device')
            ->orderByDesc('total_impressions')
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'device' => $this->getDeviceName($item->device),
            ]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadDemographicStats(array $dateRange): void
    {
        $this->genderStats = GoogleAdsDemographic::query()
            ->select(
                'gender',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->orderByDesc('total_impressions')
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'gender' => $this->getGenderName($item->gender),
            ]))
            ->toArray();

        $this->ageStats = GoogleAdsDemographic::query()
            ->select(
                'age_range',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('age_range')
            ->groupBy('age_range')
            ->orderByDesc('total_impressions')
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'age_range' => $this->getAgeRangeName($item->age_range),
            ]))
            ->toArray();
    }

    /**
     * @param  array{start: CarbonInterface, end: CarbonInterface}  $dateRange
     */
    private function loadGeographicStats(array $dateRange): void
    {
        $this->geographicStats = GoogleAdsGeographicStat::query()
            ->select(
                'location_name',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('location_name')
            ->orderByDesc('total_impressions')
            ->limit(20)
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'location_name' => $item->location_name,
            ]))
            ->toArray();
    }

    private function loadMonthlyStats(): void
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite' ? 'strftime(\'%Y\', date)' : 'YEAR(date)';
        $monthExpr = $driver === 'sqlite' ? 'strftime(\'%m\', date)' : 'MONTH(date)';

        // Cast years to string for SQLite compatibility (strftime returns string)
        $currentYear = (string) now()->year;
        $previousYear = (string) now()->subYear()->year;

        $this->monthlyStats = GoogleAdsCampaign::query()
            ->select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereRaw("{$yearExpr} = ?", [$currentYear])
            ->groupBy(DB::raw($yearExpr), DB::raw($monthExpr))
            ->orderByRaw("{$yearExpr} desc, {$monthExpr} desc")
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'month' => now()->setMonth((int) $item->month)->translatedFormat('Y. F'),
            ]))
            ->toArray();

        $this->previousYearMonthlyStats = GoogleAdsCampaign::query()
            ->select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('SUM(conversions) as total_conversions'),
            )
            ->whereRaw("{$yearExpr} = ?", [$previousYear])
            ->groupBy(DB::raw($yearExpr), DB::raw($monthExpr))
            ->orderByRaw("{$yearExpr} desc, {$monthExpr} desc")
            ->get()
            ->map(fn (object $item): array => $this->mapAggregatedStats($item, [
                'month' => now()->subYear()->setMonth((int) $item->month)->translatedFormat('Y. F'),
            ]))
            ->toArray();
    }

    private function getDeviceName(string $device): string
    {
        return match ($device) {
            'MOBILE' => __('Mobile'),
            'DESKTOP' => __('Desktop'),
            'TABLET' => __('Tablet'),
            'CONNECTED_TV' => __('Connected TV'),
            default => $device,
        };
    }

    private function getGenderName(?string $gender): string
    {
        return match ($gender) {
            'MALE' => __('Male'),
            'FEMALE' => __('Female'),
            'UNDETERMINED' => __('Undetermined'),
            default => $gender ?? __('Unknown'),
        };
    }

    private function getAgeRangeName(?string $ageRange): string
    {
        return match ($ageRange) {
            'AGE_RANGE_18_24' => '18-24',
            'AGE_RANGE_25_34' => '25-34',
            'AGE_RANGE_35_44' => '35-44',
            'AGE_RANGE_45_54' => '45-54',
            'AGE_RANGE_55_64' => '55-64',
            'AGE_RANGE_65_UP' => '65+',
            'AGE_RANGE_UNDETERMINED' => __('Undetermined'),
            default => $ageRange ?? __('Unknown'),
        };
    }

    /**
     * Map aggregated query results to standardized stats array.
     *
     * @param  object  $item  Database query result with total_impressions, total_clicks, total_cost, total_conversions
     * @param  array<string, mixed>  $extraFields  Additional fields to include in the result
     * @return array<string, mixed>
     */
    private function mapAggregatedStats(object $item, array $extraFields = []): array
    {
        $impressions = (int) $item->total_impressions;
        $clicks = (int) $item->total_clicks;
        $cost = (float) $item->total_cost;
        $conversions = (float) $item->total_conversions;

        return [
            ...$extraFields,
            'impressions' => $impressions,
            'clicks' => $clicks,
            'cost' => round($cost, 2),
            'conversions' => round($conversions, 2),
            ...$this->calculateDerivedMetrics($impressions, $clicks, $cost, $conversions),
        ];
    }

    /**
     * Calculate derived metrics from impressions, clicks, cost, and conversions.
     *
     * @return array{ctr: float, avg_cpc: float, conversion_rate: float, cost_per_conversion: float}
     */
    private function calculateDerivedMetrics(int $impressions, int $clicks, float $cost, float $conversions): array
    {
        return [
            'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0,
            'avg_cpc' => $clicks > 0 ? round($cost / $clicks, 2) : 0,
            'conversion_rate' => $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0,
            'cost_per_conversion' => $conversions > 0 ? round($cost / $conversions, 2) : 0,
        ];
    }
}
