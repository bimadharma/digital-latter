<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SuratResource\Pages;
use App\Models\Surat;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuratResource extends Resource
{
    protected static ?string $model = Surat::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('jenis_surat_id')
                    ->label('Jenis Surat ID')
                    ->required(),

                TextInput::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->required(),

                TextInput::make('username')
                    ->label('Username')
                    ->required(),

                Textarea::make('isi_data')
                    ->label('Isi Data (JSON)')
                    ->rows(6)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('jenis_surat_id')
                    ->label('Jenis Surat'),

                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat'),

                TextColumn::make('username')
                    ->label('Username'),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), 
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurats::route('/'),
            'create' => Pages\CreateSurat::route('/create'),
            'edit' => Pages\EditSurat::route('/{record}/edit'),
            'view' => Pages\ViewSurat::route('/{record}'),
        ];
    }
}
