<div class="w-full max-w-md space-y-5 bg-white p-6 rounded shadow">
  <h1 class="text-xl font-semibold">Masuk</h1>

  <form wire:submit.prevent="login" class="space-y-3">
    <label class="block">
      <span class="text-sm">No HP</span>
      <input type="text" wire:model="form.username" class="mt-1 w-full border rounded p-2">
      @error('form.username') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    <label class="block">
      <span class="text-sm">Password</span>
      <div class="mt-1 relative">
        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="form.password"
          class="w-full border rounded p-2 pr-16">
        <button type="button" wire:click="$toggle('showPassword')"
          class="absolute inset-y-0 right-2 flex items-center justify-center px-2 text-gray-600 hover:text-gray-900"
          aria-pressed="{{ $showPassword ? 'true' : 'false' }}"
          aria-label="{{ $showPassword ? 'Sembunyikan' : 'Tampilkan' }}">
          @if($showPassword)
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path
                d="M3 3l18 18M9.88 9.88a3 3 0 014.24 4.24M10.73 5.08A10.36 10.36 0 0121 12c-.72 1.39-1.74 2.61-2.98 3.58M6.74 6.74C4.77 7.9 3.1 9.72 3 12c.2 1.94 3.69 5.95 9 5.95 1.19 0 2.32-.2 3.35-.58" />
            </svg>
          @else
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          @endif
        </button>
      </div>
      @error('form.password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" wire:model="form.remember">
      <span class="text-sm">Ingat saya</span>
    </label>

    <button class="w-full bg-blue-600 text-white rounded p-2">Masuk</button>
  </form>

  <div class="text-sm text-center space-x-2">Belum punya akun?
    <a href="{{ route('register') }}" class="underline">Daftar</a>
  </div>
</div>
