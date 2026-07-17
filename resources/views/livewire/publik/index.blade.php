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
            <div class="flex flex-col items-center gap-2.5">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-950/40">Menampilkan data tahun</span>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    @foreach ($daftarTahun as $th)
                        <button wire:click="pilihTahun({{ $th }})"
                                class="rounded-full px-5 py-2 text-sm font-bold transition {{ $tahunAktif === $th ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 scale-105' : 'bg-white text-brand-950/60 ring-1 ring-brand-950/10 hover:ring-brand-400' }}">
                            {{ $th }}
                        </button>
                    @endforeach
                    @if (count($daftarTahun) === 1)
                        <span class="text-xs text-brand-950/35">(data tahun lain akan muncul otomatis)</span>
                    @endif
                </div>
            </div>

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
                {{-- Grafik interaktif --}}
                <div class="card p-5 lg:col-span-3 sm:p-6"
                     x-data="{
                        series: 'masuk',
                        sel: null,
                        tahun: {{ $periode->tahun }},
                        masuk: @js(array_values($masukPerBulan)),
                        keluar: @js(array_values($keluarPerBulan)),
                        names: @js(array_values(\App\Support\Format::BULAN)),
                        short: @js(array_values(\App\Support\Format::BULAN_SINGKAT)),
                        ticks: [1, 0.75, 0.5, 0.25, 0],
                        get vals(){ return this[this.series]; },
                        get max(){ return Math.max(1, ...this.vals); },
                        get total(){ return this.vals.reduce((a,b)=>a+b,0); },
                        get aktifBulan(){ return this.vals.filter(v => v>0).length; },
                        h(v){ return v>0 ? Math.max(4, Math.round(v / this.max * 168)) : 0; },
                        rp(v){ return 'Rp ' + (v||0).toLocaleString('id-ID'); },
                        rpShort(v){
                            v = Math.round(v);
                            if (v >= 1000000) return (v/1000000).toLocaleString('id-ID',{maximumFractionDigits:1}) + ' jt';
                            if (v >= 1000) return Math.round(v/1000) + ' rb';
                            return v;
                        }
                     }">
                    {{-- Header + toggle --}}
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="flex items-center gap-2 font-bold text-brand-950">
                                <x-icon name="trending-up" class="h-4 w-4 text-brand-500" />
                                <span x-text="series==='masuk' ? 'Pemasukan Iuran per Bulan' : 'Pengeluaran per Bulan'"></span>
                            </h3>
                            <p class="mt-0.5 text-xs text-brand-950/45">
                                Tahun <span class="font-bold text-brand-700" x-text="tahun"></span> ·
                                <span x-text="aktifBulan"></span> bulan ada aktivitas
                            </p>
                        </div>
                        <div class="flex gap-1 rounded-xl bg-brand-50/70 p-1">
                            <button @click="series='masuk'; sel=null"
                                    class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                                    :class="series==='masuk' ? 'bg-white text-brand-700 shadow-sm' : 'text-brand-950/50 hover:text-brand-700'">Pemasukan</button>
                            <button @click="series='keluar'; sel=null"
                                    class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                                    :class="series==='keluar' ? 'bg-white text-rose-600 shadow-sm' : 'text-brand-950/50 hover:text-rose-600'">Pengeluaran</button>
                        </div>
                    </div>

                    {{-- Strip nilai terpilih --}}
                    <div class="mt-4 flex items-center justify-between gap-3 rounded-xl px-4 py-2.5 transition"
                         :class="series==='masuk' ? 'bg-brand-50/70' : 'bg-rose-50/70'">
                        <span class="text-sm font-semibold text-brand-950/70"
                              x-text="sel===null ? 'Ketuk batang untuk lihat nilai per bulan' : (names[sel] + ' ' + tahun)"></span>
                        <span class="shrink-0 text-base font-extrabold"
                              :class="series==='masuk' ? 'text-brand-700' : 'text-rose-600'"
                              x-text="sel===null ? ('Total ' + rp(total)) : rp(vals[sel])"></span>
                    </div>

                    {{-- Area plot --}}
                    <div class="relative mt-5" style="height:180px">
                        {{-- Garis bantu + label sumbu --}}
                        <template x-for="t in ticks" :key="t">
                            <div class="absolute left-0 right-0 flex items-center" :style="`bottom:${t*100}%`">
                                <span class="w-11 pr-1.5 text-right text-[0.6rem] font-medium text-brand-950/35" x-text="rpShort(max*t)"></span>
                                <div class="h-px flex-1" :class="t===0 ? 'bg-brand-950/15' : 'bg-brand-950/8'"></div>
                            </div>
                        </template>

                        {{-- Batang --}}
                        <div class="absolute inset-0 flex items-end justify-between gap-1 pl-11">
                            <template x-for="(v,i) in vals" :key="i">
                                <div class="relative flex h-full flex-1 items-end justify-center">
                                    {{-- Tooltip di atas batang terpilih --}}
                                    <span x-show="sel===i" x-cloak x-transition
                                          class="absolute z-10 -translate-y-1 whitespace-nowrap rounded-lg bg-brand-950 px-2 py-1 text-[0.65rem] font-bold text-white shadow-lg"
                                          :style="`bottom:${h(v)+6}px`" x-text="rpShort(v)"></span>
                                    <button type="button"
                                            @click="sel = (sel===i ? null : i)"
                                            @mouseenter="sel=i" @mouseleave="sel=null"
                                            class="w-full max-w-[24px] cursor-pointer rounded-t-md transition-all duration-200"
                                            :class="series==='masuk' ? 'bg-gradient-to-t from-brand-300 to-brand-500 hover:from-brand-400 hover:to-brand-600' : 'bg-gradient-to-t from-rose-300 to-rose-500 hover:from-rose-400 hover:to-rose-600'"
                                            :style="`height:${h(v)}px; opacity:${(sel===null || sel===i) ? 1 : 0.35}`"
                                            :aria-label="names[i] + ': ' + rp(v)"></button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Label bulan --}}
                    <div class="mt-2 flex justify-between gap-1 pl-11">
                        <template x-for="(m,i) in short" :key="i">
                            <button type="button" @click="sel = (sel===i ? null : i)"
                                    class="flex-1 text-center text-[0.6rem] font-bold transition"
                                    :class="sel===i ? (series==='masuk' ? 'text-brand-700' : 'text-rose-600') : 'text-brand-950/40'"
                                    x-text="m"></button>
                        </template>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-4 flex items-center justify-between border-t border-brand-950/5 pt-3 text-sm">
                        <span class="text-brand-950/50">{{ $ringkasan['jumlah_peserta'] }} KK berpartisipasi</span>
                        <span class="font-bold" :class="series==='masuk' ? 'text-brand-700' : 'text-rose-600'"
                              x-text="'Total ' + rp(total)"></span>
                    </div>
                </div>

                {{-- Santunan --}}
                <div class="card p-5 lg:col-span-2 sm:p-6">
                    <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="heart-hand" class="h-4 w-4 text-rose-400" /> Santunan Diberikan {{ $periode->tahun }}</h3>
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
                <h3 class="font-bold text-brand-950">Dokumen Resmi Tahun {{ $periode->tahun }}</h3>
                <p class="text-xs text-brand-950/45">Lihat laporan lengkap dalam format PDF.</p>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('publik.surat', $periode->tahun) }}" target="_blank" class="btn-primary flex-1"><x-icon name="report" class="h-4 w-4" /> Surat Laporan</a>
                    <a href="{{ route('publik.rincian', $periode->tahun) }}" target="_blank" class="btn-ghost flex-1"><x-icon name="coins" class="h-4 w-4" /> Rincian Buku Besar</a>
                </div>
            </div>
        </div>
    @endif
</div>
