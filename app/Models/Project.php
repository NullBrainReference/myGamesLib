<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_public',
        'icon_big',
        'icon_small',
    ];

    /**
     * Owners of the project
     */
    public function owners()
    {
        return $this->belongsToMany(User::class, 'project_owners')
                    ->withTimestamps();
    }

    /**
     * Editors of the project
     */
    public function editors()
    {
        return $this->belongsToMany(User::class, 'project_editors')
                    ->withTimestamps();
    }

    /**
     * General participants of the project
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'project_participants')
                    ->withTimestamps();
    }

    public function mechanics(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mechanic::class, 'project_mechanic', 'project_id', 'mechanic_id');
    }
}
