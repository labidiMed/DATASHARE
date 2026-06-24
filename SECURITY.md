# SECURITY.md — Garantie de sécurité

## 1. Mesures de sécurité en place

| Domaine | Mesure |
|---|---|
| Authentification | JWT signé (HS256), stateless, expiration 1h |
| Mots de passe (comptes) | Hachés + salés (bcrypt, `password` cast `hashed`) |
| Mots de passe (fichiers) | Hachés (bcrypt), jamais renvoyés (`#[Hidden]`) |
| Autorisation | Un utilisateur ne peut accéder/supprimer que ses fichiers (403 sinon) |
| Tokens de téléchargement | 40 caractères aléatoires non prédictibles (`Str::random(40)`), unicité garantie |
| Validation | Côté serveur (Form Requests) **et** côté client |
| Upload | Taille max 1 Go, extensions dangereuses bloquées (`.exe`, `.bat`, `.sh`, …) |
| Exposition des données | `stored_path` et `password_hash` masqués dans les réponses JSON |
| CORS | Géré par Laravel (`HandleCors`) |
| Secrets | `.env` non commité (`.gitignore`), `.env.example` fourni |

## 2. Scan de sécurité des dépendances

### Back-end (Composer)
```bash
docker compose exec backend composer audit
```
**Résultat :** `No security vulnerability advisories found.` ✅

### Front-end (npm)
```bash
docker compose exec frontend npm audit
```
**Résultat :** `found 0 vulnerabilities` ✅

## 3. Analyse des résultats

Aucune vulnérabilité connue détectée dans les dépendances (back et front) au moment du scan. Les paquets clés (`laravel/framework`, `php-open-source-saver/jwt-auth`, `vue`, `axios`) sont à jour.

## 4. Décisions de sécurité

- **JWT plutôt que sessions** : API stateless, adaptée à une SPA, exigée par la spécification.
- **Liste noire d'extensions** plutôt que liste blanche : le service accepte tout type de document, on bloque uniquement les exécutables/scripts à risque.
- **Pas de récupération de mot de passe fichier** : conforme au MVP (US09), le hash est irréversible.
- **`user_id` nullable** : permet l'upload anonyme (US07) sans compromettre l'isolation des comptes.

## 5. Points d'amélioration (hors MVP)

- Rate limiting / throttling sur `/auth/login` (anti brute-force).
- Scan antivirus des fichiers uploadés (ex. ClamAV).
- Rotation des tokens JWT (refresh tokens).
- HTTPS forcé en production (HSTS).
