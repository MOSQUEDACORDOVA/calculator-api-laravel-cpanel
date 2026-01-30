<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'exchange_rate',
    ];

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
