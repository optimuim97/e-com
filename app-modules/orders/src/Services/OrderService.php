<?php

namespace Modules\Orders\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Models\Cart;
use Modules\Cart\Services\CartService;
use Modules\Orders\DTOs\CreateOrderDTO;
use Modules\Orders\DTOs\ProcessPaymentDTO;
use Modules\Orders\DTOs\UpdateOrderStatusDTO;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Models\Payment;
use Modules\Orders\Models\ShippingAddress;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private PaymentService $paymentService
    ) {}

    /**
     * Créer une commande depuis le panier
     */
    public function createOrderFromCart(CreateOrderDTO $dto): Order
    {
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            throw new \Exception('Le panier est vide');
        }

        return DB::transaction(function () use ($cart, $dto) {
            // Calculer les frais de livraison
            $shippingCost = $this->cartService->calculateShipping($dto->shipping_address['postal_code'] ?? null);
            $cart->update(['shipping' => $shippingCost]);
            $cart->updateTotals();

            // Créer la commande
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'subtotal' => $cart->subtotal,
                'tax' => $cart->tax,
                'shipping_cost' => $cart->shipping,
                'discount' => $cart->discount,
                'total' => $cart->total,
                'payment_method' => $dto->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'paid_amount' => 0,
                'remaining_amount' => $cart->total,
                'coupon_code' => $cart->coupon_code,
                'customer_notes' => $dto->customer_notes,
            ]);

            // Créer les articles de commande
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->subtotal,
                    'options' => $cartItem->options,
                ]);

                // Déduire du stock
                if ($cartItem->product->track_inventory) {
                    $cartItem->product->decrement('quantity', $cartItem->quantity);
                    
                    // Mettre à jour le statut du stock
                    if ($cartItem->product->quantity <= 0) {
                        $cartItem->product->update(['stock_status' => 'out_of_stock']);
                    } elseif ($cartItem->product->quantity <= $cartItem->product->low_stock_threshold) {
                        $cartItem->product->update(['stock_status' => 'low_stock']);
                    }
                }
            }

            // Créer l'adresse de livraison
            ShippingAddress::create(array_merge(
                ['order_id' => $order->id],
                $dto->shipping_address
            ));

            // Traiter le paiement initial si nécessaire
            if ($dto->payment_method === 'online' || $dto->payment_method === 'partial') {
                $paymentAmount = $dto->initial_payment_amount ?? $order->total;
                
                if ($dto->payment_method === 'partial' && !$dto->initial_payment_amount) {
                    throw new \Exception('Le montant du paiement initial est requis pour un paiement partiel');
                }

                $this->paymentService->processPayment([
                    'order_id' => $order->id,
                    'amount' => $paymentAmount,
                    'payment_method' => 'online',
                ]);
            }

            // Vider le panier
            $cart->clear();

            return $order->load(['items.product', 'shippingAddress', 'payments']);
        });
    }

    /**
     * Obtenir les commandes de l'utilisateur
     */
    public function getUserOrders(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['items.product', 'shippingAddress', 'payments'])
            ->latest();

        // Filtrer par statut
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtrer par statut de paiement
        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Filtrer par plage de dates
        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Obtenir une commande par son ID
     */
    public function getOrderById(int $orderId): Order
    {
        $order = Order::with(['items.product', 'shippingAddress', 'payments'])
            ->findOrFail($orderId);

        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin ?? false) {
            throw new \Exception('Vous n\'êtes pas autorisé à consulter cette commande');
        }

        return $order;
    }

    /**
     * Obtenir une commande par son numéro
     */
    public function getOrderByNumber(string $orderNumber): Order
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'shippingAddress', 'payments'])
            ->firstOrFail();

        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin ?? false) {
            throw new \Exception('Vous n\'êtes pas autorisé à consulter cette commande');
        }

        return $order;
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateOrderStatus(UpdateOrderStatusDTO $dto): Order
    {
        $order = Order::findOrFail($dto->order_id);

        switch ($dto->status) {
            case 'confirmed':
                $order->confirm();
                break;
            case 'shipped':
                $order->markAsShipped($dto->tracking_number);
                break;
            case 'delivered':
                $order->markAsDelivered();
                break;
            case 'cancelled':
                $order->cancel($dto->admin_notes);
                $this->restoreStock($order);
                break;
            default:
                $order->update(['status' => $dto->status]);
                break;
        }

        if ($dto->admin_notes) {
            $order->update(['admin_notes' => $dto->admin_notes]);
        }

        return $order->load(['items.product', 'shippingAddress', 'payments']);
    }

    /**
     * Annuler une commande
     */
    public function cancelOrder(int $orderId, string $reason = null): Order
    {
        $order = Order::findOrFail($orderId);

        // Vérifier que la commande peut être annulée
        if (in_array($order->status, ['delivered', 'cancelled', 'refunded'])) {
            throw new \Exception('Cette commande ne peut pas être annulée');
        }

        // Vérifier que l'utilisateur est autorisé
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin ?? false) {
            throw new \Exception('Vous n\'êtes pas autorisé à annuler cette commande');
        }

        $order->cancel($reason);
        $this->restoreStock($order);

        return $order->load(['items.product', 'shippingAddress', 'payments']);
    }

    /**
     * Restaurer le stock après annulation
     */
    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product && $item->product->track_inventory) {
                $item->product->increment('quantity', $item->quantity);
                
                // Mettre à jour le statut du stock
                if ($item->product->quantity > $item->product->low_stock_threshold) {
                    $item->product->update(['stock_status' => 'in_stock']);
                }
            }
        }
    }

    /**
     * Obtenir toutes les commandes (Admin)
     */
    public function getAllOrders(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Order::with(['user', 'items.product', 'shippingAddress'])
            ->latest();

        // Filtrer par statut
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtrer par statut de paiement
        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Recherche
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%")
                  ->orWhereHas('user', function ($q2) use ($filters) {
                      $q2->where('name', 'like', "%{$filters['search']}%")
                         ->orWhere('email', 'like', "%{$filters['search']}%");
                  });
            });
        }

        $perPage = $filters['per_page'] ?? 20;

        return $query->paginate($perPage);
    }

    /**
     * Statistiques des commandes
     */
    public function getOrderStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pending_payments' => Order::where('payment_status', 'pending')->sum('remaining_amount'),
        ];
    }
}
