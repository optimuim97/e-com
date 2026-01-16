<?php

namespace Modules\Cart\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'coupon_code',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Utilisateur propriétaire du panier
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Articles du panier
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculer le sous-total
     */
    public function calculateSubtotal(): float
    {
        return $this->items->sum('subtotal');
    }

    /**
     * Calculer la taxe (TVA 20%)
     */
    public function calculateTax(float $taxRate = 0.20): float
    {
        return $this->calculateSubtotal() * $taxRate;
    }

    /**
     * Calculer le total
     */
    public function calculateTotal(): float
    {
        return $this->calculateSubtotal() + $this->tax + $this->shipping - $this->discount;
    }

    /**
     * Mettre à jour tous les montants
     */
    public function updateTotals(float $taxRate = 0.20, float $shippingCost = 0): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->tax = $this->calculateTax($taxRate);
        $this->shipping = $shippingCost;
        $this->total = $this->calculateTotal();
        $this->save();
    }

    /**
     * Vérifier si le panier est vide
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Vider le panier
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->update([
            'subtotal' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => 0,
            'coupon_code' => null,
        ]);
    }

    /**
     * Nombre total d'articles
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Vérifier si le panier a expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
