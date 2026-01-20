<?php

namespace App\Filament\Resources\StreamEvents\Pages;

use App\Filament\Resources\StreamEvents\StreamEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStreamEvents extends ListRecords
{
    protected static string $resource = StreamEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
