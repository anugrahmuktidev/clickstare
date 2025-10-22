<div class="mx-auto w-full max-w-6xl px-3 sm:px-4 py-6 sm:py-8 space-y-6">

  {{-- Header --}}
  <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold">Validasi Siswa</h1>
      <p class="text-gray-600 text-sm sm:text-base">
        Pilih satu atau banyak siswa dari sekolah Anda untuk disetujui / ditolak.
      </p>
    </div>
    <a href="{{ route('guru.dashboard') }}"
       class="text-sm text-blue-600 hover:underline sm:flex-shrink-0">← Kembali ke Dashboard</a>
  </header>

  @if (session('ok'))
    <div class="p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
      {{ session('ok') }}
    </div>
  @endif

  {{-- Filter & Aksi --}}
  <div class="bg-white border rounded-lg p-3 sm:p-4 shadow-sm">
    <div class="flex flex-col gap-3">
      {{-- bar filter --}}
      <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
          <input type="text"
                 wire:model.debounce.400ms="search"
                 placeholder="Cari nama / NISN / username"
                 class="border rounded px-3 py-2 text-sm w-full sm:w-72">

          <select wire:model="kelas" class="border rounded px-3 py-2 text-sm w-full sm:w-44">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $k)
              <option value="{{ $k }}">{{ $k }}</option>
            @endforeach
          </select>
        </div>

        <div class="flex items-center gap-3">
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model.live="selectAllPage">
            <span>Pilih semua (halaman ini)</span>
          </label>

          <div class="flex gap-2">
            <button wire:click="approveSelected"
                    wire:loading.attr="disabled"
                    @if (count($checked) === 0) disabled @endif
                    class="px-3 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
              Setujui Terpilih ({{ count($checked) }})
            </button>
            <button wire:click="rejectSelected"
                    wire:loading.attr="disabled"
                    @if (count($checked) === 0) disabled @endif
                    class="px-3 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
              Tolak Terpilih ({{ count($checked) }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MOBILE LIST (kartu), tampil < md --}}
  <div class="md:hidden space-y-3">
    @forelse ($students as $s)
      <div class="bg-white border rounded-lg shadow-sm p-3" wire:key="card-{{ $s->id }}">
        <div class="flex items-start gap-3">
          <input type="checkbox" value="{{ $s->id }}" wire:model.live="checked" class="mt-1.5">
          <div class="flex-1">
            <div class="font-medium">{{ $s->name }}</div>
            <div class="mt-1 grid grid-cols-2 gap-x-3 gap-y-1 text-sm">
              <div><span class="text-gray-500">NISN:</span> {{ $s->nisn ?? '—' }}</div>
              <div><span class="text-gray-500">Kelas:</span> {{ $s->kelas ?? '—' }}</div>
              <div class="col-span-2">
                <span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800 border border-yellow-200">
                  Pending
                </span>
              </div>
            </div>

            <div class="mt-3 flex gap-2">
              <button wire:click="approveOne({{ $s->id }})"
                      class="flex-1 px-3 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                ACC
              </button>
              <button wire:click="rejectOne({{ $s->id }})"
                      class="flex-1 px-3 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700">
                Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="bg-white border rounded-lg p-4 text-center text-gray-500">Tidak ada data siswa pending.</div>
    @endforelse

    <div class="mt-2">{{ $students->links() }}</div>
  </div>

  {{-- TABEL ≥ md --}}
  <div class="hidden md:block bg-white border rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-gray-700">
            <th class="py-2 pl-3 pr-2 w-10">
              <input type="checkbox" wire:model.live="selectAllPage">
            </th>
            <th class="py-2 px-3 text-left">Nama</th>
            <th class="py-2 px-3 text-left">NISN</th>
            <th class="py-2 px-3 text-left">Kelas</th>
            <th class="py-2 px-3 text-left">Status</th>
            <th class="py-2 px-3"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $s)
            <tr class="border-t" wire:key="row-{{ $s->id }}">
              <td class="py-2 pl-3 pr-2">
                <input type="checkbox" value="{{ $s->id }}" wire:model.live="checked">
              </td>
              <td class="py-2 px-3">{{ $s->name }}</td>
              <td class="py-2 px-3">{{ $s->nisn ?? '—' }}</td>
              <td class="py-2 px-3">{{ $s->kelas ?? '—' }}</td>
              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800 border border-yellow-200">
                  Pending
                </span>
              </td>
              <td class="py-2 px-3 text-right">
                <button wire:click="approveOne({{ $s->id }})"
                        class="px-2.5 py-1.5 rounded bg-emerald-600 text-white text-xs hover:bg-emerald-700">
                  ACC
                </button>
                <button wire:click="rejectOne({{ $s->id }})"
                        class="ml-2 px-2.5 py-1.5 rounded bg-red-600 text-white text-xs hover:bg-red-700">
                  Tolak
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-6 text-center text-gray-500">Tidak ada data siswa pending.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-3 border-t">
      {{ $students->links() }}
    </div>
  </div>

</div>
