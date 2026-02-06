<?php

namespace App\Filament\Resources\AvatarTemplates;

use App\Filament\Resources\AvatarTemplates\Pages\CreateAvatarTemplate;
use App\Filament\Resources\AvatarTemplates\Pages\EditAvatarTemplate;
use App\Filament\Resources\AvatarTemplates\Pages\ListAvatarTemplates;
use App\Filament\Resources\AvatarTemplates\Schemas\AvatarTemplateForm;
use App\Filament\Resources\AvatarTemplates\Tables\AvatarTemplatesTable;
use App\Models\AvatarTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AvatarTemplateResource extends Resource
{
    protected static ?string $model = AvatarTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AvatarTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvatarTemplatesTable::configure($table);
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
            'index' => ListAvatarTemplates::route('/'),
            'create' => CreateAvatarTemplate::route('/create'),
            'edit' => EditAvatarTemplate::route('/{record}/edit'),
        ];
    }
}
