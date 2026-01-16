# Collection Postman - Module Products

Collection complète pour tester tous les endpoints du module de gestion de produits.

## 📦 Contenu

### Fichiers
- `products-complete-collection.json` - Collection Postman complète (70+ requêtes)
- `products-environment.json` - Variables d'environnement
- `README.md` - Ce fichier

### Organisation de la Collection

#### 1. **Catégories** (12 requêtes)
- Liste des catégories
- Arborescence complète
- Catégories racines
- Détails par ID/slug
- CRUD complet
- Réorganisation

#### 2. **Produits** (14 requêtes)
- Liste avec pagination
- Filtres avancés
- Produits vedettes/similaires
- Détails par ID/slug
- CRUD complet
- Duplication

#### 3. **Gestion des Stocks** (5 requêtes)
- Mise à jour stock (set/increment/decrement)
- Alertes stock bas
- Produits en rupture

#### 4. **Opérations en Masse** (2 requêtes)
- Mise à jour statut
- Archivage multiple

#### 5. **Cas d'Erreurs** (4 requêtes)
- Tests de validation
- Gestion des erreurs 401/404/422

#### 6. **Workflows Complets** (7 requêtes)
- Création structure complète
- Gestion stock de A à Z

## 🚀 Installation

### 1. Importer dans Postman

#### Méthode 1: Import Direct
1. Ouvrez Postman Desktop
2. Cliquez sur **Import** (coin supérieur gauche)
3. Glissez-déposez les deux fichiers JSON:
   - `products-complete-collection.json`
   - `products-environment.json`
4. Cliquez sur **Import**

#### Méthode 2: Import par URL (si sur GitHub)
1. Cliquez sur **Import**
2. Sélectionnez l'onglet **Link**
3. Collez l'URL du fichier JSON brut
4. Cliquez sur **Continue** puis **Import**

### 2. Configurer l'Environnement

1. Sélectionnez **E-Commerce Products Environment** dans le menu déroulant (coin supérieur droit)
2. Cliquez sur l'icône 👁️ (œil) à côté
3. Modifiez la variable `base_url` si nécessaire:
   ```
   http://localhost:8003
   ```

### 3. Authentification

#### Obtenir un Token
1. Utilisez la collection Auth Module pour vous connecter:
   ```bash
   POST /api/login
   {
       "email": "admin@example.com",
       "password": "password"
   }
   ```
2. Copiez le token de la réponse
3. Dans l'environnement Products, définissez `auth_token` avec ce token

**OU** utilisez l'authentification automatique:
- La collection hérite du bearer token défini au niveau collection
- Le token `{{auth_token}}` est automatiquement utilisé pour les routes protégées

## 📖 Utilisation

### Tests de Base

#### 1. Routes Publiques (Sans Auth)
```
GET /api/products              → Liste tous les produits
GET /api/products/featured     → Produits en vedette
GET /api/categories            → Liste des catégories
GET /api/categories/tree       → Arborescence complète
```

**Astuce**: Ces routes ne nécessitent pas d'authentification

#### 2. Routes Protégées (Avec Auth)
```
POST /api/products             → Créer un produit
PUT /api/products/{id}         → Modifier un produit
DELETE /api/products/{id}      → Supprimer un produit
PATCH /api/products/{id}/stock → Gérer le stock
```

**Important**: Assurez-vous que `auth_token` est défini dans l'environnement

### Workflows Recommandés

#### A. Premier Démarrage
Exécutez dans l'ordre:
1. **Catégories** → **Créer Catégorie** (note l'ID retourné)
2. **Produits** → **Créer Produit Simple** (avec category_ids)
3. **Produits** → **Liste des Produits** (vérifier la création)
4. **Catégories** → **Arborescence Complète** (voir la structure)

#### B. Test Complet E-Commerce
Utilisez le dossier **Workflows Complets**:
1. **1. Créer Structure Complète** (exécuter toutes les requêtes)
   - Crée catégorie → sous-catégorie → produit
   - Auto-sauvegarde les IDs dans les variables
2. **2. Gestion Stock Complet**
   - Simule ventes et réapprovisionnement

#### C. Tests de Filtres
```
GET /api/products?search=iphone&min_price=500&max_price=2000&status=active
GET /api/products?category_ids[]=1&category_ids[]=2&sort_by=price
GET /api/products?is_featured=1&sort_order=desc
```

### Variables Automatiques

La collection utilise des **Tests Scripts** pour sauvegarder automatiquement:
- `category_id` - Premier ID de catégorie trouvé
- `category_slug` - Slug de la catégorie
- `created_category_id` - ID de la catégorie créée
- `product_id` - Premier ID de produit trouvé
- `product_slug` - Slug du produit
- `created_product_id` - ID du produit créé
- Variables workflow (pour les scénarios complets)

**Utilisation**: Ces variables sont réutilisées automatiquement dans les requêtes suivantes.

## 🔍 Exemples de Requêtes

### Créer une Catégorie
```json
POST /api/categories
{
    "name": "Électronique",
    "slug": "electronique",
    "description": "Produits électroniques",
    "is_active": true,
    "sort_order": 0,
    "meta_title": "Électronique - Boutique",
    "meta_description": "Découvrez nos produits électroniques"
}
```

### Créer un Produit Complet
```json
POST /api/products
{
    "name": "MacBook Pro 16\"",
    "slug": "macbook-pro-16",
    "description": "Ordinateur portable professionnel",
    "short_description": "Le meilleur MacBook",
    "price": 2999.99,
    "compare_price": 3499.99,
    "cost": 2200.00,
    "sku": "MBP-16-M3-512",
    "barcode": "194253123456",
    "quantity": 25,
    "low_stock_threshold": 5,
    "track_inventory": true,
    "weight": 2.15,
    "status": "active",
    "is_featured": true,
    "attributes": {
        "processor": "M3 Pro",
        "ram": "32GB",
        "storage": "512GB SSD"
    },
    "category_ids": [1, 2]
}
```

### Filtrer les Produits
```
GET /api/products?search=macbook&min_price=1000&max_price=3000&is_featured=1&sort_by=price&sort_order=asc
```

### Gérer le Stock
```json
PATCH /api/products/1/stock
{
    "quantity": 50,
    "operation": "decrement"  // ou "increment", "set"
}
```

### Mise à Jour en Masse
```json
POST /api/products/bulk/status
{
    "product_ids": [1, 2, 3, 4, 5],
    "status": "active"  // ou "draft", "archived"
}
```

## 🧪 Tests Automatisés

Chaque requête importante inclut des tests:

```javascript
// Exemple de test intégré
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Product has required fields", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.have.property('name');
    pm.expect(jsonData.data).to.have.property('price');
});
```

**Exécution**: Les tests s'exécutent automatiquement après chaque requête.

### Runner de Collection
1. Cliquez sur **E-Commerce - Products Module API**
2. Cliquez sur **Run** (bouton en haut à droite)
3. Sélectionnez les requêtes à tester
4. Cliquez sur **Run E-Commerce - Products Module API**
5. Visualisez les résultats avec statistiques de succès/échec

## 📊 Structure des Réponses

### Succès (200, 201)
```json
{
    "success": true,
    "message": "Produit récupéré avec succès",
    "data": {
        "id": 1,
        "name": "iPhone 15 Pro",
        "price": 1299.99,
        // ... autres champs
    }
}
```

### Pagination
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "total": 150,
        "per_page": 20,
        "last_page": 8
    }
}
```

### Erreur (400, 401, 404, 422)
```json
{
    "success": false,
    "message": "Erreur de validation",
    "errors": {
        "name": ["Le champ name est obligatoire"],
        "price": ["Le champ price doit être un nombre"]
    }
}
```

## 🛠️ Dépannage

### Erreur 401 Unauthorized
**Cause**: Token manquant ou expiré
**Solution**:
1. Connectez-vous via la collection Auth
2. Copiez le nouveau token
3. Mettez à jour `auth_token` dans l'environnement

### Erreur 404 Not Found
**Cause**: Route incorrecte ou ID inexistant
**Solution**:
- Vérifiez que `base_url` est correct
- Exécutez d'abord une requête GET pour obtenir des IDs valides
- Vérifiez que les migrations ont été exécutées

### Erreur 422 Validation Error
**Cause**: Données invalides
**Solution**:
- Consultez le champ `errors` dans la réponse
- Vérifiez les champs obligatoires: name, slug, price, sku
- Assurez-vous que slug et SKU sont uniques

### Variables Non Définies
**Cause**: Variables d'environnement non initialisées
**Solution**:
1. Exécutez d'abord les requêtes GET (auto-remplissent les variables)
2. Ou définissez manuellement dans l'environnement:
   - `category_id` = 1
   - `product_id` = 1

### Connection Refused
**Cause**: Serveur Laravel non démarré
**Solution**:
```bash
cd C:\Users\sidik\Documents\Pro\e-commerce
php artisan serve --host=localhost --port=8003
```

## 🔗 Ressources

### Documentation Complète
- [README.md](../README.md) - Documentation du module
- [INSTALLATION.md](../INSTALLATION.md) - Guide d'installation

### Liens Utiles
- [Documentation Postman](https://learning.postman.com/docs/)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [ValidatedDTO](https://github.com/WendelAdriel/laravel-validated-dto)

## 📝 Notes

### Ordre d'Exécution Recommandé
1. **Auth Module** → Login → Récupérer token
2. **Products** → Liste des catégories (valider l'API)
3. **Products** → Créer catégorie (besoin auth)
4. **Products** → Créer produit (besoin category_id)
5. **Products** → Tester filtres et stocks

### Bonnes Pratiques
- ✅ Toujours vérifier `auth_token` avant les routes protégées
- ✅ Utiliser les workflows pour créer des données de test cohérentes
- ✅ Consulter les tests automatiques pour comprendre les réponses attendues
- ✅ Utiliser des slugs et SKU uniques lors de la création

### Données de Test
La collection inclut des exemples réalistes:
- **Catégories**: Électronique, High-Tech, Ordinateurs, Smartphones
- **Produits**: iPhone 15 Pro, MacBook Pro 16", Samsung Galaxy S24
- **Prix**: 899.99 - 2999.99 (gamme e-commerce standard)
- **Stocks**: 25 - 200 unités (avec seuils d'alerte)

## 🎯 Prochaines Étapes

1. **Importer la collection** dans Postman
2. **Configurer l'environnement** avec votre base_url
3. **Obtenir un token** via Auth Module
4. **Exécuter le workflow** "Créer Structure Complète"
5. **Tester les filtres** et fonctionnalités avancées
6. **Explorer les cas d'erreurs** pour comprendre la validation

---

**Version**: 1.0.0 (Janvier 2026)  
**Module**: Products Management  
**Compatibilité**: Laravel 11.x + Sanctum
