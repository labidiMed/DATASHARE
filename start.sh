#!/usr/bin/env bash
#
# Démarre DataShare : configuration (1re fois) + lancement de la stack Docker.
# Usage : bash start.sh
#
set -e
cd "$(dirname "$0")"

echo "🚀 Démarrage de DataShare..."

# 1. Docker doit tourner
if ! docker info >/dev/null 2>&1; then
  echo "❌ Docker ne répond pas. Lance Docker Desktop puis réessaie."
  exit 1
fi

# 2. Fichiers d'environnement (créés depuis les modèles si absents)
[ -f .env ] || { cp .env.example .env && echo "📄 .env créé depuis .env.example"; }
[ -f backend/.env ] || { cp backend/.env.example backend/.env && echo "📄 backend/.env créé depuis backend/.env.example"; }

# 3. Construction + lancement des conteneurs
docker compose up -d --build

# 4. Attendre que le backend soit prêt (dépendances Composer installées)
echo "⏳ Attente du backend..."
until docker compose exec -T backend php artisan --version >/dev/null 2>&1; do
  sleep 3
done

# 5. Clés applicatives (générées uniquement si absentes)
NEED_RESTART=0
if ! grep -q '^APP_KEY=base64:' backend/.env; then
  docker compose exec -T backend php artisan key:generate
  NEED_RESTART=1
fi
if ! grep -q '^JWT_SECRET=.\+' backend/.env; then
  docker compose exec -T backend php artisan jwt:secret --force
  NEED_RESTART=1
fi

# Recharger l'environnement du backend si de nouvelles clés ont été générées
if [ "$NEED_RESTART" = "1" ]; then
  docker compose restart backend scheduler
  until docker compose exec -T backend php artisan --version >/dev/null 2>&1; do
    sleep 3
  done
fi

# 6. Schéma de base de données (n'applique que les migrations en attente)
docker compose exec -T backend php artisan migrate --force

echo ""
echo "✅ DataShare est lancé !"
echo "   SPA  : http://localhost:5173"
echo "   API  : http://localhost:8000/api/v1"
