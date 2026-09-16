<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'user_id',
        'reason',
        'expired',
        'success',
    ];

    protected $casts = [
        'expired' => 'boolean',
        'success'  => 'boolean',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(HirePosition::class, 'position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
