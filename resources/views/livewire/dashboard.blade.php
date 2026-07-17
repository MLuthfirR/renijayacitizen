@php use App\Support\Format as F; @endphp
<div class="space-y-6">

    @if (! $ada)
        {{-- Empty state --}}
        <div class="card mx-auto max-w-xl p-10 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-50 text-brand-600">
                <x-icon name="calendar" class="h-8 w-8" />
            </div>
            <h2 class="mt-5 text-xl font-bold text-brand-950">Belum ada periode</h2>
            <p class="mx-auto mt-2 max-w-sm text-sm text-brand-950/55">
                Mulai dengan membuat periode tahun terlebih dahulu untuk mencatat iuran dan santunan.
            </p>
            <a href="{{ route('pengaturan.index') }}" wire:navigate class="btn-primary mt-6">
                <x-icon name="plus" class="h-4 w-4" /> Buat Periode
            </a>
        </div>
    @else
        {{-- Header ringkas periode --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-brand-950/50">Ringkasan Keuangan Duka Cita</p>
                <div class="mt-0.5 flex items-center gap-2.5">
                    <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Tahun {{ $periode->tahun }}</h2>
                    @if ($periode->status === 'terkunci')
                        <span class="badge-slate"><x-icon name="lock" class="h-3 w-3" /> Terkunci</span>
                    @else
                        <span class="badge-green"><x-icon name="dot" class="h-2.5 w-2.5" /> Aktif</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('iuran.index') }}" wire:navigate class="btn-ghost btn-sm"><x-icon name="wallet" class="h-4 w-4" /> Input Iuran</a>
                <a href="{{ route('laporan.index') }}" wire:navigate class="btn-primary btn-sm"><x-icon name="report" class="h-4 w-4" /> Lihat Laporan</a>
            </div>
        </div>

        {{-- Kartu statistik utama --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Saldo Awal --}}
            <div class="card card-hover p-5">
                <div class="flex items-center gap-2 text-brand-950/50">
                    <x-icon name="coins" class="h-4 w-4" />
                    <span class="text-xs font-semibold uppercase tracking-wide">Saldo Awal</span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-brand-950">{{ F::rupiah($saldoAwal) }}</p>
                <p class="mt-1 text-xs text-brand-950/45">Per 1 Januari {{ $periode->tahun }}</p>
            </div>
            {{-- Pemasukan --}}
            <div class="card card-hover p-5">
                <div class="flex items-center gap-2 text-brand-600">
                    <x-icon name="trending-up" class="h-4 w-4" />
                    <span class="text-xs font-semibold uppercase tracking-wide">Pemasukan</span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-brand-700">{{ F::rupiah($totalMasuk) }}</p>
                <p class="mt-1 text-xs text-brand-950/45">Iuran sepanjang tahun</p>
            </div>
            {{-- Pengeluaran --}}
            <div class="card card-hover p-5">
                <div class="flex items-center gap-2 text-rose-500">
                    <x-icon name="trending-down" class="h-4 w-4" />
                    <span class="text-xs font-semibold uppercase tracking-wide">Pengeluaran</span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-rose-600">{{ F::rupiah($totalKeluar) }}</p>
                <p class="mt-1 text-xs text-brand-950/45">{{ $jumlahSantunan }} santunan</p>
            </div>
            {{-- Saldo Akhir --}}
            <div class="card card-hover overflow-hidden p-5 ring-2 ring-brand-500/20"
                 style="background-image: linear-gradient(135deg, #ffffff, #ecfdf7);">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-icon name="wallet" class="h-4 w-4" />
                    <span class="text-xs font-semibold uppercase tracking-wide">Saldo Akhir</span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-brand-800">{{ F::rupiah($saldoAkhir) }}</p>
                <p class="mt-1 text-xs text-brand-700/60">Saldo berjalan saat ini</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Grafik --}}
            <div class="card p-6 lg:col-span-2">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-brand-950">Arus Kas per Bulan</h3>
                        <p class="text-xs text-brand-950/45">Perbandingan pemasukan iuran & pengeluaran</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-brand-500"></span> Masuk</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span> Keluar</span>
                    </div>
                </div>
                <div class="flex items-end justify-between gap-1.5" style="height: 200px;">
                    @foreach (range(1, 12) as $b)
                        @php
                            $mIn = $masukPerBulan[$b] ?? 0;
                            $mOut = $keluarPerBulan[$b] ?? 0;
                            $hIn = max($mIn > 0 ? 4 : 0, round($mIn / $maxBar * 170));
                            $hOut = max($mOut > 0 ? 4 : 0, round($mOut / $maxBar * 170));
                        @endphp
                        <div class="group flex flex-1 flex-col items-center gap-1.5">
                            <div class="flex w-full flex-1 items-end justify-center gap-0.5">
                                <div class="relative w-full max-w-[14px] rounded-t-md bg-gradient-to-t from-brand-400 to-brand-500 transition-all"
                                     style="height: {{ $hIn }}px" title="Masuk {{ F::rupiah($mIn) }}">
                                    <span class="pointer-events-none absolute -top-6 left-1/2 -translate-x-1/2 whitespace-nowrap rounded bg-brand-950 px-1.5 py-0.5 text-[0.6rem] font-semibold text-white opacity-0 group-hover:opacity-100">{{ F::rupiahRingkas($mIn) }}</span>
                                </div>
                                <div class="w-full max-w-[14px] rounded-t-md bg-gradient-to-t from-rose-300 to-rose-400 transition-all"
                                     style="height: {{ $hOut }}px" title="Keluar {{ F::rupiah($mOut) }}"></div>
                            </div>
                            <span class="text-[0.6rem] font-semibold text-brand-950/40">{{ F::namaBulanSingkat($b) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Statistik peserta --}}
            <div class="space-y-4">
                <div class="card p-6">
                    <div class="flex items-center gap-2 text-brand-950/50">
                        <x-icon name="users" class="h-4 w-4" />
                        <span class="text-xs font-semibold uppercase tracking-wide">Peserta</span>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold text-brand-950">{{ $jumlahPeserta }} <span class="text-base font-semibold text-brand-950/40">KK</span></p>
                    <p class="mt-1 text-xs text-brand-950/45">{{ $jumlahLuar }} di antaranya di luar lingkungan RT</p>
                    <a href="{{ route('peserta.index') }}" wire:navigate class="btn-soft btn-sm mt-4 w-full">Kelola Peserta</a>
                </div>

                <div class="card p-6">
                    <div class="mb-3 flex items-center gap-2 text-brand-950/50">
                        <x-icon name="heart-hand" class="h-4 w-4" />
                        <span class="text-xs font-semibold uppercase tracking-wide">Santunan Terakhir</span>
                    </div>
                    @forelse ($santunanTerakhir as $s)
                        <div class="flex items-center justify-between gap-2 border-t border-brand-950/5 py-2 first:border-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-brand-950/80">{{ $s->nama_keluarga }}</p>
                                <p class="text-xs text-brand-950/40">{{ F::namaBulan($s->bulan) }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-bold text-rose-600">{{ F::rupiah($s->nominal) }}</span>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-brand-950/40">Belum ada santunan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Peserta menunggak --}}
        @if ($menunggak->count())
        <div class="card p-6">
            <div class="mb-4 flex items-center gap-2">
                <x-icon name="alert" class="h-4 w-4 text-amber-500" />
                <h3 class="font-bold text-brand-950">Perlu Ditagih</h3>
                <span class="badge-amber">s/d {{ F::namaBulan($bulanIni) }}</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($menunggak as $m)
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-amber-50/50 px-3.5 py-2.5 ring-1 ring-amber-100">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-brand-950/85">{{ $m['peserta']->nama }}</p>
                            <p class="text-xs text-brand-950/45">{{ $m['peserta']->alamat }}</p>
                        </div>
                        <span class="badge-amber shrink-0">{{ $m['jumlah'] }} bln</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif
</div>
