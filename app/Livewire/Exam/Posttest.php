<?php

namespace App\Livewire\Exam;

use Livewire\Component;
use App\Models\Question;
use App\Models\TestAnswer;
use App\Models\TestAttempt;
use Livewire\Attributes\Layout;
use App\Models\ExamParticipation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]
class Posttest extends Component
{
    /** @var \Illuminate\Support\Collection<int, \App\Models\Question> */
    public $questions;

    /** jawaban[question_id] = option_id|null */
    public array $jawaban = [];

    /** index soal aktif (0-based) */
    public int $current = 0;

    public int $total = 0;

    /** durasi maksimum per soal (detik) */
    public int $perQuestionSeconds = 120;
    public int $secondsRemaining = 0;
    public int $questionStartedAt = 0;
    public bool $timedOut = false;

    // hasil setelah submit
    public bool $showResult = false;
    public int $correct = 0;
    public int $score = 0;
    public ?int $attemptId = null;

    public function mount(): void
    {
        $p = ExamParticipation::firstOrCreate(
            ['user_id' => Auth::id()],
            ['current_step' => 'pretest']
        );

        if ($p->current_step !== 'posttest') {
            $this->redirectRoute("exam.{$p->current_step}", navigate: true);
            return;
        }

        $this->questions = Question::with('options')
            ->where('tipe', 'post')
            ->orderBy('nomor')
            ->get()
            ->values();

        $this->total = $this->questions->count();

        foreach ($this->questions as $q) {
            if (! array_key_exists($q->id, $this->jawaban)) {
                $this->jawaban[$q->id] = null;
            }
        }

        $this->startTimerForCurrentQuestion();
    }

    public function updated(string $name, $value): void
    {
        if (str_starts_with($name, 'jawaban.')) {
            $qid = (int) substr($name, strlen('jawaban.'));
            $this->resetErrorBag("jawaban.$qid");
        }
    }

    public function prev(): void
    {
        // Navigasi balik dinonaktifkan untuk posttest.
        return;
    }

    public function next(bool $force = false): void
    {
        $this->resetErrorBag();

        if ($this->total === 0) {
            return;
        }

        $qid = optional($this->questions[$this->current])->id;
        if (! $force && $qid !== null && $this->jawaban[$qid] === null) {
            $this->addError("jawaban.$qid", 'Pilih satu jawaban terlebih dahulu.');
            return;
        }

        if ($this->current < $this->total - 1) {
            $this->forgetTimerForIndex($this->current);
            $this->current++;
            $this->startTimerForCurrentQuestion(reset: true);
        } elseif ($force) {
            $this->timedOut = true;
            $this->submit(force: true);
        }
    }

    public function goTo(int $index): void
    {
        $this->resetErrorBag();

        if ($index <= $this->current || $index >= $this->total) {
            return;
        }

        if ($this->total === 0) {
            return;
        }

        $qid = optional($this->questions[$this->current])->id;
        if ($qid !== null && $this->jawaban[$qid] === null) {
            $this->addError("jawaban.$qid", 'Pilih satu jawaban terlebih dahulu.');
            return;
        }

        $this->forgetTimerForIndex($this->current);
        $this->current = $index;
        $this->startTimerForCurrentQuestion(reset: true);
    }

    public function submit(bool $force = false): void
    {
        if ($this->showResult) {
            return;
        }

        $this->resetErrorBag();

        if (! $force) {
            foreach ($this->questions as $q) {
                if ($this->jawaban[$q->id] === null) {
                    $this->addError("jawaban.$q->id", 'Masih ada soal yang belum dijawab.');
                }
            }
            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }
        }

        if ($this->total === 0) {
            $this->showResult = true;
            $this->clearTimerSessions();
            $this->secondsRemaining = 0;
            $this->questionStartedAt = 0;
            return;
        }

        $benar = 0;
        foreach ($this->questions as $q) {
            $chosen = $this->jawaban[$q->id];
            if ($chosen !== null) {
                $opt = $q->options->firstWhere('id', (int) $chosen);
                if ($opt && $opt->benar) {
                    $benar++;
                }
            }
        }
        $this->correct = $benar;
        $this->score   = (int) round(($benar / max(1, $this->total)) * 100);

        $attempt = TestAttempt::create([
            'user_id'     => Auth::id(),
            'tipe'        => 'post',
            'total_soal'  => $this->total,
            'total_benar' => $this->correct,
            'score'       => $this->score,
        ]);
        $this->attemptId = $attempt->id;

        $rows = [];
        $now  = now();
        foreach ($this->questions as $q) {
            $chosenId = $this->jawaban[$q->id];
            if ($chosenId === null) {
                continue;
            }

            $chosenId  = (int) $chosenId;
            $isCorrect = (bool) optional($q->options->firstWhere('id', $chosenId))->benar;

            $rows[] = [
                'test_attempt_id' => $attempt->id,
                'question_id'     => $q->id,
                'option_id'       => $chosenId,
                'is_correct'      => $isCorrect,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }
        if (! empty($rows)) {
            TestAnswer::insert($rows);
        }

        $this->showResult = true;
        $this->clearTimerSessions();
        $this->secondsRemaining = 0;
        $this->questionStartedAt = 0;
    }

    public function tick(): void
    {
        if ($this->showResult || $this->total === 0) {
            return;
        }

        $this->updateTimer();

        if ($this->secondsRemaining <= 0) {
            $this->next(force: true);
        }
    }

    protected function startTimerForCurrentQuestion(bool $reset = false): void
    {
        if ($this->total === 0) {
            $this->secondsRemaining = 0;
            return;
        }

        $key = $this->timerSessionKey($this->current);
        $start = (int) session($key, 0);

        if ($reset || $start <= 0) {
            $start = now()->timestamp;
            session([$key => $start]);
        }

        $this->questionStartedAt = $start;
        $this->updateTimerInternal();
    }

    protected function updateTimer(): void
    {
        if ($this->questionStartedAt <= 0) {
            $this->startTimerForCurrentQuestion();
            return;
        }

        $this->updateTimerInternal();
    }

    protected function updateTimerInternal(): void
    {
        $now = now()->timestamp;
        $elapsed = $now - $this->questionStartedAt;

        if ($elapsed < 0) {
            $elapsed = 0;
        }

        $remaining = $this->perQuestionSeconds - $elapsed;
        $this->secondsRemaining = max(0, $remaining);
    }

    protected function timerSessionKey(int $index): string
    {
        return "posttest_question_{$index}_started_at";
    }

    protected function forgetTimerForIndex(int $index): void
    {
        session()->forget($this->timerSessionKey($index));
    }

    protected function clearTimerSessions(): void
    {
        for ($i = 0; $i < $this->total; $i++) {
            session()->forget($this->timerSessionKey($i));
        }
    }

    public function finish()
    {
        $p = ExamParticipation::where('user_id', Auth::id())->firstOrFail();

        $p->update([
            'posttest_completed_at' => now(),
            'current_step'          => 'done',
        ]);

        return $this->redirectRoute('education.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.exam.posttest');
    }
}
