# Guide d'Installation - Module Products

## Installation Rapide

### 1. Mise à Jour de l'Autoloader

Le module a déjà été ajouté au `composer.json`. Exécutez:

```bash
composer dump-autoload
```

### 2. Exécuter les Migrations

```bash
php artisan migrate
```

Cela créera les tables suivantes:
- `categories` - Catégories de produits (hiérarchiques)
- `products` - Produits complets
- `product_images` - Images des produits
- `category_product` - Relation produits-catégories (many-to-many)

### 3. Vérification

Vérifiez que le module est bien chargé:

```bash
php artisan route:list | grep products
php artisan route:list | grep categories
```

Vous devriez voir toutes les routes du module.

## Configuration

### Service Provider

Le service provider est déjà enregistré dans `bootstrap/providers.php`:

```php
Modules\Products\Providers\ProductsServiceProvider::class,
```

### Routes Disponibles

#### Routes Publiques (Sans authentification)

**Produits:**
- `GET /api/products` - Liste des produits avec filtres
- `GET /api/products/{id}` - Détails d'un produit
- `GET /api/products/slug/{slug}` - Produit par slug
- `GET /api/products/featured` - Produits en vedette
- `GET /api/products/{id}/related` - Produits similaires

**Catégories:**
- `GET /api/categories` - Liste des catégories
- `GET /api/categories/tree` - Arborescence complète
- `GET /api/categories/roots` - Catégories racines
- `GET /api/categories/{id}` - Détails d'une catégorie
- `GET /api/categories/slug/{slug}` - Catégorie par slug
- `GET /api/categories/with-product-count` - Avec compteur

#### Routes Protégées (Authentification requise)

**Produits:**
- `POST /api/products` - Créer un produit
- `PUT/PATCH /api/products/{id}` - Modifier un produit
- `DELETE /api/products/{id}` - Supprimer un produit
- `POST /api/products/{id}/duplicate` - Dupliquer un produit
- `PATCH /api/products/{id}/stock` - Mettre à jour le stock
- `POST /api/products/bulk/status` - Mise à jour en masse
- `GET /api/products/inventory/low-stock` - Stock faible
- `GET /api/products/inventory/out-of-stock` - Rupture de stock

**Catégories:**
- `POST /api/categories` - Créer une catégorie
- `PUT/PATCH /api/categories/{id}` - Modifier une catégorie
- `DELETE /api/categories/{id}` - Supprimer une catégorie
- `POST /api/categories/reorder` - Réorganiser

## Premier Démarrage

### 1. Créer des Catégories

```bash
curl -X POST http://localhost:8003/api/categories \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Électronique",
    "slug": "electronique",
    "description": "Produits électroniques",
    "is_active": true,
    "sort_order": 0
  }'
```

### 2. Créer un Produit

```bash
curl -X POST http://localhost:8003/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "description": "Le dernier iPhone",
    "short_description": "Performances exceptionnelles",
    "price": 1299.99,
    "sku": "IPHONE-15-PRO",
    "quantity": 100,
    "status": "active",
    "category_ids": [1]
  }'
```

### 3. Tester les Routes Publiques

```bash
# Liste des produits
curl http://localhost:8003/api/products

# Produits en vedette
curl http://localhost:8003/api/products/featured

# Arborescence des catégories
curl http://localhost:8003/api/categories/tree
```

## Structure du Module

```
app-modules/products/
├── src/
│   ├── Models/                    # Modèles Eloquent
│   ├── Services/                  # Logique métier
│   ├── DTOs/                      # Validation des données
│   ├── Http/Controllers/Api/      # Contrôleurs API
│   └── Providers/                 # Service Provider
├── database/migrations/           # Migrations
├── routes/                        # Routes API
├── composer.json                  # Configuration du module
└── README.md                      # Documentation complète
```

## Fonctionnalités Principales

### Gestion des Produits
- ✅ CRUD complet
- ✅ Multi-catégories
- ✅ Images multiples
- ✅ Gestion des stocks
- ✅ Variantes et attributs
- ✅ Prix promotionnels
- ✅ SEO optimisé
- ✅ Recherche et filtres avancés

### Gestion des Catégories
- ✅ Hiérarchie illimitée
- ✅ Arborescence complète
- ✅ Drag & drop (via API)
- ✅ SEO optimisé

### Gestion des Stocks
- ✅ Suivi automatique
- ✅ Alertes stock bas
- ✅ Rupture de stock
- ✅ Backorder

## Tests Rapides

### Créer des Données de Test

Créez un fichier seeder `database/seeders/ProductsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des catégories
        $electronics = Category::create([
            'name' => 'Électronique',
            'slug' => 'electronique',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $computers = Category::create([
            'name' => 'Ordinateurs',
            'slug' => 'ordinateurs',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Créer des produits
        $product1 = Product::create([
            'name' => 'MacBook Pro 16"',
            'slug' => 'macbook-pro-16',
            'description' => 'Ordinateur portable professionnel',
            'short_description' => 'Le meilleur MacBook',
            'price' => 2999.99,
            'compare_price' => 3499.99,
            'sku' => 'MBP-16-001',
            'quantity' => 50,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $product1->categories()->attach([$computers->id]);

        $product2 = Product::create([
            'name' => 'Dell XPS 15',
            'slug' => 'dell-xps-15',
            'description' => 'Ordinateur portable Dell',
            'short_description' => 'Performances exceptionnelles',
            'price' => 1899.99,
            'sku' => 'DELL-XPS-15-001',
            'quantity' => 30,
            'status' => 'active',
        ]);

        $product2->categories()->attach([$computers->id]);
    }
}
```

Puis exécutez:

```bash
php artisan db:seed --class=ProductsSeeder
```

## Dépannage

### Erreur "Class not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Routes non trouvées

Vérifiez que le service provider est enregistré:

```bash
php artisan about
```

Recherchez "Modules\Products\Providers\ProductsServiceProvider" dans la sortie.

### Erreurs de Migration

Si les migrations échouent:

```bash
php artisan migrate:fresh  # ATTENTION: Supprime toutes les données!
```

Ou:

```bash
php artisan migrate:rollback
php artisan migrate
```

## Prochaines Étapes

1. ✅ **Module installé et fonctionnel**
2. 📝 Créer des catégories via l'API
3. 📦 Créer des produits via l'API
4. 🖼️ Ajouter des images aux produits
5. 🔍 Tester les filtres et recherches
6. 📊 Implémenter la gestion des stocks

## Documentation Complète

Consultez le fichier [README.md](README.md) pour:
- Documentation complète de l'API
- Tous les endpoints disponibles
- Exemples détaillés
- Cas d'usage
- Fonctionnalités avancées

## Support

Pour toute question:
1. Consultez le README.md
2. Vérifiez les logs: `storage/logs/laravel.log`
3. Testez les routes: `php artisan route:list`
