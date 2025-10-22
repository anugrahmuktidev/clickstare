<?php

namespace App\Livewire\Exam;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ExamParticipation;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]
class VideoExam extends Component
{
    public ?Video $video = null;
    public bool $canComplete = false;

    public function mount()
    {
        $p = ExamParticipation::where('user_id', Auth::id())->firstOrFail();

        if ($p->current_step !== 'video') {
            $this->redirectRoute("exam.{$p->current_step}", navigate: true);
            return;
        }

        // Ambil video edukasi yang ditandai aktif oleh admin.
        $this->video = Video::where('is_active', true)
            ->latest('id')
            ->first()
            ?? Video::latest('id')->first();

        $this->canComplete = $this->video === null;
    }

    public function markFinished(): void
    {
        if ($this->video !== null) {
            $this->canComplete = true;
        }
    }

    public function completeVideo()
    {
        if (! $this->canComplete && $this->video !== null) {
            $this->addError('video', 'Tonton video hingga selesai terlebih dahulu.');
            return;
        }

        $p = ExamParticipation::where('user_id', Auth::id())->firstOrFail();

        $p->update([
            'video_watched_at' => now(),
            'current_step'     => 'posttest',
        ]);

        session()->flash('success', 'Video selesai. Lanjut posttest.');
        return $this->redirectRoute('exam.posttest', navigate: true);
    }

    public function render()
    {
        return view('livewire.exam.video', [
            'video' => $this->video,
        ]);
    }
}
