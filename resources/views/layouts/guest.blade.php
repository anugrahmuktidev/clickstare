<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'ClicSTARe — Guest' }}</title>
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
  @livewireStyles

  {{-- Aksen visual ringan --}}
  <style>
    /* Gradient halus anti-rokok: merah → putih */
    .anti-smoke-bg {
      background: radial-gradient(1200px 600px at 20% -10%, rgba(220,38,38,.12), transparent 60%),
                  radial-gradient(900px 500px at 120% 30%, rgba(220,38,38,.10), transparent 60%),
                  linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }
    .ribbon {
    position: fixed;
    top: 80px;
    right: -80px; /* lebih dekat ke tengah agar tidak terpotong */
    z-index: 50;
    transform: rotate(45deg);
    background: #ef4444;
    color: #fff;
    padding: 10px 100px; /* tambahkan panjang pita */
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(239,68,68,0.35);
    white-space: nowrap; /* biar teks tidak turun baris */
  }
  @media (max-width: 640px) {
  .ribbon {
    top: 40px;
    right: -80px;
    transform: rotate(45deg) scale(.85);
    padding: 8px 70px;
    font-size: 12px;
    box-shadow: 0 3px 10px rgba(239,68,68,0.25);
  }
}
  </style>
</head>
<body class="anti-smoke-bg text-gray-800 antialiased min-h-screen">

  {{-- Pita pojok: Zona Tanpa Rokok --}}
  <div class="ribbon">Zona Tanpa Rokok</div>

  {{-- Header kecil dengan ikon no-smoking --}}
  <header class="w-full">
    <div class="mx-auto max-w-6xl px-4 pt-6">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 flex items-center justify-center rounded-full bg-red-100 ring-2 ring-red-300">
          {{-- Ikon no smoking (SVG) --}}
          <svg viewBox="0 0 24 24" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 14h12m2 0h1a3 3 0 0 0 0-6h-1m-2 6v-3m0 0a2 2 0 1 0-4 0v3M4 4l16 16" />
          </svg>
        </div>
        <div>
          <p class="text-sm font-semibold text-red-600 tracking-wide">Bahaya Merokok Elektrik</p>
          <h1 class="text-lg font-bold text-gray-900">ClicSTARe</h1>
        </div>
      </div>
    </div>
  </header>

  <main class="min-h-[78vh] flex items-center justify-center px-2 py-8">
    {{-- Cek: @extends vs slot --}}
    @if (View::hasSection('content'))
      @yield('content')
    @else
      {{ $slot ?? '' }}
    @endif
  </main>

  {{-- Footer ajakan --}}
  <footer class="w-full">
    <div class="mx-auto max-w-6xl px-4 pb-8">
      <div class="rounded-xl border border-red-100 bg-white/80 backdrop-blur p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-50">
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="9"></circle>
              <path d="M8 12h8M9 15h6" stroke-linecap="round"/>
              <path d="M7 7l10 10" stroke-linecap="round"/>
            </svg>
          </span>
          <p class="text-sm text-gray-700">
            <span class="font-semibold text-gray-900">Hidup sehat tanpa rokok.</span>
            Kurangi paparan asap, lindungi diri & orang tercinta.
          </p>
        </div>
        <a href="{{ route('risk.info') }}" class="text-sm font-semibold text-red-600 hover:text-red-700 underline underline-offset-4">
          Pelajari risiko merokok & temukan dukungan berhenti
        </a>
      </div>
    </div>
  </footer>

  @livewireScripts
</body>
</html>
