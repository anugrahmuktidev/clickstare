<?php

namespace App\Livewire\Journals;

use App\Models\Journal;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.guest')]
class Index extends Component
{
    use WithPagination;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public int $perPage = 9;

    public function render()
    {
        /** @var LengthAwarePaginator<Journal> $journals */
        $journals = Journal::query()
            ->latest('updated_at')
            ->paginate($this->perPage);

        return view('livewire.journals.index', [
            'journals' => $journals,
        ]);
    }
}
