<?php

namespace App\Filament\Resources\Frames\Schemas;

use Filament\Schemas\Schema;
use App\Models\Frame;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;

class FrameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Frame Details')
                ->schema([
 
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                            if ($operation === 'create' && filled($state)) {
                                $set('slug', Frame::generateSlug($state));
                            }
                        }),
 
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->prefix(url('/frame') . '/')
                        ->helperText('Auto-generated from name. You can override it.')
                        ->rules(['alpha_dash']),
 
                    Toggle::make('is_active')
                        ->label('Active')
                        ->helperText('Inactive frames return a 404 for guests.')
                        ->default(true)
                        ->inline(false),
 
                ])->columns(2),
 
            Section::make('Frame Image')
                ->schema([
 
                    FileUpload::make('frame_path')
                        ->label('Frame PNG (with transparent hole)')
                        ->required()
                        ->image()
                        ->acceptedFileTypes(['image/png'])
                        ->disk('public')
                        ->directory('frames')
                        ->visibility('public')
                        ->imagePreviewHeight('320')
                        ->maxSize(5120)
                        ->helperText('PNG with a transparent cutout where the user\'s photo will appear. Square (1:1) recommended.'),
 
                    FileUpload::make('thumbnail_path')
                        ->label('Thumbnail (optional)')
                        ->image()
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                        ->disk('public')
                        ->directory('frames/thumbnails')
                        ->visibility('public')
                        ->imagePreviewHeight('160')
                        ->maxSize(1024)
                        ->helperText('Small preview shown in listings. Falls back to the frame itself if empty.'),
 
                ])->columns(2),
 
            ]);
    }
}
