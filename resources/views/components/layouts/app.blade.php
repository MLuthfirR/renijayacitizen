@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' : '' }}{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased">
<div x-data="{ sidebar: false }" class="min-h-full">

    {{-- Sidebar mobile overlay --}}
    <div x-cloak x-show="sidebar" x-transition.opacity class="fixed inset-0 z-40 bg-brand-950/30 lg:hidden"
         @click="sidebar = false"></div>

    {{-- Sidebar --}}
    <aside x-cloak
        :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-white/95 backdrop-blur ring-1 ring-brand-950/5 transition-transform duration-300 lg:translate-x-0">

        <div class="flex items-center gap-3 px-6 py-6">
            <div class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30">
                <x-icon name="heart-hand" class="h-6 w-6" />
            </div>
            <div class="leading-tight">
                <p class="text-sm font-extrabold text-brand-950">Duka Cita</p>
                <p class="text-xs font-medium text-brand-950/50">RT.02 / RW.06 Reni Jaya</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 pb-4">
            @php
                $nav = [
                    ['dashboard', 'dashboard', 'Dashboard', 'dashboard'],
                    ['peserta.index', 'peserta.*', 'Peserta', 'users'],
                    ['iuran.index', 'iuran.*', 'Kartu Iuran', 'wallet'],
                    ['santunan.index', 'santunan.*', 'Santunan', 'heart-hand'],
                    ['laporan.index', 'laporan.*', 'Laporan', 'report'],
                    ['pengaturan.index', 'pengaturan.*', 'Pengaturan', 'settings'],
                ];
            @endphp
            <p class="px-3 pb-1 pt-3 text-[0.7rem] font-bold uppercase tracking-wider text-brand-950/35">Menu</p>
            @foreach ($nav as [$route, $pattern, $label, $icon])
                <a href="{{ route($route) }}" wire:navigate
                   class="nav-link {{ request()->routeIs($pattern) ? 'nav-link-active' : '' }}">
                    <x-icon name="{{ $icon }}" class="h-5 w-5 shrink-0" />
                    <span>{{ $label }}</span>
                </a>
            @endforeach

            <p class="px-3 pb-1 pt-5 text-[0.7rem] font-bold uppercase tracking-wider text-brand-950/35">Publik</p>
            <a href="{{ route('publik.index') }}" target="_blank" class="nav-link">
                <x-icon name="globe" class="h-5 w-5 shrink-0" />
                <span>Halaman Transparansi</span>
                <x-icon name="arrow-right" class="ml-auto h-4 w-4 opacity-40" />
            </a>
        </nav>

        <div class="border-t border-brand-950/5 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full text-left hover:bg-rose-50 hover:text-rose-600">
                    <x-icon name="logout" class="h-5 w-5 shrink-0" />
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="lg:pl-72">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex items-center gap-4 border-b border-brand-950/5 bg-canvas/80 px-4 py-3.5 backdrop-blur-md sm:px-8">
            <button @click="sidebar = true" class="btn-ghost btn-sm lg:hidden" aria-label="Buka menu">
                <x-icon name="menu" class="h-5 w-5" />
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-lg font-bold text-brand-950">{{ $title ?? config('app.name') }}</h1>
            </div>

            {{-- Pemilih periode --}}
            @isset($daftarPeriode)
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" class="btn-ghost btn-sm gap-2">
                    <x-icon name="calendar" class="h-4 w-4 text-brand-500" />
                    <span class="font-bold">{{ $periodeAktif?->tahun ?? '—' }}</span>
                    <x-icon name="chevron-down" class="h-4 w-4 opacity-50" />
                </button>
                <div x-cloak x-show="open" x-transition
                     class="absolute right-0 z-40 mt-2 w-44 overflow-hidden rounded-2xl bg-white p-1.5 shadow-xl ring-1 ring-brand-950/10">
                    <p class="px-3 py-1.5 text-[0.7rem] font-bold uppercase tracking-wide text-brand-950/40">Pilih Tahun</p>
                    @foreach ($daftarPeriode as $p)
                        <form method="POST" action="{{ route('periode.pilih') }}">
                            @csrf
                            <input type="hidden" name="periode_id" value="{{ $p->id }}">
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-brand-50 {{ $periodeAktif?->id === $p->id ? 'text-brand-700' : 'text-brand-950/70' }}">
                                {{ $p->tahun }}
                                @if ($periodeAktif?->id === $p->id)
                                    <x-icon name="check" class="h-4 w-4" />
                                @elseif ($p->status === 'terkunci')
                                    <x-icon name="lock" class="h-3.5 w-3.5 opacity-40" />
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            @endisset

            <div class="hidden items-center gap-2.5 rounded-full bg-white py-1 pl-1 pr-3.5 ring-1 ring-brand-950/8 sm:flex">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                </span>
                <span class="max-w-[10rem] truncate text-sm font-semibold text-brand-950/80">{{ auth()->user()->name ?? 'Pengurus' }}</span>
            </div>
        </header>

        {{-- Flash --}}
        @if (session('sukses') || session('error'))
            <div class="px-4 pt-4 sm:px-8" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)">
                @if (session('sukses'))
                    <div class="flex items-center gap-2.5 rounded-2xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800 ring-1 ring-brand-200">
                        <x-icon name="check-circle" class="h-5 w-5 text-brand-500" /> {{ session('sukses') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center gap-2.5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200">
                        <x-icon name="alert" class="h-5 w-5" /> {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        <main class="px-4 py-6 sm:px-8 sm:py-8">
            <div class="mx-auto max-w-7xl animate-rise">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
