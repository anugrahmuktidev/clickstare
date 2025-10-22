@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="mx-auto max-w-6xl px-4 py-12 space-y-10">
  <header class="space-y-3 text-center">
    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700">Jurnal</span>
    <h1 class="text-3xl font-bold text-gray-900">Koleksi Jurnal ClicSTARe</h1>
    <p class="text-sm text-gray-600 max-w-3xl mx-auto">Telusuri jurnal pendukung yang dapat diunduh bebas untuk memperkaya materi kampanye anti rokok.</p>
  </header>

  <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($journals as $journal)
      <article class="flex h-full flex-col overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="relative h-48 bg-slate-100 overflow-hidden rounded-t-2xl">
          <iframe
            src="{{ Storage::url($journal->file_path) }}#toolbar=0&navpanes=0&scrollbar=0&zoom=page-width"
            title="Preview {{ $journal->judul }}"
            class="absolute inset-0 w-full h-full border-0"
            loading="lazy"
          ></iframe>
          <div class="pointer-events-none absolute inset-x-0 top-0 h-10 bg-white"></div>
        </div>
        <div class="flex flex-1 flex-col p-5 space-y-3">
          <h2 class="text-lg font-semibold text-gray-900">{{ $journal->judul }}</h2>
          @if ($journal->deskripsi)
            <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($journal->deskripsi, 140) }}</p>
          @endif
          <div class="mt-auto flex items-center justify-between pt-2 text-xs text-gray-500">
            <span>Diperbarui {{ $journal->updated_at?->diffForHumans() }}</span>
            <a href="{{ Storage::url($journal->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-full bg-emerald-600 px-3 py-1.5 text-white text-xs font-semibold hover:bg-emerald-700">Unduh</a>
          </div>
        </div>
      </article>
    @empty
      <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border bg-white p-10 text-center text-gray-500">
        Belum ada jurnal tersedia.
      </div>
    @endforelse
  </section>

  <div>
    {{ $journals->links() }}
  </div>
</div>
