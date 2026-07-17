<?php

namespace App\Livewire\Santunan;

use App\Models\PengeluaranLain;
use App\Models\Santunan;
use App\Support\PeriodeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Santunan')]
class Index extends Component
{
    public bool $modal = false;
    public string $jenis = 'santunan'; // 'santunan' | 'lain'
    public ?int $editId = null;

    public string $judul = '';        // nama_keluarga / deskripsi
    public int $nominal = 0;
    public ?int $bulan = null;
    public ?string $keterangan = '';

    protected function periode()
    {
        return PeriodeContext::current();
    }

    public function tambah(string $jenis): void
    {
        $this->reset(['editId', 'judul', 'nominal', 'bulan', 'keterangan']);
        $this->jenis = $jenis;
        $this->bulan = min((int) date('n'), 12);
        if ($jenis === 'santunan') {
            $this->nominal = 500000;
        }
        $this->modal = true;
    }

    public function edit(string $jenis, int $id): void
    {
        $this->jenis = $jenis;
        $this->editId = $id;
        if ($jenis === 'santunan') {
            $s = Santunan::findOrFail($id);
            $this->judul = $s->nama_keluarga;
            $this->nominal = $s->nominal;
            $this->bulan = $s->bulan;
            $this->keterangan = $s->keterangan;
        } else {
            $p = PengeluaranLain::findOrFail($id);
            $this->judul = $p->deskripsi;
            $this->nominal = $p->nominal;
            $this->bulan = $p->bulan;
            $this->keterangan = $p->keterangan;
        }
        $this->modal = true;
    }

    public function simpan(): void
    {
        $periode = $this->periode();
        abort_unless($periode, 400);

        $this->validate([
            'judul' => 'required|string|max:150',
            'nominal' => 'required|integer|min:1',
            'bulan' => 'required|integer|min:1|max:12',
            'keterangan' => 'nullable|string|max:150',
        ], [], [
            'judul' => $this->jenis === 'santunan' ? 'nama keluarga' : 'deskripsi',
            'nominal' => 'nominal',
            'bulan' => 'bulan',
        ]);

        $ket = $this->keterangan ?: \App\Support\Format::namaBulan($this->bulan).' '.$periode->tahun;

        if ($this->jenis === 'santunan') {
            $data = [
                'periode_id' => $periode->id,
                'nama_keluarga' => $this->judul,
                'nominal' => $this->nominal,
                'bulan' => $this->bulan,
                'keterangan' => $ket,
            ];
            if ($this->editId) {
                Santunan::findOrFail($this->editId)->update($data);
            } else {
                $data['urutan'] = ((int) Santunan::where('periode_id', $periode->id)->max('urutan')) + 1;
                Santunan::create($data);
            }
        } else {
            $data = [
                'periode_id' => $periode->id,
                'deskripsi' => $this->judul,
                'nominal' => $this->nominal,
                'bulan' => $this->bulan,
                'keterangan' => $ket,
            ];
            if ($this->editId) {
                PengeluaranLain::findOrFail($this->editId)->update($data);
            } else {
                $data['urutan'] = ((int) PengeluaranLain::where('periode_id', $periode->id)->max('urutan')) + 1;
                PengeluaranLain::create($data);
            }
        }

        $this->modal = false;
        session()->flash('sukses', 'Data pengeluaran disimpan.');
    }

    public function hapus(string $jenis, int $id): void
    {
        if ($jenis === 'santunan') {
            Santunan::findOrFail($id)->delete();
        } else {
            PengeluaranLain::findOrFail($id)->delete();
        }
        session()->flash('sukses', 'Data dihapus.');
    }

    public function render()
    {
        $periode = $this->periode();

        return view('livewire.santunan.index', [
            'periode' => $periode,
            'santunan' => $periode ? $periode->santunan()->orderBy('bulan')->orderBy('urutan')->get() : collect(),
            'pengeluaranLain' => $periode ? $periode->pengeluaranLain()->orderBy('bulan')->get() : collect(),
            'totalSantunan' => $periode ? $periode->totalSantunan() : 0,
            'totalLain' => $periode ? $periode->totalPengeluaranLain() : 0,
        ]);
    }
}
