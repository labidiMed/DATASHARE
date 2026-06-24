# PERF.md — Suivi de performance

## 1. Test de charge back-end (k6)

### Endpoint testé
`GET /api/v1/download/{token}` — métadonnées publiques d'un fichier (chemin de lecture critique, appelé avant chaque téléchargement).

### Script
Voir [`perf/k6-download.js`](perf/k6-download.js) — 10 utilisateurs virtuels (VUs) pendant 15 s.

### Exécution
```bash
TOKEN=<token_valide>
docker run --rm -i \
  -e BASE_URL=http://host.docker.internal:8000/api/v1 \
  -e TOKEN="$TOKEN" \
  grafana/k6 run - < perf/k6-download.js
```

### Résultats

| Métrique | Valeur |
|---|---|
| Requêtes totales | 85 |
| Taux d'échec | **0,00 %** ✅ |
| Checks réussis | 100 % (85/85) |
| Latence moyenne | 837 ms |
| Latence médiane | 891 ms |
| p(90) | 1,69 s |
| p(95) | 1,81 s |
| Max | 2,16 s |

### Interprétation

- **Fiabilité : excellente** — 0 % d'erreur, 100 % des réponses en `200`, même sous 10 utilisateurs simultanés.
- **Latence : élevée sous charge** — le seuil p(95) < 300 ms n'est **pas** atteint. La cause est identifiée : en développement, le back-end tourne via `php artisan serve`, un serveur **mono-processus** qui **sérialise** les requêtes concurrentes. Les 10 VUs font la queue → latence cumulée.
- **Ce n'est pas un problème applicatif** : la requête elle-même est simple (1 SELECT indexé sur `download_token` UNIQUE). En isolation (1 VU), la latence min observée est de **64 ms**.

### Recommandations production

- Servir l'API via **PHP-FPM + Nginx** ou **Laravel Octane** (workers multiples, traitement parallèle).
- Cache des métadonnées (Redis) pour les liens très consultés.
- Pool de connexions PostgreSQL.

## 2. Budget de performance front-end

Build de production (`npm run build`, Vite) :

| Asset | Taille | Gzip |
|---|---|---|
| `index.js` (vendor + app) | 143,3 Ko | **54,8 Ko** |
| `index.css` | 3,0 Ko | 1,1 Ko |
| Vues (lazy-loaded, par route) | 0,7–2,6 Ko | < 1,3 Ko |

### Budget cible & respect

| Budget | Cible | Réel | État |
|---|---|---|---|
| JS initial (gzip) | < 150 Ko | ~55 Ko | ✅ |
| CSS (gzip) | < 50 Ko | ~1,1 Ko | ✅ |

- **Code-splitting par route** : chaque écran (`LoginView`, `UploadView`, …) est chargé à la demande (lazy loading via `import()` dans le routeur) → le bundle initial reste léger.

## 3. Métriques suivies

- Temps de réponse API (k6 : avg / p95).
- Taille des fichiers uploadés (limite 1 Go, validée serveur).
- Taille du bundle front (budget ci-dessus).
