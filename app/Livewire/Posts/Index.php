<?php

namespace App\Livewire\Posts;

use App\Models\Post;
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

    public int $perPage = 6;

    public function render()
    {
        /** @var LengthAwarePaginator<Post> $posts */
        $posts = Post::query()
            ->latest('updated_at')
            ->paginate($this->perPage);

        return view('livewire.posts.index', [
            'posts' => $posts,
        ]);
    }
}
