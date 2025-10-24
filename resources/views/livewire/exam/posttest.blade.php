<div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    <h1 class="text-xl font-bold">Posttest</h1>

    @php
        $total    = $total ?? $this->total;
        $current  = $current ?? $this->current;
        $q        = $questions[$current] ?? null;

        // hitung yang sudah dijawab (nilai bukan null)
        $answered = collect($jawaban ?? [])->filter(fn($v) => !is_null($v))->count();
        $percent  = intval(($answered / max(1, $total)) * 100);

        $secondsRemaining = $this->secondsRemaining ?? 0;
        $minutesLeft      = intdiv($secondsRemaining, 60);
        $secondsLeft      = $secondsRemaining % 60;
        $formattedTime    = sprintf('%02d:%02d', $minutesLeft, $secondsLeft);

        $perQuestionSeconds = $this->perQuestionSeconds ?? 0;
        $perMinutes         = intdiv($perQuestionSeconds, 60);
        $perSeconds         = $perQuestionSeconds % 60;

        $perLabelParts = [];
        if ($perMinutes > 0) {
            $perLabelParts[] = $perMinutes . ' menit';
        }
        if ($perSeconds > 0) {
            $perLabelParts[] = $perSeconds . ' detik';
        }
        if (empty($perLabelParts)) {
            $perLabelParts[] = '0 detik';
        }
        $perQuestionLabel = implode(' ', $perLabelParts);
    @endphp

    @if ($showResult)
        {{-- Ringkasan hasil --}}
        <div class="space-y-4">
            @if ($timedOut)
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Waktu posttest telah habis; sistem mengumpulkan jawaban secara otomatis.
                </div>
            @endif

            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                <h2 class="text-lg font-semibold text-emerald-800">Hasil Posttest</h2>
                <p class="text-emerald-700 mt-1">
                    Benar: <span class="font-semibold">{{ $correct }}</span> /
                    {{ $total }} — Skor:
                    <span class="font-semibold">{{ $score }}</span>
                </p>

                @if($attemptId)
                    <p class="text-xs text-emerald-700 mt-2">ID Attempt: {{ $attemptId }}</p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3">
                <button wire:click="finish"
                        class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                    Selesai
                </button>
            </div>
        </div>
    @else
        <div wire:poll.1000ms="tick" class="space-y-6">
            {{-- Progres & timer --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Soal {{ $current + 1 }} dari {{ $total }}</span>
                    <span>{{ $percent }}% terjawab</span>
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Sisa waktu</span>
                    <span class="font-semibold text-red-600">{{ $formattedTime }}</span>
                </div>

                <p class="text-xs text-gray-500">
                    Batas waktu setiap soal: {{ $perQuestionLabel }}. Saat waktu habis, sistem otomatis lanjut dan Anda tidak bisa kembali.
                </p>

                {{-- Indikator nomor --}}
                <div class="flex flex-wrap gap-2 pt-1">
                    @for ($i = 0; $i < $total; $i++)
                        @php
                            $qid        = $questions[$i]->id ?? null;
                            $isCurrent  = $i === $current;
                            $isAnswered = $qid ? (array_key_exists($qid, $jawaban ?? []) && !is_null($jawaban[$qid])) : false;
                            $isLocked   = $i < $current;
                        @endphp
                        <button type="button"
                                wire:click="goTo({{ $i }})"
                                @disabled($isLocked)
                                class="h-8 w-8 rounded-full text-xs font-semibold transition
                                       {{ $isCurrent ? 'bg-blue-600 text-white'
                                        : ($isLocked ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                                     : ($isAnswered ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200')) }}">
                            {{ $i + 1 }}
                        </button>
                    @endfor
                </div>
            </div>

            {{-- Satu soal per layar --}}
            @if ($q)
                <form wire:submit.prevent="submit" class="space-y-6">
                    <div class="space-y-3" wire:key="q-{{ $q->id }}">
                        <div class="font-semibold">
                            {{ $current + 1 }}. {{ $q->teks }}
                        </div>

                        <div class="space-y-2">
                            @foreach ($q->options as $opt)
                                <label class="flex items-center gap-3" wire:key="q-{{ $q->id }}-opt-{{ $opt->id }}">
                                    <input type="radio"
                                           class="h-4 w-4"
                                           name="q-{{ $q->id }}"
                                           wire:model="jawaban.{{ $q->id }}"
                                           value="{{ $opt->id }}">
                                    <span>{{ $opt->teks }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('jawaban.' . $q->id)
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Navigasi --}}
                    <div class="flex items-center justify-between pt-4">
                        <button type="button"
                                disabled
                                class="px-4 py-2 rounded border bg-gray-100 text-gray-400 cursor-not-allowed">
                            Tidak Bisa Kembali
                        </button>

                        <div class="flex gap-3">
                            @if ($current < $total - 1)
                                <button type="button"
                                        wire:click="next"
                                        class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                                    Berikutnya
                                </button>
                            @else
                                <button type="submit"
                                        class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                                    Kumpulkan Posttest
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            @else
                <p>Tidak ada soal posttest.</p>
            @endif
        </div>
    @endif
</div>
