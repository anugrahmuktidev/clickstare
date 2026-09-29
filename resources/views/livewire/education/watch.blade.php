<div class="mx-auto max-w-5xl space-y-6">
  <div class="bg-white shadow rounded p-4">
    <h1 class="text-xl font-bold mb-3">{{ $video->judul }}</h1>

   <video id="educationVideo" controls class="w-full rounded" preload="metadata" wire:ignore>
    <source src="{{ $video->video_url }}" type="video/mp4">
   </video>


    @if($video->deskripsi)
      <p class="text-gray-700 mt-4">{{ $video->deskripsi }}</p>
    @endif

    @if(auth()->user()?->isSiswa())
      <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
        @if($videoCompleted)
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-emerald-700 font-medium">Video selesai ditonton. Anda dapat melanjutkan ke posttest.</p>
            <a href="{{ route('exam.posttest') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold">
              Lanjut ke Posttest
            </a>
          </div>
        @else
          <p class="text-sm text-slate-700">Tonton video sampai selesai untuk membuka tombol posttest.</p>
        @endif
      </div>
    @endif
  </div>
<br>
  <a href="{{ route('education.index') }}" class="text-blue-600 hover:underline">← Kembali</a>

  <script>
    (function () {
      const video = document.getElementById('educationVideo');
      if (!video) return;

      let maxWatched = 0;
      let lastSafeTime = 0;
      let seekingByGuard = false;

      video.addEventListener('timeupdate', function () {
        if (!video.seeking) {
          maxWatched = Math.max(maxWatched, video.currentTime);
          lastSafeTime = video.currentTime;
        }
      });

      video.addEventListener('seeking', function () {
        if (seekingByGuard) {
          seekingByGuard = false;
          return;
        }

        if (video.currentTime > maxWatched + 1.5) {
          seekingByGuard = true;
          video.currentTime = lastSafeTime;
        }
      });

      video.addEventListener('ended', function () {
        @this.completeVideo();
      });
    })();
  </script>
</div>
