<?php

namespace Modules\Orders\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'paid_amount',
        'remaining_amount',
        'coupon_code',
        'customer_notes',
        'admin_notes',
        'tracking_number',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Utilisateur propriétaire
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Articles de la commande
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Adresse de livraison
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(ShippingAddress::class);
    }

    /**
     * Paiements
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Générer un numéro de commande unique
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Calculer le montant restant à payer
     */
    public function calculateRemainingAmount(): float
    {
        return max(0, $this->total - $this->paid_amount);
    }

    /**
     * Vérifier si la commande est complètement payée
     */
    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total;
    }

    /**
     * Vérifier si la commande est partiellement payée
     */
    public function isPartiallyPaid(): bool
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total;
    }

    /**
     * Enregistrer un paiement
     */
    public function recordPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = $this->calculateRemainingAmount();
        
        if ($this->isFullyPaid()) {
            $this->payment_status = 'paid';
        } elseif ($this->isPartiallyPaid()) {
            $this->payment_status = 'partially_paid';
        }
        
        $this->save();
    }

    /**
     * Confirmer la commande
     */
    public function confirm(): void
    {
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();
    }

    /**
     * Marquer comme expédiée
     */
    public function markAsShipped(string $trackingNumber = null): void
    {
        $this->status = 'shipped';
        $this->shipped_at = now();
        
        if ($trackingNumber) {
            $this->tracking_number = $trackingNumber;
        }
        
        $this->save();
    }

    /**
     * Marquer comme livrée
     */
    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    /**
     * Annuler la commande
     */
    public function cancel(string $reason = null): void
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        
        if ($reason) {
            $this->admin_notes = $reason;
        }
        
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
