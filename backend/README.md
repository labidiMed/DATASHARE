# DataShare — API (back-end)

API REST de la plateforme DataShare, construite avec **Laravel 13 (PHP 8.3)**, **PostgreSQL** et authentification **JWT**.

> Pour l'installation, le lancement et la documentation complète (tests, sécurité, performances), voir le [README principal](../README.md) à la racine du projet.

## Structure

- `app/Http/Controllers/Api` — contrôleurs de l'API (Auth, File, Download, Tag)
- `app/Http/Requests` — validation des requêtes (Form Requests)
- `app/Http/Resources` — transformation des modèles en JSON
- `app/Models` — modèles Eloquent (User, File, Tag)
- `app/Services` — logique métier (stockage des fichiers)
- `app/Console/Commands` — commandes Artisan (purge des fichiers expirés)
- `routes/api.php` — routes de l'API (préfixe `/api/v1`)
- `database/migrations` — schéma de la base de données
- `tests/` — tests PHPUnit (Feature & Unit)

## Commandes utiles

```bash
php artisan migrate            # appliquer les migrations
php artisan test --coverage    # lancer les tests avec couverture
php artisan files:purge        # purger les fichiers expirés
```

**Auteur :** LABIDI Mhamed
