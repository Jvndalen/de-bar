<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'product_id',
        'total',
        'treat_balance_id',
        'type',
        'reverted_at',
    ];

    protected $casts = [
        'total' => 'integer',
        'reverted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function treatBalance(): BelongsTo
    {
        return $this->belongsTo(TreatBalance::class);
    }

    public function scopeNotReverted($query)
    {
        return $query->whereNull('reverted_at');
    }
}
