<?php

declare(strict_types=1);

namespace App\Filament\Resources\AnalyticsPageviews;

use App\Enums\AnalyticsSortEnum;
use App\Enums\NavigationGroup;
use App\Filament\Resources\AnalyticsPageviews\Pages\CreateAnalyticsPageview;
use App\Filament\Resources\AnalyticsPageviews\Pages\EditAnalyticsPageview;
use App\Filament\Resources\AnalyticsPageviews\Pages\ListAnalyticsPageviews;
use App\Filament\Resources\AnalyticsPageviews\Schemas\AnalyticsPageviewForm;
use App\Filament\Resources\AnalyticsPageviews\Tables\AnalyticsPageviewsTable;
use App\Models\AnalyticsPageview;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class AnalyticsPageviewResource extends Resource
{
    protected static ?string $model = AnalyticsPageview::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Analytics;

    protected static ?int $navigationSort = AnalyticsSortEnum::AnalyticsPageview->value;

    public static function form(Schema $schema): Schema
    {
        return AnalyticsPageviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnalyticsPageviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnalyticsPageviews::route('/'),
            'create' => CreateAnalyticsPageview::route('/create'),
            'edit' => EditAnalyticsPageview::route('/{record}/edit'),
        ];
    }
}
