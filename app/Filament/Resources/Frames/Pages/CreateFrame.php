<?php

namespace App\Filament\Resources\Frames\Pages;

use App\Filament\Resources\Frames\FrameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFrame extends CreateRecord
{
    protected static string $resource = FrameResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
 
        return $data;
    }
 
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
