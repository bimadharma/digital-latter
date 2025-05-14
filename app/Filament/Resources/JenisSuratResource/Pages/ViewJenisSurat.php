<?php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use App\Filament\Resources\JenisSuratResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewJenisSurat extends ViewRecord
{
    protected static string $resource = JenisSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => asset('storage/' . $this->record->template_file))
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }
}
