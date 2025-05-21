<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisSuratResource\Pages;
use App\Filament\Resources\JenisSuratResource\RelationManagers;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('kode_jenis'),
                Forms\Components\TextInput::make('nama_jenis')
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->required(),

                // Upload file Word
                Forms\Components\FileUpload::make('template_file')
                    ->label('Template File (Word)')
                    ->disk('public') // Simpan di storage/app/public
                    ->directory('template-files') // Folder tujuan
                    ->acceptedFileTypes([
                        'application/msword', // .doc
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
                    ])
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return $file->getClientOriginalName(); // Gunakan nama asli file
                    })
                    ->required()
                    ->helperText('Unggah file template dalam format .doc atau .docx saja.'),


                Forms\Components\Repeater::make('template_fields')
                    ->label('Template Fields')
                    ->schema([
                        Forms\Components\TextInput::make('field_name')
                            ->label('Nama Field')
                            ->required(),

                        Forms\Components\Select::make('field_type')
                            ->label('Tipe Field')
                            ->options([
                                'text' => 'Text Biasa',
                                'textarea' => 'Textarea (Multi Baris)',
                                'table' => 'Tabel Dinamis',
                                'signature' => 'Tanda Tangan (Upload Gambar)',
                            ])
                            ->required()
                            ->default('text')
                            ->reactive(),

                        // TABLE
                        Forms\Components\Repeater::make('columns')
                            ->label('Kolom (khusus jika tabel)')
                            ->visible(fn($get) => $get('field_type') === 'table')
                            ->schema([
                                Forms\Components\TextInput::make('column_name')
                                    ->label('Nama Kolom')
                                    ->required(),
                            ]),                
                    ])
                    ->addActionLabel('Tambah Field Baru')
                    ->helperText('Pilih jenis field sesuai dengan struktur templatemu.')
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_jenis')->label('Kode Jenis')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_jenis')->label('Nama Jenis')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi')->limit(50),
                Tables\Columns\TextColumn::make('template_file')->label('File Template')->url(fn($record) => asset('storage/' . $record->template_file), true)->openUrlInNewTab(),
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
            'view' => Pages\ViewJenisSurat::route('/{record}'),
        ];
    }
}
