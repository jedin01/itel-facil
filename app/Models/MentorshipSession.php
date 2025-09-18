<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorshipSession extends Model
{
    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'subject_id',
        'scheduled_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the mentor for the mentorship session.
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the mentee for the mentorship session.
     */
    public function mentee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Get the subject for the mentorship session.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}