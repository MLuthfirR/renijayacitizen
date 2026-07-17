<?php

namespace App\Support;

use App\Models\Periode;
use Illuminate\Support\Collection;

class PeriodeContext
{
    /** Periode yang sedang aktif dipilih (dari sesi), atau tahun terbaru. */
    public static function current(): ?Periode
    {
        $id = session('periode_id');

        if ($id) {
            $periode = Periode::find($id);
            if ($periode) {
                return $periode;
            }
        }

        return Periode::orderByDesc('tahun')->first();
    }

    /** Semua periode, terbaru dahulu. */
    public static function all(): Collection
    {
        return Periode::orderByDesc('tahun')->get();
    }

    public static function set(int $periodeId): void
    {
        session(['periode_id' => $periodeId]);
    }
}
