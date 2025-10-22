@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="mx-auto max-w-6xl px-4 py-12 space-y-10">
  <header class="space-y-3 text-center">
    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700">Artikel</span>
    <h1 class="text-3xl font-bold text-gray-900">Rangkaian Postingan Edukatif</h1>
    <p class="text-sm text-gray-600 max-w-3xl mx-auto">Kumpulan artikel singkat seputar bahaya rokok, gaya hidup sehat, dan tips praktis untuk mendampingi program ClicSTARe.</p>
  </header>

  <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($posts as $post)
      <article class="flex h-full flex-col overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        @if ($post->gambar_path)
          <img src="{{ Storage::url($post->gambar_path) }}" alt="{{ $post->judul }}" class="aspect-[16/9] w-full object-cover" loading="lazy">
        @else
          <div class="aspect-[16/9] w-full bg-gradient-to-br from-red-200 via-amber-100 to-white flex items-center justify-center text-sm font-semibold text-red-600">ClicSTARe</div>
        @endif
        <div class="flex flex-1 flex-col p-5 space-y-3">
          <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $post->updated_at?->translatedFormat('d F Y') }}</p>
          <h2 class="text-lg font-semibold text-gray-900">{{ $post->judul }}</h2>
          <p class="text-sm text-gray-600 flex-1">{!! \Illuminate\Support\Str::limit(strip_tags($post->konten), 130) !!}</p>
          <div class="pt-2">
            <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700">
              Baca selengkapnya
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
              </svg>
            </a>
          </div>
        </div>
      </article>
    @empty
      <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border bg-white p-10 text-center text-gray-500">
        Belum ada postingan.
      </div>
    @endforelse
  </section>

  <div>
    {{ $posts->links() }}
  </div>
</div>
