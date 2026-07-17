<?php

namespace App\Support;

use App\Models\Pengaturan;
use App\Models\Periode;

/**
 * Menyusun data ketiga dokumen laporan dari sebuah Periode:
 *  1. Surat pengantar (ringkasan)
 *  2. Rincian / buku besar
 *  3. Kartu iuran peserta
 */
class LaporanBuilder
{
    public function __construct(public Periode $periode)
    {
    }

    public function pengaturan(): Pengaturan
    {
        return Pengaturan::get();
    }

    /** Ringkasan angka untuk surat pengantar. */
    public function ringkasan(): array
    {
        $totalMasuk = $this->periode->totalMasuk();
        $totalSantunan = $this->periode->totalSantunan();
        $totalLain = $this->periode->totalPengeluaranLain();

        return [
            'tahun' => $this->periode->tahun,
            'saldo_awal' => $this->periode->saldo_awal,
            'total_masuk' => $totalMasuk,
            'total_santunan' => $totalSantunan,
            'total_lain' => $totalLain,
            'total_keluar' => $totalSantunan + $totalLain,
            'saldo_akhir' => $this->periode->saldoAkhir(),
            'jumlah_peserta' => $this->periode->peserta()->where('status', '!=', 'berhenti')->count(),
            'jumlah_luar' => $this->periode->jumlahPesertaLuar(),
            'bulan_akhir' => $this->periode->bulanTerakhirAktif(),
        ];
    }

    /**
     * Baris-baris buku besar mengikuti format asli.
     * Setiap baris: ['tipe','kode','deskripsi','pos','debet','kredit','keterangan'].
     */
    public function bukuBesar(): array
    {
        $rows = [];
        $masuk = $this->periode->masukPerBulan();
        $totalMasuk = array_sum($masuk);

        // 900 — Jenis tabungan / kas awal
        $rows[] = ['tipe' => 'section', 'kode' => '900', 'deskripsi' => 'JENIS TABUNGAN'];
        $rows[] = [
            'tipe' => 'item', 'kode' => '901',
            'deskripsi' => 'Kas per Januari '.$this->periode->tahun,
            'pos' => 'Kredit', 'debet' => null, 'kredit' => $this->periode->saldo_awal, 'keterangan' => '',
        ];

        // 100 — Dana masuk
        $rows[] = ['tipe' => 'section', 'kode' => '100', 'deskripsi' => 'DANA MASUK'];
        for ($b = 1; $b <= 12; $b++) {
            $rows[] = [
                'tipe' => 'item', 'kode' => (string) (101 + $b),
                'deskripsi' => 'Setoran '.Format::namaBulan($b),
                'pos' => 'Kredit', 'debet' => null, 'kredit' => $masuk[$b], 'keterangan' => '',
            ];
        }
        $rows[] = ['tipe' => 'total', 'kode' => '', 'deskripsi' => 'Total Dana Masuk', 'pos' => '', 'debet' => null, 'kredit' => $totalMasuk, 'keterangan' => ''];

        // 200 — Dana keluar
        $rows[] = ['tipe' => 'section', 'kode' => '200', 'deskripsi' => 'DANA KELUAR'];
        $kode = 201;
        foreach ($this->periode->santunan()->orderBy('bulan')->orderBy('urutan')->get() as $s) {
            $rows[] = [
                'tipe' => 'item', 'kode' => (string) $kode++,
                'deskripsi' => 'Santunan '.$s->nama_keluarga,
                'pos' => 'Debit', 'debet' => $s->nominal, 'kredit' => null,
                'keterangan' => $s->keterangan ?: Format::namaBulan($s->bulan).' '.$this->periode->tahun,
            ];
        }
        foreach ($this->periode->pengeluaranLain()->orderBy('bulan')->get() as $p) {
            $rows[] = [
                'tipe' => 'item', 'kode' => (string) $kode++,
                'deskripsi' => $p->deskripsi,
                'pos' => 'Debit', 'debet' => $p->nominal, 'kredit' => null,
                'keterangan' => $p->keterangan ?: Format::namaBulan($p->bulan).' '.$this->periode->tahun,
            ];
        }

        $totalKeluar = $this->periode->totalKeluar();
        $rows[] = ['tipe' => 'total', 'kode' => '', 'deskripsi' => 'Total Dana Keluar', 'pos' => '', 'debet' => $totalKeluar, 'kredit' => null, 'keterangan' => ''];

        // Jumlah & saldo akhir
        $rows[] = ['tipe' => 'grandtotal', 'kode' => '', 'deskripsi' => 'Jumlah', 'pos' => '', 'debet' => $totalKeluar, 'kredit' => $this->periode->saldo_awal + $totalMasuk, 'keterangan' => ''];
        $rows[] = ['tipe' => 'saldo', 'kode' => '', 'deskripsi' => 'Saldo Akhir', 'pos' => '', 'debet' => null, 'kredit' => $this->periode->saldoAkhir(), 'keterangan' => ''];

        return $rows;
    }

    /** Data kartu iuran: tiap peserta + status 12 bulan. */
    public function kartu(): array
    {
        $peserta = $this->periode->peserta()
            ->with(['iuran' => fn ($q) => $q->select('peserta_id', 'bulan', 'nominal')])
            ->orderBy('urutan')->get();

        return $peserta->map(function ($p) {
            $bayar = $p->iuran->where('nominal', '>', 0)->pluck('bulan')->all();
            $bulan = [];
            for ($b = 1; $b <= 12; $b++) {
                if ($p->bulanTidakAktif($b)) {
                    $bulan[$b] = 'nonaktif';
                } elseif (in_array($b, $bayar)) {
                    $bulan[$b] = 'lunas';
                } else {
                    $bulan[$b] = 'kosong';
                }
            }
            return [
                'urutan' => $p->urutan,
                'nama' => $p->nama,
                'alamat' => $p->alamat,
                'status' => $p->status,
                'selesai_bulan' => $p->selesai_bulan,
                'bulan' => $bulan,
            ];
        })->all();
    }
}
