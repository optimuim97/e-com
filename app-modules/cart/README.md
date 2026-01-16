# Module Cart

Module de gestion du panier d'achat pour l'application e-commerce.

## Fonctionnalités

- ✅ Panier pour utilisateurs invités (session-based)
- ✅ Panier pour utilisateurs authentifiés
- ✅ Fusion automatique du panier invité lors de la connexion
- ✅ Gestion des quantités et des options de produits
- ✅ Application de codes promo
- ✅ Calcul automatique des frais de livraison
- ✅ Calcul de la TVA
- ✅ Expiration automatique des paniers invités (7 jours)

## Installation

1. Les migrations sont chargées automatiquement via le ServiceProvider
2. Exécuter les migrations :
```bash
php artisan migrate
```

## Utilisation

### API Endpoints

#### 1. Obtenir le panier actuel
```http
GET /api/cart
```

**Réponse :**
```json
{
  "success": true,
  "message": "Panier récupéré avec succès",
  "data": {
    "id": 1,
    "user_id": 1,
    "session_id": null,
    "subtotal": "99.98",
    "tax": "19.99",
    "shipping": "5.99",
    "discount": "0.00",
    "total": "125.96",
    "coupon_code": null,
    "expires_at": null,
    "items": [
      {
        "id": 1,
        "product_id": 5,
        "quantity": 2,
        "price": "49.99",
        "subtotal": "99.98",
        "options": {"color": "red", "size": "M"},
        "product": {
          "id": 5,
          "name": "T-Shirt",
          "slug": "t-shirt"
        }
      }
    ]
  }
}
```

#### 2. Ajouter un produit au panier
```http
POST /api/cart/add
Content-Type: application/json

{
  "product_id": 5,
  "quantity": 2,
  "options": {
    "color": "red",
    "size": "M"
  }
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Produit ajouté au panier",
  "data": {
    "id": 1,
    "cart_id": 1,
    "product_id": 5,
    "quantity": 2,
    "price": "49.99",
    "subtotal": "99.98",
    "options": {"color": "red", "size": "M"}
  }
}
```

#### 3. Mettre à jour la quantité
```http
PUT /api/cart/items/{cartItemId}
Content-Type: application/json

{
  "quantity": 3
}
```

#### 4. Retirer un article
```http
DELETE /api/cart/items/{cartItemId}
```

#### 5. Vider le panier
```http
DELETE /api/cart/clear
```

#### 6. Appliquer un code promo
```http
POST /api/cart/coupon
Content-Type: application/json

{
  "coupon_code": "PROMO10"
}
```

**Note :** Actuellement applique une réduction de 10%. À intégrer avec un système de coupons complet.

#### 7. Retirer le code promo
```http
DELETE /api/cart/coupon
```

#### 8. Calculer les frais de livraison
```http
POST /api/cart/shipping/calculate
Content-Type: application/json

{
  "postal_code": "75001"
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Frais de livraison calculés",
  "data": {
    "shipping_cost": "5.99",
    "free_shipping_threshold": 100,
    "remaining_for_free_shipping": "0.02"
  }
}
```

**Règles de livraison :**
- Gratuit si le sous-total >= 100€
- Sinon 5.99€

## Architecture

### Tables de base de données

#### `carts`
- `id` - Clé primaire
- `user_id` - ID utilisateur (nullable pour invités)
- `session_id` - ID de session (nullable, pour invités)
- `subtotal` - Sous-total des articles
- `tax` - TVA (20%)
- `shipping` - Frais de livraison
- `discount` - Réduction appliquée
- `total` - Total général
- `coupon_code` - Code promo appliqué
- `expires_at` - Date d'expiration (7 jours pour invités)
- `timestamps`

#### `cart_items`
- `id` - Clé primaire
- `cart_id` - Référence au panier
- `product_id` - Référence au produit
- `quantity` - Quantité
- `price` - Prix unitaire au moment de l'ajout
- `subtotal` - Sous-total (price * quantity)
- `options` - Options du produit (JSON: couleur, taille, etc.)
- `timestamps`

### Services

#### CartService

**Méthodes principales :**

```php
// Obtenir ou créer un panier
public function getOrCreateCart(): Cart

// Ajouter au panier
public function addToCart(AddToCartDTO $data): CartItem

// Mettre à jour un article
public function updateCartItem(UpdateCartItemDTO $data): CartItem

// Retirer un article
public function removeFromCart(int $cartItemId): void

// Vider le panier
public function clearCart(): void

// Obtenir le panier
public function getCart(): Cart

// Appliquer un coupon
public function applyCoupon(ApplyCouponDTO $data): Cart

// Retirer le coupon
public function removeCoupon(): Cart

// Calculer les frais de livraison
public function calculateShipping(?string $postalCode = null): float

// Fusionner le panier invité (lors de la connexion)
public function mergeGuestCart(int $userId): void
```

## Comportements spéciaux

### Panier invité
- Utilise un `session_id` UUID unique
- Expire après 7 jours d'inactivité
- Se fusionne automatiquement au panier utilisateur lors de la connexion

### Fusion des paniers
Lors de la connexion d'un utilisateur :
1. Le système récupère le panier invité (basé sur la session)
2. Le système récupère ou crée le panier utilisateur
3. Les articles du panier invité sont ajoutés au panier utilisateur
4. Si un article existe déjà, les quantités sont cumulées
5. Le panier invité est supprimé
6. Les totaux sont recalculés

### Validation de stock
- Vérifie la disponibilité avant d'ajouter au panier
- Vérifie le stock lors de la mise à jour des quantités
- Empêche d'ajouter plus que la quantité disponible

### Calcul automatique des totaux
- Les totaux sont recalculés automatiquement à chaque modification
- Le `subtotal` est calculé en additionnant tous les articles
- La `tax` est fixée à 20% du subtotal
- Le `shipping` est calculé selon les règles définies
- Le `total` = subtotal + tax + shipping - discount

## Intégration

### Fusion lors de la connexion

Dans votre AuthController ou middleware, appelez :

```php
use Modules\Cart\Services\CartService;

public function login(Request $request)
{
    // ... votre logique d'authentification ...
    
    // Fusionner les paniers
    app(CartService::class)->mergeGuestCart(auth()->id());
    
    // ...
}
```

### Dans vos vues/composants

```javascript
// Récupérer le panier
const cart = await fetch('/api/cart').then(r => r.json())

// Ajouter au panier
const response = await fetch('/api/cart/add', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    product_id: 5,
    quantity: 1,
    options: { size: 'M' }
  })
})
```

## TODO / Améliorations futures

- [ ] Intégrer un système complet de coupons (validation, types, limites)
- [ ] Ajouter des règles de livraison plus complexes (zones, poids)
- [ ] Implémenter des promotions automatiques
- [ ] Ajouter la gestion des produits en rupture de stock
- [ ] Notifier l'utilisateur quand un produit du panier change de prix
- [ ] Sauvegarder l'historique des paniers abandonnés
