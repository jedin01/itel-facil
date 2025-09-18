<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListHelpers extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $query = User::where('is_alumni', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        $helpers = $query->with('course')->paginate(10);

        return view('livewire.list-helpers', [
            'helpers' => $helpers,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}