<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'subject_id',
    ];

    /**
     * Get the user that owns the forum thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject that the forum thread belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the posts for the forum thread.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }
}