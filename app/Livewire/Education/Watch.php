<?php

namespace App\Livewire\Education;

use Livewire\Component;
use App\Models\ExamParticipation;
use App\Models\Video;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]

class Watch extends Component
{
    public Video $video;
    public bool $videoCompleted = false;

    public function mount(Video $video)
    {
        $user = Auth::user();

        if ($user?->isSiswa()) {
            $participation = ExamParticipation::query()
                ->where('user_id', $user->id)
                ->where('exam_session_id', session('active_exam_session_id'))
                ->whereNotNull('pretest_completed_at')
                ->first();

            if (! $participation) {
                session()->flash('error', 'Selesaikan pretest terlebih dahulu untuk membuka video edukasi.');
                $this->redirectRoute('education.index', navigate: true);
                return;
            }

            $requiredVideo = Video::query()
                ->where('is_after_pretest', true)
                ->latest('id')
                ->first();

            if ($requiredVideo && ! $participation->video_watched_at && $requiredVideo->isNot($video)) {
                $this->redirectRoute('education.watch', $requiredVideo, navigate: true);
                return;
            }

            $this->videoCompleted = $participation->video_watched_at !== null;
        }

        $this->video = $video;
    }

    public function completeVideo(): void
    {
        $user = Auth::user();

        if (! $user?->isSiswa()) {
            $this->videoCompleted = true;
            return;
        }

        $participation = ExamParticipation::query()
            ->where('user_id', $user->id)
            ->where('exam_session_id', session('active_exam_session_id'))
            ->whereNotNull('pretest_completed_at')
            ->first();

        if (! $participation) {
            session()->flash('error', 'Selesaikan pretest terlebih dahulu untuk membuka video edukasi.');
            $this->redirectRoute('education.index', navigate: true);
            return;
        }

        if (! $participation->video_watched_at) {
            $participation->update(['video_watched_at' => now()]);
        }

        $this->videoCompleted = true;
    }

    public function render()
    {
        return view('livewire.education.watch');
    }
}
