<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax_cost' => 'decimal:2',
        'manufacturing_cost' => 'decimal:2',
        'currency_id' => 'integer',
    ];

    protected $hidden = [
        'currency_id',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
