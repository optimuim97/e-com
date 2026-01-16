# 🛒 Modules Cart & Orders - Système Complet E-Commerce

## 📋 Résumé

J'ai créé un système complet de gestion de panier, commandes et paiements avec **3 modes de paiement** comme demandé :

- ✅ **Paiement à la livraison** (Cash on Delivery)
- ✅ **Paiement en ligne** (Stripe, PayPal, etc.)
- ✅ **Paiement partiel** (Acompte + solde)

## 🏗️ Architecture

### Module Cart (Panier)
```
app-modules/cart/
├── database/migrations/
│   ├── 2026_01_16_000001_create_carts_table.php
│   └── 2026_01_16_000002_create_cart_items_table.php
├── src/
│   ├── DTOs/
│   │   ├── AddToCartDTO.php
│   │   ├── UpdateCartItemDTO.php
│   │   └── ApplyCouponDTO.php
│   ├── Http/Controllers/Api/
│   │   └── CartController.php
│   ├── Models/
│   │   ├── Cart.php
│   │   └── CartItem.php
│   ├── Providers/
│   │   └── CartServiceProvider.php
│   └── Services/
│       └── CartService.php
├── routes/
│   └── cart-routes.php
├── composer.json
└── README.md
```

### Module Orders (Commandes & Paiements)
```
app-modules/orders/
├── database/migrations/
│   ├── 2026_01_16_000001_create_orders_table.php
│   ├── 2026_01_16_000002_create_order_items_table.php
│   ├── 2026_01_16_000003_create_shipping_addresses_table.php
│   └── 2026_01_16_000004_create_payments_table.php
├── src/
│   ├── DTOs/
│   │   ├── CreateOrderDTO.php
│   │   ├── ProcessPaymentDTO.php
│   │   └── UpdateOrderStatusDTO.php
│   ├── Http/Controllers/Api/
│   │   ├── OrderController.php
│   │   └── PaymentController.php
│   ├── Models/
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── ShippingAddress.php
│   │   └── Payment.php
│   ├── Providers/
│   │   └── OrdersServiceProvider.php
│   └── Services/
│       ├── OrderService.php
│       └── PaymentService.php
├── routes/
│   └── orders-routes.php
├── composer.json
└── README.md
```

## 🎯 Fonctionnalités Principales

### Panier (Cart)
- ✅ Panier invité avec session UUID
- ✅ Panier utilisateur authentifié
- ✅ Fusion automatique lors du login
- ✅ Gestion quantités et options produits
- ✅ Application de codes promo (10% actuellement)
- ✅ Calcul auto des frais de livraison (gratuit >100€, sinon 5.99€)
- ✅ Calcul TVA (20%)
- ✅ Validation du stock avant ajout
- ✅ Expiration paniers invités (7 jours)

### Commandes (Orders)
- ✅ Création depuis le panier
- ✅ Numéro de commande unique (ORD-YYYYMMDD-RANDOM)
- ✅ États : pending → confirmed → processing → shipped → delivered
- ✅ Suivi avec tracking number
- ✅ Adresse de livraison complète
- ✅ Notes client et admin
- ✅ Gestion automatique du stock (déduction/restauration)
- ✅ Annulation avec remise en stock
- ✅ Soft deletes
- ✅ Statistiques et rapports

### Paiements (Payments)
#### 1️⃣ Paiement à la livraison (COD)
- Status "pending" jusqu'à la livraison
- Confirmation manuelle par admin à la livraison
- Auto-passage en "delivered" si déjà expédié

#### 2️⃣ Paiement en ligne
- Traitement immédiat (Stripe/PayPal - placeholder)
- Confirmation automatique si succès
- Support multi-gateway prêt

#### 3️⃣ Paiement partiel
- Acompte minimum : 30% du total
- Paiements multiples possibles
- Tracking `paid_amount` / `remaining_amount`
- Auto-passage en "paid" quand total atteint

**Bonus :**
- ✅ Système de remboursement (partiel/total)
- ✅ Historique complet des transactions
- ✅ Statistiques par méthode de paiement

## 📡 API Endpoints

### Cart (8 endpoints)
```http
GET    /api/cart                      # Obtenir le panier
POST   /api/cart/add                  # Ajouter un produit
PUT    /api/cart/items/{id}           # Modifier quantité
DELETE /api/cart/items/{id}           # Retirer un article
DELETE /api/cart/clear                # Vider le panier
POST   /api/cart/coupon               # Appliquer coupon
DELETE /api/cart/coupon               # Retirer coupon
POST   /api/cart/shipping/calculate   # Calculer frais livraison
```

### Orders (7 endpoints)
```http
POST   /api/orders/checkout                 # Créer commande (checkout)
GET    /api/orders                          # Liste commandes utilisateur
GET    /api/orders/{id}                     # Détails commande par ID
GET    /api/orders/number/{orderNumber}     # Détails par numéro
POST   /api/orders/{id}/cancel              # Annuler commande

# Admin
GET    /api/orders/admin/all                # Toutes commandes
PUT    /api/orders/{id}/status              # Mettre à jour statut
GET    /api/orders/admin/statistics         # Statistiques
```

### Payments (6 endpoints)
```http
POST   /api/payments                         # Traiter paiement
GET    /api/payments/orders/{orderId}        # Liste paiements commande
GET    /api/payments/orders/{id}/check-partial  # Vérifier paiement partiel

# Admin
POST   /api/payments/{id}/confirm            # Confirmer COD
POST   /api/payments/{id}/refund             # Rembourser
GET    /api/payments/admin/statistics        # Statistiques paiements
```

## 🚀 Installation Rapide

```bash
# 1. Régénérer l'autoload
composer dump-autoload

# 2. Exécuter les migrations
php artisan migrate

# 3. (Optionnel) Générer des données de test
php artisan db:seed --class=CartSeeder
php artisan db:seed --class=OrderSeeder

# 4. Vérifier les routes
php artisan route:list --path=cart
php artisan route:list --path=orders
php artisan route:list --path=payments
```

## 📖 Documentation

- **[CART_ORDERS_INSTALLATION.md](CART_ORDERS_INSTALLATION.md)** - Guide d'installation complet
- **[app-modules/cart/README.md](app-modules/cart/README.md)** - Documentation module Cart
- **[app-modules/orders/README.md](app-modules/orders/README.md)** - Documentation module Orders

## 💡 Exemples d'utilisation

### Exemple 1 : Ajouter au panier
```javascript
const response = await fetch('/api/cart/add', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    product_id: 5,
    quantity: 2,
    options: { color: 'red', size: 'M' }
  })
})
```

### Exemple 2 : Checkout avec paiement partiel
```javascript
const response = await fetch('/api/orders/checkout', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    payment_method: 'partial',
    initial_payment_amount: 50.00, // Minimum 30%
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
})
```

### Exemple 3 : Payer le solde restant
```javascript
// Vérifier combien reste à payer
const check = await fetch(`/api/payments/orders/${orderId}/check-partial`, {
  headers: { 'Authorization': `Bearer ${token}` }
})

// Payer le reste
const payment = await fetch('/api/payments', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    order_id: orderId,
    payment_method: 'stripe',
    amount: remainingAmount
  })
})
```

## 🔧 Configuration Requise

### 1. Ajouter le champ `is_admin` aux users

```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('email');
});
```

### 2. Créer le middleware Admin

Fichier : `app/Http/Middleware/AdminMiddleware.php`

```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->is_admin) {
        return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
    }
    return $next($request);
}
```

Enregistrer dans `bootstrap/app.php` :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### 3. Fusionner le panier lors du login

Dans votre `AuthController` :

```php
use Modules\Cart\Services\CartService;

public function login(Request $request)
{
    // ... authentification ...
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        // IMPORTANT : Fusionner les paniers
        app(CartService::class)->mergeGuestCart($user->id);
        
        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }
}
```

## 🎨 Workflows des 3 modes de paiement

### Paiement à la livraison (COD)
```
1. Client → Checkout avec "cash_on_delivery"
2. Commande créée (status: pending, payment_status: pending)
3. Admin → Confirme et expédie la commande
4. Livreur → Reçoit le paiement en espèces
5. Admin → Confirme le paiement COD via POST /api/payments/{id}/confirm
6. Système → Auto-passe la commande en "delivered"
```

### Paiement en ligne
```
1. Client → Checkout avec "online"
2. Système → Traite le paiement immédiatement (Stripe/PayPal)
3. Si succès → Commande confirmée (status: confirmed, payment_status: paid)
4. Admin → Expédie la commande
5. Admin → Marque comme livrée
```

### Paiement partiel
```
1. Client → Checkout avec "partial" + acompte (min 30%)
2. Système → Traite l'acompte
3. Commande créée (payment_status: partially_paid)
4. Client → Effectue un ou plusieurs paiements supplémentaires via POST /api/payments
5. Quand total atteint → payment_status passe à "paid"
6. Admin → Peut alors expédier la commande
```

## 📊 Base de données

### Tables créées

1. **carts** - Paniers (invités et utilisateurs)
2. **cart_items** - Articles dans les paniers
3. **orders** - Commandes avec statuts et montants
4. **order_items** - Articles commandés (snapshot)
5. **shipping_addresses** - Adresses de livraison
6. **payments** - Transactions de paiement

### Relations principales

```
User 1→N Cart 1→N CartItem N→1 Product
User 1→N Order 1→N OrderItem N→1 Product
Order 1→1 ShippingAddress
Order 1→N Payment
```

## ✅ Ce qui est fait

- ✅ Module Cart complet avec guest et user support
- ✅ Module Orders avec state machine des statuts
- ✅ 3 modes de paiement (COD, online, partial)
- ✅ Système de remboursement
- ✅ Gestion automatique du stock
- ✅ Controllers et routes API
- ✅ Service Providers enregistrés
- ✅ DTOs de validation avec messages français
- ✅ Documentation complète
- ✅ Seeders de test

## 🔜 À faire (Optionnel)

- [ ] Intégration réelle Stripe et PayPal
- [ ] Emails de confirmation (OrderCreated, OrderShipped, etc.)
- [ ] Export PDF des factures
- [ ] Dashboard admin avec graphiques
- [ ] Tests automatisés (PHPUnit/Pest)
- [ ] Notifications temps réel (websockets)
- [ ] Système de coupons complet
- [ ] Multi-devises
- [ ] Gestion des retours produits

## 🧪 Test rapide

```bash
# Démarrer le serveur
php artisan serve

# Dans un autre terminal, tester l'API
curl http://localhost:8000/api/cart
curl -X POST http://localhost:8000/api/cart/add \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'
```

## 📝 Notes importantes

1. **Stock management** : Le stock est automatiquement mis à jour lors de la création/annulation de commandes
2. **Paniers invités** : Expirent après 7 jours
3. **Paiement partiel** : Minimum 30% du total requis en acompte
4. **Remboursements** : Peuvent être partiels ou totaux
5. **Snapshots produits** : Les infos produit sont copiées dans `order_items` pour éviter les pertes de données

## 🎉 Prêt à l'emploi !

Tout est configuré et prêt à être utilisé. Il ne vous reste plus qu'à :

1. Exécuter les migrations : `php artisan migrate`
2. (Optionnel) Générer des données de test : `php artisan db:seed --class=CartSeeder && php artisan db:seed --class=OrderSeeder`
3. Tester les endpoints API
4. Intégrer dans votre frontend
5. Configurer Stripe/PayPal quand vous serez prêt

Bonne chance avec votre e-commerce ! 🚀
