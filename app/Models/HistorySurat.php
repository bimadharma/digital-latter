<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorySurat extends Model
{
    use HasFactory;

    protected $table = 'history_surat';

    protected $fillable = [
        'surat_id',
        'user_id',
        'aksi',
        'waktu_aksi',
    ];

    public $timestamps = false;
    // app/Models/HistorySurat.php

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
