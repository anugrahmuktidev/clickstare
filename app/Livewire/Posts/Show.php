<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Show extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function render()
    {
        $latestPosts = Post::query()
            ->whereKeyNot($this->post->getKey())
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('livewire.posts.show', [
            'post' => $this->post,
            'latestPosts' => $latestPosts,
        ]);
    }
}
