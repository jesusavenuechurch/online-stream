<?php

namespace App\Filament\Resources\StreamEvents\Pages;

use App\Filament\Resources\StreamEvents\StreamEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStreamEvent extends EditRecord
{
    protected static string $resource = StreamEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
