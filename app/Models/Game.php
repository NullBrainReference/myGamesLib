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

    protected $fillable = [
        'title',
        'description',
        'img_src',
        'average_rating',
        'ratings_count'
    ]; // Fillable columns

    protected $casts = [
        'average_rating' => 'float',
        'ratings_count' => 'integer'
    ];

    public $incrementing = false; // If game_id is non-incrementing

    public function getAverageRatingAttribute($value)
    {
        if (!is_null($value) && $value !== 0) {
            return round($value, 1);
        }

        $avg = $this->ratings()->avg('rating');
        return round($avg ?? 0, 1);
    }

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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'game_id', 'game_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'game_tag', 'game_id', 'tag_id');
    }

    public function mechanics(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mechanic::class, 'game_mechanic', 'game_id', 'mechanic_id');
    }

}
