<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Products\Models\Product;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'options' => 'array',
    ];

    /**
     * Panier parent
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Produit associé
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculer le sous-total
     */
    public function calculateSubtotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Mettre à jour le sous-total
     */
    public function updateSubtotal(): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->save();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Calculer automatiquement le sous-total avant sauvegarde
        static::saving(function ($item) {
            $item->subtotal = $item->calculateSubtotal();
        });

        // Mettre à jour les totaux du panier après modifications
        static::saved(function ($item) {
            $item->cart->updateTotals();
        });

        static::deleted(function ($item) {
            $item->cart->updateTotals();
        });
    }
}
