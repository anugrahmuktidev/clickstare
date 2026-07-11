<div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    @php
        $isPostPhase = $phase === 'post';
        $actionLabel = $isPostPhase ? 'Selesaikan Posttest' : 'Selesaikan Pretest';
        $savedLabel = $isPostPhase
            ? 'Jawaban bagian sikap posttest Anda sudah tersimpan.'
            : 'Jawaban bagian sikap pretest Anda sudah tersimpan.';
    @endphp

    <div>
        <h1 class="text-xl font-semibold text-slate-900">
            {{ $isPostPhase ? 'Posttest: Bagian Sikap' : 'Pretest: Bagian Sikap' }}
        </h1>
    </div>

    @if ($finished)
        <div class="space-y-5">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm text-slate-700">{{ $savedLabel }}</p>
            </div>

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
