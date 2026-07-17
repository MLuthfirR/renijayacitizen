@php use App\Support\Format as F; @endphp
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Santunan & Pengeluaran</h2>
        <p class="mt-0.5 text-sm text-brand-950/50">
            @if ($periode) Dana keluar tahun {{ $periode->tahun }} @else Belum ada periode aktif @endif
        </p>
    </div>

    @if (! $periode)
        <div class="card p-10 text-center">
            <p class="text-brand-950/55">Buat periode terlebih dahulu di <a href="{{ route('pengaturan.index') }}" wire:navigate class="font-semibold text-brand-600">Pengaturan</a>.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Total Santunan</p>
                <p class="mt-2 text-2xl font-extrabold text-rose-600">{{ F::rupiah($totalSantunan) }}</p>
                <p class="mt-1 text-xs text-brand-950/40">{{ $santunan->count() }} penerima</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Pengeluaran Lain</p>
                <p class="mt-2 text-2xl font-extrabold text-amber-600">{{ F::rupiah($totalLain) }}</p>
                <p class="mt-1 text-xs text-brand-950/40">{{ $pengeluaranLain->count() }} item</p>
            </div>
            <div class="card p-5 ring-2 ring-rose-200/60" style="background-image: linear-gradient(135deg,#fff,#fff1f2);">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-950/45">Total Dana Keluar</p>
                <p class="mt-2 text-2xl font-extrabold text-rose-700">{{ F::rupiah($totalSantunan + $totalLain) }}</p>
            </div>
        </div>

        {{-- Santunan --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-brand-950/5 px-5 py-4">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="heart-hand" class="h-4 w-4 text-rose-400" /> Santunan Duka Cita</h3>
                <button wire:click="tambah('santunan')" class="btn-primary btn-sm"><x-icon name="plus" class="h-4 w-4" /> Tambah</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-brand-950/5 text-left text-xs font-semibold uppercase tracking-wide text-brand-950/45">
                            <th class="px-5 py-3">Penerima / Keluarga</th>
                            <th class="px-5 py-3">Bulan</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-950/5">
                        @forelse ($santunan as $s)
                            <tr class="group hover:bg-brand-50/40">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-brand-950">{{ $s->nama_keluarga }}</p>
                                    @if ($s->keterangan)<p class="text-xs text-brand-950/40">{{ $s->keterangan }}</p>@endif
                                </td>
                                <td class="px-5 py-3 text-brand-950/60">{{ F::namaBulan($s->bulan) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-rose-600">{{ F::rupiah($s->nominal) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100">
                                        <button wire:click="edit('santunan', {{ $s->id }})" class="grid h-8 w-8 place-items-center rounded-lg text-brand-950/50 hover:bg-brand-50 hover:text-brand-700"><x-icon name="pencil" class="h-4 w-4" /></button>
                                        <button wire:click="hapus('santunan', {{ $s->id }})" wire:confirm="Hapus santunan {{ $s->nama_keluarga }}?" class="grid h-8 w-8 place-items-center rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-brand-950/40">Belum ada santunan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pengeluaran lain --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-brand-950/5 px-5 py-4">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="coins" class="h-4 w-4 text-amber-400" /> Pengeluaran Lain-lain</h3>
                <button wire:click="tambah('lain')" class="btn-soft btn-sm"><x-icon name="plus" class="h-4 w-4" /> Tambah</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-brand-950/5 text-left text-xs font-semibold uppercase tracking-wide text-brand-950/45">
                            <th class="px-5 py-3">Deskripsi</th>
                            <th class="px-5 py-3">Bulan</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-950/5">
                        @forelse ($pengeluaranLain as $x)
                            <tr class="group hover:bg-brand-50/40">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-brand-950">{{ $x->deskripsi }}</p>
                                    @if ($x->keterangan)<p class="text-xs text-brand-950/40">{{ $x->keterangan }}</p>@endif
                                </td>
                                <td class="px-5 py-3 text-brand-950/60">{{ F::namaBulan($x->bulan) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-amber-600">{{ F::rupiah($x->nominal) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100">
                                        <button wire:click="edit('lain', {{ $x->id }})" class="grid h-8 w-8 place-items-center rounded-lg text-brand-950/50 hover:bg-brand-50 hover:text-brand-700"><x-icon name="pencil" class="h-4 w-4" /></button>
                                        <button wire:click="hapus('lain', {{ $x->id }})" wire:confirm="Hapus item ini?" class="grid h-8 w-8 place-items-center rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-brand-950/40">Tidak ada pengeluaran lain.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal --}}
    <x-modal model="modal" :title="($editId ? 'Ubah ' : 'Tambah ') . ($jenis === 'santunan' ? 'Santunan' : 'Pengeluaran Lain')">
        <form wire:submit="simpan" class="space-y-4">
            <div>
                <label class="label">{{ $jenis === 'santunan' ? 'Nama Keluarga' : 'Deskripsi' }} <span class="text-rose-500">*</span></label>
                <input wire:model="judul" class="input @error('judul') input-error @enderror"
                       placeholder="{{ $jenis === 'santunan' ? 'mis. Kelg. Ibu Winarsih' : 'mis. Administrasi' }}">
                @error('judul') <p class="errortext">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <input wire:model="nominal" type="number" min="0" step="50000" class="input @error('nominal') input-error @enderror">
                    @error('nominal') <p class="errortext">{{ $message }}</p> @enderror
                    @if ($jenis === 'santunan')
                        <div class="mt-2 flex gap-2">
                            <button type="button" wire:click="$set('nominal', 500000)" class="btn-soft btn-sm">Rp 500rb</button>
                            <button type="button" wire:click="$set('nominal', 1000000)" class="btn-soft btn-sm">Rp 1 jt</button>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="label">Bulan <span class="text-rose-500">*</span></label>
                    <select wire:model="bulan" class="input @error('bulan') input-error @enderror">
                        <option value="">Pilih bulan</option>
                        @foreach (\App\Support\Format::BULAN as $n => $nama)<option value="{{ $n }}">{{ $nama }}</option>@endforeach
                    </select>
                    @error('bulan') <p class="errortext">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="label">Keterangan <span class="font-normal text-brand-950/40">(opsional)</span></label>
                <input wire:model="keterangan" class="input" placeholder="Otomatis diisi bulan & tahun bila kosong">
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" x-on:click="$wire.modal = false" class="btn-ghost">Batal</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><x-icon name="check" class="h-4 w-4" /> Simpan</button>
            </div>
        </form>
    </x-modal>
</div>
