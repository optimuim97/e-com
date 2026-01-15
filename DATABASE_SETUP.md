# Configuration de la base de données

Avant d'exécuter les migrations, configurez votre base de données dans le fichier `.env`:

## Option 1: Utiliser SQLite (Simple, pas besoin de serveur MySQL)

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=e_commerce
# DB_USERNAME=root
# DB_PASSWORD=
```

Puis créez le fichier de base de données:
```bash
touch database/database.sqlite
# ou sur Windows:
type nul > database\database.sqlite
```

## Option 2: Utiliser MySQL (nécessite un serveur MySQL en cours d'exécution)

Assurez-vous que MySQL est démarré et configurez votre `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_commerce
DB_USERNAME=root
DB_PASSWORD=your_password
```

Puis exécutez:
```bash
php artisan migrate
```

## Option SQLite (plus simple pour le développement)

1. Modifiez votre `.env`:
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=e_commerce
# DB_USERNAME=root
# DB_PASSWORD=
```

2. Créez le fichier de base de données:
```bash
touch database/database.sqlite  # ou New-Item database/database.sqlite sur Windows
```

3. Relancez les migrations:
```bash
php artisan migrate
```
