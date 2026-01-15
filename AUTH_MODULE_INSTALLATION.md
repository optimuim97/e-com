# Module d'Authentification - Installation Complète

## ✅ Module créé avec succès !

Le module d'authentification a été installé dans `app-modules/auth/`

## 📋 Prochaines étapes

### 1. Configurer la base de données

Choisissez SQLite (recommandé pour le développement) ou MySQL.

#### Option A: SQLite (Simple)
```bash
# Modifiez votre .env
DB_CONNECTION=sqlite

# Créez le fichier de base de données
New-Item database/database.sqlite -ItemType File
```

#### Option B: MySQL
```bash
# Assurez-vous que MySQL est démarré
# Vérifiez votre .env
DB_CONNECTION=mysql
DB_DATABASE=e_commerce
```

### 2. Exécuter les migrations
```bash
php artisan migrate
```

### 3. Tester l'API

#### Inscription
```bash
curl -X POST http://localhost:8000/api/register `
  -H "Content-Type: application/json" `
  -d '{\"name\":\"John Doe\",\"email\":\"john@test.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}'
```

#### Connexion
```bash
curl -X POST http://localhost:8000/api/login `
  -H "Content-Type: application/json" `
  -d '{\"email\":\"john@test.com\",\"password\":\"password123\"}'
```

#### Basic Auth (comme dans votre exemple)
```bash
curl -X GET http://localhost:8000/api/users/me `
  --user "john@test.com:password123"
```

## 📁 Fichiers créés

### Backend (Laravel)
- ✅ `app-modules/auth/src/Http/Controllers/Api/AuthController.php` - Contrôleur API
- ✅ `app-modules/auth/routes/auth-routes.php` - Routes API
- ✅ `app-modules/auth/tests/Feature/Api/AuthControllerTest.php` - Tests

### Frontend (JavaScript)
- ✅ `app-modules/auth/resources/js/authService.js` - Service d'authentification

### Documentation
- ✅ `app-modules/auth/README.md` - Documentation complète
- ✅ `DATABASE_SETUP.md` - Guide de configuration de la base de données

## 🔐 Endpoints disponibles

### Publics
- `POST /api/register` - Inscription
- `POST /api/login` - Connexion
- `GET /api/users/me` - Authentification Basic Auth

### Protégés (nécessite Bearer token)
- `GET /api/me` - Profil utilisateur
- `POST /api/logout` - Déconnexion
- `POST /api/refresh` - Rafraîchir le token

## 🚀 Utilisation JavaScript

```javascript
import authService from './app-modules/auth/resources/js/authService';

// Inscription
await authService.register('John', 'john@test.com', 'password123', 'password123');

// Connexion standard
await authService.login('john@test.com', 'password123');

// Basic Auth (comme votre exemple)
await authService.auth('john@test.com', 'password123');

// Écouter l'événement de connexion
authService.on('onLogin', ({ user, token }) => {
  console.log('Connecté:', user);
});
```

## 🧪 Lancer les tests

```bash
php artisan test --filter AuthControllerTest
```

## 📦 Packages installés
- ✅ Laravel Sanctum (authentification API avec tokens)
- ✅ Internachi/Modular (architecture modulaire)

## 💡 Notes importantes

1. **Sanctum** est configuré pour gérer les tokens API
2. Le modèle `User` a été mis à jour avec le trait `HasApiTokens`
3. Les routes sont automatiquement chargées via le module
4. Le service JavaScript inclut la méthode `auth()` avec Basic Auth comme demandé

## 🔗 Ressources

- Documentation du module: `app-modules/auth/README.md`
- Configuration DB: `DATABASE_SETUP.md`
- Tests: `app-modules/auth/tests/`

Besoin d'aide ? Consultez la documentation ou lancez `php artisan route:list` pour voir toutes les routes disponibles.
