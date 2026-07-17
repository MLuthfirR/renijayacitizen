<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Support\LaporanBuilder;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPdfController extends Controller
{
    protected function periode(int $tahun): Periode
    {
        return Periode::where('tahun', $tahun)->firstOrFail();
    }

    protected function builder(int $tahun): LaporanBuilder
    {
        return new LaporanBuilder($this->periode($tahun));
    }

    // ---- Surat pengantar ----
    public function surat(int $tahun)
    {
        return $this->renderSurat($tahun);
    }

    public function suratPublik(int $tahun)
    {
        return $this->renderSurat($tahun);
    }

    protected function renderSurat(int $tahun)
    {
        $b = $this->builder($tahun);
        $pdf = Pdf::loadView('pdf.surat', [
            'p' => $b->pengaturan(),
            'r' => $b->ringkasan(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Surat-Laporan-Duka-Cita-{$tahun}.pdf");
    }

    // ---- Rincian / buku besar ----
    public function rincian(int $tahun)
    {
        return $this->renderRincian($tahun);
    }

    public function rincianPublik(int $tahun)
    {
        return $this->renderRincian($tahun);
    }

    protected function renderRincian(int $tahun)
    {
        $b = $this->builder($tahun);
        $pdf = Pdf::loadView('pdf.rincian', [
            'p' => $b->pengaturan(),
            'periode' => $b->periode,
            'rows' => $b->bukuBesar(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Rincian-Duka-Cita-{$tahun}.pdf");
    }

    // ---- Kartu iuran ----
    public function kartu(int $tahun)
    {
        $b = $this->builder($tahun);
        $pdf = Pdf::loadView('pdf.kartu', [
            'p' => $b->pengaturan(),
            'periode' => $b->periode,
            'kartu' => $b->kartu(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Kartu-Iuran-Duka-Cita-{$tahun}.pdf");
    }
}
