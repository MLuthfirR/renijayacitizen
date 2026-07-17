<?php

namespace App\Livewire\Iuran;

use App\Models\Iuran;
use App\Models\Peserta;
use App\Support\PeriodeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Kartu Iuran')]
class Index extends Component
{
    use WithPagination;

    public string $cari = '';
    public int $perPage = 30;

    // Modal isi nominal presisi
    public bool $modal = false;
    public ?int $editPesertaId = null;
    public string $editNama = '';
    public array $nominal = []; // [bulan => nominal]

    public function updatingCari(): void
    {
        $this->resetPage();
    }

    protected function periode()
    {
        return PeriodeContext::current();
    }

    protected function terkunci(): bool
    {
        return $this->periode()?->status === 'terkunci';
    }

    /** Toggle cepat: klik sel untuk tandai lunas (nominal default) / batal. */
    public function toggle(int $pesertaId, int $bulan): void
    {
        if ($this->terkunci()) {
            session()->flash('error', 'Periode terkunci, tidak bisa diubah.');
            return;
        }

        $periode = $this->periode();
        $iuran = Iuran::where('peserta_id', $pesertaId)->where('bulan', $bulan)->first();

        if ($iuran && $iuran->nominal > 0) {
            $iuran->delete();
        } else {
            Iuran::updateOrCreate(
                ['peserta_id' => $pesertaId, 'bulan' => $bulan],
                ['periode_id' => $periode->id, 'nominal' => $periode->iuran_default, 'dibayar_pada' => now()]
            );
        }
    }

    public function bukaNominal(int $pesertaId): void
    {
        $p = Peserta::findOrFail($pesertaId);
        $this->editPesertaId = $p->id;
        $this->editNama = $p->nama;
        $this->nominal = [];
        $existing = Iuran::where('peserta_id', $p->id)->pluck('nominal', 'bulan');
        for ($b = 1; $b <= 12; $b++) {
            $this->nominal[$b] = (int) ($existing[$b] ?? 0);
        }
        $this->modal = true;
    }

    public function simpanNominal(): void
    {
        if ($this->terkunci()) {
            session()->flash('error', 'Periode terkunci, tidak bisa diubah.');
            return;
        }

        $periode = $this->periode();
        for ($b = 1; $b <= 12; $b++) {
            $nom = max(0, (int) ($this->nominal[$b] ?? 0));
            if ($nom > 0) {
                Iuran::updateOrCreate(
                    ['peserta_id' => $this->editPesertaId, 'bulan' => $b],
                    ['periode_id' => $periode->id, 'nominal' => $nom]
                );
            } else {
                Iuran::where('peserta_id', $this->editPesertaId)->where('bulan', $b)->delete();
            }
        }
        $this->modal = false;
        session()->flash('sukses', "Iuran {$this->editNama} disimpan.");
    }

    /** Isi satu bulan penuh untuk semua peserta aktif di halaman (bantu input massal). */
    public function isiKolom(int $bulan): void
    {
        if ($this->terkunci()) {
            session()->flash('error', 'Periode terkunci.');
            return;
        }
        $periode = $this->periode();
        $peserta = Peserta::where('periode_id', $periode->id)->where('status', 'aktif')->get();
        foreach ($peserta as $p) {
            if ($p->bulanTidakAktif($bulan)) {
                continue;
            }
            Iuran::updateOrCreate(
                ['peserta_id' => $p->id, 'bulan' => $bulan],
                ['periode_id' => $periode->id, 'nominal' => $periode->iuran_default, 'dibayar_pada' => now()]
            );
        }
        session()->flash('sukses', 'Semua peserta aktif ditandai lunas untuk bulan '.\App\Support\Format::namaBulan($bulan).'.');
    }

    public function render()
    {
        $periode = $this->periode();

        $daftar = null;
        $petaIuran = [];
        $totalBulan = array_fill(1, 12, 0);
        $totalKeseluruhan = 0;

        if ($periode) {
            $daftar = Peserta::where('periode_id', $periode->id)
                ->when($this->cari, fn ($q) => $q->where('nama', 'ilike', "%{$this->cari}%"))
                ->orderBy('urutan')
                ->paginate($this->perPage);

            // Peta iuran untuk peserta di halaman ini
            $ids = collect($daftar->items())->pluck('id');
            $rows = Iuran::whereIn('peserta_id', $ids)->get();
            foreach ($rows as $r) {
                $petaIuran[$r->peserta_id][$r->bulan] = $r->nominal;
            }

            // Total per bulan & keseluruhan (seluruh periode, bukan cuma halaman)
            $totalBulan = $periode->masukPerBulan();
            $totalKeseluruhan = array_sum($totalBulan);
        }

        return view('livewire.iuran.index', [
            'periode' => $periode,
            'daftar' => $daftar,
            'petaIuran' => $petaIuran,
            'totalBulan' => $totalBulan,
            'totalKeseluruhan' => $totalKeseluruhan,
            'terkunci' => $this->terkunci(),
        ]);
    }
}
