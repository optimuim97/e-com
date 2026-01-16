<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_details',
        'gateway_response',
        'refunded_amount',
        'refunded_at',
        'refund_reason',
        'processed_at',
        'failed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'payment_details' => 'array',
        'refunded_at' => 'datetime',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Commande associée
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Marquer le paiement comme complété
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->processed_at = now();
        $this->save();

        // Mettre à jour le montant payé de la commande
        $this->order->recordPayment($this->amount);
    }

    /**
     * Marquer le paiement comme échoué
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->status = 'failed';
        $this->failed_at = now();
        
        if ($reason) {
            $this->gateway_response = $reason;
        }
        
        $this->save();
    }

    /**
     * Rembourser le paiement
     */
    public function refund(float $amount, string $reason = null): void
    {
        $this->refunded_amount += $amount;
        $this->refund_reason = $reason;
        $this->refunded_at = now();
        
        if ($this->refunded_amount >= $this->amount) {
            $this->status = 'refunded';
        }
        
        $this->save();

        // Mettre à jour le montant payé de la commande
        $this->order->paid_amount -= $amount;
        $this->order->remaining_amount = $this->order->calculateRemainingAmount();
        $this->order->payment_status = 'refunded';
        $this->order->save();
    }

    /**
     * Vérifier si le paiement est complété
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si le paiement est échoué
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Vérifier si le paiement est remboursé
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}
