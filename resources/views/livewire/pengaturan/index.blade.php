@php use App\Support\Format as F; @endphp
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-brand-950">Pengaturan</h2>
        <p class="mt-0.5 text-sm text-brand-950/50">Identitas organisasi, penanda tangan surat, dan periode tahun.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Identitas + penanda tangan --}}
        <form wire:submit="simpanPengaturan" class="card space-y-6 p-6 lg:col-span-3">
            <div>
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="home" class="h-4 w-4 text-brand-500" /> Identitas Organisasi</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Nama Organisasi (kop surat)</label>
                        <input wire:model="nama_organisasi" class="input @error('nama_organisasi') input-error @enderror">
                        @error('nama_organisasi') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Alamat Baris 1</label>
                        <input wire:model="alamat_baris1" class="input">
                    </div>
                    <div>
                        <label class="label">Alamat Baris 2</label>
                        <input wire:model="alamat_baris2" class="input">
                    </div>
                    <div>
                        <label class="label">Nama RT/RW</label>
                        <input wire:model="nama_rt" class="input @error('nama_rt') input-error @enderror">
                        @error('nama_rt') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Perumahan</label>
                        <input wire:model="nama_perumahan" class="input">
                    </div>
                    <div>
                        <label class="label">Tempat Penandatanganan</label>
                        <input wire:model="tempat" class="input" placeholder="Reni Jaya">
                    </div>
                    <div>
                        <label class="label">Iuran Default / Bulan (Rp)</label>
                        <input wire:model="iuran_default" type="number" min="0" step="500" class="input @error('iuran_default') input-error @enderror">
                        @error('iuran_default') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-brand-950/5 pt-6">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="user" class="h-4 w-4 text-brand-500" /> Penanda Tangan Surat</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label">Seksi Duka Cita</label>
                        <input wire:model="seksi_duka_cita" class="input @error('seksi_duka_cita') input-error @enderror">
                        @error('seksi_duka_cita') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Ketua PKK</label>
                        <input wire:model="ketua_pkk" class="input @error('ketua_pkk') input-error @enderror">
                        @error('ketua_pkk') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Ketua RT</label>
                        <input wire:model="ketua_rt" class="input @error('ketua_rt') input-error @enderror">
                        @error('ketua_rt') <p class="errortext">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <x-icon name="check" class="h-4 w-4" /> Simpan Pengaturan
                </button>
            </div>
        </form>

        {{-- Periode --}}
        <div class="card p-6 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="calendar" class="h-4 w-4 text-brand-500" /> Periode Tahun</h3>
                <button wire:click="bukaModalPeriode" class="btn-soft btn-sm"><x-icon name="plus" class="h-4 w-4" /> Baru</button>
            </div>

            <div class="space-y-2.5">
                @forelse ($periodeList as $p)
                    <div class="rounded-2xl p-3.5 ring-1 {{ $p->id === $periodeAktifId ? 'bg-brand-50/60 ring-brand-200' : 'bg-white ring-brand-950/5' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-extrabold text-brand-950">{{ $p->tahun }}</span>
                                @if ($p->id === $periodeAktifId)<span class="badge-green">Aktif dipilih</span>@endif
                                @if ($p->status === 'terkunci')<span class="badge-slate"><x-icon name="lock" class="h-3 w-3" /> Terkunci</span>@endif
                            </div>
                            <div class="flex items-center gap-1">
                                <button wire:click="kunci({{ $p->id }})" class="grid h-8 w-8 place-items-center rounded-lg text-brand-950/40 transition hover:bg-brand-50 hover:text-brand-700" title="{{ $p->status === 'aktif' ? 'Kunci' : 'Buka kunci' }}">
                                    <x-icon name="lock" class="h-4 w-4" />
                                </button>
                                <button wire:click="hapusPeriode({{ $p->id }})"
                                        wire:confirm="Hapus periode {{ $p->tahun }} beserta seluruh data peserta, iuran, dan santunannya? Tindakan ini tidak bisa dibatalkan."
                                        class="grid h-8 w-8 place-items-center rounded-lg text-rose-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-4 text-xs text-brand-950/50">
                            <span>Awal {{ F::rupiahRingkas($p->saldo_awal) }}</span>
                            <span class="font-semibold text-brand-700">Akhir {{ F::rupiahRingkas($p->saldoAkhir()) }}</span>
                            <span>{{ $p->jumlahPeserta() }} KK</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl bg-brand-50/40 py-6 text-center text-sm text-brand-950/45">Belum ada periode. Klik “Baru”.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal buat periode --}}
    <x-modal model="modalPeriode" title="Buat Periode Baru" subtitle="Saldo awal otomatis dari saldo akhir tahun sebelumnya.">
        <form wire:submit="simpanPeriode" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Tahun</label>
                    <input wire:model="tahunBaru" type="number" class="input @error('tahunBaru') input-error @enderror" placeholder="2026">
                    @error('tahunBaru') <p class="errortext">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Iuran / Bulan (Rp)</label>
                    <input wire:model="iuranBaru" type="number" min="0" step="500" class="input @error('iuranBaru') input-error @enderror">
                    @error('iuranBaru') <p class="errortext">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Saldo Awal (Rp)</label>
                    <input wire:model="saldoAwalBaru" type="number" min="0" step="1000" class="input @error('saldoAwalBaru') input-error @enderror">
                    @error('saldoAwalBaru') <p class="errortext">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($salinDari)
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl bg-brand-50/60 p-3.5 ring-1 ring-brand-100">
                    <input wire:model="salinPeserta" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-brand-950/20 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm">
                        <span class="font-semibold text-brand-950">Salin daftar peserta</span>
                        <span class="block text-brand-950/50">Peserta aktif dari tahun sebelumnya disalin ke periode baru (tanpa data iuran).</span>
                    </span>
                </label>
            @endif

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" x-on:click="$wire.modalPeriode = false" class="btn-ghost">Batal</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <x-icon name="plus" class="h-4 w-4" /> Buat Periode
                </button>
            </div>
        </form>
    </x-modal>
</div>
