<div class="mx-auto max-w-5xl space-y-6" x-data="{ videoCompleted: @js($videoCompleted), posttestUrl: @js(route('exam.posttest')), maxWatched: 0, lastSafeTime: 0, seekingByGuard: false }">
  <div class="bg-white shadow rounded p-4">
    <h1 class="text-xl font-bold mb-3">{{ $video->judul }}</h1>

   <video id="educationVideo" controls class="w-full rounded" preload="metadata" wire:ignore
          x-on:timeupdate="if (!$el.seeking) { maxWatched = Math.max(maxWatched, $el.currentTime); lastSafeTime = $el.currentTime }"
          x-on:seeking="if (seekingByGuard) { seekingByGuard = false } else if ($el.currentTime > maxWatched + 1.5) { seekingByGuard = true; $el.currentTime = lastSafeTime }"
          x-on:ended="$wire.completeVideo().then(() => videoCompleted = true)">
    <source src="{{ $video->video_url }}" type="{{ $video->mime }}">
   </video>


    @if($video->deskripsi)
      <p class="text-gray-700 mt-4">{{ $video->deskripsi }}</p>
    @endif

    @if(auth()->user()?->isSiswa())
      <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
        <div class="flex flex-col items-start gap-3">
            <p class="text-sm {{ $videoCompleted ? 'text-emerald-700 font-medium' : 'text-slate-700' }}">
              {{ $videoCompleted ? 'Video selesai ditonton. Anda dapat melanjutkan ke posttest.' : 'Tonton video sampai selesai untuk mengaktifkan tombol posttest.' }}
            </p>
            <button type="button" @disabled(! $videoCompleted) x-bind:disabled="!videoCompleted"
               x-on:click="if (videoCompleted) window.location.href = posttestUrl"
               x-bind:style="videoCompleted ? 'background-color:#f97316;cursor:pointer' : 'background-color:#fdba74;cursor:not-allowed'"
               style="background-color:{{ $videoCompleted ? '#f97316' : '#fdba74' }};"
               class="inline-flex shrink-0 items-center justify-center px-4 py-2 rounded text-white text-sm font-semibold disabled:opacity-70">
              Lanjut ke Posttest
            </button>
        </div>
      </div>
    @endif
  </div>
<br>
  <a href="{{ route('education.index') }}" class="text-blue-600 hover:underline">← Kembali</a>

</div>
