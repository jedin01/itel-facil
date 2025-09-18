<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    /**
     * Get the subject that the document belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user that uploaded the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}