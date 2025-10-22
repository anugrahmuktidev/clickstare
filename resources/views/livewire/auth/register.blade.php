<div class="w-full max-w-md space-y-5 bg-white p-6 rounded shadow">
  <h1 class="text-xl font-semibold">Registrasi Clicstare</h1>

  {{-- Pilih Role --}}
  <div class="space-y-2">
    <label class="text-sm font-medium">Daftar sebagai</label>
    <select wire:model.live="role" class="w-full border rounded p-2">
      <option value="siswa">Siswa</option>
      <option value="guru">Guru</option>
    </select>
  </div>

  <form wire:submit.prevent="register" class="space-y-3">
    {{-- Nama --}}
    <label class="block">
      <span class="text-sm">Nama Lengkap</span>
      <input type="text" wire:model="name" class="mt-1 w-full border rounded p-2">
      @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    {{-- NISN / NIP (dinamis) --}}
    <label class="block">
      <span class="text-sm">{{ $role === 'guru' ? 'NIP' : 'Nomor HP' }}</span>
      <input type="text" wire:model="id_number" class="mt-1 w-full border rounded p-2">
      @error('id_number') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    {{-- Umum --}}
    <label class="block">
      <span class="text-sm">Sekolah</span>
      <select wire:model="sekolah_id" class="mt-1 w-full border rounded p-2">
        <option value="">Pilih Sekolah</option>
        @foreach($sekolahs as $sk)
          <option value="{{ $sk->id }}">{{ $sk->nama }}</option>
        @endforeach
      </select>
      @error('sekolah_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>

    {{-- Khusus per role --}}
    @if($role === 'siswa')
      <div class="grid grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm">Umur</span>
          <input type="number" wire:model="umur" class="mt-1 w-full border rounded p-2">
          @error('umur') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </label>
        <label class="block">
          <span class="text-sm">Kelas</span>
          <input type="text" wire:model="kelas" class="mt-1 w-full border rounded p-2">
          @error('kelas') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </label>
      </div>

      <label class="block">
        <span class="text-sm">Jenis Kelamin</span>
        <select wire:model="jenis_kelamin" class="mt-1 w-full border rounded p-2">
          <option value="">-- Pilih Jenis Kelamin --</option>
          <option value="laki-laki">Laki-laki</option>
          <option value="perempuan">Perempuan</option>
        </select>
        @error('jenis_kelamin') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </label>

      <label class="block">
        <span class="text-sm">Pekerjaan Orang Tua</span>
        <input type="text" wire:model="pekerjaan_orangtua" class="mt-1 w-full border rounded p-2">
        @error('pekerjaan_orangtua') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </label>

      <label class="block">
        <span class="text-sm">Alamat</span>
        <textarea wire:model="alamat" rows="3" class="mt-1 w-full border rounded p-2"></textarea>
        @error('alamat') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </label>
    @else
      <div class="grid grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm">Umur</span>
          <input type="number" wire:model="umur" class="mt-1 w-full border rounded p-2">
          @error('umur') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </label>
       <label class="block">
  <span class="text-sm">Jabatan</span>
  <select wire:model="jabatan" class="mt-1 w-full border rounded p-2">
    <option value="">-- Pilih Jabatan --</option>
    <option value="walikelas">Wali Kelas</option>
    <option value="guru">Guru</option>
    <option value="kepsek">Kepala Sekolah</option>
    <option value="bk">Guru BK</option>
  </select>
  @error('jabatan') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
</label>

      </div>
    @endif

    {{-- Password --}}
    <label class="block">
      <span class="text-sm">Password</span>
      <input type="password" wire:model="password" class="mt-1 w-full border rounded p-2">
      @error('password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </label>
    <label class="block">
      <span class="text-sm">Konfirmasi Password</span>
      <input type="password" wire:model="password_confirmation" class="mt-1 w-full border rounded p-2">
    </label>

    <button class="w-full bg-emerald-600 text-white rounded p-2">Daftar</button>
  </form>

  <div class="text-sm text-center">
    Sudah punya akun? <a href="{{ route('login') }}" class="underline">Masuk</a>
  </div>
</div>
