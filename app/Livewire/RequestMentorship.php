<?php

namespace App\Livewire;

use App\Models\MentorshipSession;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;

class RequestMentorship extends Component
{
    public $mentor_id;
    public $subject_id;
    public $scheduled_at;
    public $notes;

    protected $rules = [
        'mentor_id' => 'required|exists:users,id',
        'subject_id' => 'required|exists:subjects,id',
        'scheduled_at' => 'required|date|after_or_equal:now',
        'notes' => 'nullable|string|max:500',
    ];

    public function requestSession()
    {
        $this->validate();

        MentorshipSession::create([
            'mentor_id' => $this->mentor_id,
            'mentee_id' => auth()->id(),
            'subject_id' => $this->subject_id,
            'scheduled_at' => $this->scheduled_at,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Mentorship session requested successfully!');

        $this->reset(['mentor_id', 'subject_id', 'scheduled_at', 'notes']);
    }

    public function render()
    {
        $mentors = User::where('is_alumni', true)->get();
        $subjects = Subject::all();

        return view('livewire.request-mentorship', [
            'mentors' => $mentors,
            'subjects' => $subjects,
        ]);
    }
}