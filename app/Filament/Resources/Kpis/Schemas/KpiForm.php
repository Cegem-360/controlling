<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kpis\Schemas;

use App\Enums\KpiCategory;
use App\Enums\KpiDataSource;
use App\Enums\KpiGoalType;
use App\Enums\KpiValueType;
use App\Models\AnalyticsPageview;
use App\Models\SearchPage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

final class KpiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Source Integration')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Select::make('page_path')
                                    ->label(fn (Get $get): string => match ($get('data_source')) {
                                        KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => 'Page URL',
                                        KpiDataSource::Analytics, KpiDataSource::Analytics->value => 'Page Path',
                                        default => 'Page Path / URL',
                                    })
                                    ->options(fn (Get $get): array => match ($get('data_source')) {
                                        KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => SearchPage::query()
                                            ->distinct()
                                            ->pluck('page_url', 'page_url')
                                            ->toArray(),
                                        KpiDataSource::Analytics, KpiDataSource::Analytics->value => AnalyticsPageview::query()
                                            ->distinct()
                                            ->pluck('page_path', 'page_path')
                                            ->toArray(),
                                        default => [],
                                    })
                                    ->searchable()
                                    ->helperText(fn (Get $get): string => match ($get('data_source')) {
                                        KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => 'Search Console page URL being tracked',
                                        KpiDataSource::Analytics, KpiDataSource::Analytics->value => 'Analytics page path being tracked',
                                        default => 'Page identifier',
                                    })
                                    ->visible(fn (Get $get): bool => KpiDataSource::isIntegrationSource($get('data_source'))),
                                Select::make('metric_type')
                                    ->label('Metric Type')
                                    ->options(fn (Get $get): array => match ($get('data_source')) {
                                        KpiDataSource::SearchConsole, KpiDataSource::SearchConsole->value => [
                                            'impressions' => 'Impressions',
                                            'clicks' => 'Clicks',
                                            'ctr' => 'CTR (%)',
                                            'position' => 'Position',
                                        ],
                                        KpiDataSource::Analytics, KpiDataSource::Analytics->value => [
                                            'pageviews' => 'Pageviews',
                                            'unique_pageviews' => 'Unique Pageviews',
                                            'bounce_rate' => 'Bounce Rate',
                                        ],
                                        default => [],
                                    })
                                    ->helperText('Select the metric you want to track')
                                    ->visible(fn (Get $get): bool => KpiDataSource::isIntegrationSource($get('data_source')))
                                    ->required(fn (Get $get): bool => KpiDataSource::isIntegrationSource($get('data_source'))),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (Get $get): bool => KpiDataSource::isIntegrationSource($get('data_source'))),
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->required()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                                TextInput::make('name')
                                    ->required()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                                Select::make('data_source')
                                    ->options(KpiDataSource::class)
                                    ->required()
                                    ->live()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                                Select::make('category')
                                    ->options(KpiCategory::class)
                                    ->required()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                                Toggle::make('is_active')
                                    ->required(),
                            ]),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ]),
                Section::make('Target & Goal Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('target_value')
                                    ->numeric()
                                    ->label('Target Value')
                                    ->required(),
                                Select::make('goal_type')
                                    ->label('Goal Type')
                                    ->options(KpiGoalType::class)
                                    ->native(false)
                                    ->required(),
                                Select::make('value_type')
                                    ->label('Value Type')
                                    ->options(KpiValueType::class)
                                    ->native(false)
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label('Start Date')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->maxDate(fn (Get $get): mixed => $get('target_date'))
                                    ->helperText('When to start tracking this KPI')
                                    ->required(),
                                DatePicker::make('target_date')
                                    ->label('Target Date')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->minDate(fn (Get $get): mixed => $get('from_date'))
                                    ->helperText('When you want to achieve the target')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('comparison_start_date')
                                    ->label('Comparison Start Date')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->maxDate(fn (Get $get): mixed => $get('comparison_end_date'))
                                    ->helperText('Start date for comparison period')
                                    ->required(),
                                DatePicker::make('comparison_end_date')
                                    ->label('Comparison End Date')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->minDate(fn (Get $get): mixed => $get('comparison_start_date'))
                                    ->helperText('End date for comparison period')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
