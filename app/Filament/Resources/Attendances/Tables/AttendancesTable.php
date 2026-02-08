<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attendee.title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendee.full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendee.zone.name')
                    ->label('Zone')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendee.group.name')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('joined_at')
                    ->label('Joined At')
                    ->dateTime('M j, Y g:i A')  // Show both date AND time
                    ->sortable(),

                TextColumn::make('left_at')
                    ->label('Left At')
                    ->dateTime('M j, Y g:i A')  // Show both date AND time
                    ->sortable()
                    ->placeholder('Still watching'),  // Show text if null

                TextColumn::make('last_ping')
                    ->label('Last Seen')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('attendance_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Any date'),
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->placeholder('Any date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('joined_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('joined_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export Current View')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn($action) => date('Y-m-d-His') . ' - Attendance_Report')
                    ])
            ])
            ->defaultSort('joined_at', 'desc');
    }
}