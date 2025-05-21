<?php

namespace App\Filament\Resources\SuratResource\Pages;

use App\Filament\Resources\SuratResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Infolist;

class ViewSurat extends ViewRecord
{
    protected static string $resource = SuratResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('jenis_surat_id')->label('Jenis Surat ID')->state($this->record->jenis_surat_id),
                TextEntry::make('nomor_surat')->label('Nomor Surat')->state($this->record->nomor_surat),
                TextEntry::make('username')->label('Username')->state($this->record->username),
                TextEntry::make('created_at')->label('Tanggal Dibuat')->state($this->record->created_at->format('d M Y, H:i')),
                KeyValueEntry::make('isi_data')
                ->label('Isi Data')
                ->state(json_decode($this->record->isi_data, true)),
            ]);
    }
}
