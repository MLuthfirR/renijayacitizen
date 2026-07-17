<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengeluaranLain extends Model
{
    protected $table = 'pengeluaran_lain';

    protected $fillable = [
        'periode_id', 'urutan', 'deskripsi', 'nominal', 'bulan', 'keterangan',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'nominal' => 'integer',
        'bulan' => 'integer',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
