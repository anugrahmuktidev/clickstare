<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'ClicSTARe' }}</title>
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
  @livewireStyles
</head>
<body class="bg-gray-50">
<header class="border-b bg-white">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="font-semibold">ClicSTARe</div>
    <nav class="flex items-center gap-4 text-sm">
      @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('dashboard') }}" class="underline">Admin</a>
        <a href="{{ route('admin.sekolah.index') }}" class="underline">Sekolah</a>
      @endif
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="px-3 py-1.5 bg-red-600 text-white rounded">Logout</button>
      </form>
    </nav>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6">
  {{ $slot }}
</main>
@livewireScripts
</body>
</html>
