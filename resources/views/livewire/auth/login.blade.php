<div>
    <form wire:submit="login" class="card p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-brand-950">Masuk Pengurus</h2>
            <p class="mt-1 text-sm text-brand-950/50">Silakan masuk untuk mengelola data iuran.</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="label" for="email">Email</label>
                <input wire:model="email" id="email" type="email" autocomplete="username" autofocus
                       class="input @error('email') input-error @enderror" placeholder="nama@email.com">
                @error('email') <p class="errortext">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="password">Kata Sandi</label>
                <div x-data="{ show: false }" class="relative">
                    <input wire:model="password" id="password" :type="show ? 'text' : 'password'"
                           autocomplete="current-password"
                           class="input pr-11 @error('password') input-error @enderror" placeholder="••••••••">
                    <button type="button" @click="show = !show" tabindex="-1"
                            class="absolute inset-y-0 right-0 grid w-11 place-items-center text-brand-950/40 hover:text-brand-700">
                        <x-icon name="search" class="h-4 w-4" x-show="!show" />
                        <x-icon name="lock" class="h-4 w-4" x-show="show" x-cloak />
                    </button>
                </div>
                @error('password') <p class="errortext">{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-brand-950/70">
                <input wire:model="remember" type="checkbox"
                       class="h-4 w-4 rounded border-brand-950/20 text-brand-600 focus:ring-brand-500">
                Ingat saya di perangkat ini
            </label>
        </div>

        <button type="submit" class="btn-primary mt-6 w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">Masuk</span>
            <span wire:loading wire:target="login">Memproses…</span>
            <x-icon name="arrow-right" class="h-4 w-4" wire:loading.remove wire:target="login" />
        </button>
    </form>
</div>
