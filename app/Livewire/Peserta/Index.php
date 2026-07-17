<?php

namespace App\Livewire\Peserta;

use App\Models\Peserta;
use App\Support\PeriodeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Peserta')]
class Index extends Component
{
    use WithPagination;

    public string $cari = '';
    public string $filterStatus = 'semua';

    // Form
    public bool $modal = false;
    public ?int $editId = null;
    public string $nama = '';
    public ?string $blok = '';
    public ?string $nomor = '';
    public ?int $urutan = null;
    public bool $luar_lingkungan = false;
    public string $status = 'aktif';
    public ?int $mulai_bulan = null;
    public ?int $selesai_bulan = null;
    public ?string $keterangan = '';

    public function updatingCari(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    protected function periode()
    {
        return PeriodeContext::current();
    }

    public function tambah(): void
    {
        $this->reset(['editId', 'nama', 'blok', 'nomor', 'luar_lingkungan', 'status', 'mulai_bulan', 'selesai_bulan', 'keterangan']);
        $this->status = 'aktif';
        $this->luar_lingkungan = false;
        $this->urutan = ((int) Peserta::where('periode_id', $this->periode()?->id)->max('urutan')) + 1;
        $this->modal = true;
    }

    public function edit(int $id): void
    {
        $p = Peserta::findOrFail($id);
        $this->editId = $p->id;
        $this->nama = $p->nama;
        $this->blok = $p->blok;
        $this->nomor = $p->nomor;
        $this->urutan = $p->urutan;
        $this->luar_lingkungan = $p->luar_lingkungan;
        $this->status = $p->status;
        $this->mulai_bulan = $p->mulai_bulan;
        $this->selesai_bulan = $p->selesai_bulan;
        $this->keterangan = $p->keterangan;
        $this->modal = true;
    }

    public function simpan(): void
    {
        $periode = $this->periode();
        abort_unless($periode, 400, 'Periode belum dipilih.');

        $data = $this->validate([
            'nama' => 'required|string|max:120',
            'blok' => 'nullable|string|max:30',
            'nomor' => 'nullable|string|max:30',
            'urutan' => 'required|integer|min:0',
            'luar_lingkungan' => 'boolean',
            'status' => 'required|in:aktif,pindah,berhenti',
            'mulai_bulan' => 'nullable|integer|min:1|max:12',
            'selesai_bulan' => 'nullable|integer|min:1|max:12',
            'keterangan' => 'nullable|string|max:255',
        ], [], ['nama' => 'nama']);

        $data['periode_id'] = $periode->id;

        if ($this->editId) {
            Peserta::findOrFail($this->editId)->update($data);
            session()->flash('sukses', 'Data peserta diperbarui.');
        } else {
            Peserta::create($data);
            session()->flash('sukses', 'Peserta baru ditambahkan.');
        }

        $this->modal = false;
    }

    public function hapus(int $id): void
    {
        Peserta::findOrFail($id)->delete();
        session()->flash('sukses', 'Peserta dihapus.');
    }

    public function render()
    {
        $periode = $this->periode();

        $query = Peserta::query()
            ->where('periode_id', $periode?->id)
            ->when($this->cari, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('nama', 'ilike', "%{$this->cari}%")
                       ->orWhere('blok', 'ilike', "%{$this->cari}%")
                       ->orWhere('nomor', 'ilike', "%{$this->cari}%");
                });
            })
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('urutan');

        return view('livewire.peserta.index', [
            'periode' => $periode,
            'daftar' => $periode ? $query->paginate(25) : null,
            'total' => $periode ? Peserta::where('periode_id', $periode->id)->count() : 0,
            'totalLuar' => $periode ? Peserta::where('periode_id', $periode->id)->where('luar_lingkungan', true)->count() : 0,
        ]);
    }
}
