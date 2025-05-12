<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;
    protected $table = 'surat';
    protected $fillable = [
        'user_id',
        'username',
        'jenis_surat_id',
        'nomor_surat',
        'judul_surat',
        'isi_data',
        'file_surat',
    ];

    // Relasi: Surat milik satu JenisSurat
    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }
}
