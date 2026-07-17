@php use App\Support\Format as F; @endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Peserta</h2>
            <p class="mt-0.5 text-sm text-brand-950/50">
                @if ($periode)
                    {{ $total }} KK terdaftar tahun {{ $periode->tahun }} · {{ $totalLuar }} di luar lingkungan RT
                @else
                    Belum ada periode aktif
                @endif
            </p>
        </div>
        @if ($periode)
            <button wire:click="tambah" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Tambah Peserta</button>
        @endif
    </div>

    @if (! $periode)
        <div class="card p-10 text-center">
            <p class="text-brand-950/55">Buat periode terlebih dahulu di <a href="{{ route('pengaturan.index') }}" wire:navigate class="font-semibold text-brand-600">Pengaturan</a>.</p>
        </div>
    @else
        {{-- Toolbar --}}
        <div class="card flex flex-wrap items-center gap-3 p-3">
            <div class="relative min-w-[200px] flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-950/30" />
                <input wire:model.live.debounce.300ms="cari" class="input pl-10" placeholder="Cari nama atau alamat…">
            </div>
            <div class="flex gap-1 rounded-xl bg-brand-50/60 p-1">
                @foreach (['semua' => 'Semua', 'aktif' => 'Aktif', 'pindah' => 'Pindah', 'berhenti' => 'Berhenti'] as $key => $label)
                    <button wire:click="$set('filterStatus', '{{ $key }}')"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $filterStatus === $key ? 'bg-white text-brand-700 shadow-sm' : 'text-brand-950/50 hover:text-brand-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- MOBILE: kartu peserta --}}
        <div class="space-y-3 md:hidden">
            @forelse ($daftar as $p)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-brand-950/30">#{{ $p->urutan }}</span>
                                <span class="truncate font-bold text-brand-950">{{ $p->nama }}</span>
                            </div>
                            <p class="mt-0.5 inline-flex items-center gap-1 text-xs text-brand-950/50">
                                <x-icon name="map-pin" class="h-3.5 w-3.5 text-brand-950/30" /> {{ $p->alamat }}
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            <button wire:click="edit({{ $p->id }})" class="grid h-9 w-9 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-icon name="pencil" class="h-4 w-4" /></button>
                            <button wire:click="hapus({{ $p->id }})" wire:confirm="Hapus peserta {{ $p->nama }}?" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-500"><x-icon name="trash" class="h-4 w-4" /></button>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-brand-950/5 pt-3">
                        <div class="flex items-center gap-1.5">
                            @if ($p->status === 'aktif')<span class="badge-green">Aktif</span>
                            @elseif ($p->status === 'pindah')<span class="badge-amber">Pindah</span>
                            @else<span class="badge-rose">Berhenti</span>@endif
                            @if ($p->luar_lingkungan)<span class="badge-slate">Luar RT</span>@endif
                        </div>
                        <span class="text-sm font-bold text-brand-700">{{ F::rupiah($p->totalDibayar()) }}</span>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-brand-950/40">Tidak ada peserta ditemukan.</div>
            @endforelse
        </div>

        {{-- TABLET/DESKTOP: tabel --}}
        <div class="card hidden overflow-hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-brand-950/5 text-left text-xs font-semibold uppercase tracking-wide text-brand-950/45">
                            <th class="px-4 py-3 font-semibold">No</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">Alamat</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Dibayar</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-950/5">
                        @forelse ($daftar as $p)
                            <tr class="group transition hover:bg-brand-50/40">
                                <td class="px-4 py-3 text-brand-950/40">{{ $p->urutan }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-brand-950">{{ $p->nama }}</span>
                                        @if ($p->luar_lingkungan)<span class="badge-slate">Luar</span>@endif
                                    </div>
                                    @if ($p->keterangan)<p class="text-xs text-brand-950/40">{{ $p->keterangan }}</p>@endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 text-brand-950/60">
                                        <x-icon name="map-pin" class="h-3.5 w-3.5 text-brand-950/30" /> {{ $p->alamat }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($p->status === 'aktif')
                                        <span class="badge-green">Aktif</span>
                                    @elseif ($p->status === 'pindah')
                                        <span class="badge-amber">Pindah{{ $p->selesai_bulan ? ' · '.F::namaBulanSingkat($p->selesai_bulan) : '' }}</span>
                                    @else
                                        <span class="badge-rose">Berhenti</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-brand-700">{{ F::rupiah($p->totalDibayar()) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100">
                                        <button wire:click="edit({{ $p->id }})" class="grid h-8 w-8 place-items-center rounded-lg text-brand-950/50 hover:bg-brand-50 hover:text-brand-700" title="Ubah">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <button wire:click="hapus({{ $p->id }})" wire:confirm="Hapus peserta {{ $p->nama }}?"
                                                class="grid h-8 w-8 place-items-center rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-brand-950/40">Tidak ada peserta ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($daftar && $daftar->hasPages())
            <div class="card px-4 py-3">{{ $daftar->links() }}</div>
        @endif
    @endif

    {{-- Modal form --}}
    <x-modal model="modal" :title="$editId ? 'Ubah Peserta' : 'Tambah Peserta'" max="max-w-xl">
        <form wire:submit="simpan" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label class="label">Nama <span class="text-rose-500">*</span></label>
                    <input wire:model="nama" class="input @error('nama') input-error @enderror" placeholder="Nama kepala keluarga">
                    @error('nama') <p class="errortext">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="label">No. Urut</label>
                    <input wire:model="urutan" type="number" min="0" class="input @error('urutan') input-error @enderror">
                    @error('urutan') <p class="errortext">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Blok</label>
                    <input wire:model="blok" class="input" placeholder="mis. M 1">
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Nomor</label>
                    <input wire:model="nomor" class="input" placeholder="mis. 14">
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Status</label>
                    <select wire:model="status" class="input">
                        <option value="aktif">Aktif</option>
                        <option value="pindah">Pindah</option>
                        <option value="berhenti">Berhenti</option>
                    </select>
                </div>
                <div class="sm:col-span-3 flex items-end">
                    <label class="flex w-full cursor-pointer items-center gap-2.5 rounded-xl bg-brand-50/60 px-3.5 py-2.5 ring-1 ring-brand-100">
                        <input wire:model="luar_lingkungan" type="checkbox" class="h-4 w-4 rounded border-brand-950/20 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm font-medium text-brand-950/70">Di luar lingkungan RT</span>
                    </label>
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Mulai Bulan <span class="font-normal text-brand-950/40">(opsional)</span></label>
                    <select wire:model="mulai_bulan" class="input">
                        <option value="">—</option>
                        @foreach (\App\Support\Format::BULAN as $n => $nama)<option value="{{ $n }}">{{ $nama }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Selesai / Pindah Bulan <span class="font-normal text-brand-950/40">(opsional)</span></label>
                    <select wire:model="selesai_bulan" class="input">
                        <option value="">—</option>
                        @foreach (\App\Support\Format::BULAN as $n => $nama)<option value="{{ $n }}">{{ $nama }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-6">
                    <label class="label">Keterangan <span class="font-normal text-brand-950/40">(opsional)</span></label>
                    <input wire:model="keterangan" class="input" placeholder="Catatan tambahan">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" x-on:click="$wire.modal = false" class="btn-ghost">Batal</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <x-icon name="check" class="h-4 w-4" /> Simpan
                </button>
            </div>
        </form>
    </x-modal>
</div>
