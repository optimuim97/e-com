# Module d'Authentification

Ce module fournit une API complète d'authentification pour votre application Laravel.

## Installation

Le module est déjà installé. Pour exécuter les migrations :

```bash
php artisan migrate
```

## Endpoints API

### Endpoints Publics

#### 1. Inscription
**POST** `/api/register`

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Réponse:**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "access_token": "1|token...",
  "token_type": "Bearer"
}
```

#### 2. Connexion
**POST** `/api/login`

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "access_token": "2|token...",
  "token_type": "Bearer"
}
```

#### 3. Authentification Basique (Basic Auth)
**GET** `/api/users/me`

Utilise l'authentification HTTP Basic (username:password)

**Exemple avec axios:**
```javascript
await axios.create({
  auth: {
    username: 'john@example.com',
    password: 'password123'
  },
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
}).get('/api/users/me');
```

### Endpoints Protégés (Require Bearer Token)

**Headers requis:**
```
Authorization: Bearer {your_token}
```

#### 4. Obtenir l'utilisateur courant
**GET** `/api/me`

**Réponse:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### 5. Déconnexion
**POST** `/api/logout`

**Réponse:**
```json
{
  "message": "Logout successful"
}
```

#### 6. Rafraîchir le token
**POST** `/api/refresh`

**Réponse:**
```json
{
  "message": "Token refreshed",
  "access_token": "3|token...",
  "token_type": "Bearer"
}
```

## Utilisation avec JavaScript

### Import du service
```javascript
import authService from './app-modules/auth/resources/js/authService';
```

### Inscription
```javascript
try {
  const result = await authService.register(
    'John Doe',
    'john@example.com',
    'password123',
    'password123'
  );
  console.log('Inscription réussie:', result);
} catch (error) {
  console.error('Erreur:', error.response.data);
}
```

### Connexion standard
```javascript
try {
  const result = await authService.login('john@example.com', 'password123');
  console.log('Connexion réussie:', result);
} catch (error) {
  console.error('Erreur:', error.response.data);
}
```

### Connexion avec Basic Auth (comme votre exemple)
```javascript
try {
  await authService.auth('john@example.com', 'password123');
  console.log('Authentification réussie');
} catch (error) {
  console.error('Erreur d\'authentification:', error);
}
```

### Vérifier l'utilisateur connecté
```javascript
if (authService.isAuthenticated()) {
  const user = authService.getUser();
  console.log('Utilisateur connecté:', user);
}
```

### Obtenir les informations de l'utilisateur
```javascript
try {
  const data = await authService.me();
  console.log('Utilisateur:', data.user);
} catch (error) {
  console.error('Erreur:', error);
}
```

### Déconnexion
```javascript
try {
  await authService.logout();
  console.log('Déconnexion réussie');
} catch (error) {
  console.error('Erreur:', error);
}
```

### Écouter les événements
```javascript
// Événement de connexion
authService.on('onLogin', ({ user, token }) => {
  console.log('Utilisateur connecté:', user);
  // Redirection, mise à jour de l'UI, etc.
});

// Événement de déconnexion
authService.on('onLogout', () => {
  console.log('Utilisateur déconnecté');
  // Redirection vers login, etc.
});

// Événement d'erreur
authService.on('onError', (error) => {
  console.error('Erreur d\'authentification:', error);
});
```

## Configuration CORS (Si nécessaire)

Si vous utilisez une application frontend séparée, ajoutez dans `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'supports_credentials' => true,
```

## Tests

Pour tester l'API avec curl:

```bash
# Inscription
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@test.com","password":"password123","password_confirmation":"password123"}'

# Connexion
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@test.com","password":"password123"}'

# Me (avec token)
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"

# Basic Auth
curl -X GET http://localhost:8000/api/users/me \
  --user "john@test.com:password123"
```

## Structure du Module

```
app-modules/auth/
├── src/
│   └── Http/
│       └── Controllers/
│           └── Api/
│               └── AuthController.php
├── routes/
│   └── auth-routes.php
├── resources/
│   └── js/
│       └── authService.js
└── composer.json
```
