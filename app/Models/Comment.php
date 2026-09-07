<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function mechanic(): HasOne
    {
        return $this->hasOne(Mechanic::class, 'comment_id', 'id');
    }

    public function getScoreAttribute(): int
    {
        return $this->votes()->sum('vote');
    }

    public function getUserVoteAttribute(): ?int
    {
        if (!auth()->check()) return null;
        return $this->votes()->where('user_id', auth()->id())->value('vote');
    }


    public function commentable()
    {
        return $this->morphTo();
    }

    public function getObjectUrlAttribute(): string
    {
        return match (true) {
            $this->commentable instanceof \App\Models\Game => route('game.view', $this->commentable->game_id),
            $this->commentable instanceof \App\Models\Blog => route('blog.view', $this->commentable->id),
            $this->commentable instanceof \App\Models\Thread => route('forum.thread', $this->commentable->id),
            default => '#',
        };
    }

    public function getObjectTitleAttribute(): string
    {
        return $this->commentable->title ?? 'Unknown';
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get all nested replies for this comment.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->latest();
    }

}
