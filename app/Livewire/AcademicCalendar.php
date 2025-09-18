<?php

namespace App\Livewire;

use App\Models\AcademicCalendarEvent;
use App\Models\Course;
use Livewire\Component;

class AcademicCalendar extends Component
{
    public $month;
    public $year;
    public $type = '';
    public $course_id = '';

    public function mount()
    {
        $this->month = date('m');
        $this->year = date('Y');
    }

    public function render()
    {
        $query = AcademicCalendarEvent::query();

        if ($this->month && $this->year) {
            $query->whereYear('start_date', $this->year)
                  ->whereMonth('start_date', $this->month);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->course_id) {
            $query->where('course_id', $this->course_id);
        }

        $events = $query->with('course')->orderBy('start_date')->get()->groupBy(function($date) {
            return $date->start_date->format('Y-m-d');
        });

        $courses = Course::all(); // For the filter dropdown

        return view('livewire.academic-calendar', [
            'events' => $events,
            'courses' => $courses,
        ]);
    }

    public function previousMonth()
    {
        $date = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->format('m');
        $this->year = $date->format('Y');
    }

    public function nextMonth()
    {
        $date = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->format('m');
        $this->year = $date->format('Y');
    }
}