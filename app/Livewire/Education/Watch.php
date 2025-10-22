<?php

namespace App\Livewire\Education;

use Livewire\Component;
use App\Models\Video;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]

class Watch extends Component
{
    public Video $video;

    public function mount(Video $video)
    {
        $this->video = $video;
    }

    public function render()
    {
        return view('livewire.education.watch');
    }
}
