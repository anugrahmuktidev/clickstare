@extends('layouts.guest')

@section('content')
  <div class="bg-white shadow-md rounded-lg text-center px-4 py-6 max-w-md">
    <h1 class="text-xl font-bold mb-4">Akun Anda Menunggu Validasi</h1>
    <p class="text-gray-600 mb-4">Silakan tunggu sampai admin memverifikasi akun Anda.</p>

    <div class="flex items-center justify-center gap-3">
      <button onclick="location.reload()"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
        Refresh
      </button>
      <span id="watcher-status" class="text-sm text-gray-500">Memeriksa status…</span>
    </div>
  </div>

  <script>
    async function checkValidation() {
      try {
        const res = await fetch('{{ route('validation.status') }}', { cache: 'no-store' });
        const data = await res.json();
        if (data.validated) {
          document.getElementById('watcher-status').textContent = 'Tervalidasi. Mengalihkan...';
          window.location.href = data.redirect;
        } else {
          document.getElementById('watcher-status').textContent = 'Belum divalidasi, cek lagi otomatis.';
        }
      } catch (e) {
        document.getElementById('watcher-status').textContent = 'Gagal cek status. Coba refresh.';
      }
    }
    checkValidation();
    setInterval(checkValidation, 5000);
  </script>
@endsection
