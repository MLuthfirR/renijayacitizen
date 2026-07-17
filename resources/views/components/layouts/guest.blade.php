@props(['title' => 'Masuk'])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased">
<div class="relative flex min-h-full items-center justify-center overflow-hidden px-4 py-12">
    {{-- Ornamen latar lembut --}}
    <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-brand-200/40 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-brand-100/60 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md animate-rise">
        <div class="mb-8 flex flex-col items-center text-center">
            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30">
                <x-icon name="heart-hand" class="h-7 w-7" />
            </div>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-brand-950">Iuran Duka Cita</h1>
            <p class="mt-1 text-sm font-medium text-brand-950/50">RT.02 / RW.06 — Perum Reni Jaya, Pamulang</p>
        </div>

        {{ $slot }}

        <p class="mt-8 text-center text-xs text-brand-950/40">
            &copy; {{ date('Y') }} PKK RT.02/RW.06 Reni Jaya ·
            <a href="{{ route('publik.index') }}" class="font-semibold text-brand-600 hover:underline">Lihat laporan publik</a>
        </p>
    </div>
</div>
</body>
</html>
