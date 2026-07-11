<?php

namespace App\Livewire\Exam;

use App\Models\AttitudeAnswer;
use App\Models\AttitudeQuestion;
use App\Models\ExamParticipation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Sikap extends Component
{
    /**
     * @var \Illuminate\Support\Collection<int,\App\Models\AttitudeQuestion>
     */
    public $questions;

    /**
     * answers[question_id] = option code (STS/TS/S/SS)
     */
    public array $answers = [];

    public bool $finished = false;
    public string $phase = 'pre'; // pre atau post

    public array $choiceLabels = [
        'STS' => 'Sangat Tidak Setuju (STS)',
        'TS'  => 'Tidak Setuju (TS)',
        'S'   => 'Setuju (S)',
        'SS'  => 'Sangat Setuju (SS)',
    ];

    public function mount(): void
    {
        $routeName = request()->route()?->getName();
        $this->phase = $routeName === 'exam.sikap_post' ? 'post' : 'pre';

        $p = ExamParticipation::firstOrCreate(
            ['user_id' => Auth::id()],
            ['current_step' => 'pretest']
        );

        $requiredStep = $this->phase === 'post' ? 'sikap_post' : 'sikap';
        if ($p->current_step !== $requiredStep) {
            $this->redirectRoute("exam.{$p->current_step}", navigate: true);
            return;
        }

        $this->questions = AttitudeQuestion::query()
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
        $questionIds = $this->questions->pluck('id');

        if ($questionIds->isNotEmpty()) {
            $existing = AttitudeAnswer::query()
                ->where('user_id', $userId)
                ->where('stage', $this->phase)
                ->whereIn('attitude_question_id', $questionIds)
                ->get()
                ->keyBy('attitude_question_id');

            if ($existing->isNotEmpty()) {
                foreach ($existing as $questionId => $answer) {
                    $this->answers[$questionId] = $answer->value;
                }

                if ($existing->count() === $this->questions->count()) {
                    $this->finished = true;
                }
            }
        } else {
            $this->finished = true;
        }

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
        $questionIds = $this->questions->pluck('id')->all();
        $now = now();

        $rows = [];
        foreach ($this->questions as $question) {
            $rows[] = [
                'attitude_question_id' => $question->id,
                'user_id'              => $userId,
                'stage'                => $this->phase,
                'value'                => $this->answers[$question->id],
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        DB::transaction(function () use ($rows, $userId, $questionIds) {
            AttitudeAnswer::query()
                ->where('user_id', $userId)
                ->where('stage', $this->phase)
                ->whereIn('attitude_question_id', $questionIds)
                ->delete();

            AttitudeAnswer::insert($rows);
        });

        $this->finished = true;
    }

    public function proceed(): void
    {
        if (! $this->finished) {
            $this->addError('answers', 'Selesaikan pertanyaan sikap terlebih dahulu.');
            return;
        }

        $p = ExamParticipation::where('user_id', Auth::id())->firstOrFail();

        if ($this->phase === 'post') {
            $p->update([
                'sikap_post_completed_at' => now(),
                'posttest_completed_at'   => now(),
                'current_step'            => 'done',
            ]);

            session()->flash('success', 'Posttest selesai.');
            $this->redirectRoute('education.index', navigate: true);
            return;
        }

        $p->update([
            'sikap_completed_at'   => now(),
            'pretest_completed_at' => now(),
            'current_step'         => 'posttest',
        ]);

        session()->flash('success', 'Pretest selesai. Video edukasi sudah terbuka.');

        $this->redirectRoute('education.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.exam.sikap');
    }
}
