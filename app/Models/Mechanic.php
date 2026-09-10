<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    use HasFactory;

    protected $primaryKey = 'mechanic_id';

    protected $fillable = [
        'title',
        'content',
        'approved',
        'user_id',
        'comment_id',
        'game_id',
        'project_id',
        'parent_id',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    // --- Context Relationships ---

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Variant Relationships ---

    /**
     * Get the original canonical mechanic if this is a variant.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class, 'parent_id', 'mechanic_id');
    }

    /**
     * Get all pending or historical variants proposed by users.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Mechanic::class, 'parent_id', 'mechanic_id');
    }

    public function pendingVariants(): HasMany
    {
        return $this->variants()->where('approved', false);
    }

    // --- Scope Helpers & Status ---

    public function isGeneric(): bool
    {
        return is_null($this->game_id) && is_null($this->project_id);
    }

    public function isVariant(): bool
    {
        return !is_null($this->parent_id);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopeCanonical(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
