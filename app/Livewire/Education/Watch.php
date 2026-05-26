<?php

namespace App\Livewire\Education;

use Livewire\Component;
use App\Models\Video;
use App\Models\TestAttempt;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]

class Watch extends Component
{
    public Video $video;

    public function mount(Video $video)
    {
        $user = Auth::user();

        if ($user?->isSiswa()) {
            $hasPretestAttempt = TestAttempt::query()
                ->where('user_id', $user->id)
                ->where('tipe', 'pre')
                ->exists();

            if (! $hasPretestAttempt) {
                session()->flash('error', 'Selesaikan pretest terlebih dahulu untuk membuka video edukasi.');
                $this->redirectRoute('education.index', navigate: true);
                return;
            }
        }

        $this->video = $video;
    }

    public function render()
    {
        return view('livewire.education.watch');
    }
}
