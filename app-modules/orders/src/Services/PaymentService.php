<?php

namespace Modules\Orders\Services;

use Illuminate\Support\Facades\DB;
use Modules\Orders\DTOs\ProcessPaymentDTO;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\Payment;

class PaymentService
{
    /**
     * Traiter un paiement
     */
    public function processPayment(array $data): Payment
    {
        $order = Order::findOrFail($data['order_id']);
        $amount = $data['amount'];
        $paymentMethod = $data['payment_method'];

        // Vérifier que le montant ne dépasse pas le montant restant
        if ($amount > $order->remaining_amount) {
            throw new \Exception('Le montant du paiement dépasse le montant restant de la commande');
        }

        return DB::transaction(function () use ($order, $amount, $paymentMethod, $data) {
            // Créer l'enregistrement de paiement
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $this->generateTransactionId(),
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'currency' => 'EUR',
                'status' => 'pending',
                'payment_details' => $data['payment_details'] ?? null,
            ]);

            // Traiter selon la méthode de paiement
            switch ($paymentMethod) {
                case 'cash_on_delivery':
                    $this->processCashOnDelivery($payment);
                    break;
                    
                case 'online':
                case 'stripe':
                case 'paypal':
                    $this->processOnlinePayment($payment, $data);
                    break;
                    
                default:
                    throw new \Exception('Méthode de paiement non supportée');
            }

            return $payment->load('order');
        });
    }

    /**
     * Traiter un paiement à la livraison
     */
    private function processCashOnDelivery(Payment $payment): void
    {
        // Pour le paiement à la livraison, on marque comme en attente
        // Il sera confirmé manuellement lors de la livraison
        $payment->update([
            'status' => 'pending',
        ]);
    }

    /**
     * Traiter un paiement en ligne
     */
    private function processOnlinePayment(Payment $payment, array $data): void
    {
        try {
            // TODO: Intégrer avec Stripe, PayPal, etc.
            // Pour l'instant, simulation de paiement réussi
            
            $payment->markAsCompleted();
            
            // Si c'était un paiement partiel et que la commande est maintenant payée,
            // confirmer la commande
            if ($payment->order->isFullyPaid() && $payment->order->status === 'pending') {
                $payment->order->confirm();
            }
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirmer un paiement à la livraison
     */
    public function confirmCashOnDeliveryPayment(int $paymentId): Payment
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->payment_method !== 'cash_on_delivery') {
            throw new \Exception('Ce paiement n\'est pas un paiement à la livraison');
        }

        if ($payment->status !== 'pending') {
            throw new \Exception('Ce paiement a déjà été traité');
        }

        $payment->markAsCompleted();

        // Marquer la commande comme livrée si elle est expédiée
        if ($payment->order->status === 'shipped') {
            $payment->order->markAsDelivered();
        }

        return $payment->load('order');
    }

    /**
     * Rembourser un paiement
     */
    public function refundPayment(int $paymentId, float $amount, string $reason = null): Payment
    {
        $payment = Payment::findOrFail($paymentId);

        if (!$payment->isCompleted()) {
            throw new \Exception('Seuls les paiements complétés peuvent être remboursés');
        }

        $maxRefundAmount = $payment->amount - $payment->refunded_amount;
        
        if ($amount > $maxRefundAmount) {
            throw new \Exception("Le montant du remboursement ne peut pas dépasser {$maxRefundAmount}€");
        }

        return DB::transaction(function () use ($payment, $amount, $reason) {
            // TODO: Traiter le remboursement avec la gateway de paiement
            
            $payment->refund($amount, $reason);

            // Si toute la commande est remboursée, marquer comme remboursée
            if ($payment->order->paid_amount <= 0) {
                $payment->order->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded',
                ]);
            }

            return $payment->load('order');
        });
    }

    /**
     * Obtenir l'historique des paiements d'une commande
     */
    public function getOrderPayments(int $orderId): \Illuminate\Database\Eloquent\Collection
    {
        return Payment::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Vérifier le statut d'un paiement en ligne
     */
    public function checkPaymentStatus(string $transactionId): Payment
    {
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        // TODO: Vérifier le statut avec la gateway de paiement
        
        return $payment;
    }

    /**
     * Générer un ID de transaction unique
     */
    private function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Calculer le montant total payé pour une commande
     */
    public function getTotalPaidAmount(int $orderId): float
    {
        return Payment::where('order_id', $orderId)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Vérifier si une commande peut recevoir un paiement partiel
     */
    public function canAcceptPartialPayment(Order $order): bool
    {
        return $order->payment_method === 'partial' 
            && $order->remaining_amount > 0 
            && !in_array($order->status, ['cancelled', 'refunded']);
    }

    /**
     * Calculer le montant minimum pour un paiement partiel (30% du total)
     */
    public function getMinimumPartialPayment(Order $order): float
    {
        return $order->total * 0.30;
    }

    /**
     * Statistiques des paiements
     */
    public function getPaymentStatistics(): array
    {
        return [
            'total_payments' => Payment::where('status', 'completed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'refunded_amount' => Payment::where('status', 'refunded')->sum('refunded_amount'),
            'by_method' => [
                'cash_on_delivery' => Payment::where('payment_method', 'cash_on_delivery')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'online' => Payment::whereIn('payment_method', ['online', 'stripe', 'paypal'])
                    ->where('status', 'completed')
                    ->sum('amount'),
            ],
        ];
    }
}
