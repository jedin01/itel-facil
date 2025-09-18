<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /**
     * Get the subjects for the course.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the users for the course.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the academic calendar events for the course.
     */
    public function academicCalendarEvents(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }
}
