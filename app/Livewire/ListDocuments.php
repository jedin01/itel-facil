<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class ListDocuments extends Component
{
    use WithPagination;

    public $search = '';
    public $type = ''; // 'exam' or 'material'
    public $subject_id = '';

    public function render()
    {
        $query = Document::query();

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->subject_id) {
            $query->where('subject_id', $this->subject_id);
        }

        $documents = $query->with(['subject.course', 'user'])->paginate(10);
        $subjects = Subject::all(); // For the filter dropdown

        return view('livewire.list-documents', [
            'documents' => $documents,
            'subjects' => $subjects,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingSubjectId()
    {
        $this->resetPage();
    }
}