<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Cart\DTOs\AddToCartDTO;
use Modules\Cart\DTOs\ApplyCouponDTO;
use Modules\Cart\DTOs\UpdateCartItemDTO;
use Modules\Cart\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Obtenir le panier actuel
     */
    public function index(): JsonResponse
    {
        try {
            $cart = $this->cartService->getCart();

            return response()->json([
                'success' => true,
                'message' => 'Panier récupéré avec succès',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ajouter un produit au panier
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $dto = AddToCartDTO::fromRequest($request);
            $cartItem = $this->cartService->addToCart($dto);

            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'data' => $cartItem,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mettre à jour la quantité d'un article
     */
    public function update(Request $request, int $cartItemId): JsonResponse
    {
        try {
            $dto = UpdateCartItemDTO::fromArray([
                'cart_item_id' => $cartItemId,
                'quantity' => $request->input('quantity'),
            ]);

            $cartItem = $this->cartService->updateCartItem($dto);

            return response()->json([
                'success' => true,
                'message' => 'Quantité mise à jour',
                'data' => $cartItem,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Retirer un article du panier
     */
    public function destroy(int $cartItemId): JsonResponse
    {
        try {
            $this->cartService->removeFromCart($cartItemId);

            return response()->json([
                'success' => true,
                'message' => 'Article retiré du panier',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Vider le panier
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Panier vidé',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Appliquer un code promo
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        try {
            $dto = ApplyCouponDTO::fromRequest($request);
            $cart = $this->cartService->applyCoupon($dto);

            return response()->json([
                'success' => true,
                'message' => 'Code promo appliqué',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Retirer le code promo
     */
    public function removeCoupon(): JsonResponse
    {
        try {
            $cart = $this->cartService->removeCoupon();

            return response()->json([
                'success' => true,
                'message' => 'Code promo retiré',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculer les frais de livraison
     */
    public function calculateShipping(Request $request): JsonResponse
    {
        try {
            $postalCode = $request->input('postal_code');
            $shipping = $this->cartService->calculateShipping($postalCode);

            return response()->json([
                'success' => true,
                'message' => 'Frais de livraison calculés',
                'data' => [
                    'shipping_cost' => $shipping,
                    'free_shipping_threshold' => 100,
                    'remaining_for_free_shipping' => max(0, 100 - $this->cartService->getCart()->subtotal),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
