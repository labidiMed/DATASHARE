# DataShare

Plateforme de transfert de fichiers : dépôt de fichiers, liens de téléchargement temporaires (protection par mot de passe, expiration), historique et tags.

**Auteur :** LABIDI Mhamed

## Stack

- **Back-end** : Laravel 13 (PHP 8.3) — API REST, JWT
- **Front-end** : Vue 3 (Vite, Vue Router, Pinia)
- **Base de données** : PostgreSQL 16
- **Infrastructure** : Docker Compose

## Démarrage

```bash
cp .env.example .env
docker compose up -d --build
```

- API : http://localhost:8000
- SPA : http://localhost:5173

## Commandes utiles

```bash
docker compose ps                  # état des conteneurs
docker compose logs -f backend     # logs du backend
docker compose down                # arrêt (conserve les volumes)
```
