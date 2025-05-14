<?php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use App\Filament\Resources\JenisSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisSurat extends EditRecord
{
    protected static string $resource = JenisSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Override afterSave untuk redirect setelah save
    protected function afterSave()
    {
        // Redirect ke halaman index setelah berhasil edit
        return redirect($this->getResource()::getUrl('index'));
    }
}
