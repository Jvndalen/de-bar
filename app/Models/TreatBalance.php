<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatBalance extends Model
{
    protected $fillable = [
        'user_id',
        'initial_amount',
        'remaining_amount',
        'is_active',
    ];

    protected $casts = [
        'initial_amount' => 'integer',
        'remaining_amount' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('remaining_amount', '>', 0);
    }
}

