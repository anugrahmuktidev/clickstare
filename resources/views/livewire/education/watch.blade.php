<div class="mx-auto max-w-5xl space-y-6">
  <div class="bg-white shadow rounded p-4">
    <h1 class="text-xl font-bold mb-3">{{ $video->judul }}</h1>

   <video controls class="w-full rounded" preload="metadata">
  <source src="{{ $video->video_url }}" type="video/mp4">
</video>


    @if($video->deskripsi)
      <p class="text-gray-700 mt-4">{{ $video->deskripsi }}</p>
    @endif
  </div>
<br>
  <a href="{{ route('education.index') }}" class="text-blue-600 hover:underline">← Kembali</a>
</div>
