#!/usr/bin/env bash
#
# Lance les tests de DataShare.
#   bash test.sh          → tests back-end (PHPUnit + couverture)
#   bash test.sh --e2e    → ajoute les tests end-to-end (Cypress)
#
# Prérequis : la stack doit tourner (bash start.sh).
#
set -e
cd "$(dirname "$0")"

echo "🧪 Tests back-end (PHPUnit)..."
docker compose exec -T backend php artisan test --coverage

if [ "$1" = "--e2e" ]; then
  echo ""
  echo "🧪 Tests end-to-end (Cypress)..."

  HOSTDIR="$(pwd -W 2>/dev/null || pwd)/frontend"
  PROJECT="$(basename "$(pwd)" | tr '[:upper:]' '[:lower:]')"

  # Serveur front temporaire dont l'API est joignable depuis le conteneur Cypress
  docker rm -f ds-e2e-front >/dev/null 2>&1 || true
  MSYS_NO_PATHCONV=1 docker run -d --name ds-e2e-front \
    -v "${HOSTDIR}:/app" -v "${PROJECT}_frontend-node-modules:/app/node_modules" \
    -e VITE_API_URL=http://host.docker.internal:8000/api/v1 \
    -w /app -p 5180:5173 node:22-alpine sh -c "npm run dev -- --host 0.0.0.0" >/dev/null

  echo "⏳ Attente du serveur front e2e..."
  until curl -sf http://localhost:5180 >/dev/null 2>&1; do sleep 2; done

  set +e
  MSYS_NO_PATHCONV=1 docker run --rm \
    -v "${HOSTDIR}:/e2e" -w /e2e \
    -e CYPRESS_baseUrl=http://host.docker.internal:5180 \
    -e CYPRESS_apiUrl=http://host.docker.internal:8000/api/v1 \
    cypress/included:15.6.0
  E2E_CODE=$?
  set -e

  docker rm -f ds-e2e-front >/dev/null 2>&1 || true
  [ "$E2E_CODE" -ne 0 ] && { echo "❌ Tests e2e en échec"; exit "$E2E_CODE"; }
fi

echo ""
echo "✅ Tests terminés avec succès."
