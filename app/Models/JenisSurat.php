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

    protected $casts = [
        'template_fields' => 'array', 
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Cek apakah kode_jenis belum diset, baru isi
            if (empty($model->kode_jenis)) {
                // Cari kode_jenis terakhir yang ada
                $last = static::orderBy('kode_jenis', 'desc')->first();
                $lastNumber = $last ? (int) str_replace('JNS-', '', $last->kode_jenis) : 0;

                do {
                    $newKode = 'JNS-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                    $exists = static::where('kode_jenis', $newKode)->exists(); // Cek apakah kode sudah ada
                    $lastNumber++;
                } while ($exists);

                $model->kode_jenis = $newKode;
            }
        });
    }
}
