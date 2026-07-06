<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity_sold',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'unit_price'    => 'decimal:2',
        'total'         => 'decimal:2',
        'quantity_sold' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
