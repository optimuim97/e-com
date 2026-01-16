<?php

namespace Modules\Cart\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Cart\DTOs\AddToCartDTO;
use Modules\Cart\DTOs\ApplyCouponDTO;
use Modules\Cart\DTOs\UpdateCartItemDTO;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Products\Models\Product;

class CartService
{
    /**
     * Obtenir ou créer le panier de l'utilisateur
     */
    public function getOrCreateCart(): Cart
    {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();

        $cart = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'expires_at' => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    /**
     * Ajouter un produit au panier
     */
    public function addToCart(AddToCartDTO $dto): CartItem
    {
        $product = Product::findOrFail($dto->product_id);
        
        // Vérifier le stock
        if ($product->track_inventory && $product->quantity < $dto->quantity) {
            throw new \Exception("Stock insuffisant. Disponible: {$product->quantity}");
        }

        $cart = $this->getOrCreateCart();

        // Vérifier si le produit existe déjà dans le panier
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $dto->product_id)
            ->first();

        if ($cartItem) {
            // Mettre à jour la quantité
            $newQuantity = $cartItem->quantity + $dto->quantity;
            
            if ($product->track_inventory && $product->quantity < $newQuantity) {
                throw new \Exception("Stock insuffisant. Disponible: {$product->quantity}");
            }
            
            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            // Créer un nouvel article
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $dto->product_id,
                'quantity' => $dto->quantity,
                'price' => $product->price,
                'options' => $dto->options,
            ]);
        }

        return $cartItem->load('product');
    }

    /**
     * Mettre à jour la quantité d'un article
     */
    public function updateCartItem(UpdateCartItemDTO $dto): CartItem
    {
        $cartItem = CartItem::findOrFail($dto->cart_item_id);
        
        // Vérifier que l'article appartient au panier de l'utilisateur
        $this->validateCartOwnership($cartItem->cart);

        // Vérifier le stock
        $product = $cartItem->product;
        if ($product->track_inventory && $product->quantity < $dto->quantity) {
            throw new \Exception("Stock insuffisant. Disponible: {$product->quantity}");
        }

        $cartItem->update([
            'quantity' => $dto->quantity,
        ]);

        return $cartItem->load('product');
    }

    /**
     * Retirer un article du panier
     */
    public function removeFromCart(int $cartItemId): void
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // Vérifier que l'article appartient au panier de l'utilisateur
        $this->validateCartOwnership($cartItem->cart);

        $cartItem->delete();
    }

    /**
     * Vider le panier
     */
    public function clearCart(): void
    {
        $cart = $this->getOrCreateCart();
        $cart->clear();
    }

    /**
     * Obtenir le panier actuel avec ses articles
     */
    public function getCart(): Cart
    {
        return $this->getOrCreateCart()->load('items.product');
    }

    /**
     * Appliquer un code promo
     */
    public function applyCoupon(ApplyCouponDTO $dto): Cart
    {
        $cart = $this->getOrCreateCart();

        // TODO: Implémenter la logique de validation du coupon
        // Pour l'instant, on applique une réduction de 10%
        $discountPercentage = 0.10;
        $discount = $cart->subtotal * $discountPercentage;

        $cart->update([
            'coupon_code' => $dto->coupon_code,
            'discount' => $discount,
        ]);

        $cart->updateTotals();

        return $cart->load('items.product');
    }

    /**
     * Retirer le code promo
     */
    public function removeCoupon(): Cart
    {
        $cart = $this->getOrCreateCart();

        $cart->update([
            'coupon_code' => null,
            'discount' => 0,
        ]);

        $cart->updateTotals();

        return $cart->load('items.product');
    }

    /**
     * Calculer les frais de livraison
     */
    public function calculateShipping(string $postalCode = null): float
    {
        // TODO: Implémenter une vraie logique de calcul basée sur le code postal
        $cart = $this->getOrCreateCart();
        
        // Livraison gratuite au-dessus de 100€
        if ($cart->subtotal >= 100) {
            return 0;
        }

        // Sinon 5.99€
        return 5.99;
    }

    /**
     * Obtenir l'ID de session
     */
    private function getSessionId(): string
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', Str::uuid()->toString());
        }

        return session()->get('cart_session_id');
    }

    /**
     * Vérifier que le panier appartient à l'utilisateur actuel
     */
    private function validateCartOwnership(Cart $cart): void
    {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();

        if ($userId && $cart->user_id !== $userId) {
            throw new \Exception('Vous n\'êtes pas autorisé à modifier ce panier');
        }

        if (!$userId && $cart->session_id !== $sessionId) {
            throw new \Exception('Vous n\'êtes pas autorisé à modifier ce panier');
        }
    }

    /**
     * Fusionner les paniers lors de la connexion
     */
    public function mergeGuestCart(int $userId): void
    {
        $sessionId = $this->getSessionId();
        
        // Panier invité
        $guestCart = Cart::where('session_id', $sessionId)->first();
        
        if (!$guestCart || $guestCart->isEmpty()) {
            return;
        }

        // Panier utilisateur
        $userCart = Cart::where('user_id', $userId)->first();
        
        if (!$userCart) {
            // Transférer le panier invité à l'utilisateur
            $guestCart->update([
                'user_id' => $userId,
                'session_id' => null,
            ]);
        } else {
            // Fusionner les articles
            foreach ($guestCart->items as $guestItem) {
                $userItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($userItem) {
                    $userItem->update([
                        'quantity' => $userItem->quantity + $guestItem->quantity,
                    ]);
                } else {
                    $guestItem->update(['cart_id' => $userCart->id]);
                }
            }

            // Supprimer le panier invité
            $guestCart->delete();
            
            // Mettre à jour les totaux
            $userCart->updateTotals();
        }
    }
}
