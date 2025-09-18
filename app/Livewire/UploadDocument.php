<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class UploadDocument extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $type = 'material'; // Default to material
    public $subject_id;
    public $file;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['exam', 'material'])],
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240', // Max 10MB
        ];
    }

    public function store()
    {
        $this->validate();

        $filePath = $this->file->store('documents', 'public');

        Document::create([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'subject_id' => $this->subject_id,
            'file_path' => $filePath,
            'user_id' => auth()->id(), // Assign the authenticated user as uploader
        ]);

        session()->flash('message', 'Document uploaded successfully.');

        $this->reset(['title', 'description', 'type', 'subject_id', 'file']);
    }

    public function render()
    {
        $subjects = Subject::all();
        return view('livewire.upload-document', [
            'subjects' => $subjects,
        ]);
    }
}