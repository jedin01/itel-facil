<?php

namespace App\Livewire;

use App\Models\ForumThread;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class ListThreads extends Component
{
    use WithPagination;

    public $search = '';
    public $subject_id = '';

    public function render()
    {
        $query = ForumThread::query();

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        }

        if ($this->subject_id) {
            $query->where('subject_id', $this->subject_id);
        }

        $threads = $query->with(['user', 'subject.course'])->latest()->paginate(10);
        $subjects = Subject::all(); // For the filter dropdown

        return view('livewire.list-threads', [
            'threads' => $threads,
            'subjects' => $subjects,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubjectId()
    {
        $this->resetPage();
    }
}