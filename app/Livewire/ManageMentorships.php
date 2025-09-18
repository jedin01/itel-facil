<?php

namespace App\Livewire;

use App\Models\MentorshipSession;
use Livewire\Component;
use Livewire\WithPagination;

class ManageMentorships extends Component
{
    use WithPagination;

    public $filterStatus = '';

    public function render()
    {
        $user = auth()->user();
        $mentorships = collect();

        if ($user->isMentor()) {
            $query = $user->mentoringSessions()->with(['mentee', 'subject.course']);
            if ($this->filterStatus) {
                $query->where('status', $this->filterStatus);
            }
            $mentorships = $query->paginate(5, ['*'], 'mentorPage');
        }

        if ($user->isMentee()) {
            $query = $user->menteeSessions()->with(['mentor', 'subject.course']);
            if ($this->filterStatus) {
                $query->where('status', $this->filterStatus);
            }
            $mentorships = $mentorships->merge($query->paginate(5, ['*'], 'menteePage'));
        }

        return view('livewire.manage-mentorships', [
            'mentorships' => $mentorships,
        ]);
    }

    public function acceptSession(MentorshipSession $session)
    {
        if (auth()->id() === $session->mentor_id && $session->status === 'pending') {
            $session->update(['status' => 'accepted']);
            session()->flash('message', 'Sessão de mentoria aceita.');
        } else {
            session()->flash('error', 'Não autorizado ou status inválido.');
        }
    }

    public function rejectSession(MentorshipSession $session)
    {
        if (auth()->id() === $session->mentor_id && $session->status === 'pending') {
            $session->update(['status' => 'rejected']);
            session()->flash('message', 'Sessão de mentoria rejeitada.');
        } else {
            session()->flash('error', 'Não autorizado ou status inválido.');
        }
    }

    public function completeSession(MentorshipSession $session)
    {
        if ((auth()->id() === $session->mentor_id || auth()->id() === $session->mentee_id) && $session->status === 'accepted') {
            $session->update(['status' => 'completed']);
            session()->flash('message', 'Sessão de mentoria concluída.');
        } else {
            session()->flash('error', 'Não autorizado ou status inválido.');
        }
    }

    public function cancelSession(MentorshipSession $session)
    {
        if ((auth()->id() === $session->mentor_id || auth()->id() === $session->mentee_id) && ($session->status === 'pending' || $session->status === 'accepted')) {
            $session->update(['status' => 'canceled']);
            session()->flash('message', 'Sessão de mentoria cancelada.');
        } else {
            session()->flash('error', 'Não autorizado ou status inválido.');
        }
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
}