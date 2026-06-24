# TESTING.md — Plan de tests

## 1. Stratégie

| Niveau | Outil | Portée |
|---|---|---|
| Tests unitaires | PHPUnit | Logique isolée (modèle `File`) |
| Tests d'intégration (Feature) | PHPUnit | API end-to-end côté serveur (HTTP → BDD) |
| Tests end-to-end | Cypress | Parcours utilisateur réels (navigateur → API → BDD) |

## 2. Tests back-end (PHPUnit)

### Couverture des User Stories

| Fichier de test | US couvertes |
|---|---|
| `AuthTest` | US03 (inscription), US04 (connexion) |
| `FileUploadTest` | US01 (upload), US07 (anonyme), contrôles de saisie |
| `DownloadTest` | US02 (téléchargement), US09 (mot de passe), US10 (expiration) |
| `FileManagementTest` | US05 (historique), US06 (suppression), autorisation |
| `TagTest` | US08 (tags + filtrage) |
| `PurgeExpiredFilesTest` | US10 (purge automatique) |
| `FileModelTest` (unit) | Logique `isExpired` / `isProtected` |

### Exécution
```bash
docker compose exec backend php artisan test
docker compose exec backend php artisan test --coverage   # avec couverture (PCOV)
```

### Résultats

```
Tests:    26 passed (51 assertions)
```

### Couverture de code

**Total : 95,3 %** (objectif : 70 % ✅)

| Composant | Couverture |
|---|---|
| Services / Commands / Requests / Resources | 100 % |
| Controllers (Auth, Download, File, Tag) | 83–100 % |
| Modèles (User, File) | 86–88 % |

> Pour obtenir une capture du rapport : exécuter `docker compose exec backend php artisan test --coverage` et capturer le tableau affiché en fin d'exécution.

## 3. Tests end-to-end (Cypress)

### Scénarios critiques (`frontend/cypress/e2e/`)

| Spec | Scénario |
|---|---|
| `auth.cy.js` | Inscription → déconnexion → reconnexion (US03/US04) |
| `upload.cy.js` | Téléversement d'un fichier + génération du lien (US01) |
| `download.cy.js` | Téléchargement public d'un fichier + erreur sur lien invalide (US02) |

### Exécution
```bash
# La stack doit tourner (docker compose up -d)
cd frontend
npm install
npm run e2e          # mode headless
npm run e2e:open     # mode interactif
```

### Résultats

```
All specs passed!   4 tests, 4 passing, 0 failing
```

## 4. Critères d'acceptation

- ✅ Tous les tests passent (26 back + 4 e2e).
- ✅ Couverture back-end ≥ 70 % (95,3 % atteint).
- ✅ Chaque US obligatoire (US01–US06) couverte par au moins un test.
- ✅ Cas d'erreur testés : 401, 403, 404, 410, 422.

## 5. Notes techniques

- Les tests PHPUnit utilisent **SQLite en mémoire** (`phpunit.xml`) : rapides et isolés, sans toucher la base de développement PostgreSQL.
- Les tests Cypress s'exécutent contre la stack réelle (front + API + PostgreSQL).
- Le hachage bcrypt (rounds 12) rend `/auth/register` lent (~4 s en dev) : le timeout Cypress est relevé à 10 s en conséquence.
