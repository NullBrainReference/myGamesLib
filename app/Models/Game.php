<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    //
    protected $table = 'games'; // Explicitly define table name
    protected $primaryKey = 'game_id'; // Define custom primary key

    protected $fillable = ['title', 'description', 'img_src']; // Fillable columns

    public $incrementing = false; // If game_id is non-incrementing
}
