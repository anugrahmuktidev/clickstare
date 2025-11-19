{{-- resources/views/livewire/education/dashboard.blade.php --}}
<div class="mx-auto max-w-3xl md:max-w-5xl lg:max-w-6xl space-y-8 sm:space-y-10 px-3 sm:px-4">

  {{-- Top bar: Logout --}}
  <div class="pt-4 flex items-center justify-end">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border text-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        Keluar
      </button>
    </form>
  </div>

  @if (auth()->user()->isSiswa() && $certificateAttempt)
    <section class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 sm:p-5 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
        <div class="flex-1">
          <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Selamat!</p>
          <p class="text-base sm:text-lg font-semibold text-emerald-900 mt-1">
            Anda menuntaskan posttest dengan {{ $certificateAttempt->total_benar }} jawaban benar dari
            {{ $certificateAttempt->total_soal }} soal.
          </p>
          <p class="text-sm text-emerald-800 mt-1">
            Unduh sertifikat kelulusan sebagai bukti menyelesaikan program edukasi ClicSTARe.
          </p>
        </div>
        <a href="{{ route('education.certificate.download') }}" class="inline-flex items-center justify-center px-4 py-2.5
                  rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700"
           target="_blank" rel="noopener">
          Download Sertifikat
        </a>
      </div>
    </section>
  @endif

  {{-- VIDEO EDUKASI: scroll horizontal + tombol kiri/kanan --}}
  <section class="relative bg-white shadow rounded-lg p-4 sm:p-6">
    <div class="flex items-center justify-between gap-3 mb-4">
      <h2 class="font-bold text-base sm:text-lg">Video Edukasi</h2>

      {{-- tombol panah: tampil ≥ md, disembunyikan di hp (user pakai swipe) --}}
      <div class="hidden md:flex gap-2">
        <button id="btnLeft"
                class="px-3 py-1.5 rounded border hover:bg-gray-50"
                type="button" aria-label="Geser kiri">◀</button>
        <button id="btnRight"
                class="px-3 py-1.5 rounded border hover:bg-gray-50"
                type="button" aria-label="Geser kanan">▶</button>
      </div>
    </div>

    {{-- gradient fade kiri/kanan --}}
    <div class="pointer-events-none absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-white to-transparent rounded-l-lg"></div>
    <div class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-white to-transparent rounded-r-lg"></div>

    <div id="vidRow"
         class="flex gap-3 sm:gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-1 sm:pb-2 -mx-1 sm:mx-0 px-1 sm:px-0"
         style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
      @forelse ($videos as $v)
        <a href="{{ route('education.watch', $v->id) }}"
           class="snap-start bg-gray-50 rounded-lg border hover:bg-gray-100 transition p-2
                  min-w-[72%] max-w-[72%]
                  sm:min-w-[260px] sm:max-w-[260px]
                  lg:min-w-[300px] lg:max-w-[300px]">
          <div class="aspect-video w-full rounded-md bg-gray-200 overflow-hidden mb-2">
            @if ($v->thumbnail_url)
              <img src="{{ $v->thumbnail_url }}" alt="{{ $v->judul }}" class="w-full h-full object-cover">
            @else
              <div class="w-full h-full grid place-items-center text-gray-500 text-sm sm:text-base px-3 text-center">
                {{ \Illuminate\Support\Str::limit($v->judul, 60) }}
              </div>
            @endif
          </div>
          <div class="px-1">
            <h3 class="font-semibold text-sm sm:text-[15px] leading-snug line-clamp-2 break-words">{{ $v->judul }}</h3>
            @if ($v->deskripsi)
              <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2 break-words">{{ $v->deskripsi }}</p>
            @endif
          </div>
        </a>
      @empty
        <p class="text-gray-500 px-1">Belum ada video edukasi.</p>
      @endforelse
    </div>

    <script>
      (function(){
        const row  = document.getElementById('vidRow');
        const step = () => {
          // geser ~ 1 kartu + gap
          const card = row?.querySelector('a');
          if (!card) return 260;
          const style = getComputedStyle(card);
          const w = card.getBoundingClientRect().width;
          const gap = parseFloat(getComputedStyle(row).columnGap || getComputedStyle(row).gap || 16);
          return Math.round(w + gap);
        };
        document.getElementById('btnLeft')?.addEventListener('click', () => row.scrollBy({ left: -step(), behavior: 'smooth' }));
        document.getElementById('btnRight')?.addEventListener('click', () => row.scrollBy({ left:  step(), behavior: 'smooth' }));
      })();
    </script>
  </section>

 

 {{-- Form Buat Pertanyaan (Siswa) --}}
@if (auth()->user()->isSiswa())
  <section class="bg-white shadow rounded-lg p-4 sm:p-6">
    <h2 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Buat Pertanyaan</h2>

  @if (session('ok'))
  <div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 3000)" {{-- 3000ms = 3 detik --}}
    x-transition
    class="mb-3 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200"
  >
    {{ session('ok') }}
  </div>
@endif

    <form wire:submit.prevent="ask" class="space-y-3">
      <textarea
        wire:model.defer="isi"
        rows="4"
        class="w-full border rounded px-3 py-2 text-sm sm:text-base"
        placeholder="Tulis pertanyaan Anda secara lengkap…"></textarea>
      @error('isi') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

      <p class="text-xs text-gray-500">
        Judul akan dibuat otomatis dari kalimat pertama pertanyaan Anda.
      </p>

      <button class="w-full sm:w-auto px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base">
        Kirim
      </button>
    </form>
  </section>
@endif


  {{-- Forum Sekolah --}}
  <section class="bg-white shadow rounded-lg p-4 sm:p-6">
    <h2 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Forum Sekolah Anda</h2>

    @forelse ($threads as $t)
      @php $replyCount = $t->replies->count(); @endphp
      <article x-data="{ openReplies: false, openForm: false }" class="border rounded p-3 sm:p-4 mb-4">
        <header class="mb-2 flex items-start justify-between gap-2">
          <div>
            <h3 class="font-semibold break-words line-clamp-1">{{ $t->judul }}</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
              oleh <strong>{{ $t->asker->name }}</strong> • {{ $t->created_at->diffForHumans() }}
            </p>
          </div>
          <div class="shrink-0 flex items-center gap-2">
            <span class="px-2 py-0.5 text-[10px] sm:text-xs rounded bg-gray-100 text-gray-700">{{ $replyCount }} Balasan</span>
            @if($t->status === 'closed')
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
          <p class="text-gray-700 mb-3 text-sm sm:text-base break-words">{{ $preview }}</p>
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

          @php
            $me = auth()->user();
            $bolehBalas = $t->status !== 'closed' && (
              $me->role === 'admin' ||
              ($me->role === 'guru'  && (int)$me->sekolah_id === (int)$t->sekolah_id) ||
              ($me->role === 'siswa' && (int)$me->id === (int)$t->user_id)
            );
          @endphp
          @if ($bolehBalas)
            <button type="button" @click="openForm = !openForm"
                    class="px-3 py-1.5 text-xs sm:text-sm rounded border hover:bg-gray-50">
              <span x-show="!openForm">Tulis Jawaban</span>
              <span x-show="openForm">Tutup Form</span>
            </button>
          @endif
        </div>


        <div x-show="openReplies" x-cloak class="space-y-3">
          @foreach ($t->replies as $r)
            <div class="p-3 border rounded" wire:key="reply-{{ $r->id }}">
              <div class="text-xs sm:text-sm text-gray-500 mb-1">
                {{ $r->user->name }} • {{ $r->created_at->diffForHumans() }}
                @if($r->is_solution)
                  <span class="ml-2 px-2 py-0.5 text-[10px] sm:text-xs rounded bg-emerald-100 text-emerald-700">Solusi</span>
                @endif
              </div>
              <div class="text-sm sm:text-base break-words">{{ $r->isi }}</div>

              @can('resolve-thread', $t)
                @unless($r->is_solution)
                  <button wire:click="markSolution({{ $r->id }})"
                          class="mt-2 px-3 py-1 text-sm rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                    Tandai Solusi
                  </button>
                @endunless
              @endcan
            </div>
          @endforeach
        </div>

        @if ($bolehBalas)
          <div x-show="openForm" x-cloak class="mt-3">
            <textarea wire:model.defer="reply.{{ $t->id }}" rows="2"
                      class="w-full border rounded px-3 py-2 text-sm sm:text-base"
                      placeholder="Tulis jawaban..."></textarea>
            @error("reply.$t->id")
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <div class="mt-2">
              <button wire:click="answer({{ $t->id }})"
                      class="px-3 py-1.5 rounded bg-gray-800 hover:bg-black text-white text-sm">
                Kirim Jawaban
              </button>
            </div>
          </div>
        @endif

      </article>
    @empty
      <p class="text-gray-500">Belum ada pertanyaan di sekolah Anda.</p>
    @endforelse

    <div class="mt-4">{{ $threads->links() }}</div>
  </section>

  {{-- FAQ --}}
  <section class="bg-gray-50 shadow rounded-lg p-4 sm:p-6">
    <h2 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">FAQ</h2>
    <div class="space-y-3">
      @forelse ($faqs as $f)
        <details class="group border rounded-lg px-4 py-3 bg-blue-50 hover:bg-blue-100 transition">
          <summary class="cursor-pointer font-semibold text-blue-800 flex justify-between items-center">
            <span>{{ $f->pertanyaan }}</span>
            <svg class="w-4 h-4 transform group-open:rotate-180 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </summary>
          <div class="mt-2 text-sm sm:text-base text-gray-700">
            {{ $f->jawaban }}
          </div>
        </details>
      @empty
        <p class="text-gray-500">Belum ada FAQ.</p>
      @endforelse
    </div>
  </section>

</div>
