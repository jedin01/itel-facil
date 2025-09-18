<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'content',
    ];

    /**
     * Get the thread that the post belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class);
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}