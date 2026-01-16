# Module Orders

Module de gestion des commandes et paiements pour l'application e-commerce.

## Fonctionnalités

- ✅ Création de commandes depuis le panier
- ✅ Trois modes de paiement :
  - **Paiement à la livraison (COD)** - Paiement en espèces lors de la réception
  - **Paiement en ligne** - Stripe, PayPal, etc.
  - **Paiement partiel** - Payer un acompte (minimum 30%) puis le reste
- ✅ Gestion des adresses de livraison
- ✅ Suivi des statuts de commande
- ✅ Gestion des paiements multiples
- ✅ Système de remboursement (partiel ou total)
- ✅ Historique des transactions
- ✅ Gestion automatique du stock
- ✅ Statistiques et rapports

## Installation

1. Les migrations sont chargées automatiquement
2. Exécuter les migrations :
```bash
php artisan migrate
```

## Utilisation

### API Endpoints - Commandes

#### 1. Créer une commande (Checkout)
```http
POST /api/orders/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_method": "online",
  "shipping_address": {
    "full_name": "Jean Dupont",
    "email": "jean@example.com",
    "phone": "0612345678",
    "company": "Ma Société",
    "address_line1": "123 Rue de la Paix",
    "address_line2": "Apt 4B",
    "city": "Paris",
    "state": "Île-de-France",
    "postal_code": "75001",
    "country": "FR",
    "delivery_instructions": "Sonner deux fois",
    "address_type": "home"
  },
  "initial_payment_amount": 150.00,
  "customer_notes": "Livraison urgente SVP"
}
```

**payment_method** : `cash_on_delivery`, `online`, ou `partial`
- Pour `partial`, `initial_payment_amount` est **requis** (minimum 30% du total)
- Pour `online`, le paiement est traité immédiatement
- Pour `cash_on_delivery`, le paiement sera confirmé à la livraison

**Réponse :**
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "data": {
    "id": 1,
    "order_number": "ORD-20260116-A1B2C3",
    "user_id": 1,
    "status": "pending",
    "payment_status": "paid",
    "payment_method": "online",
    "subtotal": "99.98",
    "tax": "19.99",
    "shipping_cost": "5.99",
    "discount": "0.00",
    "total": "125.96",
    "paid_amount": "125.96",
    "remaining_amount": "0.00",
    "items": [...],
    "shipping_address": {...},
    "payments": [...]
  }
}
```

#### 2. Liste des commandes utilisateur
```http
GET /api/orders?status=confirmed&payment_status=paid&from_date=2026-01-01
Authorization: Bearer {token}
```

**Filtres disponibles :**
- `status` : pending, confirmed, processing, shipped, delivered, cancelled, refunded
- `payment_status` : pending, paid, partially_paid, failed, refunded
- `from_date` : format YYYY-MM-DD
- `to_date` : format YYYY-MM-DD

#### 3. Détails d'une commande (par ID)
```http
GET /api/orders/{orderId}
Authorization: Bearer {token}
```

#### 4. Détails d'une commande (par numéro)
```http
GET /api/orders/number/{orderNumber}
Authorization: Bearer {token}
```

#### 5. Annuler une commande
```http
POST /api/orders/{orderId}/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Changement d'avis"
}
```

**Note :** Le stock des produits est automatiquement restauré.

#### 6. Mettre à jour le statut (Admin)
```http
PUT /api/orders/{orderId}/status
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "status": "shipped",
  "tracking_number": "TRACK123456789",
  "admin_notes": "Expédié via Colissimo"
}
```

#### 7. Liste toutes les commandes (Admin)
```http
GET /api/orders/admin/all?search=ORD-20260116&status=pending
Authorization: Bearer {admin_token}
```

#### 8. Statistiques des commandes (Admin)
```http
GET /api/orders/admin/statistics
Authorization: Bearer {admin_token}
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "pending": 5,
    "confirmed": 12,
    "shipped": 8,
    "delivered": 45,
    "cancelled": 3,
    "total_revenue": "15678.90",
    "pending_payments": "2345.67"
  }
}
```

### API Endpoints - Paiements

#### 1. Traiter un paiement
```http
POST /api/payments
Authorization: Bearer {token}
Content-Type: application/json

{
  "order_id": 1,
  "payment_method": "stripe",
  "amount": 50.00,
  "payment_details": {
    "card_last4": "4242",
    "card_brand": "visa"
  }
}
```

**payment_method** : `cash_on_delivery`, `stripe`, `paypal`, `bank_transfer`

#### 2. Confirmer un paiement COD (Admin)
```http
POST /api/payments/{paymentId}/confirm
Authorization: Bearer {admin_token}
```

Confirme un paiement à la livraison. Si la commande est déjà expédiée, elle passe automatiquement en "delivered".

#### 3. Rembourser un paiement (Admin)
```http
POST /api/payments/{paymentId}/refund
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "amount": 25.00,
  "reason": "Produit défectueux"
}
```

**Note :** 
- Le montant peut être partiel ou total
- Met à jour automatiquement le `paid_amount` et `remaining_amount` de la commande
- Si le montant payé tombe à 0, le statut passe à "refunded"

#### 4. Liste des paiements d'une commande
```http
GET /api/payments/orders/{orderId}
Authorization: Bearer {token}
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "payments": [
      {
        "id": 1,
        "transaction_id": "TXN-6791a2b3c4d5e-1737072051",
        "payment_method": "stripe",
        "amount": "100.00",
        "status": "completed",
        "refunded_amount": "0.00",
        "processed_at": "2026-01-16 10:30:00"
      }
    ],
    "total_paid": "100.00",
    "remaining_amount": "25.96",
    "payment_status": "partially_paid"
  }
}
```

#### 5. Vérifier la possibilité de paiement partiel
```http
GET /api/payments/orders/{orderId}/check-partial
Authorization: Bearer {token}
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "can_accept_partial": true,
    "minimum_amount": "37.79",
    "remaining_amount": "125.96",
    "payment_method": "partial"
  }
}
```

#### 6. Statistiques des paiements (Admin)
```http
GET /api/payments/admin/statistics
Authorization: Bearer {admin_token}
```

## Workflows de paiement

### 1. Paiement à la livraison (COD)

```
Client
  ↓ Checkout avec payment_method = "cash_on_delivery"
Commande créée (status: pending, payment_status: pending)
  ↓ Admin confirme l'expédition
Commande expédiée (status: shipped)
  ↓ Livreur reçoit le paiement
Admin confirme le paiement COD
  ↓
Commande livrée (status: delivered, payment_status: paid)
```

**API Calls :**
```bash
# 1. Client crée la commande
POST /api/orders/checkout {"payment_method": "cash_on_delivery", ...}

# 2. Admin expédie
PUT /api/orders/{id}/status {"status": "shipped", "tracking_number": "..."}

# 3. Admin confirme le paiement
POST /api/payments/{paymentId}/confirm
```

### 2. Paiement en ligne (Online)

```
Client
  ↓ Checkout avec payment_method = "online"
Paiement traité immédiatement
  ↓ Si succès
Commande confirmée (status: confirmed, payment_status: paid)
  ↓ Admin expédie
Commande expédiée (status: shipped)
  ↓ Livraison effectuée
Commande livrée (status: delivered)
```

**API Calls :**
```bash
# 1. Client crée la commande (paiement automatique)
POST /api/orders/checkout {"payment_method": "online", ...}

# 2. Admin expédie
PUT /api/orders/{id}/status {"status": "shipped", "tracking_number": "..."}

# 3. Admin marque comme livrée
PUT /api/orders/{id}/status {"status": "delivered"}
```

### 3. Paiement partiel (Partial)

```
Client
  ↓ Checkout avec payment_method = "partial" + initial_payment (min 30%)
Premier paiement traité
  ↓
Commande créée (payment_status: partially_paid, remaining > 0)
  ↓ Client effectue des paiements supplémentaires
Nouveau paiement traité
  ↓ Quand paid_amount = total
Commande complètement payée (payment_status: paid)
  ↓ Admin expédie
Commande livrée
```

**API Calls :**
```bash
# 1. Client crée la commande avec acompte (minimum 30%)
POST /api/orders/checkout {
  "payment_method": "partial",
  "initial_payment_amount": 37.79,
  ...
}

# 2. Client effectue un autre paiement
POST /api/payments {
  "order_id": 1,
  "payment_method": "stripe",
  "amount": 50.00
}

# 3. Client effectue le paiement final
POST /api/payments {
  "order_id": 1,
  "payment_method": "stripe",
  "amount": 38.17
}

# Maintenant payment_status = "paid", la commande peut être expédiée
```

## Architecture

### Tables de base de données

#### `orders`
- Informations de base : `id`, `order_number`, `user_id`
- Montants : `subtotal`, `tax`, `shipping_cost`, `discount`, `total`
- Paiements : `paid_amount`, `remaining_amount`, `payment_status`, `payment_method`
- Statuts : `status` (pending → confirmed → shipped → delivered)
- Dates : `confirmed_at`, `shipped_at`, `delivered_at`, `cancelled_at`
- Autres : `coupon_code`, `tracking_number`, `customer_notes`, `admin_notes`

#### `order_items`
Snapshot des produits au moment de la commande :
- `product_id`, `product_name`, `product_sku`
- `quantity`, `price`, `subtotal`
- `options` (JSON)

#### `shipping_addresses`
- `full_name`, `email`, `phone`, `company`
- `address_line1`, `address_line2`
- `city`, `state`, `postal_code`, `country`
- `delivery_instructions`, `address_type`

#### `payments`
- `transaction_id` (unique)
- `payment_method`, `amount`, `currency`
- `status` : pending, processing, completed, failed, cancelled, refunded
- `payment_details` (JSON) : détails de la transaction
- `gateway_response` : réponse de la gateway
- `refunded_amount`, `refunded_at`, `refund_reason`

### Services

#### OrderService

```php
// Créer une commande depuis le panier
public function createOrderFromCart(CreateOrderDTO $data): Order

// Liste des commandes utilisateur
public function getUserOrders(array $filters = []): LengthAwarePaginator

// Obtenir une commande
public function getOrderById(int $orderId): Order
public function getOrderByNumber(string $orderNumber): Order

// Mettre à jour le statut
public function updateOrderStatus(UpdateOrderStatusDTO $data): Order

// Annuler une commande
public function cancelOrder(int $orderId, string $reason): Order

// Restaurer le stock (lors de l'annulation)
public function restoreStock(Order $order): void

// Admin : toutes les commandes
public function getAllOrders(array $filters = []): LengthAwarePaginator

// Statistiques
public function getOrderStatistics(): array
```

#### PaymentService

```php
// Traiter un paiement
public function processPayment(ProcessPaymentDTO $data): Payment

// Confirmer un paiement COD
public function confirmCashOnDeliveryPayment(int $paymentId): Payment

// Rembourser
public function refundPayment(int $paymentId, float $amount, string $reason): Payment

// Vérifier si paiement partiel possible
public function canAcceptPartialPayment(Order $order): bool

// Obtenir le montant minimum pour paiement partiel (30%)
public function getMinimumPartialPayment(Order $order): float

// Total payé
public function getTotalPaidAmount(int $orderId): float

// Statistiques
public function getPaymentStatistics(): array
```

## Gestion du stock

### À la création de la commande
1. Pour chaque article :
   - Décrémenter `product.quantity`
   - Si `quantity == 0` → `stock_status = 'out_of_stock'`
   - Si `quantity <= stock_threshold` → `stock_status = 'low_stock'`

### À l'annulation
1. Pour chaque article :
   - Incrémenter `product.quantity`
   - Mettre à jour `stock_status` en conséquence

## Intégration avec les passerelles de paiement

Actuellement, le système est prêt pour l'intégration mais utilise des placeholders.

### TODO : Intégrer Stripe

```php
// Dans PaymentService::processOnlinePayment()
if ($data->payment_method === 'stripe') {
    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    
    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => $data->amount * 100, // en centimes
        'currency' => 'eur',
        'metadata' => [
            'order_id' => $data->order_id,
        ],
    ]);
    
    // ... traiter la réponse
}
```

### TODO : Intégrer PayPal

```php
// Dans PaymentService::processOnlinePayment()
if ($data->payment_method === 'paypal') {
    // Intégration PayPal SDK
}
```

## Exemples d'utilisation

### Frontend : Processus de checkout complet

```javascript
// 1. Récupérer le panier
const cart = await fetch('/api/cart').then(r => r.json())

// 2. Calculer les frais de livraison
const shipping = await fetch('/api/cart/shipping/calculate', {
  method: 'POST',
  body: JSON.stringify({ postal_code: '75001' })
}).then(r => r.json())

// 3. Créer la commande
const order = await fetch('/api/orders/checkout', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    payment_method: 'partial',
    initial_payment_amount: 50.00,
    shipping_address: {
      full_name: 'Jean Dupont',
      email: 'jean@example.com',
      phone: '0612345678',
      address_line1: '123 Rue de la Paix',
      city: 'Paris',
      postal_code: '75001',
      country: 'FR',
      address_type: 'home'
    }
  })
}).then(r => r.json())

// 4. Si paiement partiel, afficher le remaining_amount
console.log(`Reste à payer : ${order.data.remaining_amount}€`)
```

## Middleware Admin

⚠️ **Important** : Les routes admin utilisent le middleware `admin` qui doit être créé.

Créez `app/Http/Middleware/AdminMiddleware.php` :

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return $next($request);
    }
}
```

Enregistrez-le dans `bootstrap/app.php` :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

## TODO / Améliorations futures

- [ ] Intégration réelle Stripe et PayPal
- [ ] Emails de confirmation et notifications
- [ ] Export PDF des factures
- [ ] Historique des modifications de statut
- [ ] Support multi-devises
- [ ] Calcul des frais de livraison par zone
- [ ] Gestion des retours produits
- [ ] Programme de fidélité / points
- [ ] Split payment (payer avec plusieurs cartes)
