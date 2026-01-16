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

final class SetGoogleAdsKpiGoalAction
{
    public static function make(Closure $getCampaigns, Closure $getAdGroups): Action
    {
        return Action::make('setKpiGoal')
            ->label(__('Set KPI Goal'))
            ->slideOver()
            ->stickyModalFooter()
            ->schema(function () use ($getCampaigns, $getAdGroups): array {
                $campaigns = $getCampaigns();
                $adGroups = $getAdGroups();

                $campaignOptions = collect($campaigns)->mapWithKeys(fn (array $campaign): array => [
                    $campaign['campaign_id'] => "{$campaign['campaign_name']} (Clicks: {$campaign['clicks']})",
                ])->toArray();

                $adGroupOptions = collect($adGroups)->mapWithKeys(fn (array $adGroup): array => [
                    $adGroup['ad_group_name'] => "{$adGroup['campaign_name']} → {$adGroup['ad_group_name']} (Clicks: {$adGroup['clicks']})",
                ])->toArray();

                return [
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
                ];
            })
            ->action(function (array $data) use ($getCampaigns, $getAdGroups): void {
                $sourceType = $data['source_type'];
                $metricType = $data['metric_type'];

                if ($sourceType === 'campaign') {
                    $campaignId = $data['campaign_id'];
                    $campaigns = $getCampaigns();
                    $campaignData = collect($campaigns)->firstWhere('campaign_id', $campaignId);
                    $sourceName = $campaignData['campaign_name'] ?? $campaignId;
                    $sourceValue = $campaignId;
                } else {
                    $adGroupName = $data['ad_group_name'];
                    $adGroups = $getAdGroups();
                    $adGroupData = collect($adGroups)->firstWhere('ad_group_name', $adGroupName);
                    $sourceName = $adGroupData ? "{$adGroupData['campaign_name']} → {$adGroupName}" : $adGroupName;
                    $sourceValue = $adGroupName;
                }

                $kpiCode = 'google_ads_' . $sourceType . '_' . str_replace(['/', ' ', '.', '?', '→'], ['_', '_', '_', '_', '_'], $sourceValue) . '_' . $metricType;

                // Determine format based on metric type
                $format = match ($metricType) {
                    'ctr', 'conversion_rate' => 'percentage',
                    'cost', 'avg_cpc', 'cost_per_conversion' => 'number',
                    'impressions', 'clicks', 'conversions' => 'number',
                    default => 'number',
                };

                $kpi = Kpi::query()->updateOrCreate(
                    [
                        'code' => $kpiCode,
                        'team_id' => Filament::getTenant()->id,
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
                            ->url(KpiResource::getUrl('view', ['record' => $kpi->getRouteKey(), 'tenant' => Filament::getTenant()])),
                    ])
                    ->send();
            })
            ->closeModalByClickingAway(false);
    }
}
