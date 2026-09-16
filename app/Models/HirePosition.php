<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HirePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'tickets_amount',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'tickets_amount' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(PositionSkill::class, 'hire_position_skill');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(PositionTicket::class, 'position_id');
    }

    // Helper: Check if position has remaining open slots
    public function acceptedTicketsCount(): int
    {
        return $this->tickets()->where('success', true)->count();
    }

    public function isFilled(): bool
    {
        return $this->acceptedTicketsCount() >= $this->tickets_amount;
    }
}
