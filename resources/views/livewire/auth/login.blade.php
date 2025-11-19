<div class="w-full max-w-md space-y-5 bg-white p-6 rounded shadow">
  <h1 class="text-xl font-semibold">Masuk</h1>

  <form wire:submit.prevent="login" class="space-y-3">
    <label class="block">
      <span class="text-sm">No HP</span>
      <input type="text" wire:model="form.username" class="mt-1 w-full border rounded p-2">
      @error('form.username') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    <div class="block">
      <label for="login-password" class="text-sm">Password</label>
      <input id="login-password" type="{{ $showPassword ? 'text' : 'password' }}" wire:model="form.password"
        class="mt-1 w-full border rounded p-2">
      <div class="mt-2 flex items-center gap-2 text-sm text-gray-700">
        <input id="show-login-password" type="checkbox" wire:click="$toggle('showPassword')"
          @checked($showPassword)>
        <label for="show-login-password" class="select-none cursor-pointer">Tampilkan password</label>
      </div>
      @error('form.password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <button class="w-full bg-blue-600 text-white rounded p-2">Masuk</button>
  </form>

  <div class="text-sm text-center space-x-2">Belum punya akun?
    <a href="{{ route('register') }}" class="underline">Daftar</a>
  </div>
</div>
