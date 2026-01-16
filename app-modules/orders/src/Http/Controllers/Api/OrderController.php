<?php

namespace Modules\Orders\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\DTOs\CreateOrderDTO;
use Modules\Orders\DTOs\UpdateOrderStatusDTO;
use Modules\Orders\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Créer une commande depuis le panier (checkout)
     */
    public function checkout(Request $request): JsonResponse
    {
        try {
            $dto = CreateOrderDTO::fromRequest($request);
            $order = $this->orderService->createOrderFromCart($dto);

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Liste des commandes de l'utilisateur connecté
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'payment_status' => $request->input('payment_status'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ];

            $orders = $this->orderService->getUserOrders($filters);

            return response()->json([
                'success' => true,
                'message' => 'Commandes récupérées',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Détails d'une commande par ID
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Commande récupérée',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Détails d'une commande par numéro
     */
    public function showByNumber(string $orderNumber): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderByNumber($orderNumber);

            return response()->json([
                'success' => true,
                'message' => 'Commande récupérée',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Annuler une commande
     */
    public function cancel(int $orderId, Request $request): JsonResponse
    {
        try {
            $reason = $request->input('reason', 'Annulation par le client');
            $order = $this->orderService->cancelOrder($orderId, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Commande annulée',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mettre à jour le statut (admin seulement)
     */
    public function updateStatus(Request $request, int $orderId): JsonResponse
    {
        try {
            $dto = UpdateOrderStatusDTO::fromArray([
                'order_id' => $orderId,
                'status' => $request->input('status'),
                'tracking_number' => $request->input('tracking_number'),
                'admin_notes' => $request->input('admin_notes'),
            ]);

            $order = $this->orderService->updateOrderStatus($dto);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Liste toutes les commandes (admin)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'payment_status' => $request->input('payment_status'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
                'search' => $request->input('search'),
            ];

            $orders = $this->orderService->getAllOrders($filters);

            return response()->json([
                'success' => true,
                'message' => 'Commandes récupérées',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Statistiques des commandes (admin)
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->orderService->getOrderStatistics();

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
