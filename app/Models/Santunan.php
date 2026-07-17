<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Santunan extends Model
{
    protected $table = 'santunan';

    protected $fillable = [
        'periode_id', 'urutan', 'nama_keluarga', 'nominal', 'bulan', 'keterangan',
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
