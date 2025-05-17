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
}
