@php use App\Support\Format as F; @endphp
<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-brand-200/40 blur-3xl"></div>
            <div class="absolute -left-20 top-20 h-72 w-72 rounded-full bg-brand-100/50 blur-3xl"></div>
        </div>
        <div class="mx-auto max-w-5xl px-4 pb-4 pt-10 text-center sm:px-6 sm:pt-16">
            <span class="badge-green mx-auto"><x-icon name="shield" class="h-3.5 w-3.5" /> Transparansi Keuangan Warga</span>
            <h1 class="mx-auto mt-4 max-w-2xl text-3xl font-extrabold tracking-tight text-brand-950 sm:text-4xl">
                Laporan Iuran Duka Cita
            </h1>
            <p class="mx-auto mt-3 max-w-xl text-sm text-brand-950/55 sm:text-base">
                Ringkasan pemasukan, pengeluaran, dan saldo dana duka cita
                RT.02/RW.06 Perum Reni Jaya yang dapat dilihat oleh seluruh warga.
            </p>
        </div>
    </section>

    @if (! $periode)
        <div class="mx-auto max-w-md px-4 py-12 text-center">
            <div class="card p-10">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-50 text-brand-500"><x-icon name="info" class="h-7 w-7" /></div>
                <p class="mt-4 text-brand-950/55">Laporan belum tersedia. Silakan kembali lagi nanti.</p>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-5xl space-y-6 px-4 pb-16 sm:px-6">
            {{-- Pemilih tahun --}}
            @if (count($daftarTahun) > 1)
                <div class="flex flex-wrap items-center justify-center gap-2">
                    @foreach ($daftarTahun as $th)
                        <button wire:click="pilihTahun({{ $th }})"
                                class="rounded-full px-4 py-1.5 text-sm font-semibold transition {{ $tahunAktif === $th ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white text-brand-950/60 ring-1 ring-brand-950/10 hover:ring-brand-400' }}">
                            {{ $th }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Kartu saldo utama --}}
            <div class="card overflow-hidden">
                <div class="grid gap-px bg-brand-950/5 sm:grid-cols-4">
                    <div class="bg-white p-5 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Saldo Awal</p>
                        <p class="mt-2 text-lg font-extrabold text-brand-950 sm:text-xl">{{ F::rupiah($ringkasan['saldo_awal']) }}</p>
                    </div>
                    <div class="bg-white p-5 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Pemasukan</p>
                        <p class="mt-2 text-lg font-extrabold text-brand-700 sm:text-xl">{{ F::rupiah($ringkasan['total_masuk']) }}</p>
                    </div>
                    <div class="bg-white p-5 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Pengeluaran</p>
                        <p class="mt-2 text-lg font-extrabold text-rose-600 sm:text-xl">{{ F::rupiah($ringkasan['total_keluar']) }}</p>
                    </div>
                    <div class="p-5 text-center" style="background-image:linear-gradient(135deg,#ecfdf7,#d1fae9);">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Saldo Akhir</p>
                        <p class="mt-2 text-lg font-extrabold text-brand-800 sm:text-xl">{{ F::rupiah($ringkasan['saldo_akhir']) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                {{-- Grafik pemasukan --}}
                <div class="card p-5 lg:col-span-3 sm:p-6">
                    <h3 class="font-bold text-brand-950">Pemasukan Iuran per Bulan</h3>
                    <p class="text-xs text-brand-950/45">Tahun {{ $periode->tahun }}</p>
                    @php $maxM = max(1, max($masukPerBulan)); @endphp
                    <div class="mt-5 flex items-end justify-between gap-1.5" style="height:150px;">
                        @foreach (range(1, 12) as $b)
                            @php $h = max($masukPerBulan[$b] > 0 ? 6 : 0, round($masukPerBulan[$b] / $maxM * 128)); @endphp
                            <div class="flex flex-1 flex-col items-center gap-1.5">
                                <div class="w-full max-w-[20px] rounded-t-md bg-gradient-to-t from-brand-300 to-brand-500" style="height:{{ $h }}px" title="{{ F::namaBulan($b) }}: {{ F::rupiah($masukPerBulan[$b]) }}"></div>
                                <span class="text-[0.6rem] font-semibold text-brand-950/40">{{ F::namaBulanSingkat($b) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-brand-950/5 pt-3 text-sm">
                        <span class="text-brand-950/50">{{ $ringkasan['jumlah_peserta'] }} KK berpartisipasi</span>
                        <span class="font-bold text-brand-700">{{ F::rupiah($ringkasan['total_masuk']) }}</span>
                    </div>
                </div>

                {{-- Santunan --}}
                <div class="card p-5 lg:col-span-2 sm:p-6">
                    <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="heart-hand" class="h-4 w-4 text-rose-400" /> Santunan Diberikan</h3>
                    <div class="mt-3 space-y-1">
                        @forelse ($santunan as $s)
                            <div class="flex items-center justify-between gap-2 border-b border-brand-950/5 py-2 last:border-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-brand-950/85">{{ $s->nama_keluarga }}</p>
                                    <p class="text-xs text-brand-950/40">{{ F::namaBulan($s->bulan) }} {{ $periode->tahun }}</p>
                                </div>
                                <span class="shrink-0 text-sm font-bold text-rose-600">{{ F::rupiah($s->nominal) }}</span>
                            </div>
                        @empty
                            <p class="py-4 text-center text-sm text-brand-950/40">Belum ada santunan tahun ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Unduh dokumen resmi --}}
            <div class="card p-5 sm:p-6">
                <h3 class="font-bold text-brand-950">Dokumen Resmi</h3>
                <p class="text-xs text-brand-950/45">Lihat laporan lengkap dalam format PDF.</p>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('publik.surat', $periode->tahun) }}" target="_blank" class="btn-primary flex-1"><x-icon name="report" class="h-4 w-4" /> Surat Laporan</a>
                    <a href="{{ route('publik.rincian', $periode->tahun) }}" target="_blank" class="btn-ghost flex-1"><x-icon name="coins" class="h-4 w-4" /> Rincian Buku Besar</a>
                </div>
            </div>
        </div>
    @endif
</div>
