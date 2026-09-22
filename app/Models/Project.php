<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
            'project_id',  // Foreign key on hire_positions table
            'position_id', // Foreign key on position_tickets table
            'id',          // Local key on projects table
            'id'           // Local key on hire_positions table
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
            'project_id',       // Foreign key on hire_positions table
            'hire_position_id', // Foreign key on hire_pos_ticket_requests table
            'id',               // Local key on projects table
            'id'                // Local key on hire_positions table
        );
    }
}
