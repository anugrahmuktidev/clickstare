@extends('layouts.guest')

@section('content')
  <div class="w-full">
    <div class="mx-auto max-w-3xl space-y-10 px-4 py-10 sm:py-12 lg:py-16">
      <div class="space-y-3 text-center">
        <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-red-700">
          Waspada Bahaya Rokok
        </span>
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
          Ketahui Risikonya, Mulai Langkah Berhenti Hari Ini
        </h1>
        <p class="text-base text-gray-600 sm:text-lg">
          Rokok bukan hanya merusak kesehatan Anda, tetapi juga orang-orang yang Anda sayangi.
          Pelajari dampaknya dan temukan dukungan praktis untuk berhenti merokok.
        </p>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <article class="rounded-xl border border-red-100 bg-white/90 p-6 shadow-sm shadow-red-50 backdrop-blur">
          <h2 class="text-lg font-semibold text-gray-900">Risiko yang Langsung Terasa</h2>
          <ul class="mt-3 space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Napas pendek, batuk kronis, dan menurunnya stamina tubuh.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Gigi menguning, kulit kusam, hingga bau rokok yang menempel pada pakaian.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Risiko kesehatan meningkat bagi keluarga karena paparan asap rokok.
            </li>
          </ul>
        </article>

        <article class="rounded-xl border border-red-100 bg-white/90 p-6 shadow-sm shadow-red-50 backdrop-blur">
          <h2 class="text-lg font-semibold text-gray-900">Dampak Jangka Panjang</h2>
          <ul class="mt-3 space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Penyakit kardiovaskular seperti serangan jantung dan stroke.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Peningkatan risiko kanker paru, mulut, tenggorokan, hingga kandung kemih.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
              Gangguan kesuburan dan lebih rentan terhadap komplikasi kehamilan.
            </li>
          </ul>
        </article>
      </div>

      <section class="rounded-2xl border border-emerald-100 bg-emerald-50/80 p-6 sm:p-8">
        <div class="space-y-4 text-center sm:text-left">
          <h2 class="text-2xl font-semibold text-emerald-900">Mulai Berhenti dengan Dukungan yang Tepat</h2>
          <p class="text-sm text-emerald-800 sm:text-base">
            Setiap orang punya perjalanan berbeda untuk berhenti merokok. Jangan ragu minta bantuan dan gunakan sumber daya yang tersedia.
          </p>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
          <div class="rounded-lg border border-white/60 bg-white/90 p-4 text-left shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Langkah Praktis</h3>
            <ul class="mt-3 space-y-2 text-sm text-emerald-800">
              <li>• Tentukan tanggal berhenti dan cari teman untuk mendukung.</li>
              <li>• Ganti kebiasaan merokok dengan aktivitas sehat seperti olahraga ringan.</li>
              <li>• Catat pemicu keinginan merokok dan siapkan strategi untuk menghadapinya.</li>
            </ul>
          </div>
          <div class="rounded-lg border border-white/60 bg-white/90 p-4 text-left shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Sumber Bantuan</h3>
            <ul class="mt-3 space-y-2 text-sm text-emerald-800">
              <li>• Konsultasi ke puskesmas atau klinik berhenti merokok terdekat.</li>
              <li>• Hubungi layanan konseling bebas rokok melalui <span class="font-semibold">Quitline 0-800-177-6565</span>.</li>
              <li>• Manfaatkan komunitas dukungan keluarga dan teman untuk tetap konsisten.</li>
            </ul>
          </div>
        </div>
      </section>

      <section class="rounded-2xl border border-blue-100 bg-blue-50/80 p-6 sm:p-8">
        <div class="space-y-4 text-center sm:text-left">
          <h2 class="text-2xl font-semibold text-blue-900">Butuh Motivasi Tambahan?</h2>
          <p class="text-sm text-blue-800 sm:text-base">
            ClicSTARe menyediakan materi edukasi dan jurnal untuk membantu perjalanan berhenti merokok.
            Mulailah menjelajah dan catat perkembangan Anda secara berkala.
          </p>
        </div>
        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center">
          <a href="{{ route('education.index') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
            Buka materi edukasi
          </a>
          <a href="{{ route('journals.index') }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-white px-5 py-2 text-sm font-semibold text-blue-800 shadow hover:border-blue-300">
            Lihat jurnal inspiratif
          </a>
        </div>
      </section>

      <div class="rounded-xl border border-gray-200 bg-white/90 p-6 text-center text-sm text-gray-600">
        Ingatlah: berhenti merokok adalah proses. Rayakan setiap kemajuan kecil dan jangan menyerah saat
        mengalami kesulitan. Anda tidak sendirian.
      </div>
    </div>
  </div>
@endsection
