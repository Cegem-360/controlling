<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Enums\KpiGoalType;
use App\Enums\KpiValueType;
use App\Filament\Resources\Kpis\KpiResource;
use App\Models\Kpi;
use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

final class SetAnalyticsKpiGoalAction
{
    public static function make(Closure $getTopPages): Action
    {
        return Action::make('setKpiGoal')
            ->label(__('Set KPI Goal'))
            ->slideOver()
            ->stickyModalFooter()
            ->schema(function () use ($getTopPages): array {
                $topPages = $getTopPages();
                $pageOptions = collect($topPages)->mapWithKeys(fn (array $page): array => [$page['page_path'] => "{$page['page_title']} ({$page['page_path']})"])->toArray();

                return [
                    Select::make('page_path')
                        ->label(__('Select Analytics Page'))
                        ->options($pageOptions)
                        ->required()
                        ->searchable()
                        ->preload()
                        ->helperText(__('Choose a page from your Google Analytics data')),
                    Select::make('metric_type')
                        ->label(__('Select Metric'))
                        ->options([
                            'pageviews' => __('Pageviews'),
                            'unique_pageviews' => __('Unique Pageviews'),
                            'bounce_rate' => __('Bounce Rate'),
                        ])
                        ->required()
                        ->native(false)
                        ->helperText(__('Choose which metric to track')),
                    DatePicker::make('from_date')
                        ->label(__('Start Date'))
                        ->required()
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->default(now())
                        ->maxDate(fn ($get) => $get('target_date'))
                        ->helperText(__('When to start tracking this KPI')),
                    DatePicker::make('target_date')
                        ->label(__('Target Date'))
                        ->required()
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->minDate(fn ($get) => $get('from_date') ?? now())
                        ->helperText(__('When you want to achieve the target')),
                    DatePicker::make('comparison_start_date')
                        ->label(__('Comparison Start Date'))
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->maxDate(fn ($get) => $get('comparison_end_date'))
                        ->helperText(__('Start date for comparison period')),
                    DatePicker::make('comparison_end_date')
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
            ->action(function (array $data) use ($getTopPages): void {
                $pagePath = $data['page_path'];
                $metricType = $data['metric_type'];
                $kpiCode = 'analytics_' . str_replace(['/', ' '], ['_', '_'], $pagePath) . '_' . $metricType;

                $topPages = $getTopPages();
                $pageData = collect($topPages)->firstWhere('page_path', $pagePath);
                $pageTitle = $pageData['page_title'] ?? $pagePath;

                // Determine format based on metric type
                $format = match ($metricType) {
                    'bounce_rate' => 'percentage',
                    'pageviews', 'unique_pageviews' => 'number',
                    default => 'number',
                };

                $kpi = Kpi::query()->updateOrCreate(
                    [
                        'code' => $kpiCode,
                        'team_id' => Filament::getTenant()->id,
                    ],
                    [
                        'name' => "{$pageTitle} - {$metricType}",
                        'description' => "Track {$metricType} for {$pagePath}",
                        'data_source' => 'analytics',
                        'category' => 'traffic',
                        'format' => $format,
                        'page_path' => $pagePath,
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
                            ->url(KpiResource::getUrl('view', ['record' => $kpi->getRouteKey(), 'tenant' => Filament::getTenant()])),
                    ])
                    ->send();
            })
            ->closeModalByClickingAway(false);
    }
}
