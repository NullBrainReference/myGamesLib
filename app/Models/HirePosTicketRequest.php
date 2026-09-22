<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HirePosTicketRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hire_position_id',
        'quantity',
        'reason',
        'status',
        'granted_quantity',
    ];

    public function hirePosition(): BelongsTo
    {
        return $this->belongsTo(HirePosition::class, 'hire_position_id');
    }
}
