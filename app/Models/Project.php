<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_public',
        'is_approved',
        'icon_big',
        'icon_small',
        'comment_id',
        'parent_project_id',
    ];

    protected $casts = [
        'is_public'   => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Parent original project (if this project is a fork)
     */
    public function parentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }

    /**
     * Child projects forked from this project
     */
    public function forks(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_project_id');
    }

    /**
     * Owners of the project
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_owners')
                    ->withTimestamps();
    }

    /**
     * Editors of the project
     */
    public function editors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_editors')
                    ->withTimestamps();
    }

    /**
     * General participants of the project
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_participants')
                    ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function mechanics(): HasMany
    {
        return $this->hasMany(Mechanic::class, 'project_id');
    }

    public function hirePositions(): HasMany
    {
        return $this->hasMany(HirePosition::class);
    }

    /**
     * Get all applicant tickets submitted across all positions in this project.
     */
    public function positionTickets(): HasManyThrough
    {
        return $this->hasManyThrough(
            PositionTicket::class,
            HirePosition::class,
            'project_id',
            'position_id',
            'id',
            'id'
        );
    }

    /**
     * Get all ticket slot extension requests for this project's positions.
     */
    public function ticketRequests(): HasManyThrough
    {
        return $this->hasManyThrough(
            HirePosTicketRequest::class,
            HirePosition::class,
            'project_id',
            'hire_position_id',
            'id',
            'id'
        );
    }

    public function approveRequests(): HasMany
    {
        return $this->hasMany(ProjectApproveRequest::class);
    }

    public function pendingApproveRequest(): HasOne
    {
        return $this->hasOne(ProjectApproveRequest::class)->where('status', 'pending');
    }

    public function hasPendingApproveRequest(): bool
    {
        return $this->pendingApproveRequest()->exists();
    }
}
