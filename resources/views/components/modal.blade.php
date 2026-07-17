@props(['model', 'title' => null, 'subtitle' => null, 'max' => 'max-w-lg'])

<div x-data="{ open: $wire.entangle('{{ $model }}') }"
     x-cloak x-show="open"
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-[60] overflow-y-auto">
    {{-- Backdrop --}}
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 bg-brand-950/40 backdrop-blur-sm" x-on:click="open = false"></div>

    <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-6 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-6 sm:scale-95"
             class="relative w-full {{ $max }} rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-brand-950/5 sm:p-7">
            @if ($title)
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-brand-950">{{ $title }}</h3>
                        @if ($subtitle)<p class="mt-0.5 text-sm text-brand-950/50">{{ $subtitle }}</p>@endif
                    </div>
                    <button type="button" x-on:click="open = false"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-brand-950/40 transition hover:bg-brand-50 hover:text-brand-700">
                        <x-icon name="close" class="h-5 w-5" />
                    </button>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
