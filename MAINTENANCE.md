# MAINTENANCE.md — Documentation de maintenance

## 1. Mise à jour des dépendances

### Back-end (Composer)
```bash
docker compose exec backend composer outdated   # lister les paquets obsolètes
docker compose exec backend composer update     # mettre à jour (selon contraintes composer.json)
docker compose exec backend composer audit      # vérifier les vulnérabilités
docker compose exec backend php artisan test    # valider la non-régression
```

### Front-end (npm)
```bash
docker compose exec frontend npm outdated
docker compose exec frontend npm update
docker compose exec frontend npm audit
docker compose exec frontend npm run build       # vérifier que le build passe
```

## 2. Fréquence recommandée

| Type de mise à jour | Fréquence | Justification |
|---|---|---|
| Correctifs de sécurité (patch) | Dès publication | Vulnérabilités critiques |
| Versions mineures | Mensuelle | Corrections de bugs, petites features |
| Versions majeures | Trimestrielle / planifiée | Changements cassants (breaking changes) |
| Image Docker de base (PHP, Node, PostgreSQL) | Trimestrielle | Patchs OS + runtime |

## 3. Risques et précautions

| Risque | Précaution |
|---|---|
| Breaking change sur version majeure | Lire le CHANGELOG / guide de migration ; tester sur une branche dédiée |
| Régression non détectée | **Toujours** relancer la suite de tests après mise à jour ([TESTING.md](TESTING.md)) |
| Incompatibilité de versions (PHP/Node) | Les versions sont figées dans les `Dockerfile` → mise à jour contrôlée |
| Perte de données en base | Sauvegarde du volume `db-data` avant toute migration majeure |

## 4. Sauvegarde / restauration de la base

```bash
# Sauvegarde
docker compose exec db pg_dump -U datashare datashare > backup.sql

# Restauration
docker compose exec -T db psql -U datashare datashare < backup.sql
```

## 5. Bonnes pratiques en place

- **Conventional Commits** : historique Git lisible (`feat`, `fix`, `test`, `build`, `docs`…).
- **Versions figées** dans les `Dockerfile` (reproductibilité de l'environnement).
- **Secrets hors du dépôt** (`.env` gitignoré, `.env.example` fourni).
- **Migrations versionnées** (`database/migrations`) : tout changement de schéma est tracé et rejouable.
- **Tests automatisés** (95 % de couverture back) : filet de sécurité avant chaque déploiement.

## 6. Procédure de déploiement

Voir la section **Déploiement** du [README.md](README.md).
