<?php

namespace App\Livewire;

use App\Models\ForumPost;
use App\Models\ForumThread;
use Livewire\Component;

class ShowThread extends Component
{
    public ForumThread $thread;
    public $newPostContent = '';

    protected $rules = [
        'newPostContent' => 'required|string|min:5',
    ];

    public function mount(ForumThread $thread)
    {
        $this->thread = $thread;
    }

    public function addPost()
    {
        $this->validate();

        $this->thread->posts()->create([
            'user_id' => auth()->id(),
            'content' => $this->newPostContent,
        ]);

        $this->newPostContent = ''; // Clear the input field
        session()->flash('message', 'Resposta adicionada com sucesso!');
    }

    public function render()
    {
        $this->thread->load(['user', 'subject.course', 'posts.user']); // Eager load relationships

        return view('livewire.show-thread');
    }
}