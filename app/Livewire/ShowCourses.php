<?php

namespace App\Livewire;

use Livewire\Component;

class ShowCourses extends Component
{
    protected static $layout = 'components.layouts.app';

    public function render()
    {
        $courses = \App\Models\Course::with('subjects')->get();

        return view('livewire.show-courses', [
            'courses' => $courses,
        ]);
    }
}
