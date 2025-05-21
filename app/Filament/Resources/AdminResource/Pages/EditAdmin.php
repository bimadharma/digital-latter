<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

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
