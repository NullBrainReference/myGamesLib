<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'notifiable_type',
        'notifiable_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTargetUrlAttribute(): string
    {
        if (!$this->notifiable) {
            return '#';
        }

        return match ($this->type) {
            'project' => match (true) {
                $this->notifiable instanceof Project => route('projects.view', $this->notifiable->id),
                default => '#',
            },
            'friend_request' => match (true) {
                $this->notifiable instanceof User => route('profile.view', $this->notifiable->id),
                default => '#',
            },
            'message' => match (true) {
                $this->notifiable instanceof Message
                    => route('messages.show', $this->notifiable->sender_id),
                $this->notifiable instanceof User
                    => route('messages.show', $this->notifiable->id),
                default => '#',
            },
            'comment_reply' => match (true) {
                $this->notifiable instanceof Comment => $this->notifiable->object_url,
                default => '#',
            },
            'tickets_limit' => match (true) {
                $this->notifiable instanceof HirePosition
                    => route('projects.vacancies', $this->notifiable->project_id),
                $this->notifiable instanceof Project
                    => route('projects.vacancies', $this->notifiable->id),
                default => '#',
            },
            default => '#',
        };
    }

    public static function send(int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?Model $notifiable = null
        ): self
    {
        return static::create([
            'user_id'         => $userId,
            'type'            => $type,
            'title'           => $title,
            'message'         => $message,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'   => $notifiable ? $notifiable->getKey() : null,
        ]);
    }
}
