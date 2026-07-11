{{-- resources/views/livewire/guru/dashboard.blade.php --}}
<div x-data="{ tab: 'validasi' }" class="mx-auto w-full max-w-6xl px-3 sm:px-4 py-6 sm:py-8 space-y-6 sm:space-y-8">

  {{-- Header --}}
  <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold">Dashboard Guru</h1>
      <p class="text-gray-600 text-sm sm:text-base">Ringkasan aktivitas siswa di sekolah Anda.</p>
    </div>

    <div class="sm:flex-shrink-0 flex items-center gap-2">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="inline-flex items-center justify-center px-3 py-2 rounded border hover:bg-gray-50 text-sm font-medium w-full sm:w-auto">
          Keluar
        </button>
      </form>
    </div>
  </header>

  {{-- Tabbar --}}
  <section class="bg-white border rounded-lg shadow-sm p-2">
    <nav class="flex flex-wrap gap-2" aria-label="Navigasi Dashboard Guru">
      <button type="button"
              @click="tab = 'validasi'"
              :class="tab === 'validasi' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              class="px-3 py-2 rounded-md border text-sm font-medium transition">
        Validasi
      </button>
      <button type="button"
              @click="tab = 'edukasi'"
              :class="tab === 'edukasi' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              class="px-3 py-2 rounded-md border text-sm font-medium transition">
        Edukasi
      </button>
      <button type="button"
              @click="tab = 'forum'"
              :class="tab === 'forum' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              class="px-3 py-2 rounded-md border text-sm font-medium transition">
        Forum Sekolah
      </button>
      <button type="button"
              @click="tab = 'ringkasan'"
              :class="tab === 'ringkasan' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              class="px-3 py-2 rounded-md border text-sm font-medium transition">
        Ringkasan Siswa
      </button>
    </nav>
  </section>

  {{-- Tab: Validasi --}}
  <div x-show="tab === 'validasi'" x-cloak class="space-y-6 sm:space-y-8">
    <section class="bg-white border rounded-lg shadow-sm p-3 sm:p-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="font-semibold text-sm sm:text-base">Validasi Siswa</h2>
          <p class="text-xs sm:text-sm text-gray-600">Setujui/Tolak siswa dari sekolah Anda melalui halaman validasi.</p>
        </div>
        <a href="{{ route('guru.validate') }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow w-full sm:w-auto">
          Buka Halaman Validasi
        </a>
      </div>
    </section>

    {{-- Kontrol Akses Pretest/Posttest --}}
    <section class="bg-white border rounded-lg shadow-sm p-3 sm:p-4">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-sm sm:text-base">Kontrol Akses Tes Siswa</h2>
    </div>

    @if (session('exam_ok'))
      <div class="mb-3 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">
        {{ session('exam_ok') }}
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <article class="border rounded-lg p-3">
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="font-semibold text-sm">Pretest</p>
            <p class="text-xs text-gray-500 mt-1">Urutan siswa: Pengetahuan lalu Sikap.</p>
          </div>
          <span class="px-2 py-1 rounded text-xs font-semibold {{ $isPretestEnabled ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
            {{ $isPretestEnabled ? 'Aktif' : 'Nonaktif' }}
          </span>
        </div>
        @if ($isPretestEnabled)
          <button type="button"
                  wire:click="togglePretestAccess"
                  wire:loading.attr="disabled"
                  style="display:inline-flex;align-items:center;justify-content:center;padding:0.5rem 0.75rem;border-radius:0.375rem;border:1px solid #374151;background:#374151;color:#ffffff;"
                  class="mt-3 inline-flex items-center justify-center px-3 py-2 rounded text-sm font-medium text-white border border-gray-700 bg-gray-700 hover:bg-gray-800 disabled:opacity-50">
            Nonaktifkan Pretest
          </button>
        @else
          <button type="button"
                  wire:click="togglePretestAccess"
                  wire:loading.attr="disabled"
                  style="display:inline-flex;align-items:center;justify-content:center;padding:0.5rem 0.75rem;border-radius:0.375rem;border:1px solid #059669;background:#059669;color:#ffffff;"
                  class="mt-3 inline-flex items-center justify-center px-3 py-2 rounded text-sm font-medium text-white border border-emerald-600 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50">
            Aktifkan Pretest
          </button>
        @endif
      </article>

      <article class="border rounded-lg p-3">
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="font-semibold text-sm">Posttest</p>
            <p class="text-xs text-gray-500 mt-1">Urutan siswa: Pengetahuan lalu Sikap.</p>
          </div>
          <span class="px-2 py-1 rounded text-xs font-semibold {{ $isPosttestEnabled ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
            {{ $isPosttestEnabled ? 'Aktif' : 'Nonaktif' }}
          </span>
        </div>
        @if ($isPosttestEnabled)
          <button type="button"
                  wire:click="togglePosttestAccess"
                  wire:loading.attr="disabled"
                  style="display:inline-flex;align-items:center;justify-content:center;padding:0.5rem 0.75rem;border-radius:0.375rem;border:1px solid #374151;background:#374151;color:#ffffff;"
                  class="mt-3 inline-flex items-center justify-center px-3 py-2 rounded text-sm font-medium text-white border border-gray-700 bg-gray-700 hover:bg-gray-800 disabled:opacity-50">
            Nonaktifkan Posttest
          </button>
        @else
          <button type="button"
                  wire:click="togglePosttestAccess"
                  wire:loading.attr="disabled"
                  style="display:inline-flex;align-items:center;justify-content:center;padding:0.5rem 0.75rem;border-radius:0.375rem;border:1px solid #059669;background:#059669;color:#ffffff;"
                  class="mt-3 inline-flex items-center justify-center px-3 py-2 rounded text-sm font-medium text-white border border-emerald-600 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50">
            Aktifkan Posttest
          </button>
        @endif
      </article>
    </div>
    </section>
  </div>

  {{-- Tab: Edukasi --}}
  <div x-show="tab === 'edukasi'" x-cloak class="space-y-6 sm:space-y-8">
    {{-- Video Edukasi --}}
    <section class="bg-white border rounded-lg shadow-sm p-3 sm:p-4">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-sm sm:text-base">Video Edukasi</h2>
      <div class="hidden md:flex gap-2">
        <button id="vLeft" class="px-3 py-1.5 rounded border hover:bg-gray-50">◀</button>
        <button id="vRight" class="px-3 py-1.5 rounded border hover:bg-gray-50">▶</button>
      </div>
    </div>

    <div id="vRow"
         class="flex gap-3 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-1"
         style="scrollbar-width: thin;">
      @forelse ($videos as $v)
        <a href="{{ route('education.watch', $v->id) }}"
           class="snap-start bg-gray-50 rounded-lg border p-2 hover:bg-gray-100
                  min-w-[72%] max-w-[72%]
                  sm:min-w-[240px] sm:max-w-[240px]
                  md:min-w-[260px] md:max-w-[260px]
                  lg:min-w-[300px] lg:max-w-[300px]">
          <div class="aspect-video w-full rounded-md bg-gray-200 overflow-hidden mb-2">
            @if ($v->thumbnail_url)
              <img src="{{ $v->thumbnail_url }}" class="w-full h-full object-cover" alt="{{ $v->judul }}">
            @else
              <div class="w-full h-full grid place-items-center text-gray-500 text-sm px-3 text-center">
                {{ \Illuminate\Support\Str::limit($v->judul, 60) }}
              </div>
            @endif
          </div>
          <div class="px-1">
            <h3 class="font-semibold text-sm leading-snug line-clamp-2">{{ $v->judul }}</h3>
            @if ($v->deskripsi)
              <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $v->deskripsi }}</p>
            @endif
          </div>
        </a>
      @empty
        <p class="text-gray-500">Belum ada video.</p>
      @endforelse
    </div>

    <script>
      (function(){
        const row = document.getElementById('vRow');
        const step = () => {
          const card = row?.querySelector('a');
          if (!card) return 260;
          const gap = parseFloat(getComputedStyle(row).gap || 16);
          return Math.round(card.getBoundingClientRect().width + gap);
        };
        document.getElementById('vLeft')?.addEventListener('click', () => row.scrollBy({ left: -step(), behavior: 'smooth' }));
        document.getElementById('vRight')?.addEventListener('click', () => row.scrollBy({ left:  step(), behavior: 'smooth' }));
      })();
    </script>
    </section>
  </div>

  {{-- Tab: Forum Sekolah --}}
  <div x-show="tab === 'forum'" x-cloak class="space-y-6 sm:space-y-8">
    @if ($newThreads > 0 || $unansweredThreads > 0)
      <section class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-3 sm:p-4 flex flex-col sm:flex-row sm:items-start sm:gap-3">
        <div class="text-xl sm:text-2xl">🔔</div>
        <div class="space-y-1 text-sm sm:text-base">
          <p class="font-semibold">Aktivitas Forum</p>
          @if ($newThreads > 0)
            <p>{{ $newThreads }} pertanyaan baru dalam 7 hari terakhir.</p>
          @endif
          @if ($unansweredThreads > 0)
            <p>{{ $unansweredThreads }} pertanyaan belum memiliki solusi.</p>
          @endif
        </div>
      </section>
    @endif

    {{-- Forum Sekolah --}}
    <section class="bg-white border rounded-lg shadow-sm p-3 sm:p-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-3">
        <h2 class="font-semibold text-sm sm:text-base">Forum Sekolah</h2>
        <span class="text-xs sm:text-sm text-gray-500">{{ $newThreads }} pertanyaan baru (7 hari)</span>
      </div>

      @if (session('ok'))
        <div class="mb-3 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
          {{ session('ok') }}
        </div>
      @endif

      @forelse ($threads as $t)
        @php $replyCount = $t->replies->count(); @endphp
        <article x-data="{ openReplies: false, openForm: false }" class="border rounded p-3 mb-3">
          <header class="mb-2 flex items-start justify-between gap-2">
            <div>
              <h3 class="font-semibold text-sm sm:text-base break-words line-clamp-1">{{ $t->judul }}</h3>
              <p class="text-[11px] sm:text-xs text-gray-500 mt-0.5">
                oleh <b>{{ $t->asker->name }}</b> • {{ $t->created_at->diffForHumans() }}
              </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
              <span class="px-2 py-0.5 text-[10px] sm:text-xs rounded bg-gray-100 text-gray-700">{{ $replyCount }} Balasan</span>
              @if ($t->status === 'closed')
                <span class="px-2 py-0.5 text-[10px] sm:text-xs rounded bg-emerald-100 text-emerald-700">Selesai</span>
              @endif
            </div>
          </header>

          @php
            $titleRaw = (string) \Illuminate\Support\Str::of($t->judul)->before(' • #');
            $content  = (string) $t->isi;
            $afterTitle = \Illuminate\Support\Str::of($content)->startsWith($titleRaw)
              ? (string) \Illuminate\Support\Str::of($content)->after($titleRaw)
              : $content;
            $preview = (string) \Illuminate\Support\Str::of($afterTitle)->ltrim('. ')->trim();
          @endphp
          @if ($preview !== '')
            <p class="text-gray-700 text-sm sm:text-base mb-2 break-words">{{ $preview }}</p>
          @endif

          @if ($t->solution)
            <div class="mb-3 border-l-4 border-emerald-500 bg-emerald-50 p-3 rounded-r">
              <div class="text-sm text-emerald-700">
                <strong>Solusi oleh {{ $t->solution->user->name }}:</strong>
                <div class="mt-1 break-words">{{ $t->solution->isi }}</div>
              </div>
            </div>
          @endif

          <div class="flex items-center gap-2 mb-2">
            <button type="button" @click="openReplies = !openReplies"
                    class="px-3 py-1.5 text-xs sm:text-sm rounded border hover:bg-gray-50">
              <span x-show="!openReplies">Lihat {{ $replyCount }} Balasan</span>
              <span x-show="openReplies">Sembunyikan Balasan</span>
            </button>

            <button type="button" @click="openForm = !openForm"
                    class="px-3 py-1.5 text-xs sm:text-sm rounded border hover:bg-gray-50">
              <span x-show="!openForm">Tulis Jawaban</span>
              <span x-show="openForm">Tutup Form</span>
            </button>
          </div>

          <div x-show="openReplies" x-cloak class="space-y-2">
            @foreach ($t->replies as $r)
              <div class="p-2 border rounded">
                <div class="text-[11px] sm:text-xs text-gray-500 mb-1">
                  {{ $r->user->name }} • {{ $r->created_at->diffForHumans() }}
                  @if ($r->is_solution)
                    <span class="ml-2 px-2 py-0.5 text-[10px] rounded bg-emerald-100 text-emerald-700">Solusi</span>
                  @endif
                </div>
                <div class="text-sm sm:text-base break-words">{{ $r->isi }}</div>

                @can('resolve-thread', $t)
                  @unless($r->is_solution)
                    <button wire:click="markSolution({{ $r->id }})"
                            class="mt-2 px-3 py-1 text-xs sm:text-sm rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                      Tandai Solusi
                    </button>
                  @endunless
                @endcan
              </div>
            @endforeach
          </div>

          {{-- Balas cepat --}}
          <div x-show="openForm" x-cloak class="mt-3">
            <textarea wire:model.defer="reply.{{ $t->id }}" rows="2"
                      class="w-full border rounded px-3 py-2 text-sm sm:text-base"
                      placeholder="Tulis jawaban..."></textarea>
            <label class="mt-2 inline-flex items-center gap-2 text-xs sm:text-sm text-gray-700">
              <input type="checkbox" class="rounded border-gray-300" wire:model.defer="asSolution.{{ $t->id }}">
              Tandai sebagai solusi (menutup diskusi)
            </label>
            <div class="mt-2">
              <button wire:click="answer({{ $t->id }})"
                      class="px-3 py-1.5 rounded bg-gray-800 hover:bg-black text-white text-sm">
                Kirim Jawaban
              </button>
            </div>
          </div>
        </article>
      @empty
        <p class="text-gray-500">Belum ada pertanyaan di sekolah Anda.</p>
      @endforelse

      <div class="mt-2">{{ $threads->links() }}</div>
    </section>
  </div>

  {{-- Tab: Ringkasan Siswa --}}
  <div x-show="tab === 'ringkasan'" x-cloak class="space-y-6 sm:space-y-8">
    {{-- Ringkasan --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <div class="p-3 sm:p-4 bg-white border rounded-lg shadow-sm">
      <p class="text-xs sm:text-sm text-gray-500">Total Siswa</p>
      <p class="text-xl sm:text-2xl font-bold">{{ number_format($totalSiswa) }}</p>
    </div>
    <div class="p-3 sm:p-4 bg-white border rounded-lg shadow-sm">
      <p class="text-xs sm:text-sm text-gray-500">Siswa Tervalidasi</p>
      <p class="text-xl sm:text-2xl font-bold">{{ number_format($totalSiswaValid) }}</p>
    </div>
    <div class="p-3 sm:p-4 bg-white border rounded-lg shadow-sm">
      <p class="text-xs sm:text-sm text-gray-500">Rata-rata Pretest</p>
      <p class="text-xl sm:text-2xl font-bold">{{ $avgPre }}<span class="text-xs sm:text-sm">/100</span></p>
    </div>
    <div class="p-3 sm:p-4 bg-white border rounded-lg shadow-sm">
      <p class="text-xs sm:text-sm text-gray-500">Rata-rata Posttest</p>
      <p class="text-xl sm:text-2xl font-bold">{{ $avgPost }}<span class="text-xs sm:text-sm">/100</span></p>
    </div>
    </section>

    {{-- Progress Siswa (responsif) --}}
    <section class="bg-white border rounded-lg shadow-sm p-3 sm:p-4">
    <h2 class="font-semibold text-sm sm:text-base mb-2 sm:mb-3">Progress Siswa (10 data per halaman)</h2>

    {{-- Mobile: list cards --}}
    <div class="md:hidden space-y-3">
      @forelse ($students as $s)
        <div class="border rounded-lg p-3">
          <div class="font-medium">{{ $s->name }}</div>
          <div class="mt-1 grid grid-cols-2 gap-2 text-sm">
            <div><span class="text-gray-500">Pretest:</span> <b>{{ $s->pre_score !== null ? $s->pre_score : '—' }}</b></div>
            <div><span class="text-gray-500">Posttest:</span> <b>{{ $s->post_score !== null ? $s->post_score : '—' }}</b></div>
            <div class="col-span-2 text-gray-600">Terakhir: {{ $s->last_answered_at?->diffForHumans() ?? '—' }}</div>
          </div>
        </div>
      @empty
        <p class="text-gray-500">Belum ada data.</p>
      @endforelse
    </div>

    {{-- Desktop/Tablet: table --}}
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-gray-600">
            <th class="py-2 pr-4">Nama</th>
            <th class="py-2 pr-4">Pretest</th>
            <th class="py-2 pr-4">Posttest</th>
            <th class="py-2 pr-4">Terakhir</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $s)
            <tr class="border-t">
              <td class="py-2 pr-4">{{ $s->name }}</td>
              <td class="py-2 pr-4">{{ $s->pre_score !== null ? $s->pre_score : '—' }}</td>
              <td class="py-2 pr-4">{{ $s->post_score !== null ? $s->post_score : '—' }}</td>
              <td class="py-2 pr-4 text-gray-600">{{ $s->last_answered_at?->diffForHumans() ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $students->links() }}
    </div>
    </section>
  </div>

</div>
