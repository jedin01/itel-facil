<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicCalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'type',
        'course_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the course that the academic calendar event belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}