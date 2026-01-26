<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Kpis;

use App\Enums\KpiDataSource;
use App\Enums\KpiGoalType;
use App\Enums\KpiValueType;
use App\Models\AnalyticsPageview;
use App\Models\GoogleAdsAdGroup;
use App\Models\GoogleAdsCampaign;
use App\Models\Kpi;
use App\Models\SearchPage;
use App\Models\SearchQuery;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
final class Edit extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Team $team = null;

    public ?Kpi $kpi = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(Kpi $kpi): void
    {
        $this->team = Auth::user()->teams()->first();

        if (! $this->team instanceof Team) {
            return;
        }

        // Verify the KPI belongs to the current team
        if ($kpi->team_id !== $this->team->id) {
            abort(403);
        }

        $this->kpi = $kpi;
        $this->form->fill($this->kpi->attributesToArray());
    }

    public function render(): View
    {
        return view('livewire.pages.kpis.edit');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make(__('Data Source Integration'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('page_path')
                                        ->label(fn (Get $get): string => match ($get('data_source')) {
                                            KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => __('Page URL / Query'),
                                            KpiDataSource::Analytics, KpiDataSource::Analytics->value => __('Page Path'),
                                            KpiDataSource::GoogleAds, KpiDataSource::GoogleAds->value => __('Campaign / Ad Group'),
                                            default => __('Page Path / URL'),
                                        })
                                        ->options(fn (Get $get): array => $this->getPagePathOptions($get('data_source'), $get('source_type')))
                                        ->searchable()
                                        ->helperText(fn (Get $get): string => match ($get('data_source')) {
                                            KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => __('Search Console page URL or query'),
                                            KpiDataSource::Analytics, KpiDataSource::Analytics->value => __('Analytics page path being tracked'),
                                            KpiDataSource::GoogleAds, KpiDataSource::GoogleAds->value => __('Google Ads campaign or ad group'),
                                            default => __('Page identifier'),
                                        }),
                                    Select::make('metric_type')
                                        ->label(__('Metric Type'))
                                        ->options(fn (Get $get): array => $this->getMetricTypeOptions($get('data_source')))
                                        ->helperText(__('Select the metric you want to track'))
                                        ->required(),
                                ]),
                        ])
                        ->collapsible()
                        ->visible(fn (Get $get): bool => KpiDataSource::isIntegrationSource($get('data_source'))),

                    Section::make(__('Basic Information'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('code')
                                        ->label(__('Code'))
                                        ->required()
                                        ->disabled(),
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->required()
                                        ->disabled(),
                                    Select::make('data_source')
                                        ->label(__('Data Source'))
                                        ->options([
                                            KpiDataSource::Analytics->value => __('Google Analytics'),
                                            KpiDataSource::SearchConsole->value => __('Search Console'),
                                            KpiDataSource::GoogleAds->value => __('Google Ads'),
                                            KpiDataSource::Manual->value => __('Manual'),
                                            KpiDataSource::Calculated->value => __('Calculated'),
                                        ])
                                        ->required()
                                        ->live()
                                        ->disabled(),
                                    Select::make('source_type')
                                        ->label(__('Source Type'))
                                        ->options(fn (Get $get): array => match ($get('data_source')) {
                                            KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => [
                                                'page' => __('Page'),
                                                'query' => __('Query'),
                                            ],
                                            KpiDataSource::GoogleAds, KpiDataSource::GoogleAds->value => [
                                                'campaign' => __('Campaign'),
                                                'ad_group' => __('Ad Group'),
                                            ],
                                            default => [],
                                        })
                                        ->live()
                                        ->visible(fn (Get $get): bool => in_array($get('data_source'), [KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value, KpiDataSource::GoogleAds, KpiDataSource::GoogleAds->value], true)),
                                    Toggle::make('is_active')
                                        ->label(__('Active'))
                                        ->required(),
                                ]),
                            Textarea::make('description')
                                ->label(__('Description'))
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('Target & Goal Settings'))
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('target_value')
                                        ->label(__('Target Value'))
                                        ->numeric()
                                        ->required(),
                                    Select::make('goal_type')
                                        ->label(__('Goal Type'))
                                        ->options([
                                            KpiGoalType::Increase->value => __('Increase'),
                                            KpiGoalType::Decrease->value => __('Decrease'),
                                        ])
                                        ->native(false)
                                        ->required(),
                                    Select::make('value_type')
                                        ->label(__('Value Type'))
                                        ->options([
                                            KpiValueType::Percentage->value => __('Percentage'),
                                            KpiValueType::Fixed->value => __('Fixed'),
                                        ])
                                        ->native(false)
                                        ->required(),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('from_date')
                                        ->label(__('Start Date'))
                                        ->native(false)
                                        ->displayFormat('Y-m-d')
                                        ->maxDate(fn (Get $get): mixed => $get('target_date'))
                                        ->helperText(__('When to start tracking this KPI'))
                                        ->required(),
                                    DatePicker::make('target_date')
                                        ->label(__('Target Date'))
                                        ->native(false)
                                        ->displayFormat('Y-m-d')
                                        ->minDate(fn (Get $get): mixed => $get('from_date'))
                                        ->helperText(__('When you want to achieve the target'))
                                        ->required(),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('comparison_start_date')
                                        ->label(__('Comparison Start Date'))
                                        ->native(false)
                                        ->displayFormat('Y-m-d')
                                        ->maxDate(fn (Get $get): mixed => $get('comparison_end_date'))
                                        ->helperText(__('Start date for comparison period'))
                                        ->required(),
                                    DatePicker::make('comparison_end_date')
                                        ->label(__('Comparison End Date'))
                                        ->native(false)
                                        ->displayFormat('Y-m-d')
                                        ->minDate(fn (Get $get): mixed => $get('comparison_start_date'))
                                        ->helperText(__('End date for comparison period'))
                                        ->required(),
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('Save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                            Action::make('cancel')
                                ->label(__('Cancel'))
                                ->color('gray')
                                ->url(route('kpis.show', $this->kpi)),
                        ]),
                    ]),
            ])
            ->record($this->kpi)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (! $this->kpi instanceof Kpi) {
            Notification::make()
                ->title(__('KPI not found'))
                ->danger()
                ->send();

            return;
        }

        $this->kpi->fill($data);
        $this->kpi->save();

        Notification::make()
            ->title(__('KPI updated successfully'))
            ->success()
            ->send();

        $this->redirect(route('kpis.show', $this->kpi));
    }

    /**
     * @return array<string, string>
     */
    private function getPagePathOptions(string|KpiDataSource|null $dataSource, ?string $sourceType): array
    {
        if ($dataSource instanceof KpiDataSource) {
            $dataSource = $dataSource->value;
        }

        return match ($dataSource) {
            'search_console' => $sourceType === 'query'
                ? SearchQuery::query()->where('team_id', $this->team?->id)->distinct()->pluck('query', 'query')->toArray()
                : SearchPage::query()->where('team_id', $this->team?->id)->distinct()->pluck('page_url', 'page_url')->toArray(),
            'analytics' => AnalyticsPageview::query()->where('team_id', $this->team?->id)->distinct()->pluck('page_path', 'page_path')->toArray(),
            'google_ads' => $sourceType === 'campaign'
                ? GoogleAdsCampaign::query()->where('team_id', $this->team?->id)->distinct()->pluck('campaign_name', 'campaign_id')->toArray()
                : GoogleAdsAdGroup::query()->where('team_id', $this->team?->id)->distinct()->pluck('ad_group_name', 'ad_group_name')->toArray(),
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function getMetricTypeOptions(string|KpiDataSource|null $dataSource): array
    {
        if ($dataSource instanceof KpiDataSource) {
            $dataSource = $dataSource->value;
        }

        return match ($dataSource) {
            'search_console' => [
                'impressions' => __('Impressions'),
                'clicks' => __('Clicks'),
                'ctr' => __('CTR (%)'),
                'position' => __('Position'),
            ],
            'analytics' => [
                'pageviews' => __('Pageviews'),
                'unique_pageviews' => __('Unique Pageviews'),
                'bounce_rate' => __('Bounce Rate'),
            ],
            'google_ads' => [
                'impressions' => __('Impressions'),
                'clicks' => __('Clicks'),
                'cost' => __('Cost'),
                'conversions' => __('Conversions'),
                'ctr' => __('CTR (%)'),
                'avg_cpc' => __('Avg. CPC'),
                'conversion_rate' => __('Conversion Rate (%)'),
                'cost_per_conversion' => __('Cost per Conversion'),
            ],
            default => [],
        };
    }
}
