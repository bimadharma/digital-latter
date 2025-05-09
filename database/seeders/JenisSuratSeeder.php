<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;

class JenisSuratSeeder extends Seeder
{
    public function run()
    {
        JenisSurat::create([
            'kode_jenis' => 'laporan-euc',
            'nama_jenis' => 'Laporan EUC',
            'deskripsi' => 'Laporan mengenai evaluasi penggunaan aplikasi',
            'template_fields' => json_encode([
                'pendahuluan', 'tahapan', 'hasil', 'kendala', 'gambaran', 
                'arsitektur_aplikasi', 'arsitektur_db', 'konfigurasi_aplikasi',
                'konfigurasi_db', 'konfigurasi_hardware', 'penutup'
            ]),
            'template_file' => null, 
        ]);
    }
}