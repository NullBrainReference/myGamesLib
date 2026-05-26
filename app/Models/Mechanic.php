<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mechanic extends Model
{
    use HasFactory;

    protected $primaryKey = 'mechanic_id';

    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * Get the games associated with this mechanic.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_mechanic', 'mechanic_id', 'game_id');
    }
}
