<?php

namespace Modules\Orders\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\DTOs\ProcessPaymentDTO;
use Modules\Orders\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Traiter un paiement
     */
    public function process(Request $request): JsonResponse
    {
        try {
            $dto = ProcessPaymentDTO::fromRequest($request);
            $payment = $this->paymentService->processPayment($dto);

            return response()->json([
                'success' => true,
                'message' => 'Paiement traité',
                'data' => $payment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Confirmer un paiement à la livraison (admin seulement)
     */
    public function confirm(int $paymentId): JsonResponse
    {
        try {
            $payment = $this->paymentService->confirmCashOnDeliveryPayment($paymentId);

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Rembourser un paiement (admin seulement)
     */
    public function refund(int $paymentId, Request $request): JsonResponse
    {
        try {
            $amount = $request->input('amount');
            $reason = $request->input('reason', 'Remboursement demandé');

            $payment = $this->paymentService->refundPayment($paymentId, $amount, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Remboursement effectué',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Liste des paiements d'une commande
     */
    public function orderPayments(int $orderId): JsonResponse
    {
        try {
            $totalPaid = $this->paymentService->getTotalPaidAmount($orderId);
            $order = \Modules\Orders\Models\Order::with('payments')->findOrFail($orderId);

            // Vérifier que l'utilisateur a accès à cette commande
            if (auth()->check() && $order->user_id !== auth()->id()) {
                throw new \Exception('Accès non autorisé');
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiements récupérés',
                'data' => [
                    'payments' => $order->payments,
                    'total_paid' => $totalPaid,
                    'remaining_amount' => $order->remaining_amount,
                    'payment_status' => $order->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Vérifier si un paiement partiel est possible
     */
    public function checkPartialPayment(int $orderId): JsonResponse
    {
        try {
            $order = \Modules\Orders\Models\Order::findOrFail($orderId);

            // Vérifier que l'utilisateur a accès à cette commande
            if (auth()->check() && $order->user_id !== auth()->id()) {
                throw new \Exception('Accès non autorisé');
            }

            $canAccept = $this->paymentService->canAcceptPartialPayment($order);
            $minimumAmount = $canAccept ? $this->paymentService->getMinimumPartialPayment($order) : null;

            return response()->json([
                'success' => true,
                'message' => 'Vérification effectuée',
                'data' => [
                    'can_accept_partial' => $canAccept,
                    'minimum_amount' => $minimumAmount,
                    'remaining_amount' => $order->remaining_amount,
                    'payment_method' => $order->payment_method,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Statistiques des paiements (admin seulement)
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->paymentService->getPaymentStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistiques récupérées',
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
