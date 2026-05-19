<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'tag_id';

    protected $fillable = [
        'title',
        'description',
        'is_r18'
    ];

    // Automatically cast the DB 0/1 to a true/false boolean in PHP
    protected $casts = [
        'is_r18' => 'boolean',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_tag', 'tag_id', 'game_id');
    }
}
