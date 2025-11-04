<?php

declare(strict_types=1);

namespace App\Filament\Resources\AnalyticsSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AnalyticsSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('sessions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('users')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('new_users')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bounce_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_session_duration')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pages_per_session')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('medium')
                    ->searchable(),
                TextColumn::make('campaign')
                    ->searchable(),
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
