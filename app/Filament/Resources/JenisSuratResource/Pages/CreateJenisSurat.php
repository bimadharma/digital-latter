<?php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\JenisSuratResource;

class CreateJenisSurat extends CreateRecord
{
    protected static string $resource = JenisSuratResource::class;

    // Override the afterSave method to redirect
    protected function afterSave()
    {
        // Redirect to the list page after saving the record
        return redirect($this->getResource()::getUrl('index'));
    }
}
