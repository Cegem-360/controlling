<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Enums\KpiGoalType;
use App\Enums\KpiValueType;
use App\Models\Kpi;
use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

final class SetSearchConsoleKpiGoalAction
{
    public static function make(Closure $getTopPages, Closure $getTopQueries): Action
    {
        return Action::make('setKpiGoal')
            ->label(__('Set KPI Goal'))
            ->slideOver()
            ->stickyModalFooter()
            ->schema(function () use ($getTopPages, $getTopQueries): array {
                $topPages = $getTopPages();
                $topQueries = $getTopQueries();

                $pageOptions = collect($topPages)->mapWithKeys(fn (array $page): array => [$page['page_url'] => "{$page['page_url']} (Clicks: {$page['clicks']})"])->toArray();

                $queryOptions = collect($topQueries)->mapWithKeys(fn (array $query): array => [$query['query'] => "{$query['query']} (Clicks: {$query['clicks']})"])->toArray();

                return [
                    Select::make('source_type')
                        ->label(__('Source Type'))
                        ->options([
                            'page' => __('Search Page'),
                            'query' => __('Search Query'),
                        ])
                        ->default('page')
                        ->required()
                        ->live()
                        ->helperText(__('Choose whether to track a page or a search query')),

                    Select::make('page_path')
                        ->label(__('Select Search Console Page'))
                        ->options($pageOptions)
                        ->required(fn ($get): bool => $get('source_type') === 'page')
                        ->visible(fn ($get): bool => $get('source_type') === 'page')
                        ->searchable()
                        ->preload()
                        ->helperText(__('Choose a page from your Search Console data')),

                    Select::make('query')
                        ->label(__('Select Search Query'))
                        ->options($queryOptions)
                        ->required(fn ($get): bool => $get('source_type') === 'query')
                        ->visible(fn ($get): bool => $get('source_type') === 'query')
                        ->searchable()
                        ->preload()
                        ->helperText(__('Choose a search query from your Search Console data')),

                    Select::make('metric_type')
                        ->label(__('Select Metric'))
                        ->options([
                            'impressions' => __('Impressions'),
                            'clicks' => __('Clicks'),
                            'ctr' => __('CTR (%)'),
                            'position' => __('Position'),
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
                ];
            })
            ->action(function (array $data): void {
                $sourceType = $data['source_type'];
                $sourceValue = $sourceType === 'page' ? $data['page_path'] : $data['query'];
                $metricType = $data['metric_type'];

                $kpiCode = 'search_console_' . $sourceType . '_' . str_replace(['/', ' ', '.', '?'], ['_', '_', '_', '_'], $sourceValue) . '_' . $metricType;

                // Determine format based on metric type
                $format = match ($metricType) {
                    'ctr' => 'percentage',
                    'impressions', 'clicks', 'position' => 'number',
                    default => 'number',
                };

                $kpi = Kpi::query()->updateOrCreate(
                    [
                        'code' => $kpiCode,
                        'team_id' => Filament::getTenant()->id,
                    ],
                    [
                        'name' => "{$sourceValue} - {$metricType}",
                        'description' => "Track {$metricType} for " . ($sourceType === 'page' ? 'page' : 'query') . " {$sourceValue}",
                        'data_source' => 'search_console',
                        'source_type' => $sourceType,
                        'category' => 'seo',
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
            })
            ->closeModalByClickingAway(false);
    }
}
