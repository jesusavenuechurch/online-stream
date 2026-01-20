<?php
namespace App\Filament\Resources\Attendees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Get;

class AttendeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->maxLength(50),
                
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(100),
                
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(100),
                
                TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->helperText('This is the login ID.'),
                
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->maxLength(255),
                
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                
                TextInput::make('church_name')
                    ->label('Church/Branch Name')
                    ->maxLength(255),
                
                Select::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload(),
                
                Select::make('group_id')
                    ->label('Group')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                
                Select::make('type')
                    ->options([
                        'pastor' => 'Pastor',
                        'member' => 'Member',
                    ])
                    ->default('member')
                    ->required()
                    ->native(false),
            ]);
    }
}