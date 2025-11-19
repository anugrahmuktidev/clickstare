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
          class="w-full border rounded p-2 pr-24">
        <button type="button" wire:click="$toggle('showPassword')"
          class="absolute inset-y-0 right-2 px-3 text-xs font-semibold text-gray-600 hover:text-gray-900"
          aria-pressed="{{ $showPassword ? 'true' : 'false' }}">
          {{ $showPassword ? 'Sembunyikan' : 'Tampilkan' }}
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
