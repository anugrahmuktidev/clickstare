@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="mx-auto max-w-6xl px-4 py-12 space-y-12">
  <article class="space-y-6">
    <header class="space-y-2">
      <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Artikel</p>
      <h1 class="text-3xl font-bold text-gray-900">{{ $post->judul }}</h1>
      <p class="text-sm text-gray-500">Diperbarui {{ $post->updated_at?->translatedFormat('d F Y, H:i') }}</p>
    </header>

    @if ($post->gambar_path)
      <img src="{{ Storage::url($post->gambar_path) }}" alt="{{ $post->judul }}" class="w-full rounded-2xl border object-cover" loading="lazy">
    @endif

    <div class="prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
      {!! $post->konten !!}
    </div>
  </article>

  @if ($latestPosts->isNotEmpty())
    <section class="space-y-4">
      <h2 class="text-xl font-semibold text-gray-900">Artikel Lainnya</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($latestPosts as $item)
          <a href="{{ route('posts.show', $item) }}" class="group flex flex-col rounded-2xl border bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            @if ($item->gambar_path)
              <img src="{{ Storage::url($item->gambar_path) }}" alt="{{ $item->judul }}" class="mb-3 h-28 w-full rounded-xl object-cover" loading="lazy">
            @endif
            <p class="text-xs text-gray-500">{{ $item->updated_at?->translatedFormat('d M Y') }}</p>
            <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-gray-900 group-hover:text-red-600">{{ $item->judul }}</h3>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</div>
