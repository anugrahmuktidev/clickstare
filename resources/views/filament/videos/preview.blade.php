<div class="space-y-2">
  <div class="font-semibold">{{ $record->judul }}</div>

  @if ($record->path)
    <video controls class="w-full rounded" preload="metadata" style="max-height: 70vh">
      <source src="{{ Storage::url($record->path) }}" type="video/mp4">
      Browser Anda tidak mendukung pemutar video.
    </video>
    @if ($record->deskripsi)
      <p class="text-gray-700 mt-2">{{ $record->deskripsi }}</p>
    @endif
  @else
    <p class="text-gray-500">Belum ada file terunggah.</p>
  @endif

  <div class="text-xs text-gray-500">
    URL siswa: <span class="underline">{{ route('education.watch', $record->id) }}</span>
  </div>
</div>
