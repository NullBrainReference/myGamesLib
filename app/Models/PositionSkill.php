<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PositionSkill extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(HirePosition::class, 'hire_position_skill');
    }
}
