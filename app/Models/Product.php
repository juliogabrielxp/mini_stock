<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Retorna o status do estoque baseado na quantidade.
     * Usado para colorir o badge na vitrine e no painel.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity === 0) return 'danger';
        if ($this->quantity <= 5)  return 'warning';
        return 'success';
    }

    public function getStockLabelAttribute(): string
    {
        if ($this->quantity === 0) return 'Sem estoque';
        if ($this->quantity <= 5)  return 'Estoque baixo';
        return 'Em estoque';
    }
}
