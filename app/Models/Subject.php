<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /**
     * Get the course that the subject belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the documents for the subject.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the forum threads for the subject.
     */
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }
}
