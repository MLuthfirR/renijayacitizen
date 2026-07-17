<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peserta extends Model
{
    protected $table = 'peserta';

    protected $fillable = [
        'periode_id', 'urutan', 'nama', 'blok', 'nomor',
        'luar_lingkungan', 'status', 'mulai_bulan', 'selesai_bulan', 'keterangan',
    ];

    protected $casts = [
        'luar_lingkungan' => 'boolean',
        'urutan' => 'integer',
        'mulai_bulan' => 'integer',
        'selesai_bulan' => 'integer',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function iuran(): HasMany
    {
        return $this->hasMany(Iuran::class);
    }

    /** Alamat tergabung, mis. "M 1/14". */
    public function getAlamatAttribute(): string
    {
        if (! $this->blok && ! $this->nomor) {
            return '-';
        }
        return trim($this->blok . '/' . $this->nomor, '/');
    }

    /** Total yang sudah dibayar peserta ini sepanjang tahun. */
    public function totalDibayar(): int
    {
        return (int) $this->iuran->sum('nominal');
    }

    /** Apakah bulan tertentu berada di luar masa keanggotaan (untuk sel "PINDAH"). */
    public function bulanTidakAktif(int $bulan): bool
    {
        if ($this->mulai_bulan && $bulan < $this->mulai_bulan) {
            return true;
        }
        if ($this->selesai_bulan && $bulan > $this->selesai_bulan) {
            return true;
        }
        return false;
    }
}
