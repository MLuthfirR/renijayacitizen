<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = [
        'tahun', 'saldo_awal', 'iuran_default', 'status', 'catatan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'saldo_awal' => 'integer',
        'iuran_default' => 'integer',
    ];

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class)->orderBy('urutan');
    }

    public function iuran(): HasMany
    {
        return $this->hasMany(Iuran::class);
    }

    public function santunan(): HasMany
    {
        return $this->hasMany(Santunan::class)->orderBy('urutan');
    }

    public function pengeluaranLain(): HasMany
    {
        return $this->hasMany(PengeluaranLain::class)->orderBy('urutan');
    }

    /** Total pemasukan iuran sepanjang tahun. */
    public function totalMasuk(): int
    {
        return (int) $this->iuran()->sum('nominal');
    }

    /** Pemasukan iuran per bulan (indeks 1-12). */
    public function masukPerBulan(): array
    {
        $data = array_fill(1, 12, 0);
        $rows = $this->iuran()
            ->selectRaw('bulan, SUM(nominal) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');
        foreach ($rows as $bulan => $total) {
            $data[(int) $bulan] = (int) $total;
        }
        return $data;
    }

    public function totalSantunan(): int
    {
        return (int) $this->santunan()->sum('nominal');
    }

    public function totalPengeluaranLain(): int
    {
        return (int) $this->pengeluaranLain()->sum('nominal');
    }

    public function totalKeluar(): int
    {
        return $this->totalSantunan() + $this->totalPengeluaranLain();
    }

    public function saldoAkhir(): int
    {
        return $this->saldo_awal + $this->totalMasuk() - $this->totalKeluar();
    }

    public function jumlahPeserta(): int
    {
        return $this->peserta()->count();
    }

    public function jumlahPesertaLuar(): int
    {
        return $this->peserta()->where('luar_lingkungan', true)->count();
    }

    /** Bulan terakhir yang memiliki aktivitas (untuk teks periode laporan). */
    public function bulanTerakhirAktif(): int
    {
        $bulanIuran = (int) $this->iuran()->where('nominal', '>', 0)->max('bulan');
        $bulanSantunan = (int) $this->santunan()->max('bulan');
        return max($bulanIuran, $bulanSantunan, 1);
    }
}
