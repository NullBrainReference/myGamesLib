<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function game()
    // {
    //     return $this->belongsTo(Game::class, 'game_id', 'game_id');
    // }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function getObjectUrlAttribute(): string
    {
        return match (true) {
            $this->commentable instanceof \App\Models\Game => route('game.view', $this->commentable->game_id),
            $this->commentable instanceof \App\Models\Blog => route('blog.view', $this->commentable->id),
            default => '#',
        };
    }

    public function getObjectTitleAttribute(): string
    {
        return $this->commentable->title ?? 'Unknown';
    }


}
