# Guide de Seeding - E-Commerce

Documentation complète pour peupler la base de données avec des données de test.

## 📋 Vue d'Ensemble

Les seeders créent automatiquement:
- **10 Utilisateurs** (admin, manager, vendeur, clients)
- **21 Catégories** (hiérarchiques avec 3 niveaux)
- **18 Produits** (électronique, ordinateurs, smartphones, audio)
- **54+ Images** (3-5 images par produit)

## 🚀 Utilisation Rapide

### Méthode 1: Seeding Complet

```bash
# Réinitialiser et peupler la base de données
php artisan migrate:fresh --seed
```

**⚠️ ATTENTION**: Supprime TOUTES les données existantes!

### Méthode 2: Seeding Seulement

```bash
# Exécuter uniquement les seeders (conserve les données)
php artisan db:seed
```

### Méthode 3: Seeder Spécifique

```bash
# Utilisateurs seulement
php artisan db:seed --class=UserSeeder

# Catégories seulement
php artisan db:seed --class=Modules\\Products\\Database\\Seeders\\CategorySeeder

# Produits seulement
php artisan db:seed --class=Modules\\Products\\Database\\Seeders\\ProductSeeder

# Images seulement
php artisan db:seed --class=Modules\\Products\\Database\\Seeders\\ProductImageSeeder
```

## 👥 Utilisateurs Créés

### Comptes d'Administration

| Nom | Email | Mot de passe | Rôle |
|-----|-------|--------------|------|
| Admin Principal | admin@ecommerce.com | admin123 | Administrateur |
| Manager Commerce | manager@ecommerce.com | manager123 | Manager |
| Vendeur Produits | vendeur@ecommerce.com | vendeur123 | Vendeur |

### Comptes Clients

| Nom | Email | Mot de passe | Vérifié |
|-----|-------|--------------|---------|
| Jean Dupont | jean.dupont@example.com | password123 | ✅ Oui |
| Marie Martin | marie.martin@example.com | password123 | ✅ Oui |
| Pierre Bernard | pierre.bernard@example.com | password123 | ✅ Oui |
| Sophie Dubois | sophie.dubois@example.com | password123 | ✅ Oui |
| Luc Thomas | luc.thomas@example.com | password123 | ✅ Oui |
| API Test User | api@test.com | api123test | ✅ Oui |
| Non Vérifié | nonverifie@example.com | password123 | ❌ Non |

## 📁 Structure des Catégories

```
Électronique
├── Ordinateurs
│   ├── Ordinateurs Portables
│   └── PC de Bureau
├── Smartphones
│   ├── iPhone
│   └── Android
├── Tablettes
└── Audio
    ├── Casques
    └── Écouteurs

Maison & Jardin
├── Électroménager
└── Meubles

Mode & Accessoires
├── Vêtements Homme
└── Vêtements Femme

Sport & Loisirs
├── Fitness
└── Sports Outdoor
```

**Total**: 21 catégories avec hiérarchie à 3 niveaux

## 📦 Produits Créés

### Ordinateurs Portables (3 produits)

1. **MacBook Pro 16" M3 Pro**
   - Prix: 2,999.99€ (compare: 3,499.99€)
   - SKU: MBP-16-M3-512-GRAY
   - Stock: 25 unités
   - ⭐ Featured

2. **Dell XPS 15 9530**
   - Prix: 1,899.99€ (compare: 2,199.99€)
   - SKU: DELL-XPS-15-9530
   - Stock: 30 unités
   - ⭐ Featured

3. **Lenovo ThinkPad X1 Carbon Gen 11**
   - Prix: 1,699.99€
   - SKU: LENOVO-X1C-G11
   - Stock: 40 unités

### Smartphones (4 produits)

4. **iPhone 15 Pro Max 256GB**
   - Prix: 1,399.99€ (compare: 1,499.99€)
   - SKU: IPHONE-15-PM-256-TITAN
   - Stock: 150 unités
   - ⭐ Featured

5. **iPhone 15 128GB**
   - Prix: 899.99€
   - SKU: IPHONE-15-128-BLUE
   - Stock: 200 unités
   - ⭐ Featured

6. **Samsung Galaxy S24 Ultra 512GB**
   - Prix: 1,399.99€ (compare: 1,599.99€)
   - SKU: SGS24U-512-GRAY
   - Stock: 100 unités
   - ⭐ Featured

7. **Google Pixel 8 Pro 256GB**
   - Prix: 999.99€
   - SKU: PIXEL-8P-256-BAY
   - Stock: 80 unités
   - ⭐ Featured

### Tablettes (2 produits)

8. **iPad Pro 12.9" M2 256GB**
   - Prix: 1,299.99€ (compare: 1,449.99€)
   - SKU: IPAD-PRO-129-M2-256
   - Stock: 60 unités
   - ⭐ Featured

9. **Samsung Galaxy Tab S9 Ultra**
   - Prix: 1,199.99€
   - SKU: TAB-S9U-256-GRAY
   - Stock: 45 unités

### Audio (4 produits)

10. **AirPods Max**
    - Prix: 579.99€ (compare: 629.99€)
    - SKU: AIRPODS-MAX-SILVER
    - Stock: 75 unités
    - ⭐ Featured

11. **Sony WH-1000XM5**
    - Prix: 399.99€
    - SKU: SONY-WH1000XM5-BLK
    - Stock: 120 unités
    - ⭐ Featured

12. **AirPods Pro 2e génération**
    - Prix: 249.99€
    - SKU: AIRPODS-PRO-2-USBC
    - Stock: 250 unités
    - ⭐ Featured

13. **Samsung Galaxy Buds Pro 2**
    - Prix: 229.99€
    - SKU: BUDS-PRO-2-WHT
    - Stock: 0 unités ❌ (rupture)

### Produits Spéciaux

14. **Édition Limitée - MacBook Air M2 Midnight**
    - Prix: 1,449.99€
    - SKU: MBA-M2-MIDNIGHT-LTD
    - Stock: 3 unités ⚠️ (stock bas)
    - ⭐ Featured

15. **Nouveau Produit à Venir**
    - Statut: Draft 📝
    - Non visible publiquement

## 🖼️ Images des Produits

Chaque produit reçoit automatiquement:
- **1 image principale** (is_primary: true)
- **2 à 4 images secondaires** (vues différentes)

Structure des chemins:
```
products/{slug}/main.jpg         (image principale)
products/{slug}/image-1.jpg      (vue 1)
products/{slug}/image-2.jpg      (vue 2)
products/{slug}/image-3.jpg      (vue 3)
products/{slug}/image-4.jpg      (vue 4)
```

**Note**: Les chemins sont créés, mais les fichiers physiques doivent être ajoutés dans `storage/app/public/products/`

## 📊 Statistiques après Seeding

```
Utilisateurs:    10
Catégories:      21
Produits:        18
Images:          54-72 (selon randomisation)
```

## 🎯 Cas d'Usage Testables

### 1. Authentification
```bash
# Tester la connexion admin
POST /api/login
{
    "email": "admin@ecommerce.com",
    "password": "admin123"
}
```

### 2. Liste des Produits
```bash
# Tous les produits
GET /api/products

# Produits en vedette
GET /api/products/featured

# Produits par catégorie
GET /api/products?category_ids[]=1
```

### 3. Recherche et Filtres
```bash
# Recherche "iPhone"
GET /api/products?search=iphone

# Prix entre 500 et 1500€
GET /api/products?min_price=500&max_price=1500

# Stock faible
GET /api/products/inventory/low-stock

# Rupture de stock
GET /api/products/inventory/out-of-stock
```

### 4. Arborescence Catégories
```bash
# Arbre complet
GET /api/categories/tree

# Catégories racines
GET /api/categories/roots

# Avec compteur de produits
GET /api/categories/with-product-count
```

## 🔧 Personnalisation

### Ajouter Plus d'Utilisateurs

Modifiez `database/seeders/UserSeeder.php`:

```php
// Ajouter un nouvel utilisateur
User::create([
    'name' => 'Votre Nom',
    'email' => 'votre@email.com',
    'password' => Hash::make('votrepassword'),
    'email_verified_at' => now(),
]);
```

### Ajouter Plus de Catégories

Modifiez `app-modules/products/database/seeders/CategorySeeder.php`:

```php
$nouvelleCategorie = Category::create([
    'name' => 'Nouvelle Catégorie',
    'slug' => 'nouvelle-categorie',
    'description' => 'Description',
    'is_active' => true,
    'sort_order' => 0,
]);
```

### Ajouter Plus de Produits

Modifiez `app-modules/products/database/seeders/ProductSeeder.php`:

```php
$nouveauProduit = Product::create([
    'name' => 'Nouveau Produit',
    'slug' => 'nouveau-produit',
    'description' => 'Description détaillée',
    'short_description' => 'Description courte',
    'price' => 99.99,
    'sku' => 'NOUVEAU-001',
    'quantity' => 100,
    'status' => 'active',
    // ... autres champs
]);

// Attacher aux catégories
$nouveauProduit->categories()->attach([1, 2]);
```

## 🛠️ Dépannage

### Erreur "Class not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Erreur "Table doesn't exist"

```bash
# Exécuter les migrations d'abord
php artisan migrate

# Puis les seeders
php artisan db:seed
```

### Erreur "Duplicate entry"

```bash
# Réinitialiser complètement
php artisan migrate:fresh --seed
```

### Erreur de Namespace

Vérifiez les imports dans les seeders:
```php
use Modules\Products\Models\Product;
use Modules\Products\Models\Category;
use Modules\Products\Models\ProductImage;
```

## 📝 Bonnes Pratiques

### 1. Ordre d'Exécution
- ✅ Utilisateurs en premier
- ✅ Catégories avant produits
- ✅ Produits avant images
- ✅ Relations en dernier

### 2. Données Réalistes
- Utilisez des prix cohérents (899-2999€)
- SKU uniques et structurés
- Codes-barres valides
- Descriptions détaillées

### 3. Variations
- Produits en stock
- Produits en stock bas (< threshold)
- Produits en rupture (quantity = 0)
- Produits draft (non publiés)

### 4. Relations
```php
// Attacher plusieurs catégories
$product->categories()->attach([1, 2, 3]);

// Hiérarchie de catégories
'parent_id' => $parentCategory->id
```

## 🔄 Réinitialisation Complète

```bash
# 1. Supprimer toutes les données
php artisan migrate:fresh

# 2. Recréer la structure
php artisan migrate

# 3. Peupler avec les seeders
php artisan db:seed

# OU tout en une commande
php artisan migrate:fresh --seed
```

## 📈 Prochaines Étapes

Après le seeding:

1. **Tester l'API** avec Postman
2. **Vérifier les données** dans la base
3. **Ajouter les vraies images** dans `storage/app/public/products/`
4. **Créer un lien symbolique** pour les images:
   ```bash
   php artisan storage:link
   ```

## 🎁 Données de Démonstration

Utilisez ces comptes pour vos démos:

**Admin**:
- 📧 admin@ecommerce.com
- 🔑 admin123

**Client**:
- 📧 jean.dupont@example.com
- 🔑 password123

**API Testing**:
- 📧 api@test.com
- 🔑 api123test

---

**Version**: 1.0.0  
**Date**: Janvier 2026  
**Modules**: Auth + Products
