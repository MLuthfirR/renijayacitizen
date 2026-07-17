@php use App\Support\Format as F; @endphp
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Laporan</h2>
        <p class="mt-0.5 text-sm text-brand-950/50">
            @if ($periode) Cetak & unduh dokumen resmi tahun {{ $periode->tahun }} @else Belum ada periode aktif @endif
        </p>
    </div>

    @if (! $periode)
        <div class="card p-10 text-center">
            <p class="text-brand-950/55">Buat periode terlebih dahulu di <a href="{{ route('pengaturan.index') }}" wire:navigate class="font-semibold text-brand-600">Pengaturan</a>.</p>
        </div>
    @else
        {{-- Kartu dokumen --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @php
                $docs = [
                    ['surat', 'Surat Pengantar', 'Ringkasan & tanda tangan', 'report', route('laporan.surat.pdf', $periode->tahun)],
                    ['rincian', 'Rincian / Buku Besar', 'Kode akun, debet & kredit', 'coins', route('laporan.rincian.pdf', $periode->tahun)],
                    ['kartu', 'Kartu Iuran', 'Centang bulanan per peserta', 'wallet', route('laporan.kartu.pdf', $periode->tahun)],
                ];
            @endphp
            @foreach ($docs as [$key, $judul, $desc, $icon, $url])
                <div class="card card-hover p-5">
                    <div class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-icon name="{{ $icon }}" class="h-6 w-6" />
                    </div>
                    <h3 class="mt-4 font-bold text-brand-950">{{ $judul }}</h3>
                    <p class="mt-0.5 text-sm text-brand-950/50">{{ $desc }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ $url }}" target="_blank" class="btn-primary btn-sm flex-1"><x-icon name="printer" class="h-4 w-4" /> Buka / Cetak</a>
                        <a href="{{ $url }}?unduh=1" target="_blank" class="btn-ghost btn-sm" title="Buka PDF"><x-icon name="download" class="h-4 w-4" /></a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Preview surat (ringkas) --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-brand-950/5 px-5 py-4">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="report" class="h-4 w-4 text-brand-500" /> Ringkasan Surat</h3>
                <a href="{{ route('laporan.surat.pdf', $periode->tahun) }}" target="_blank" class="btn-soft btn-sm"><x-icon name="printer" class="h-4 w-4" /> Cetak Surat</a>
            </div>
            <div class="p-5 sm:p-6">
                <div class="mx-auto max-w-2xl space-y-1.5 text-sm">
                    <div class="mb-3 text-center">
                        <p class="font-bold text-brand-950">{{ $pengaturan->nama_organisasi }}</p>
                        <p class="text-xs text-brand-950/50">Laporan Iuran Duka Cita Tahun {{ $ringkasan['tahun'] }}</p>
                    </div>
                    @foreach ([
                        ['Saldo awal per 1 Januari '.$ringkasan['tahun'], $ringkasan['saldo_awal'], 'text-brand-950'],
                        ['Penerimaan iuran '.$ringkasan['tahun'], $ringkasan['total_masuk'], 'text-brand-700'],
                        ['Pengeluaran santunan', $ringkasan['total_santunan'], 'text-rose-600'],
                        ['Pengeluaran lain-lain', $ringkasan['total_lain'], 'text-amber-600'],
                    ] as [$label, $val, $warna])
                        <div class="flex items-center justify-between border-b border-dashed border-brand-950/10 py-2">
                            <span class="text-brand-950/60">{{ $label }}</span>
                            <span class="font-semibold {{ $warna }}">{{ F::rupiah($val) }}</span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3 mt-2">
                        <span class="font-bold text-brand-950">Saldo akhir per {{ F::namaBulan($ringkasan['bulan_akhir']) }} {{ $ringkasan['tahun'] }}</span>
                        <span class="text-lg font-extrabold text-brand-800">{{ F::rupiah($ringkasan['saldo_akhir']) }}</span>
                    </div>
                    <p class="pt-3 text-center text-xs text-brand-950/50">
                        {{ $ringkasan['jumlah_peserta'] }} KK berpartisipasi @if ($ringkasan['jumlah_luar'] > 0)· {{ $ringkasan['jumlah_luar'] }} di luar lingkungan RT @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Preview buku besar --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-brand-950/5 px-5 py-4">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="coins" class="h-4 w-4 text-brand-500" /> Buku Besar</h3>
                <a href="{{ route('laporan.rincian.pdf', $periode->tahun) }}" target="_blank" class="btn-soft btn-sm"><x-icon name="printer" class="h-4 w-4" /> Cetak Rincian</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-brand-50/40 text-left text-xs font-semibold uppercase tracking-wide text-brand-950/45">
                            <th class="px-4 py-2.5">Kode</th>
                            <th class="px-4 py-2.5">Deskripsi</th>
                            <th class="px-4 py-2.5 text-right">Debet</th>
                            <th class="px-4 py-2.5 text-right">Kredit</th>
                            <th class="px-4 py-2.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-950/5">
                        @foreach ($bukuBesar as $row)
                            @if ($row['tipe'] === 'section')
                                <tr class="bg-brand-50/30"><td class="px-4 py-2 font-bold text-brand-700">{{ $row['kode'] }}</td><td class="px-4 py-2 font-bold text-brand-800" colspan="4">{{ $row['deskripsi'] }}</td></tr>
                            @elseif ($row['tipe'] === 'saldo')
                                <tr class="bg-amber-50"><td class="px-4 py-2.5 font-extrabold text-amber-800" colspan="2">{{ $row['deskripsi'] }}</td><td class="px-4 py-2.5 text-right font-extrabold text-amber-800" colspan="3">{{ F::rupiah($row['kredit']) }}</td></tr>
                            @elseif ($row['tipe'] === 'grandtotal')
                                <tr class="border-t-2 border-brand-950/20 font-bold"><td class="px-4 py-2"></td><td class="px-4 py-2">{{ $row['deskripsi'] }}</td><td class="px-4 py-2 text-right">{{ $row['debet'] !== null ? F::rupiah($row['debet']) : '' }}</td><td class="px-4 py-2 text-right">{{ $row['kredit'] !== null ? F::rupiah($row['kredit']) : '' }}</td><td></td></tr>
                            @else
                                <tr class="{{ $row['tipe'] === 'total' ? 'font-bold' : '' }}">
                                    <td class="px-4 py-2 text-brand-950/50">{{ $row['kode'] }}</td>
                                    <td class="px-4 py-2 text-brand-950/80">{{ $row['deskripsi'] }}</td>
                                    <td class="px-4 py-2 text-right text-rose-600">{{ ($row['debet'] ?? null) !== null ? F::rupiah($row['debet']) : '' }}</td>
                                    <td class="px-4 py-2 text-right text-brand-700">{{ ($row['kredit'] ?? null) !== null ? F::rupiah($row['kredit']) : '' }}</td>
                                    <td class="px-4 py-2 text-xs text-brand-950/45">{{ $row['keterangan'] ?? '' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
