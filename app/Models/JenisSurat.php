<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    protected $table = 'jenis_surat';

    protected $fillable = [
        'kode_jenis',
        'nama_jenis',
        'deskripsi',
        'template_fields',
        'template_file',
    ];

    // 👉 Tambahkan ini
    protected $casts = [
        'template_fields' => 'array', // Otomatis konversi JSON <-> array
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class);
    }
}
