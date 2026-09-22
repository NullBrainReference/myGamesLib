<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HirePosition extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'tickets_amount', // Total ticket quota granted by admins/default
        'is_open',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(PositionSkill::class, 'hire_position_skill');
    }

    // Submitted applications (spent tickets)
    public function tickets(): HasMany
    {
        return $this->hasMany(PositionTicket::class, 'position_id');
    }

    // Admin requests for more ticket slots
    public function ticketRequests(): HasMany
    {
        return $this->hasMany(HirePosTicketRequest::class, 'hire_position_id');
    }

    /**
     * Get total count of spent application tickets.
     */
    public function getUsedTicketsAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get remaining available application tickets.
     */
    public function getRemainingTicketsAttribute(): int
    {
        return max(0, $this->tickets_amount - $this->used_tickets);
    }

    /**
     * Check if applicants can still submit tickets.
     */
    public function hasAvailableTickets(): bool
    {
        return $this->is_open && $this->remaining_tickets > 0;
    }
}
