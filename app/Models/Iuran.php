<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Iuran extends Model
{
    protected $table = 'iuran';

    protected $fillable = [
        'peserta_id', 'periode_id', 'bulan', 'nominal', 'dibayar_pada',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'nominal' => 'integer',
        'dibayar_pada' => 'date',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
