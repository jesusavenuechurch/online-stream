<?php

namespace App\Filament\Resources\AvatarTemplates\Pages;

use App\Filament\Resources\AvatarTemplates\AvatarTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAvatarTemplates extends ListRecords
{
    protected static string $resource = AvatarTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
