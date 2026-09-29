<?php

namespace App\Livewire\Exam;

use App\Models\ExamParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Pretest extends Component
{
    public array $pocketMoneyOptions = [
        '5000-10000' => '5.000 - 10.000',
        '10000-20000' => '10.000 - 20.000',
        '20000-50000' => '20.000 - 50.000',
        '>50000' => '> 50.000',
    ];

    public ?string $pocket_money_range = null;
    public ?string $uses_electric_smoke = null;
    public ?string $uses_conventional_smoke = null;
    public ?string $uses_both_smoke_types = null;

    public function mount(): void
    {
        $participation = ExamParticipation::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_session_id' => session('active_exam_session_id'),
            ],
            ['current_step' => 'pretest']
        );

        if ($participation->current_step !== 'pretest') {
            $this->redirectRoute("exam.{$participation->current_step}", navigate: true);
            return;
        }

        $this->pocket_money_range = $participation->pocket_money_range;
        $this->uses_electric_smoke = $this->toYesNo($participation->uses_electric_smoke);
        $this->uses_conventional_smoke = $this->toYesNo($participation->uses_conventional_smoke);
        $this->uses_both_smoke_types = $this->toYesNo($participation->uses_both_smoke_types);
    }

    public function submit(): void
    {
        $data = $this->validate([
            'pocket_money_range' => ['required', 'in:' . implode(',', array_keys($this->pocketMoneyOptions))],
            'uses_electric_smoke' => ['required', 'in:ya,tidak'],
            'uses_conventional_smoke' => ['required', 'in:ya,tidak'],
            'uses_both_smoke_types' => ['required', 'in:ya,tidak'],
        ], [], [
            'pocket_money_range' => 'uang saku',
            'uses_electric_smoke' => 'merokok elektrik',
            'uses_conventional_smoke' => 'merokok tembakau/konvensional',
            'uses_both_smoke_types' => 'merokok elektrik dan konvensional/tembakau',
        ]);

        $participation = ExamParticipation::where('user_id', Auth::id())
            ->where('exam_session_id', session('active_exam_session_id'))
            ->firstOrFail();
        $participation->update([
            'pocket_money_range' => $data['pocket_money_range'],
            'uses_electric_smoke' => $data['uses_electric_smoke'] === 'ya',
            'uses_conventional_smoke' => $data['uses_conventional_smoke'] === 'ya',
            'uses_both_smoke_types' => $data['uses_both_smoke_types'] === 'ya',
            'current_step' => 'pengetahuan_test',
        ]);

        $this->redirectRoute('exam.pengetahuan_test', navigate: true);
    }

    public function render()
    {
        return view('livewire.exam.pretest');
    }

    protected function toYesNo(?bool $value): ?string
    {
        return match ($value) {
            true => 'ya',
            false => 'tidak',
            default => null,
        };
    }
}
