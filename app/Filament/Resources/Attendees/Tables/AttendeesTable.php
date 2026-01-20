<?php

namespace App\Filament\Resources\Attendees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class AttendeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->badge()
                    ->color('blue')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold')
                    ->description(fn ($record) => $record->username)
                    ->sortable(),
                
                TextColumn::make('username')
                    ->searchable()
                    ->copyable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('group.name')
                    ->label('Group')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('church_name')
                    ->label('Church/Branch')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pastor' => 'warning',
                        'member' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'pastor' => 'Pastor',
                        'member' => 'Member',
                    ])
                    ->label('Member Type'),
                
                SelectFilter::make('zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Zone'),
                
                SelectFilter::make('group')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Group'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}