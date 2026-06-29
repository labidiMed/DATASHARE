#!/usr/bin/env bash
#
# Arrête DataShare (les volumes, donc les données, sont conservés).
# Usage : bash stop.sh
#
cd "$(dirname "$0")"
docker compose down
echo "🛑 DataShare arrêté (données conservées dans les volumes)."
