<?php

declare(strict_types=1);

namespace App\Filament\Resources\KpiValues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class KpiValuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kpi.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('period')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actual_value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('variance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('variance_percentage')
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
            ]);
    }
}
