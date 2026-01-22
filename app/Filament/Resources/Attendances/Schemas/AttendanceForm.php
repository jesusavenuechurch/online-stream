<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'title')
                    ->required(),
                Select::make('attendee_id')
                    ->relationship('attendee', 'title')
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->required(),
                DateTimePicker::make('left_at'),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
                TextInput::make('session_id'),
            ]);
    }
}
