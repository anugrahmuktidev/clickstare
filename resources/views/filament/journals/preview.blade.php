<div class="space-y-3">
  <div class="text-lg font-semibold">{{ $record->judul }}</div>

  @if ($record->file_path)
    @php
      $pdfUrl = Storage::url($record->file_path) . '#zoom=page-width';
    @endphp

    <div class="relative rounded border overflow-hidden">
      <iframe
        src="{{ Storage::url($record->file_path) }}#toolbar=0&navpanes=0&scrollbar=0&zoom=page-width"
        class="absolute inset-0 w-full h-full border-0"
        style="height: 80vh;"
        title="Pratinjau Jurnal"
      ></iframe>
      <div class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-white"></div>
      <div style="padding-top: 80vh;"></div>
    </div>

    @if ($record->deskripsi)
      <p class="text-gray-700">{{ $record->deskripsi }}</p>
    @endif
  @else
    <p class="text-gray-500">Belum ada file terunggah.</p>
  @endif

  <div class="text-xs text-gray-500">
    Terakhir diperbarui {{ optional($record->updated_at)->diffForHumans() }}
  </div>
</div>
