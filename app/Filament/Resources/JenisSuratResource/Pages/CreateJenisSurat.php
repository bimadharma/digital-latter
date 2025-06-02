<?php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\JenisSuratResource;

class CreateJenisSurat extends CreateRecord
{
    protected static string $resource = JenisSuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
