<?php

namespace App\Livewire\Publik;

use App\Models\Periode;
use App\Support\LaporanBuilder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.public')]
class Index extends Component
{
    #[Url]
    public ?int $tahun = null;

    public function pilihTahun(int $tahun): void
    {
        $this->tahun = $tahun;
    }

    public function render()
    {
        $daftarTahun = Periode::orderByDesc('tahun')->pluck('tahun')->all();

        $periode = $this->tahun
            ? Periode::where('tahun', $this->tahun)->first()
            : Periode::orderByDesc('tahun')->first();

        if (! $periode && ! empty($daftarTahun)) {
            $periode = Periode::orderByDesc('tahun')->first();
        }

        $data = [
            'daftarTahun' => $daftarTahun,
            'periode' => $periode,
            'tahunAktif' => $periode?->tahun,
        ];

        if ($periode) {
            $builder = new LaporanBuilder($periode);
            $data += [
                'ringkasan' => $builder->ringkasan(),
                'santunan' => $periode->santunan()->orderBy('bulan')->orderBy('urutan')->get(),
                'masukPerBulan' => $periode->masukPerBulan(),
            ];
        }

        return view('livewire.publik.index', $data);
    }
}
