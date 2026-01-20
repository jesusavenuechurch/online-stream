<?php

namespace App\Filament\Resources\StreamEvents;

use App\Filament\Resources\StreamEvents\Pages\CreateStreamEvent;
use App\Filament\Resources\StreamEvents\Pages\EditStreamEvent;
use App\Filament\Resources\StreamEvents\Pages\ListStreamEvents;
use App\Filament\Resources\StreamEvents\Schemas\StreamEventForm;
use App\Filament\Resources\StreamEvents\Tables\StreamEventsTable;
use App\Models\StreamEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StreamEventResource extends Resource
{
    protected static ?string $model = StreamEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StreamEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StreamEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStreamEvents::route('/'),
            'create' => CreateStreamEvent::route('/create'),
            'edit' => EditStreamEvent::route('/{record}/edit'),
        ];
    }
}
