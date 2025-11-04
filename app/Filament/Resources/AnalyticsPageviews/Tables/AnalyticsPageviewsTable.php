<?php

declare(strict_types=1);

namespace App\Filament\Resources\AnalyticsPageviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AnalyticsPageviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('page_title')
                    ->searchable(),
                TextColumn::make('pageviews')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unique_pageviews')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_time_on_page')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entrances')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bounce_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exit_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}
