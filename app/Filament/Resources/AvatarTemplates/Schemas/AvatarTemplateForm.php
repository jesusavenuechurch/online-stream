<?php

namespace App\Filament\Resources\AvatarTemplates\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\View; // Import the view component
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AvatarTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visual Preview')
                    ->schema([
                        View::make('filament.components.avatar-preview')
                    ]),

                Section::make('Template Details')
                    ->schema([
                        TextInput::make('title')->required(),

                        FileUpload::make('poster_path')
                            ->image()
                            ->directory('avatar-templates')
                            ->disk('public')
                            ->required()
                            ->imageEditor()
                            ->live(), // Crucial: Makes preview update on upload

                        Toggle::make('is_active')->default(true),
                    ]),

                Section::make('Photo Frame Settings')
                    ->schema([
                        Select::make('frame_shape')
                            ->options(['circle' => 'Circle', 'square' => 'Square'])
                            ->default('circle')
                            ->live(), // Updates preview shape

                        TextInput::make('frame_size')
                            ->numeric()
                            ->default(200)
                            ->suffix('px')
                            ->live(), // Updates preview size

                        TextInput::make('frame_x')
                            ->label('X (Horizontal)')
                            ->numeric()
                            ->default(0)
                            ->live(),

                        TextInput::make('frame_y')
                            ->label('Y (Vertical)')
                            ->numeric()
                            ->default(0)
                            ->live(),
                    ])->columns(2),
            ]);
    }
}