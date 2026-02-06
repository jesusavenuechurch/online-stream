<?php

namespace App\Filament\Resources\AvatarTemplates\Pages;

use App\Filament\Resources\AvatarTemplates\AvatarTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAvatarTemplate extends EditRecord
{
    protected static string $resource = AvatarTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
