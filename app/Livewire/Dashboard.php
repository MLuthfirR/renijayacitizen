<?php

namespace App\Livewire;

use App\Models\Periode;
use App\Support\Format;
use App\Support\PeriodeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $periode = PeriodeContext::current();

        $data = [
            'periode' => $periode,
            'ada' => (bool) $periode,
        ];

        if ($periode) {
            $masukPerBulan = $periode->masukPerBulan();

            // Pengeluaran (santunan) per bulan
            $keluarPerBulan = array_fill(1, 12, 0);
            foreach ($periode->santunan as $s) {
                $keluarPerBulan[$s->bulan] = ($keluarPerBulan[$s->bulan] ?? 0) + $s->nominal;
            }
            foreach ($periode->pengeluaranLain as $p) {
                $keluarPerBulan[$p->bulan] = ($keluarPerBulan[$p->bulan] ?? 0) + $p->nominal;
            }

            $maxBar = max(1, max(array_merge($masukPerBulan, $keluarPerBulan)));

            // Peserta yang belum membayar bulan berjalan / menunggak paling banyak
            $bulanIni = min((int) date('n'), 12);
            $peserta = $periode->peserta()->with('iuran')->get();
            $menunggak = $peserta->map(function ($p) use ($bulanIni) {
                $dibayar = $p->iuran->where('nominal', '>', 0)->pluck('bulan')->all();
                $belum = [];
                for ($b = 1; $b <= $bulanIni; $b++) {
                    if ($p->bulanTidakAktif($b)) {
                        continue;
                    }
                    if (! in_array($b, $dibayar)) {
                        $belum[] = $b;
                    }
                }
                return ['peserta' => $p, 'belum' => $belum, 'jumlah' => count($belum)];
            })->filter(fn ($x) => $x['jumlah'] > 0)->sortByDesc('jumlah')->take(6)->values();

            $data += [
                'saldoAwal' => $periode->saldo_awal,
                'totalMasuk' => $periode->totalMasuk(),
                'totalKeluar' => $periode->totalKeluar(),
                'totalSantunan' => $periode->totalSantunan(),
                'saldoAkhir' => $periode->saldoAkhir(),
                'jumlahPeserta' => $periode->jumlahPeserta(),
                'jumlahLuar' => $periode->jumlahPesertaLuar(),
                'jumlahSantunan' => $periode->santunan->count(),
                'masukPerBulan' => $masukPerBulan,
                'keluarPerBulan' => $keluarPerBulan,
                'maxBar' => $maxBar,
                'santunanTerakhir' => $periode->santunan()->orderByDesc('bulan')->take(5)->get(),
                'menunggak' => $menunggak,
                'bulanIni' => $bulanIni,
            ];
        }

        return view('livewire.dashboard', $data);
    }
}
