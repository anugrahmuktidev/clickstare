<div class="space-y-4">
  <div>
    <h2 class="text-xl font-semibold">{{ $record->judul }}</h2>
    <p class="text-xs text-gray-500">Diperbarui {{ optional($record->updated_at)->diffForHumans() }}</p>
  </div>

  @if ($record->gambar_path)
    <img
      src="{{ Storage::url($record->gambar_path) }}"
      alt="Gambar {{ $record->judul }}"
      class="w-full max-h-96 object-cover rounded"
    >
  @endif

  <div class="prose max-w-none">
    {!! $record->konten !!}
  </div>
</div>
