# Collection Postman - Module Auth

## 📥 Import dans Postman

### 1. Importer la Collection
1. Ouvrez Postman
2. Cliquez sur **Import** (en haut à gauche)
3. Sélectionnez le fichier `postman_collection.json`
4. La collection "E-Commerce Auth Module" apparaîtra dans votre sidebar

### 2. Importer l'Environnement (Optionnel)
1. Cliquez sur **Import**
2. Sélectionnez le fichier `postman_environment.json`
3. Sélectionnez l'environnement "E-Commerce Local" dans le menu déroulant en haut à droite

## 🚀 Utilisation

### Variables disponibles
- `{{base_url}}` : http://localhost:8003 (modifiable)
- `{{token}}` : Se remplit automatiquement après Login/Register

### Ordre de test recommandé

1. **Register** 
   - Créer un nouveau compte
   - Le token est automatiquement sauvegardé

2. **Login**
   - Se connecter avec les identifiants
   - Le token est automatiquement sauvegardé

3. **Basic Auth Login**
   - Test de l'authentification HTTP Basic (comme axios)
   - Configurez username et password dans l'onglet Authorization

4. **Get User Profile (Me)**
   - Utilise automatiquement le token sauvegardé
   - Retourne les infos de l'utilisateur connecté

5. **Refresh Token**
   - Rafraîchit le token actuel
   - Le nouveau token est automatiquement sauvegardé

6. **Logout**
   - Déconnecte l'utilisateur
   - Invalide le token actuel

## 📂 Structure de la Collection

### Auth (Dossier principal)
- ✅ Register - Inscription
- ✅ Login - Connexion
- ✅ Basic Auth Login - Auth HTTP Basic
- ✅ Get User Profile (Me) - Profil utilisateur
- ✅ Refresh Token - Rafraîchir token
- ✅ Logout - Déconnexion

### Examples
- Exemples avec différents utilisateurs
- Utilisez-les comme templates

### Error Cases
- Tests des cas d'erreur
- Identifiants invalides
- Email déjà utilisé
- Requête sans token

## 🔧 Configuration

### Changer le port ou l'URL
1. Dans Postman, cliquez sur l'environnement "E-Commerce Local"
2. Modifiez la valeur de `base_url`
3. Ou modifiez directement dans la collection : Variables > `base_url`

### Utiliser un token manuel
1. Copiez le token depuis la réponse d'une requête Login/Register
2. Allez dans Variables de la collection
3. Collez le token dans la variable `token`

## 💡 Scripts automatiques

Les scripts suivants sont inclus :

### Register & Login
```javascript
// Sauvegarde automatique du token après succès
if (pm.response.code === 201 || pm.response.code === 200) {
    const response = pm.response.json();
    pm.collectionVariables.set('token', response.access_token);
    pm.environment.set('token', response.access_token);
}
```

Cela signifie que vous n'avez pas à copier-coller le token manuellement !

## 🧪 Tests

Toutes les requêtes incluent des tests automatiques pour :
- Vérifier les codes de réponse
- Valider la structure JSON
- Sauvegarder les tokens

## 📝 Exemples de réponses

### Register Success (201)
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@test.com"
    },
    "access_token": "1|token...",
    "token_type": "Bearer"
}
```

### Login Success (200)
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@test.com"
    },
    "access_token": "2|token...",
    "token_type": "Bearer"
}
```

### Me (200)
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@test.com"
    }
}
```

### Error (422)
```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": [
            "The email has already been taken."
        ]
    }
}
```

## 🔐 Authentification

### Bearer Token (automatique)
Les requêtes protégées utilisent automatiquement :
```
Authorization: Bearer {{token}}
```

### Basic Auth (manuelle)
Pour la requête "Basic Auth Login" :
1. Onglet **Authorization**
2. Type: **Basic Auth**
3. Username: `john@test.com`
4. Password: `password123`

## 🎯 Tips

1. **Ordre d'exécution** : Lancez d'abord Register ou Login pour obtenir un token
2. **Run Collection** : Vous pouvez lancer toute la collection avec Runner
3. **Environnements multiples** : Créez des environnements pour dev/staging/prod
4. **Variables de collection** : Le token est partagé entre toutes les requêtes

## ⚙️ Pré-requis

- Serveur Laravel lancé sur le port 8003 : `php artisan serve --port 8003`
- Base de données migrée : `php artisan migrate`
- Postman installé (Desktop ou Web)

## 🆘 Troubleshooting

### "Could not get response"
- Vérifiez que le serveur Laravel est démarré
- Vérifiez l'URL : `http://localhost:8003`

### "Unauthenticated" (401)
- Le token a expiré ou est invalide
- Relancez Login pour obtenir un nouveau token

### "The email has already been taken"
- Cet email existe déjà en base
- Utilisez un autre email ou supprimez l'utilisateur en base

## 📞 Support

Pour toute question, consultez :
- [README.md](README.md) - Documentation complète
- [AUTH_MODULE_INSTALLATION.md](../../AUTH_MODULE_INSTALLATION.md) - Guide d'installation
