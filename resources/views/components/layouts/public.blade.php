@props(['title' => 'Transparansi Keuangan'])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Duka Cita RT.02/RW.06 Reni Jaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased">
    <div class="min-h-full">
        {{-- Navbar --}}
        <header class="sticky top-0 z-30 border-b border-brand-950/5 bg-white/80 backdrop-blur-md">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
                <a href="{{ route('publik.index') }}" class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                        <x-icon name="heart-hand" class="h-5 w-5" />
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-extrabold text-brand-950">Duka Cita RT.02/RW.06</span>
                        <span class="block text-[0.7rem] font-medium text-brand-950/50">Perum Reni Jaya, Pamulang</span>
                    </span>
                </a>
                <a href="{{ route('login') }}" class="btn-ghost btn-sm">
                    <x-icon name="lock" class="h-4 w-4" /> <span class="hidden sm:inline">Masuk</span> Pengurus
                </a>
            </div>
        </header>

        <main>{{ $slot }}</main>

        <footer class="border-t border-brand-950/5 py-8 text-center">
            <p class="text-xs text-brand-950/45">&copy; {{ date('Y') }} PKK RT.02/RW.06 Reni Jaya · Pamulang, Tangerang Selatan</p>
            <p class="mt-1 text-xs text-brand-950/35">Halaman ini dibuat untuk transparansi keuangan kepada seluruh warga.</p>
        </footer>
    </div>
    @livewireScripts
</body>
</html>
