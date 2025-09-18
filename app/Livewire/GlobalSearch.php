<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Document;
use App\Models\ForumThread;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $search = '';
    public $results = [];

    public function updatedSearch()
    {
        $this->performSearch();
    }

    public function mount()
    {
        $this->performSearch();
    }

    public function performSearch()
    {
        $this->results = [];

        if (strlen($this->search) < 3) {
            return;
        }

        // Search Documents
        $this->results['documents'] = Document::where('title', 'like', '%' . $this->search . '%')
                                            ->orWhere('description', 'like', '%' . $this->search . '%')
                                            ->with(['subject.course', 'user'])
                                            ->limit(5)
                                            ->get();

        // Search Forum Threads
        $this->results['forum_threads'] = ForumThread::where('title', 'like', '%' . $this->search . '%')
                                                    ->orWhere('content', 'like', '%' . $this->search . '%')
                                                    ->with(['user', 'subject.course'])
                                                    ->limit(5)
                                                    ->get();

        // Search Courses
        $this->results['courses'] = Course::where('name', 'like', '%' . $this->search . '%')
                                        ->orWhere('description', 'like', '%' . $this->search . '%')
                                        ->limit(5)
                                        ->get();

        // Search Subjects
        $this->results['subjects'] = Subject::where('name', 'like', '%' . $this->search . '%')
                                            ->orWhere('description', 'like', '%' . $this->search . '%')
                                            ->with('course')
                                            ->limit(5)
                                            ->get();

        // Search Helpers (Users with is_alumni = true)
        $this->results['helpers'] = User::where('is_alumni', true)
                                        ->where(function ($query) {
                                            $query->where('name', 'like', '%' . $this->search . '%')
                                                  ->orWhere('email', 'like', '%' . $this->search . '%');
                                        })
                                        ->with('course')
                                        ->limit(5)
                                        ->get();
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}