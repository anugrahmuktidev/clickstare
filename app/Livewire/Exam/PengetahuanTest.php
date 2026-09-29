<?php

namespace App\Livewire\Exam;

use App\Models\ExamParticipation;
use App\Models\KnowledgeAnswer;
use App\Models\KnowledgeQuestion;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class PengetahuanTest extends Component
{
    /**
     * @var \Illuminate\Support\Collection<int,\App\Models\KnowledgeQuestion>
     */
    public $questions;

    /**
     * answers[question_id] = option code (BENAR/SALAH)
     */
    public array $answers = [];

    public bool $finished = false;
    public string $phase = 'pre'; // pre atau post
    public ?array $testResult = null;
    public bool $timedOut = false;

    public array $choiceLabels = [
        'BENAR' => 'Benar',
        'SALAH' => 'Salah',
    ];

    public function mount(): void
    {
        $routeName = request()->route()?->getName();
        $this->phase = $routeName === 'exam.pengetahuan_test_post' ? 'post' : 'pre';

        $p = ExamParticipation::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_session_id' => session('active_exam_session_id'),
            ],
            ['current_step' => 'pretest']
        );

        $requiredStep = $this->phase === 'post' ? 'pengetahuan_test_post' : 'pengetahuan_test';
        if ($p->current_step !== $requiredStep) {
            $this->redirectRoute("exam.{$p->current_step}", navigate: true);
            return;
        }

        $this->questions = KnowledgeQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        foreach ($this->questions as $question) {
            if (! array_key_exists($question->id, $this->answers)) {
                $this->answers[$question->id] = null;
            }
        }

        $userId = Auth::id();
        $examSessionId = (int) session('active_exam_session_id');
        $questionIds = $this->questions->pluck('id');

        if ($questionIds->isNotEmpty()) {
            $existing = KnowledgeAnswer::query()
                ->where('user_id', $userId)
                ->where('exam_session_id', $examSessionId)
                ->where('stage', $this->phase)
                ->whereIn('knowledge_question_id', $questionIds)
                ->get()
                ->keyBy('knowledge_question_id');

            if ($existing->isNotEmpty()) {
                foreach ($existing as $questionId => $answer) {
                    $this->answers[$questionId] = $this->normalizeAnswerValue($answer->value);
                }

                if ($existing->count() === $this->questions->count()) {
                    $this->finished = true;
                }
            }
        } else {
            $this->finished = true;
        }

        $attemptKey = $this->phase === 'post' ? 'posttest_attempt_id' : 'pretest_attempt_id';
        $timedOutKey = $this->phase === 'post' ? 'posttest_timed_out' : 'pretest_timed_out';
        $attemptId = session()->pull($attemptKey);
        $this->testResult = $this->loadTestResult(
            $this->phase === 'post' ? 'post' : 'pre',
            $examSessionId,
            $attemptId ? (int) $attemptId : null
        );
        $this->timedOut = (bool) session()->pull($timedOutKey, false);
    }

    public function updated(string $name): void
    {
        if (str_starts_with($name, 'answers.')) {
            $this->resetErrorBag($name);
        }
    }

    public function submit(): void
    {
        if ($this->questions->isEmpty()) {
            $this->finished = true;
            return;
        }

        $this->resetErrorBag();

        $validOptions = array_keys($this->choiceLabels);
        foreach ($this->questions as $question) {
            $value = $this->answers[$question->id] ?? null;
            if (! in_array($value, $validOptions, true)) {
                $this->addError("answers.$question->id", 'Pilih salah satu jawaban.');
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $userId = Auth::id();
        $examSessionId = (int) session('active_exam_session_id');
        $questionIds = $this->questions->pluck('id')->all();
        $now = now();
        $totalSoal = $this->questions->count();
        $totalBenar = 0;

        $rows = [];
        foreach ($this->questions as $question) {
            $selected = $this->answers[$question->id];
            $correct = $this->normalizeAnswerValue($question->correct_answer);

            if ($this->normalizeAnswerValue($selected) === $correct) {
                $totalBenar++;
            }

            $rows[] = [
                'knowledge_question_id' => $question->id,
                'user_id'               => $userId,
                'exam_session_id'       => $examSessionId,
                'stage'                 => $this->phase,
                'value'                 => $selected,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        $score = (int) round(($totalBenar / max(1, $totalSoal)) * 100);
        $attemptType = $this->phase === 'post' ? 'post' : 'pre';

        DB::transaction(function () use ($rows, $userId, $examSessionId, $questionIds, $attemptType, $totalSoal, $totalBenar, $score) {
            KnowledgeAnswer::query()
                ->where('user_id', $userId)
                ->where('exam_session_id', $examSessionId)
                ->where('stage', $this->phase)
                ->whereIn('knowledge_question_id', $questionIds)
                ->delete();

            KnowledgeAnswer::insert($rows);

            TestAttempt::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'exam_session_id' => $examSessionId,
                    'tipe' => $attemptType,
                ],
                [
                    'total_soal' => $totalSoal,
                    'total_benar' => $totalBenar,
                    'score' => $score,
                ]
            );
        });

        $this->testResult = [
            'total_soal' => $totalSoal,
            'total_benar' => $totalBenar,
            'score' => $score,
        ];
        $this->finished = true;
    }

    public function proceed(): void
    {
        if (! $this->finished) {
            $this->addError('answers', 'Selesaikan pertanyaan pengetahuan terlebih dahulu.');
            return;
        }

        $p = ExamParticipation::where('user_id', Auth::id())
            ->where('exam_session_id', session('active_exam_session_id'))
            ->firstOrFail();

        if ($this->phase === 'post') {
            $p->update([
                'knowledge_test_post_completed_at' => now(),
                'current_step'                     => 'sikap_post',
            ]);

            session()->flash('success', 'Bagian pengetahuan posttest selesai. Lanjut ke bagian sikap.');
            $this->redirectRoute('exam.sikap_post', navigate: true);
            return;
        }

        $p->update([
            'knowledge_test_completed_at' => now(),
            'current_step'                => 'sikap',
        ]);

        session()->flash('success', 'Bagian pengetahuan pretest selesai. Lanjut ke bagian sikap.');

        $this->redirectRoute('exam.sikap', navigate: true);
    }

    protected function loadTestResult(string $tipe, ?int $examSessionId = null, ?int $attemptId = null): ?array
    {
        $query = TestAttempt::query()
            ->where('user_id', Auth::id())
            ->where('tipe', $tipe)
            ->when($examSessionId, fn ($query) => $query->where('exam_session_id', $examSessionId));

        if ($attemptId) {
            $query->where('id', $attemptId);
        }

        $attempt = $query->latest()->first();

        if (! $attempt) {
            return null;
        }

        $totalSoal = $attempt->total_soal ?? $attempt->answers()->count();
        $totalBenar = $attempt->total_benar ?? $attempt->answers()->where('is_correct', true)->count();
        $score = $attempt->score ?? (int) round(($totalBenar / max(1, $totalSoal)) * 100);

        return [
            'total_soal'  => (int) $totalSoal,
            'total_benar' => (int) $totalBenar,
            'score'       => (int) $score,
        ];
    }

    public function render()
    {
        return view('livewire.exam.pengetahuan-test');
    }

    protected function normalizeAnswerValue(?string $value): ?string
    {
        return match ($value) {
            'BENAR', 'SALAH' => $value,
            'S', 'SS' => 'BENAR',
            'TS', 'STS' => 'SALAH',
            default => null,
        };
    }
}
