<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand',
        'name',
        'price',
        'quantity',
        'label',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->inStock();
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function getPriceInCents(): int
    {
        return (int) ($this->price * 100);
    }
}
