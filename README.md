# DataShare

Plateforme de transfert de fichiers : dépôt de fichiers, liens de téléchargement temporaires (protection par mot de passe, expiration), historique et tags.

**Auteur :** LABIDI Mhamed

## Stack

- **Back-end** : Laravel 13 (PHP 8.3) — API REST, JWT
- **Front-end** : Vue 3 (Vite, Vue Router, Pinia, Axios)
- **Base de données** : PostgreSQL 16
- **Infrastructure** : Docker Compose (4 conteneurs : `db`, `backend`, `scheduler`, `frontend`)

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Docker + Docker Compose v2)

## Installation & déploiement

### Démarrage rapide (script automatisé)

Le script gère tout : fichiers `.env`, build, clés (`APP_KEY`/`JWT_SECRET`), migrations.

```bash
git clone https://github.com/labidiMed/DATASHARE.git
cd DATASHARE
```

| Système | Démarrer | Arrêter |
|---|---|---|
| Linux / macOS / Git Bash | `bash start.sh` | `bash stop.sh` |
| Windows PowerShell | `.\start.ps1` | `docker compose down` |

> Prérequis : Docker Desktop démarré. Le script est **idempotent** (relançable sans risque).

Accès :
- **SPA Vue** : http://localhost:5173
- **API Laravel** : http://localhost:8000/api/v1

### Démarrage manuel (équivalent, étape par étape)

```bash
cp .env.example .env                 # config infra (éditer POSTGRES_PASSWORD en prod)
cp backend/.env.example backend/.env # config applicative Laravel
docker compose up -d --build
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan jwt:secret
docker compose restart backend       # recharge les clés
docker compose exec backend php artisan migrate
```

## Configuration de la base de données

Les identifiants PostgreSQL sont définis dans le fichier `.env` racine (source unique) :

| Variable | Rôle |
|---|---|
| `POSTGRES_DB` | Nom de la base |
| `POSTGRES_USER` | Utilisateur |
| `POSTGRES_PASSWORD` | Mot de passe |
| `DB_PORT` | Port exposé sur l'hôte |

Ces valeurs sont injectées automatiquement dans le conteneur `backend` par `docker-compose.yml` (elles surchargent le `.env` de Laravel).

## Commandes utiles

```bash
docker compose ps                       # état des conteneurs
docker compose logs -f backend          # logs du backend
docker compose exec backend php artisan files:purge   # purge manuelle des fichiers expirés
docker compose down                     # arrêt (conserve les volumes)
```

## Tests

```bash
# Tests back-end (PHPUnit) + couverture
docker compose exec backend php artisan test --coverage

# Tests end-to-end (Cypress) — nécessite la stack lancée
cd frontend && npm install && npm run e2e
```

## Documentation qualité

- [TESTING.md](TESTING.md) — plan de tests, couverture, exécution
- [SECURITY.md](SECURITY.md) — sécurité & scan des dépendances
- [PERF.md](PERF.md) — performances (k6, budget bundle)
- [MAINTENANCE.md](MAINTENANCE.md) — maintenance & mises à jour

## Contrat d'API

Le contrat complet est décrit au format OpenAPI 3.1 dans `datashare-openapi.yaml` (visualisable sur [editor.swagger.io](https://editor.swagger.io)).

## Structure du projet

```
DataShare/
├── backend/           # API Laravel 13 (app, routes, tests, migrations)
├── frontend/          # SPA Vue 3 (src, cypress)
├── perf/              # scripts de test de performance k6
├── docker-compose.yml # orchestration des 4 conteneurs
├── start.sh / start.ps1 / stop.sh  # scripts de lancement
├── .env.example       # modèle de configuration d'infrastructure
└── *.md               # documentation (README, TESTING, SECURITY, PERF, MAINTENANCE)
```
