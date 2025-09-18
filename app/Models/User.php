<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the course that the user belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the documents for the user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the mentorship sessions where the user is the mentor.
     */
    public function mentoringSessions(): HasMany
    {
        return $this->hasMany(MentorshipSession::class, 'mentor_id');
    }

    /**
     * Get the mentorship sessions where the user is the mentee.
     */
    public function menteeSessions(): HasMany
    {
        return $this->hasMany(MentorshipSession::class, 'mentee_id');
    }

    /**
     * Check if the user is a mentor.
     */
    public function isMentor(): bool
    {
        return $this->is_alumni;
    }

    /**
     * Check if the user is a mentee.
     */
    public function isMentee(): bool
    {
        return true;
    }

    /**
     * Get the forum threads for the user.
     */
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    /**
     * Get the forum posts for the user.
     */
    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }
}