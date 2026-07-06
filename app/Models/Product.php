<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
        'image_path',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Executa a venda com segurança.
     * Valida estoque e desconta atomicamente.
     * Lança exceção se não houver estoque suficiente.
     */
    public function sell(int $quantity, int $userId): Sale
    {
        // Recarrega do banco com lock para evitar race condition
        $this->refresh();

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        if ($this->quantity < $quantity) {
            throw new \DomainException(
                "Estoque insuficiente. Disponível: {$this->quantity} unidade(s)."
            );
        }

        // Desconta o estoque
        $this->decrement('quantity', $quantity);

        // Registra a venda
        return Sale::create([
            'product_id'    => $this->id,
            'user_id'       => $userId,
            'quantity_sold' => $quantity,
            'unit_price'    => $this->price,
            'total'         => $this->price * $quantity,
        ]);
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        return Storage::url($this->image_path);
    }

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
