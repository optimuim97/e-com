# Guide d'Installation - Modules Cart & Orders

Ce guide explique comment installer et configurer les modules Cart (Panier) et Orders (Commandes & Paiements).

## Prérequis

- PHP >= 8.2
- Laravel 11.x
- MySQL ou PostgreSQL
- Composer
- Modules **Auth** et **Products** déjà installés

## Installation

### 1. Vérifier l'autoload

Les modules sont déjà enregistrés dans `composer.json` :

```json
{
  "require": {
    "modules/cart": "*",
    "modules/orders": "*"
  },
  "autoload": {
    "psr-4": {
      "Modules\\Cart\\": "app-modules/cart/src/",
      "Modules\\Orders\\": "app-modules/orders/src/"
    }
  }
}
```

### 2. Régénérer l'autoload

```bash
composer dump-autoload
```

### 3. Vérifier les Service Providers

Les providers doivent être enregistrés dans `bootstrap/providers.php` :

```php
return [
    App\Providers\AppServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Products\Providers\ProductsServiceProvider::class,
    Modules\Cart\Providers\CartServiceProvider::class,
    Modules\Orders\Providers\OrdersServiceProvider::class,
];
```

### 4. Exécuter les migrations

```bash
php artisan migrate
```

Cela créera les tables suivantes :
- **Cart Module** : `carts`, `cart_items`
- **Orders Module** : `orders`, `order_items`, `shipping_addresses`, `payments`

### 5. Configurer les sessions (important pour les paniers invités)

Dans `config/session.php`, assurez-vous d'avoir :

```php
'driver' => env('SESSION_DRIVER', 'database'),
'lifetime' => 120,
'expire_on_close' => false,
```

Si vous utilisez `database` comme driver, créez la table sessions :

```bash
php artisan session:table
php artisan migrate
```

### 6. (Optionnel) Configurer Sanctum pour l'authentification API

Publier la configuration si pas déjà fait :

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\ServiceProvider"
```

Dans `config/sanctum.php`, vérifiez :

```php
'expiration' => null, // ou 60 * 24 pour 24h
```

### 7. Créer le middleware Admin

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
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 8. Ajouter le champ `is_admin` à la table users

Créez une migration :

```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
```

```bash
php artisan migrate
```

### 9. Configurer CORS (si frontend séparé)

Dans `config/cors.php` :

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

### 10. (Optionnel) Seeders de test

```bash
php artisan db:seed --class=CartSeeder
php artisan db:seed --class=OrderSeeder
```

## Configuration

### Variables d'environnement

Ajoutez dans `.env` :

```env
# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000

# Passerelles de paiement (TODO)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
PAYPAL_CLIENT_ID=...
PAYPAL_SECRET=...

# Frontend URL (pour CORS)
FRONTEND_URL=http://localhost:3000
```

### Configuration des produits

Assurez-vous que la table `products` a les colonnes suivantes :
- `quantity` (int) - Stock disponible
- `stock_status` (enum: 'in_stock', 'low_stock', 'out_of_stock')
- `stock_threshold` (int, default 10) - Seuil pour "low_stock"

## Vérification de l'installation

### 1. Tester les routes

```bash
# Liste des routes
php artisan route:list --path=cart
php artisan route:list --path=orders
php artisan route:list --path=payments
```

Vous devriez voir :
- Cart : 8 routes
- Orders : 7 routes
- Payments : 6 routes

### 2. Test manuel avec curl

```bash
# Obtenir le panier (sans auth)
curl http://localhost:8000/api/cart

# Ajouter au panier
curl -X POST http://localhost:8000/api/cart/add \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'
```

### 3. Test avec authentification

```bash
# Login
TOKEN=$(curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}' \
  | jq -r '.data.token')

# Créer une commande
curl -X POST http://localhost:8000/api/orders/checkout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "online",
    "shipping_address": {
      "full_name": "Test User",
      "email": "test@example.com",
      "phone": "0612345678",
      "address_line1": "123 Rue Test",
      "city": "Paris",
      "postal_code": "75001",
      "country": "FR",
      "address_type": "home"
    }
  }'
```

## Intégration dans votre application

### 1. Fusion du panier lors du login

Dans votre `AuthController` ou après l'authentification :

```php
use Modules\Cart\Services\CartService;

public function login(Request $request)
{
    // ... votre logique d'authentification ...
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Fusionner les paniers invité → utilisateur
        app(CartService::class)->mergeGuestCart($user->id);
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
        ]);
    }
    
    // ...
}
```

### 2. Frontend : Ajouter au panier

```javascript
// React/Vue/Angular example
async function addToCart(productId, quantity, options = {}) {
  const response = await fetch('/api/cart/add', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      product_id: productId,
      quantity: quantity,
      options: options
    })
  })
  
  const result = await response.json()
  
  if (result.success) {
    // Afficher notification succès
    toast.success('Produit ajouté au panier')
    
    // Rafraîchir le badge du panier
    updateCartBadge()
  } else {
    toast.error(result.message)
  }
}
```

### 3. Frontend : Checkout

```javascript
async function checkout(shippingAddress, paymentMethod, initialPayment = null) {
  const token = localStorage.getItem('auth_token')
  
  const response = await fetch('/api/orders/checkout', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      payment_method: paymentMethod,
      initial_payment_amount: initialPayment,
      shipping_address: shippingAddress
    })
  })
  
  const result = await response.json()
  
  if (result.success) {
    // Rediriger vers la page de confirmation
    router.push(`/orders/${result.data.order_number}`)
  } else {
    toast.error(result.message)
  }
}
```

## Dépannage

### Erreur : "Class 'Modules\Cart\Services\CartService' not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Erreur : "SQLSTATE[42S02]: Base table or view not found"

```bash
php artisan migrate:status
php artisan migrate
```

### Le panier n'est pas fusionné lors du login

Vérifiez que vous avez bien appelé `mergeGuestCart()` après l'authentification.

### Les routes ne fonctionnent pas

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## Tests

### Tests unitaires (TODO)

```bash
php artisan test --filter CartServiceTest
php artisan test --filter OrderServiceTest
php artisan test --filter PaymentServiceTest
```

### Tests d'intégration (TODO)

```bash
php artisan test --filter CartApiTest
php artisan test --filter OrderApiTest
```

## Prochaines étapes

1. ✅ Installation terminée
2. 📝 Lire la documentation des modules :
   - [Cart README](../cart/README.md)
   - [Orders README](../orders/README.md)
3. 🧪 Tester les endpoints avec Postman
4. 🎨 Intégrer dans votre frontend
5. 💳 Configurer les passerelles de paiement (Stripe, PayPal)
6. 📧 Configurer les notifications email
7. 🧪 Écrire les tests automatisés

## Support

Pour toute question ou problème :
1. Vérifiez les logs : `storage/logs/laravel.log`
2. Consultez la documentation des modules
3. Vérifiez que tous les prérequis sont installés

## Licence

Propriétaire - Tous droits réservés
