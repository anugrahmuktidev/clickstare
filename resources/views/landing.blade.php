{{-- resources/views/landing.blade.php --}}
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ClicSTARe — Edukasi Bahaya Rokok Elektrik</title>
  @vite('resources/css/app.css')
  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
  <script src="https://cdn.lordicon.com/lordicon.js" defer></script>
</head>

<body class="bg-white text-gray-900 antialiased">
  @php
    use Illuminate\Support\Facades\Storage;

    $posts = ($posts ?? collect())->take(4);
    $journals = ($journals ?? collect())->take(6);

    $slides = collect($slides ?? [])
        ->map(function ($slide) {
            if (is_array($slide)) {
                return (object) [
                    'image_path' => $slide['image_path'] ?? null,
                    'cta_label' => $slide['cta_label'] ?? null,
                    'cta_url' => $slide['cta_url'] ?? null,
                ];
            }

            return (object) [
                'image_path' => $slide->image_path ?? null,
                'cta_label' => $slide->cta_label ?? null,
                'cta_url' => $slide->cta_url ?? null,
            ];
        })
        ->filter(fn ($slide) => filled($slide->image_path))
        ->values();

    if ($slides->isEmpty()) {
      $slides = collect([
        (object) ['image_path' => 'images/slide1.png'],
        (object) ['image_path' => 'images/slide2.png'],
        (object) ['image_path' => 'images/slide3.png'],
        (object) ['image_path' => 'images/slide4.png'],
      ]);
    }
  @endphp

  {{-- NAV --}}
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      {{-- Logo --}}
      <a href="{{ url('/') }}" class="font-bold text-xl text-red-600">ClicSTARe</a>
      <div class="flex items-center gap-2">
        @guest
          <a href="{{ route('login') }}"
            class="px-4 py-2 rounded-md border border-red-600 text-red-700 hover:bg-red-50">Masuk</a>
          <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Daftar</a>
        @endguest

        @auth
          @php
            $user = auth()->user();
            if ($user->isAdmin()) {
              $dashboard = route('filament.admin.pages.dashboard');
            } elseif ($user->isGuru()) {
              $dashboard = route('guru.dashboard');
            } else {
              $dashboard = route('education.index'); // siswa
            }
          @endphp
          <a href="{{ $dashboard }}" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Dashboard</a>
        @endauth
      </div>
    </div>
  </header>

  {{-- Nav artikel & jurnal --}}
  <nav class="bg-white/95 border-b backdrop-blur sticky top-[56px] sm:top-[64px] z-30">
    <div class="mx-auto max-w-6xl px-4 py-2 flex items-center gap-3 text-sm font-semibold text-gray-600">
      <a href="#artikel"
        class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 hover:border-red-500 hover:text-red-600 transition">
        <span class="h-2 w-2 rounded-full bg-red-500"></span>
        Artikel
      </a>
      <a href="#jurnal"
        class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 hover:border-emerald-500 hover:text-emerald-600 transition">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        Jurnal
      </a>
    </div>
  </nav>

  {{-- HERO: ganti background merah menjadi slider bergambar dengan info dummy --}}
  <section class="relative overflow-hidden">
    <div id="hero-slider"
      class="relative w-full aspect-[16/9] min-h-[220px] md:min-h-[300px] lg:min-h-[360px] max-h-[520px] overflow-hidden">
      @foreach ($slides as $slide)
        @php
          $imagePath = $slide->image_path;
          $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : Storage::url($imagePath);
        @endphp

        <div
          class="absolute inset-0 transition-opacity duration-700 {{ $loop->first ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
          data-slide="{{ $loop->index }}">
          <img src="{{ $imageUrl }}" alt="{{ 'Slide ' . $loop->iteration }}"
            class="absolute inset-0 -z-10 h-full w-full object-cover" />

          @if ($slide->cta_label)
            <div class="absolute inset-0 -z-0 bg-black/40"></div>
            <div
              class="relative z-10 mx-auto flex h-full max-w-6xl items-center justify-center px-4 text-center text-white">
              <div>
                <a href="{{ $slide->cta_url }}"
                  class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-3 font-semibold text-white hover:bg-red-700"
                  target="_blank" rel="noopener">
                  {{ $slide->cta_label }}
                </a>
              </div>
            </div>
          @endif
        </div>
      @endforeach


      {{-- Navigasi panah --}}
      <button type="button"
        class="absolute left-3 top-1/2 -translate-y-1/2 z-20 rounded-full bg-white/80 hover:bg-white p-2 shadow"
        aria-label="Sebelumnya" data-prev>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-700">
          <path fill-rule="evenodd"
            d="M15.78 3.72a.75.75 0 010 1.06L9.56 11l6.22 6.22a.75.75 0 11-1.06 1.06l-6.75-6.75a.75.75 0 010-1.06l6.75-6.75a.75.75 0 011.06 0z"
            clip-rule="evenodd" />
        </svg>
      </button>
      <button type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 z-20 rounded-full bg-white/80 hover:bg-white p-2 shadow"
        aria-label="Berikutnya" data-next>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-700">
          <path fill-rule="evenodd"
            d="M8.22 20.28a.75.75 0 010-1.06L14.44 13 8.22 6.78a.75.75 0 111.06-1.06l6.75 6.75a.75.75 0 010 1.06l-6.75 6.75a.75.75 0 01-1.06 0z"
            clip-rule="evenodd" />
        </svg>
      </button>

      {{-- Dots --}}
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2" data-dots></div>
    </div>
  </section>

  {{-- 3 FITUR RINGKAS --}}
  <section class="mx-auto max-w-6xl px-4 py-10">
    <div class="grid gap-4 sm:gap-6 sm:grid-cols-3">
      <div class="p-5 bg-white rounded-xl border shadow-sm">
        <div class="mb-3">
          <lord-icon src="https://cdn.lordicon.com/nobciafz.json" trigger="hover"
            colors="primary:#dc2626,secondary:#fca5a5" style="width:48px;height:48px"></lord-icon>
        </div>
        <p class="font-semibold">Pretest</p>
        <p class="text-sm text-gray-600 mt-1">Cek pemahaman awal tentang bahaya rokok.</p>
      </div>
      <div class="p-5 bg-white rounded-xl border shadow-sm">
        <div class="mb-3">
          <lord-icon src="https://cdn.lordicon.com/qtqvorle.json" trigger="hover"
            colors="primary:#dc2626,secondary:#fca5a5" style="width:48px;height:48px"></lord-icon>
        </div>
        <p class="font-semibold">Video Edukasi</p>
        <p class="text-sm text-gray-600 mt-1">Materi singkat & mudah dicerna.</p>
      </div>
      <div class="p-5 bg-white rounded-xl border shadow-sm">
        <div class="mb-3">
          <lord-icon src="https://cdn.lordicon.com/hovbgwmd.json" trigger="hover"
            colors="primary:#dc2626,secondary:#fca5a5" style="width:48px;height:48px"></lord-icon>
        </div>
        <p class="font-semibold">Posttest</p>
        <p class="text-sm text-gray-600 mt-1">Nilai peningkatan dan simpulkan.</p>
      </div>
    </div>
  </section>

  {{-- ARTIKEL TERBARU --}}
  <section id="artikel" class="bg-slate-50">
    <div class="mx-auto max-w-6xl px-4 py-12">
      <div class="flex flex-col lg:flex-row lg:items-start gap-10">
        <div class="flex-1 space-y-6">
          <div class="flex items-center gap-3">
            <span
              class="px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 bg-red-100 rounded-full">Artikel</span>
            <h2 class="text-2xl font-bold text-gray-900">Postingan Terbaru</h2>
          </div>

          @php
            $featuredPost = $posts->first();
            $otherPosts = $posts->skip(1);
          @endphp

          @if ($featuredPost)
            <article class="rounded-2xl overflow-hidden border bg-white shadow-sm">
              @if ($featuredPost->gambar_path)
                <img src="{{ Storage::url($featuredPost->gambar_path) }}" alt="{{ $featuredPost->judul }}"
                  class="aspect-[16/9] w-full object-cover" />
              @endif
              <div class="p-6 space-y-3">
                <div class="flex items-center gap-2 text-xs text-gray-500 uppercase tracking-wide">
                  <span>{{ $featuredPost->updated_at?->translatedFormat('d F Y') }}</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">{{ $featuredPost->judul }}</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                  {!! \Illuminate\Support\Str::limit(strip_tags($featuredPost->konten), 180) !!}
                </p>
                <div class="pt-2">
                  <a href="{{ route('posts.show', $featuredPost) }}"
                    class="inline-flex items-center gap-2 text-red-600 font-semibold hover:text-red-700">
                    Baca selengkapnya
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                    </svg>
                  </a>
                </div>
              </div>
            </article>
          @else
            <div class="rounded-2xl border bg-white p-10 text-center text-gray-500">
              Belum ada postingan terbaru.
            </div>
          @endif
        </div>

        <div class="w-full max-w-md space-y-4">
          <p class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Artikel singkat</p>
          <div class="space-y-4">
            @forelse($otherPosts ?? collect() as $post)
              <a href="{{ route('posts.show', $post) }}"
                class="flex gap-3 rounded-xl border bg-white p-3 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                @if ($post->gambar_path)
                  <img src="{{ Storage::url($post->gambar_path) }}" alt="{{ $post->judul }}"
                    class="h-20 w-20 flex-shrink-0 rounded-lg object-cover" />
                @endif
                <div class="space-y-1">
                  <p class="text-xs text-gray-500">{{ $post->updated_at?->translatedFormat('d M Y') }}</p>
                  <h3 class="text-sm font-semibold text-gray-900 group-hover:text-red-600">{{ $post->judul }}</h3>
                  <p class="text-xs text-gray-600 leading-relaxed">
                    {!! \Illuminate\Support\Str::limit(strip_tags($post->konten), 90) !!}
                  </p>
                </div>
              </a>
            @empty
              <p class="rounded-xl border bg-white p-4 text-sm text-gray-500">Belum ada artikel lain.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- JURNAL TERKAIT --}}
  <section id="jurnal" class="mx-auto max-w-6xl px-4 py-12">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <span
          class="px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700 bg-emerald-100 rounded-full">Jurnal</span>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Kumpulan Jurnal Terbaru</h2>
        <p class="text-sm text-gray-600">Unduh jurnal pilihan sebagai bahan bacaan pendukung.</p>
      </div>
      <a href="{{ route('journals.index') }}"
        class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
        Lihat semua jurnal
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
        </svg>
      </a>
    </div>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($journals ?? collect() as $journal)
        <article class="flex flex-col rounded-2xl border bg-white shadow-sm overflow-hidden">
          <div class="relative h-44 bg-slate-100">
            <iframe src="{{ Storage::url($journal->file_path) }}#zoom=page-width" class="absolute inset-0 h-full w-full"
              title="Preview {{ $journal->judul }}" loading="lazy"></iframe>
          </div>
          <div class="flex flex-1 flex-col p-5 space-y-3">
            <h3 class="text-lg font-semibold text-gray-900">{{ $journal->judul }}</h3>
            @if ($journal->deskripsi)
              <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($journal->deskripsi, 130) }}</p>
            @endif
            <div class="mt-auto flex items-center justify-between pt-2 text-xs text-gray-500">
              <span>Diperbarui {{ $journal->updated_at?->diffForHumans() }}</span>
              <a href="{{ Storage::url($journal->file_path) }}"
                class="inline-flex items-center gap-1 rounded-full bg-emerald-600 px-3 py-1.5 text-white text-xs font-semibold hover:bg-emerald-700"
                target="_blank" rel="noopener">
                Unduh
              </a>
            </div>
          </div>
        </article>
      @empty
        <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border bg-white p-8 text-center text-gray-500">
          Belum ada jurnal tersedia.
        </div>
      @endforelse
    </div>
  </section>

  {{-- STRIP AJAKAN (CTA tunggal) --}}
  {{-- <section class="mx-auto max-w-6xl px-4 pb-12">
    <div
      class="rounded-xl bg-white border shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div>
        <p class="text-lg sm:text-xl font-semibold">Bergabung sekarang</p>
        <p class="text-gray-600 text-sm">Mulai perjalanan sehatmu hari ini.</p>
      </div>
      <a href="{{ route('register') }}" class="px-6 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700">
        Daftar Gratis
      </a>
    </div>
  </section> --}}

  {{-- FOOTER --}}
  <footer class="border-t">
    <div
      class="mx-auto max-w-6xl px-4 py-6 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-3">
      <p class="text-sm text-gray-600">© {{ date('Y') }} ClicSTARe — Kampanye Anti Rokok</p>
      @auth
        <div class="text-sm text-gray-600">
          <a href="{{ $dashboard ?? '#' }}" class="hover:text-red-700">Dashboard</a>
        </div>
      @endauth

    </div>
  </footer>

  <script>
    (function () {
      const root = document.getElementById('hero-slider');
      if (!root) return;
      const slides = Array.from(root.querySelectorAll('[data-slide]'));
      const dotsWrap = root.querySelector('[data-dots]');
      const prevBtn = root.querySelector('[data-prev]');
      const nextBtn = root.querySelector('[data-next]');

      if (!slides.length) {
        prevBtn?.classList.add('hidden');
        nextBtn?.classList.add('hidden');
        if (dotsWrap) {
          dotsWrap.innerHTML = '';
          dotsWrap.classList.add('hidden');
        }
        return;
      }

      if (slides.length === 1) {
        prevBtn?.classList.add('hidden');
        nextBtn?.classList.add('hidden');
        if (dotsWrap) {
          dotsWrap.innerHTML = '';
          dotsWrap.classList.add('hidden');
        }
        slides[0].style.opacity = '1';
        slides[0].style.pointerEvents = 'auto';
        return;
      }

      let current = 0;
      let timer = null;

      function renderDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        slides.forEach((_, i) => {
          const b = document.createElement('button');
          b.type = 'button';
          b.setAttribute('aria-label', 'Ke slide ' + (i + 1));
          b.className = 'h-2 w-2 rounded-full ' + (i === current ? 'bg-white' : 'bg-white/50 hover:bg-white/80');
          b.addEventListener('click', () => go(i));
          dotsWrap.appendChild(b);
        });
      }

      function apply() {
        slides.forEach((el, i) => {
          const active = i === current;
          el.style.opacity = active ? '1' : '0';
          el.style.pointerEvents = active ? 'auto' : 'none';
        });
        renderDots();
      }

      function go(idx) {
        current = (idx + slides.length) % slides.length;
        apply();
        restart();
      }

      function next() { go(current + 1); }
      function prev() { go(current - 1); }

      function restart() {
        if (timer) clearInterval(timer);
        timer = setInterval(next, 5000);
      }

      prevBtn?.addEventListener('click', prev);
      nextBtn?.addEventListener('click', next);

      apply();
      restart();
    })();
  </script>
</body>

</html>
