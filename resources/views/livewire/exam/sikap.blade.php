<div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    @php
        $isPostPhase = $phase === 'post';
        $titleSuffix = $isPostPhase ? 'Setelah Posttest' : 'Setelah Pretest';
        $resultTitle = $isPostPhase ? 'Hasil Posttest' : 'Hasil Pretest';
        $testName = $isPostPhase ? 'posttest' : 'pretest';
        $actionLabel = $isPostPhase ? 'Selesai' : 'Lanjut ke Video';
    @endphp

    @if ($finished)
        <div class="space-y-5">
            @if ($timedOut)
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Waktu {{ $testName }} telah habis sehingga jawaban dikumpulkan otomatis.
                </div>
            @endif

            @if ($testResult)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                    <h2 class="text-lg font-semibold text-emerald-800">{{ $resultTitle }}</h2>
                    <p class="text-emerald-700 mt-1">
                        Benar: <span class="font-semibold">{{ $testResult['total_benar'] }}</span>
                        / {{ $testResult['total_soal'] }}
                        &mdash; Skor: <span class="font-semibold">{{ $testResult['score'] }}</span>
                    </p>
                </div>
            @endif

            {{-- <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm text-slate-700">Jawaban sikap Anda sudah tersimpan.</p>
            </div> --}}

            <div class="flex items-center justify-end">
                <button wire:click="proceed"
                        class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                    {{ $actionLabel }}
                </button>
            </div>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-5">
            <p class="text-sm text-slate-600">
                Jawab setiap pernyataan berikut sesuai dengan kondisi Anda saat ini.
            </p>

            @forelse ($questions as $index => $question)
                <div class="rounded-lg border border-slate-200 p-4 space-y-3">
                    <p class="font-semibold text-slate-800">
                        {{ $index + 1 }}. {{ $question->teks }}
                    </p>

                    <div class="space-y-2">
                        @foreach ($choiceLabels as $code => $label)
                            <label class="flex items-center gap-3 text-sm text-slate-700">
                                <input type="radio"
                                       name="sikap-{{ $question->id }}"
                                       class="h-4 w-4"
                                       wire:model="answers.{{ $question->id }}"
                                       value="{{ $code }}">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('answers.' . $question->id)
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @empty
                <p class="text-sm text-slate-600">Tidak ada pertanyaan sikap yang tersedia.</p>
            @endforelse

            <div class="flex items-center justify-end">
                <button type="submit"
                        class="px-5 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                    Kumpulkan Jawaban
                </button>
            </div>
        </form>
    @endif
</div>
