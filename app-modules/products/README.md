# Module de Gestion des Produits - E-Commerce

Module complet de gestion des produits pour plateforme e-commerce Laravel, incluant la gestion des catégories, des images, des stocks et des attributs produits.

## 📋 Table des Matières

- [Caractéristiques](#caractéristiques)
- [Installation](#installation)
- [Structure du Module](#structure-du-module)
- [Modèles de Données](#modèles-de-données)
- [API Endpoints](#api-endpoints)
- [Utilisation](#utilisation)
- [Exemples](#exemples)

## ✨ Caractéristiques

### Gestion des Produits
- ✅ CRUD complet des produits
- ✅ Gestion des variantes et attributs personnalisés
- ✅ Support multi-catégories
- ✅ Gestion des images avec image principale
- ✅ Système de prix avec prix comparatif (promotions)
- ✅ Gestion avancée des stocks (seuil bas stock, rupture)
- ✅ SKU et codes-barres uniques
- ✅ Attributs physiques (poids, dimensions)
- ✅ Statuts multiples (brouillon, actif, archivé)
- ✅ Produits mis en avant
- ✅ SEO optimisé (meta title, description, keywords)
- ✅ Compteur de vues
- ✅ Recherche et filtrage avancés
- ✅ Duplication de produits
- ✅ Opérations en masse

### Gestion des Catégories
- ✅ Catégories hiérarchiques (illimitées)
- ✅ Arborescence complète
- ✅ Slugs uniques
- ✅ Images de catégories
- ✅ SEO optimisé
- ✅ Réorganisation drag-and-drop
- ✅ Compteur de produits

### Gestion des Stocks
- ✅ Suivi automatique des stocks
- ✅ Alertes stock faible
- ✅ Gestion rupture de stock
- ✅ Support backorder
- ✅ Mise à jour en masse

## 📦 Installation

### 1. Enregistrer le Module

Ajoutez le service provider dans `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Products\Providers\ProductsServiceProvider::class, // Ajouter cette ligne
];
```

### 2. Mise à Jour de l'Autoloader

Ajoutez le namespace du module dans le fichier `composer.json` principal:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Modules\\Auth\\": "app-modules/auth/src/",
            "Modules\\Products\\": "app-modules/products/src/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    }
}
```

Puis exécutez:

```bash
composer dump-autoload
```

### 3. Exécuter les Migrations

```bash
php artisan migrate
```

Cela créera les tables suivantes:
- `categories` - Catégories de produits
- `products` - Produits
- `product_images` - Images des produits
- `category_product` - Table pivot produits-catégories

## 🏗️ Structure du Module

```
app-modules/products/
├── src/
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php
│   │   └── ProductImage.php
│   ├── Services/
│   │   ├── ProductService.php
│   │   └── CategoryService.php
│   ├── DTOs/
│   │   ├── CreateProductDTO.php
│   │   ├── UpdateProductDTO.php
│   │   ├── CreateCategoryDTO.php
│   │   ├── UpdateCategoryDTO.php
│   │   └── ProductFilterDTO.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── ProductController.php
│   │           └── CategoryController.php
│   └── Providers/
│       └── ProductsServiceProvider.php
├── database/
│   └── migrations/
│       ├── 2026_01_15_000001_create_categories_table.php
│       ├── 2026_01_15_000002_create_products_table.php
│       ├── 2026_01_15_000003_create_product_images_table.php
│       └── 2026_01_15_000004_create_category_product_table.php
├── routes/
│   └── products-routes.php
├── composer.json
└── README.md
```

## 💾 Modèles de Données

### Product (Produit)

| Champ | Type | Description |
|-------|------|-------------|
| name | string | Nom du produit |
| slug | string | URL-friendly identifier (unique) |
| description | text | Description complète |
| short_description | text | Description courte (500 car.) |
| price | decimal | Prix de vente |
| compare_price | decimal | Prix comparatif (barré) |
| cost | decimal | Prix de revient |
| sku | string | Stock Keeping Unit (unique) |
| barcode | string | Code-barres (unique) |
| quantity | integer | Quantité en stock |
| low_stock_threshold | integer | Seuil d'alerte stock bas |
| track_inventory | boolean | Activer suivi des stocks |
| stock_status | enum | in_stock, out_of_stock, on_backorder |
| weight | decimal | Poids en kg |
| length/width/height | decimal | Dimensions en cm |
| status | enum | draft, active, archived |
| is_featured | boolean | Produit mis en avant |
| is_visible | boolean | Visible sur le site |
| meta_title | string | Titre SEO |
| meta_description | text | Description SEO |
| meta_keywords | text | Mots-clés SEO |
| attributes | json | Attributs personnalisés |
| view_count | integer | Nombre de vues |
| published_at | timestamp | Date de publication |

### Category (Catégorie)

| Champ | Type | Description |
|-------|------|-------------|
| name | string | Nom de la catégorie |
| slug | string | URL-friendly identifier (unique) |
| description | text | Description |
| parent_id | integer | ID catégorie parente (nullable) |
| sort_order | integer | Ordre d'affichage |
| is_active | boolean | Catégorie active |
| image | string | Chemin de l'image |
| meta_title | string | Titre SEO |
| meta_description | text | Description SEO |

### ProductImage (Image Produit)

| Champ | Type | Description |
|-------|------|-------------|
| product_id | integer | ID du produit |
| path | string | Chemin de l'image |
| alt_text | string | Texte alternatif |
| sort_order | integer | Ordre d'affichage |
| is_primary | boolean | Image principale |

## 🔌 API Endpoints

### Produits (Public)

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/products` | Liste des produits (avec filtres) |
| GET | `/api/products/{id}` | Détails d'un produit |
| GET | `/api/products/slug/{slug}` | Produit par slug |
| GET | `/api/products/featured` | Produits mis en avant |
| GET | `/api/products/{id}/related` | Produits similaires |

### Produits (Protégé - Auth Required)

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/products` | Créer un produit |
| PUT/PATCH | `/api/products/{id}` | Modifier un produit |
| DELETE | `/api/products/{id}` | Supprimer un produit |
| POST | `/api/products/{id}/duplicate` | Dupliquer un produit |
| PATCH | `/api/products/{id}/stock` | Mettre à jour le stock |
| POST | `/api/products/bulk/status` | Mise à jour en masse du statut |
| GET | `/api/products/inventory/low-stock` | Produits en stock faible |
| GET | `/api/products/inventory/out-of-stock` | Produits en rupture |

### Catégories (Public)

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/categories` | Liste des catégories |
| GET | `/api/categories/tree` | Arborescence complète |
| GET | `/api/categories/roots` | Catégories racines |
| GET | `/api/categories/{id}` | Détails d'une catégorie |
| GET | `/api/categories/slug/{slug}` | Catégorie par slug |
| GET | `/api/categories/with-product-count` | Avec compteur produits |

### Catégories (Protégé - Auth Required)

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/categories` | Créer une catégorie |
| PUT/PATCH | `/api/categories/{id}` | Modifier une catégorie |
| DELETE | `/api/categories/{id}` | Supprimer une catégorie |
| POST | `/api/categories/reorder` | Réorganiser les catégories |

## 📖 Utilisation

### Filtrer les Produits

```http
GET /api/products?search=laptop&min_price=500&max_price=1500&category_ids[]=1&category_ids[]=2&status=active&is_featured=true&sort_by=price&sort_order=asc&per_page=20
```

Paramètres disponibles:
- `search` - Recherche dans nom, description, SKU
- `category_ids[]` - Filtrer par catégories
- `min_price` / `max_price` - Fourchette de prix
- `status` - draft, active, archived
- `is_featured` - true/false
- `stock_status` - in_stock, out_of_stock, on_backorder
- `sort_by` - name, price, created_at, view_count, quantity
- `sort_order` - asc, desc
- `per_page` - Nombre par page (1-100)
- `page` - Numéro de page

### Créer un Produit

```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "MacBook Pro 16\"",
    "slug": "macbook-pro-16",
    "description": "Processeur M3 Max, 36 Go RAM, 1 To SSD",
    "short_description": "Le MacBook Pro le plus puissant",
    "price": 3499.99,
    "compare_price": 3999.99,
    "cost": 2500.00,
    "sku": "MBP-16-M3-36-1TB",
    "barcode": "1234567890123",
    "quantity": 50,
    "low_stock_threshold": 10,
    "track_inventory": true,
    "stock_status": "in_stock",
    "weight": 2.15,
    "length": 35.57,
    "width": 24.81,
    "height": 1.68,
    "status": "active",
    "is_featured": true,
    "is_visible": true,
    "meta_title": "MacBook Pro 16\" - Performances Exceptionnelles",
    "meta_description": "Découvrez le nouveau MacBook Pro 16\" avec puce M3 Max",
    "meta_keywords": "macbook, pro, apple, ordinateur, laptop",
    "attributes": {
        "color": "Space Gray",
        "processor": "M3 Max",
        "ram": "36 GB",
        "storage": "1 TB SSD"
    },
    "category_ids": [1, 5, 12],
    "published_at": "2026-01-15T10:00:00Z"
}
```

### Mettre à Jour un Produit

```http
PATCH /api/products/123
Authorization: Bearer {token}
Content-Type: application/json

{
    "price": 3299.99,
    "quantity": 45,
    "is_featured": false
}
```

### Créer une Catégorie

```http
POST /api/categories
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Ordinateurs Portables",
    "slug": "ordinateurs-portables",
    "description": "Tous nos ordinateurs portables",
    "parent_id": null,
    "sort_order": 1,
    "is_active": true,
    "meta_title": "Ordinateurs Portables - Meilleurs Prix",
    "meta_description": "Large sélection d'ordinateurs portables"
}
```

### Obtenir l'Arborescence des Catégories

```http
GET /api/categories/tree?active_only=true
```

Retourne une structure hiérarchique:
```json
{
    "tree": [
        {
            "id": 1,
            "name": "Électronique",
            "slug": "electronique",
            "children": [
                {
                    "id": 2,
                    "name": "Ordinateurs",
                    "slug": "ordinateurs",
                    "children": [
                        {
                            "id": 3,
                            "name": "Portables",
                            "slug": "portables",
                            "children": []
                        }
                    ]
                }
            ]
        }
    ]
}
```

### Mise à Jour en Masse du Statut

```http
POST /api/products/bulk/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_ids": [1, 2, 3, 4, 5],
    "status": "active"
}
```

### Dupliquer un Produit

```http
POST /api/products/123/duplicate
Authorization: Bearer {token}
```

Crée une copie du produit avec:
- Nom: "{Original} (Copy)"
- Slug: "{original-slug}-copy-{timestamp}"
- SKU: "{original-sku}-copy"
- Toutes les catégories
- Toutes les images

## 🔍 Exemples de Recherche

### Produits en Promotion
```http
GET /api/products?status=active&is_featured=true
```

### Produits d'une Catégorie
```http
GET /api/products?category_ids[]=5&status=active
```

### Produits en Stock Faible
```http
GET /api/products/inventory/low-stock
Authorization: Bearer {token}
```

### Recherche avec Tri
```http
GET /api/products?search=laptop&sort_by=price&sort_order=asc
```

## 🔐 Sécurité

### Routes Publiques
- Consultation des produits et catégories
- Recherche et filtrage
- Détails des produits

### Routes Protégées (Authentication Requise)
- Création de produits/catégories
- Modification
- Suppression
- Gestion des stocks
- Opérations en masse

## 🎯 Fonctionnalités Avancées

### Attributs Dynamiques
Stockez des attributs personnalisés en JSON:
```json
{
    "attributes": {
        "color": "Blue",
        "size": "Large",
        "material": "Cotton",
        "brand": "Nike"
    }
}
```

### Calculs Automatiques
- **is_on_sale**: Automatique si compare_price > price
- **discount_percentage**: Calculé automatiquement
- **is_low_stock**: Basé sur low_stock_threshold
- **path**: Chemin complet de catégorie (breadcrumb)

### Scopes Eloquent
```php
// Produits actifs
Product::active()->get();

// Produits mis en avant
Product::featured()->get();

// Produits en stock
Product::inStock()->get();

// Par fourchette de prix
Product::priceBetween(100, 500)->get();

// Catégories actives
Category::active()->get();

// Catégories racines
Category::root()->get();
```

## 📊 Cas d'Usage

### Boutique E-Commerce Standard
- Catalogue produits complet
- Navigation par catégories
- Recherche et filtres
- Produits en vedette
- Gestion des stocks

### Marketplace Multi-Vendeurs
- Gestion centralisée des produits
- Catégories hiérarchiques
- Attributs personnalisés
- Suivi des stocks

### Site Vitrine avec Catalogue
- Présentation des produits
- Sans gestion de stock
- SEO optimisé
- Images multiples

## 🛠️ Maintenance

### Nettoyer les Produits Archivés
```php
Product::where('status', 'archived')
    ->where('updated_at', '<', now()->subMonths(6))
    ->delete();
```

### Réorganiser les Catégories
```http
POST /api/categories/reorder
{
    "orders": [
        {"id": 1, "sort_order": 0},
        {"id": 2, "sort_order": 1},
        {"id": 3, "sort_order": 2}
    ]
}
```

## 🐛 Dépannage

### Produit Non Visible
- Vérifiez `status = 'active'`
- Vérifiez `is_visible = true`
- Vérifiez `published_at` (doit être dans le passé ou null)

### Stock Non Mis à Jour
- Vérifiez `track_inventory = true`
- Utilisez la méthode `updateStock()` du service

### Slug Dupliqué
- Les slugs doivent être uniques
- Laravel génère automatiquement si non fourni

## 📚 Ressources

- [Laravel Documentation](https://laravel.com/docs)
- [ValidatedDTO Package](https://github.com/WendelAdriel/laravel-validated-dto)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

## 📝 License

MIT License

## 👥 Support

Pour toute question ou problème:
1. Vérifiez les logs: `storage/logs/laravel.log`
2. Consultez cette documentation
3. Vérifiez que les migrations sont exécutées
4. Validez la configuration du module dans `bootstrap/providers.php`
