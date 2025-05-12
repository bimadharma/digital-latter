<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisSuratResource\Pages;
use App\Filament\Resources\JenisSuratResource\RelationManagers;
use App\Models\JenisSurat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('kode_jenis')
                ->required(),
            Forms\Components\TextInput::make('nama_jenis')
                ->required(),
            Forms\Components\TextInput::make('deskripsi')
                ->required(),
    
            // Upload file Word/PDF
            Forms\Components\FileUpload::make('template_file')
                ->label('Template File (Word/PDF)')
                ->disk('public') // Simpan di storage/app/public
                ->directory('template-files') // Folder tujuan
                ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->required()
                ->helperText('Unggah file template dalam format .pdf, .doc, atau .docx.'),
    
            // Repeater untuk field dinamis
            Forms\Components\Repeater::make('template_fields')
                ->label('Template Fields')
                ->schema([
                    Forms\Components\TextInput::make('field_name')
                        ->label('Nama Field')
                        ->required(),
                    Forms\Components\TextInput::make('field_value')
                        ->label('Nilai Field')
                        ->required(),
                ])
                ->createItemButtonLabel('Tambah Field Baru')
                ->maxItems(10)
                ->helperText('Tambahkan lebih banyak field sesuai kebutuhan.')
                ->afterStateUpdated(function ($state) {
                    return json_encode($state);
                }),
        ]);
        
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_jenis')->label('Kode Jenis')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_jenis')->label('Nama Jenis')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi')->limit(50),
                Tables\Columns\TextColumn::make('template_file')->label('File Template')->url(fn ($record) => asset('storage/' . $record->template_file), true)->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisSurats::route('/'),
            'create' => Pages\CreateJenisSurat::route('/create'),
            'edit' => Pages\EditJenisSurat::route('/{record}/edit'),
        ];
    }
}
