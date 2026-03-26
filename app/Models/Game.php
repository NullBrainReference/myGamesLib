<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;
    //
    protected $table = 'games'; // Explicitly define table name
    protected $primaryKey = 'game_id'; // Define custom primary key

    protected $fillable = ['title', 'description', 'img_src']; // Fillable columns

    public $incrementing = false; // If game_id is non-incrementing

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_user', 'game_id', 'user_id');
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'game_id', 'game_id');
    // }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'game_id', 'game_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1) ?? 0; // Rounded to 1 decimal
    }

}
