<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['user_id', 'voteable_type', 'voteable_id', 'vote'];

    public function voteable()
    {
        return $this->morphTo();
    }
}