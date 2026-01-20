<?php
namespace App\Filament\Resources\StreamEvents\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group; // Moved here in v5
use Filament\Schemas\Components\Section; // Moved here in v5
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class StreamEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // MAIN CONTENT (Left 2 Columns)
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Service Information')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->default('New Stream Event'),
                                
                                Textarea::make('description')
                                    ->rows(4)
                                    ->default('Join us for our live stream service.'),
                            ]),

                        Section::make('Recording Configuration')
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('recording_retention')
                                    ->options([
                                        'until_next_stream' => 'Overwrite on next stream',
                                        'days' => 'Specific number of days',
                                        'indefinite' => 'Keep forever',
                                    ])
                                    ->default('until_next_stream')
                                    ->required()
                                    ->live()
                                    ->native(false),

                                TextInput::make('recording_retention_days')
                                    ->numeric()
                                    ->default(7)
                                    ->visible(fn ($get) => $get('recording_retention') === 'days'),
                            ]),
                    ]),

                // SIDEBAR (Right 1 Column)
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Live Controls')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->options([
                                        'scheduled' => 'Scheduled',
                                        'live' => 'Live',
                                        'ended' => 'Ended',
                                    ])
                                    ->colors([
                                        'scheduled' => 'info',
                                        'live' => 'danger',
                                        'ended' => 'gray',
                                    ])
                                    ->default('scheduled')
                                    ->required()
                                    ->inline(),

                                Select::make('visibility')
                                    ->options([
                                        'public' => '🌐 Public Event',
                                        'pastors_only' => '🔒 Pastors Only',
                                    ])
                                    ->default('public')
                                    ->required()
                                    ->native(false),
                            ]),

                        Section::make('Schedule')
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->default(now())
                                    ->native(false),
                                
                                DateTimePicker::make('ended_at')
                                    ->native(false),
                            ]),
                    ]),

                // THE MISSING FIELD: Injected directly into the schema
                TextInput::make('created_by')
                    ->default(fn () => auth()->id())
                    ->required()
                    ->hidden(),
            ]);
    }
}