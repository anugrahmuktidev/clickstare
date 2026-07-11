<?php

namespace App\Livewire\Exam;

use App\Models\ExamParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Posttest extends Component
{
    public function mount(): void
    {
        $participation = ExamParticipation::firstOrCreate(
            ['user_id' => Auth::id()],
            ['current_step' => 'pretest']
        );

        if ($participation->current_step !== 'posttest') {
            $this->redirectRoute("exam.{$participation->current_step}", navigate: true);
            return;
        }

        $participation->update([
            'current_step' => 'pengetahuan_test_post',
        ]);

        $this->redirectRoute('exam.pengetahuan_test_post', navigate: true);
    }

    public function render()
    {
        return view('livewire.exam.posttest');
    }
}
