@php use App\Support\Format as F; @endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Kartu Iuran</h2>
            <p class="mt-0.5 text-sm text-brand-950/50">
                @if ($periode)
                    Tahun {{ $periode->tahun }} · Iuran {{ F::rupiah($periode->iuran_default) }}/bulan · Total masuk
                    <span class="font-semibold text-brand-700">{{ F::rupiah($totalKeseluruhan) }}</span>
                @else
                    Belum ada periode aktif
                @endif
            </p>
        </div>
        @if ($periode)
            <div class="relative min-w-[220px]">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-950/30" />
                <input wire:model.live.debounce.300ms="cari" class="input pl-10" placeholder="Cari nama peserta…">
            </div>
        @endif
    </div>

    @if (! $periode)
        <div class="card p-10 text-center">
            <p class="text-brand-950/55">Buat periode terlebih dahulu di <a href="{{ route('pengaturan.index') }}" wire:navigate class="font-semibold text-brand-600">Pengaturan</a>.</p>
        </div>
    @else
        @if ($terkunci)
            <div class="flex items-center gap-2.5 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600 ring-1 ring-slate-200">
                <x-icon name="lock" class="h-4 w-4" /> Periode ini terkunci. Buka kunci di Pengaturan untuk mengedit.
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2 text-xs text-brand-950/50">
            <span class="inline-flex items-center gap-1.5"><span class="grid h-5 w-5 place-items-center rounded bg-brand-500 text-white"><x-icon name="check" class="h-3 w-3"/></span> Lunas</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-5 w-5 rounded bg-white ring-1 ring-brand-950/10"></span> Belum</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-5 w-5 rounded bg-slate-100"></span> Tidak aktif</span>
            <span class="ml-1 text-brand-950/40">· Klik sel untuk menandai. Ikon <x-icon name="pencil" class="inline h-3 w-3"/> untuk isi nominal tepat.</span>
        </div>

        {{-- MOBILE: kartu per peserta dengan chip bulan yang bisa di-tap --}}
        <div class="space-y-3 lg:hidden">
            @forelse ($daftar as $p)
                @php $baris = $petaIuran[$p->id] ?? []; $totalBaris = array_sum($baris); @endphp
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-bold text-brand-950">{{ $p->nama }}</p>
                            <p class="text-xs text-brand-950/45">{{ $p->alamat }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-extrabold text-brand-700">{{ F::rupiah($totalBaris) }}</p>
                            <button wire:click="bukaNominal({{ $p->id }})" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-brand-600">
                                <x-icon name="pencil" class="h-3 w-3" /> Nominal
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-6 gap-1.5">
                        @foreach (range(1, 12) as $b)
                            @php $nom = $baris[$b] ?? 0; $lunas = $nom > 0; $nonAktif = $p->bulanTidakAktif($b); @endphp
                            @if ($nonAktif && ! $lunas)
                                <span class="grid h-11 place-items-center rounded-xl bg-slate-100 text-xs font-semibold text-slate-300">{{ F::namaBulanSingkat($b) }}</span>
                            @else
                                <button wire:click="toggle({{ $p->id }}, {{ $b }})" @disabled($terkunci)
                                        class="grid h-11 place-items-center rounded-xl text-xs font-bold transition active:scale-95 disabled:pointer-events-none
                                        {{ $lunas ? 'bg-brand-500 text-white shadow-sm shadow-brand-600/30' : 'bg-white text-brand-950/50 ring-1 ring-brand-950/10' }}">
                                    {{ F::namaBulanSingkat($b) }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-brand-950/40">Tidak ada peserta.</div>
            @endforelse
        </div>

        {{-- DESKTOP: grid tabel --}}
        <div class="card hidden overflow-hidden lg:block">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-brand-50/40 text-xs font-semibold text-brand-950/55">
                            <th class="sticky left-0 z-10 min-w-[190px] border-b border-brand-950/5 bg-brand-50/40 px-4 py-3 text-left">Peserta</th>
                            @foreach (range(1, 12) as $b)
                                <th class="border-b border-brand-950/5 px-1 py-2 text-center">
                                    <button wire:click="isiKolom({{ $b }})" @disabled($terkunci)
                                            class="mx-auto flex flex-col items-center rounded-lg px-1.5 py-1 transition hover:bg-brand-100/60 disabled:pointer-events-none"
                                            title="Tandai lunas semua peserta aktif — {{ F::namaBulan($b) }}">
                                        <span>{{ F::namaBulanSingkat($b) }}</span>
                                        <span class="text-[0.6rem] font-medium text-brand-950/35">{{ F::rupiahRingkas($totalBulan[$b] ?? 0) }}</span>
                                    </button>
                                </th>
                            @endforeach
                            <th class="border-b border-brand-950/5 px-3 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-950/5">
                        @forelse ($daftar as $p)
                            @php
                                $baris = $petaIuran[$p->id] ?? [];
                                $totalBaris = array_sum($baris);
                            @endphp
                            <tr class="group hover:bg-brand-50/30">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2 group-hover:bg-brand-50/30">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-brand-950">{{ $p->nama }}</p>
                                            <p class="text-xs text-brand-950/40">{{ $p->alamat }}</p>
                                        </div>
                                        <button wire:click="bukaNominal({{ $p->id }})"
                                                class="grid h-7 w-7 shrink-0 place-items-center rounded-lg text-brand-950/35 opacity-0 transition hover:bg-brand-100 hover:text-brand-700 group-hover:opacity-100">
                                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </td>
                                @foreach (range(1, 12) as $b)
                                    @php
                                        $nom = $baris[$b] ?? 0;
                                        $lunas = $nom > 0;
                                        $nonAktif = $p->bulanTidakAktif($b);
                                        $beda = $lunas && $nom != $periode->iuran_default;
                                    @endphp
                                    <td class="px-1 py-1.5 text-center">
                                        @if ($nonAktif && ! $lunas)
                                            <span class="mx-auto grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-slate-300">–</span>
                                        @else
                                            <button wire:click="toggle({{ $p->id }}, {{ $b }})" @disabled($terkunci)
                                                    class="relative mx-auto grid h-8 w-8 place-items-center rounded-lg transition disabled:pointer-events-none
                                                    {{ $lunas ? 'bg-brand-500 text-white shadow-sm shadow-brand-600/30 hover:bg-brand-600' : 'bg-white ring-1 ring-brand-950/10 hover:ring-brand-400 hover:bg-brand-50' }}"
                                                    title="{{ F::namaBulan($b) }}: {{ $lunas ? F::rupiah($nom) : 'Belum bayar' }}">
                                                @if ($lunas)
                                                    <x-icon name="check" class="h-4 w-4" />
                                                    @if ($beda)<span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-amber-400 ring-2 ring-white"></span>@endif
                                                @endif
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 text-right font-bold text-brand-700 whitespace-nowrap">{{ F::rupiah($totalBaris) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="14" class="px-4 py-12 text-center text-brand-950/40">Tidak ada peserta.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-brand-50/50 text-xs font-bold text-brand-950/70">
                            <td class="sticky left-0 z-10 bg-brand-50/50 px-4 py-3">Total per Bulan</td>
                            @foreach (range(1, 12) as $b)
                                <td class="px-1 py-3 text-center text-[0.65rem] leading-tight">{{ F::rupiahRingkas($totalBulan[$b] ?? 0) }}</td>
                            @endforeach
                            <td class="px-3 py-3 text-right text-brand-800">{{ F::rupiah($totalKeseluruhan) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($daftar && $daftar->hasPages())
            <div class="card px-4 py-3">{{ $daftar->links() }}</div>
        @endif
    @endif

    {{-- Modal nominal presisi --}}
    <x-modal model="modal" title="Iuran per Bulan" :subtitle="$editNama" max="max-w-2xl">
        <form wire:submit="simpanNominal" class="space-y-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach (range(1, 12) as $b)
                    <div>
                        <label class="label mb-1 text-xs">{{ F::namaBulan($b) }}</label>
                        <input wire:model="nominal.{{ $b }}" type="number" min="0" step="500" class="input py-2 text-sm" placeholder="0">
                    </div>
                @endforeach
            </div>
            <p class="helptext">Kosongkan atau isi 0 bila belum membayar. Isi nominal berbeda untuk pembayaran rapel/sebagian.</p>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" x-on:click="$wire.modal = false" class="btn-ghost">Batal</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><x-icon name="check" class="h-4 w-4" /> Simpan</button>
            </div>
        </form>
    </x-modal>
</div>
