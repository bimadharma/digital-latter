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

use Illuminate\Support\Str;
use ZipArchive;



class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('kode_jenis'),

                Forms\Components\TextInput::make('nama_jenis')->required(),

                Forms\Components\TextInput::make('deskripsi')->required(),

                Forms\Components\FileUpload::make('template_file')
                    ->label('Template File (Word)')
                    ->disk('public')
                    ->directory('template-files')
                    ->acceptedFileTypes([
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file) => $file->getClientOriginalName())
                    ->required()
                    ->helperText('Unggah file template dalam format .doc atau .docx saja.'),
                    // ->afterStateUpdated(function (callable $set, callable $get, $state) {
        
                    //     if (!$state instanceof TemporaryUploadedFile) {
                    //         return;
                    //     }

                    //     $path = $state->getRealPath();
                    //     $zip = new ZipArchive;

                    //     if ($zip->open($path) === true) {
                    //         $xml = $zip->getFromName('word/document.xml');
                    //         $zip->close();

                    //         // Gabungkan semua isi <w:t>
                    //         preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/', $xml, $textMatches);
                    //         $fullText = implode('', $textMatches[1]);

                    //         // Ambil placeholder ${...} dari teks gabungan
                    //         preg_match_all('/\$\{\s*([a-zA-Z0-9_\-\s]+)\}/', $fullText, $matches);
                    //         $keys = array_unique(array_map('trim', $matches[1]));

                    //         if (count($keys) > 0) {
                    //             $fields = [];

                    //             foreach ($keys as $key) {
                    //                 $fields[] = [
                    //                     'field_name' => $key,
                    //                     'field_type' => 'text',
                    //                     'columns' => [],
                    //                 ];
                    //             }

                    //             // Set template_fields kosong dulu agar Livewire reset state-nya
                    //             $set('template_fields', []);

                    //             // Paksa render ulang dengan memberi nilai baru
                    //             $set('template_fields', [[
                    //                 'section_title' => 'Isi Form',
                    //                 'fields' => $fields,
                    //             ]]);
                    //         }
                    //     }
                    // }),

                Forms\Components\Repeater::make('template_fields')
                    ->label('Bagian Template')
                    ->schema([
                        Forms\Components\TextInput::make('section_title')
                            ->label('Judul Bagian')
                            ->required(),

                            Forms\Components\Repeater::make('fields')
                            ->label('Field dalam Bagian')
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
                                        'grouped_table' => 'Tabel dengan Kolom Utama',
                                        'signature' => 'Tanda Tangan (Upload Gambar)',
                                    ])
                                    ->required()
                                    ->default('text')
                                    ->reactive(),
                        
                                // Existing table columns
                                Forms\Components\Repeater::make('columns')
                                    ->label('Kolom (khusus jika tabel)')
                                    ->visible(fn($get) => $get('field_type') === 'table')
                                    ->schema([
                                        Forms\Components\TextInput::make('column_name')
                                            ->label('Nama Kolom')
                                            ->required(),
                                    ]),
                        
                                // New grouped table structure
                                Forms\Components\Repeater::make('grouped_columns')
                                    ->label('Kelompok Data (khusus jika tabel dengan kolom utama)')
                                    ->visible(fn($get) => $get('field_type') === 'grouped_table')
                                    ->schema([
                                        Forms\Components\TextInput::make('group_title')
                                            ->label('Judul Kelompok (Kolom Utama)')
                                            ->required(),

                                            Forms\Components\Repeater::make('rows')
                                            ->label('Baris Data dalam Kelompok')
                                            ->schema([
                                                Forms\Components\TextInput::make('value1')
                                                    ->label('Nama Kolom'),
                                            ])
                                            ->addActionLabel('Tambah Baris')
                                    ])
                                    ->addActionLabel('Tambah Kelompok'),
                            ])
                            ->addActionLabel('Tambah Field')
                            ->helperText('Tambah field sesuai isi bagian ini.'),                        
                    ])
                    ->addActionLabel('Tambah Bagian Template')
                    ->helperText('Setiap bagian akan menjadi section berbeda dalam formulir.'),
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
