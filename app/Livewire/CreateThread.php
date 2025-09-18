<?php

namespace App\Livewire;

use App\Models\ForumThread;
use App\Models\Subject;
use Livewire\Component;

class CreateThread extends Component
{
    public $title;
    public $content;
    public $subject_id;

    protected $rules = [
        'title' => 'required|string|min:5|max:255',
        'content' => 'required|string|min:10',
        'subject_id' => 'nullable|exists:subjects,id',
    ];

    public function createThread()
    {
        $this->validate();

        ForumThread::create([
            'title' => $this->title,
            'content' => $this->content,
            'user_id' => auth()->id(),
            'subject_id' => $this->subject_id,
        ]);

        session()->flash('message', 'Tópico criado com sucesso!');

        $this->redirect(route('forum.index')); // Redirect to the forum list
    }

    public function render()
    {
        $subjects = Subject::all();
        return view('livewire.create-thread', [
            'subjects' => $subjects,
        ]);
    }
}