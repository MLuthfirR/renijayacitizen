<?php

namespace App\Livewire\Pengaturan;

use App\Models\Iuran;
use App\Models\Pengaturan;
use App\Models\Periode;
use App\Models\Peserta;
use App\Support\PeriodeContext;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengaturan')]
class Index extends Component
{
    // Pengaturan organisasi
    public string $nama_organisasi = '';
    public string $alamat_baris1 = '';
    public string $alamat_baris2 = '';
    public string $nama_rt = '';
    public string $nama_perumahan = '';
    public string $tempat = '';
    public string $seksi_duka_cita = '';
    public string $ketua_pkk = '';
    public string $ketua_rt = '';
    public int $iuran_default = 5000;

    // Form periode baru
    public bool $modalPeriode = false;
    public ?int $tahunBaru = null;
    public int $saldoAwalBaru = 0;
    public int $iuranBaru = 5000;
    public bool $salinPeserta = true;
    public ?int $salinDari = null;

    public function mount(): void
    {
        $p = Pengaturan::get();
        $this->fill($p->only([
            'nama_organisasi', 'alamat_baris1', 'alamat_baris2', 'nama_rt',
            'nama_perumahan', 'tempat', 'seksi_duka_cita', 'ketua_pkk', 'ketua_rt', 'iuran_default',
        ]));
    }

    public function simpanPengaturan(): void
    {
        $data = $this->validate([
            'nama_organisasi' => 'required|string|max:150',
            'alamat_baris1' => 'nullable|string|max:150',
            'alamat_baris2' => 'nullable|string|max:150',
            'nama_rt' => 'required|string|max:50',
            'nama_perumahan' => 'nullable|string|max:100',
            'tempat' => 'nullable|string|max:100',
            'seksi_duka_cita' => 'required|string|max:100',
            'ketua_pkk' => 'required|string|max:100',
            'ketua_rt' => 'required|string|max:100',
            'iuran_default' => 'required|integer|min:0',
        ]);

        Pengaturan::get()->update($data);
        session()->flash('sukses', 'Pengaturan berhasil disimpan.');
    }

    public function bukaModalPeriode(): void
    {
        $terakhir = Periode::orderByDesc('tahun')->first();
        $this->tahunBaru = $terakhir ? $terakhir->tahun + 1 : (int) date('Y');
        $this->saldoAwalBaru = $terakhir ? $terakhir->saldoAkhir() : 0;
        $this->iuranBaru = $this->iuran_default;
        $this->salinDari = $terakhir?->id;
        $this->salinPeserta = (bool) $terakhir;
        $this->modalPeriode = true;
    }

    public function simpanPeriode(): void
    {
        $data = $this->validate([
            'tahunBaru' => 'required|integer|min:2000|max:2100|unique:periode,tahun',
            'saldoAwalBaru' => 'required|integer|min:0',
            'iuranBaru' => 'required|integer|min:0',
        ], [], [
            'tahunBaru' => 'tahun',
            'saldoAwalBaru' => 'saldo awal',
            'iuranBaru' => 'iuran',
        ]);

        DB::transaction(function () use ($data) {
            $periode = Periode::create([
                'tahun' => $data['tahunBaru'],
                'saldo_awal' => $data['saldoAwalBaru'],
                'iuran_default' => $data['iuranBaru'],
                'status' => 'aktif',
            ]);

            if ($this->salinPeserta && $this->salinDari) {
                $sumber = Peserta::where('periode_id', $this->salinDari)
                    ->where('status', 'aktif')
                    ->orderBy('urutan')->get();
                foreach ($sumber as $s) {
                    Peserta::create([
                        'periode_id' => $periode->id,
                        'urutan' => $s->urutan,
                        'nama' => $s->nama,
                        'blok' => $s->blok,
                        'nomor' => $s->nomor,
                        'luar_lingkungan' => $s->luar_lingkungan,
                        'status' => 'aktif',
                    ]);
                }
            }

            PeriodeContext::set($periode->id);
        });

        $this->modalPeriode = false;
        session()->flash('sukses', "Periode {$data['tahunBaru']} berhasil dibuat.");
    }

    public function kunci(int $id): void
    {
        $p = Periode::findOrFail($id);
        $p->update(['status' => $p->status === 'aktif' ? 'terkunci' : 'aktif']);
        session()->flash('sukses', 'Status periode diperbarui.');
    }

    public function hapusPeriode(int $id): void
    {
        $p = Periode::findOrFail($id);
        $tahun = $p->tahun;
        $p->delete();
        session()->flash('sukses', "Periode {$tahun} dihapus.");
    }

    public function render()
    {
        return view('livewire.pengaturan.index', [
            'periodeList' => Periode::orderByDesc('tahun')->get(),
            'periodeAktifId' => PeriodeContext::current()?->id,
        ]);
    }
}
