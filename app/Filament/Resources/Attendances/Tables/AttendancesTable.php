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
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendee.full_name')
                    ->label('Attendee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('joined_at')
                    ->label('Time In')
                    ->dateTime('g:i A')
                    ->sortable(),

                TextColumn::make('left_at')
                    ->label('Time Out')
                    ->dateTime('g:i A')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('attendance_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Start of time')
                            ->default(now()), // Default to today to keep it fast
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->placeholder('End of time')
                            ->default(now()), // Default to today
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
                            ->withFilename(fn($action) => date('Y-m-d') . ' - Attendance_Report')
                    ])
            ])
            ->defaultSort('joined_at', 'desc');
    }
}