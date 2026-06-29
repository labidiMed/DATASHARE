# Démarre DataShare : configuration (1re fois) + lancement de la stack Docker.
# Usage : .\start.ps1   (ou clic droit > Exécuter avec PowerShell)

Set-Location $PSScriptRoot
Write-Host "Demarrage de DataShare..."

# 1. Docker doit tourner
docker info 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Docker ne repond pas. Lance Docker Desktop puis reessaie." -ForegroundColor Red
    exit 1
}

# 2. Fichiers d'environnement
if (-not (Test-Path .env)) { Copy-Item .env.example .env; Write-Host ".env cree depuis .env.example" }
if (-not (Test-Path backend/.env)) { Copy-Item backend/.env.example backend/.env; Write-Host "backend/.env cree" }

# 3. Construction + lancement
docker compose up -d --build

# 4. Attendre que le backend soit pret
Write-Host "Attente du backend..."
do {
    docker compose exec -T backend php artisan --version 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) { break }
    Start-Sleep -Seconds 3
} while ($true)

# 5. Cles applicatives (si absentes)
$needRestart = $false
if (-not (Select-String -Path backend/.env -Pattern '^APP_KEY=base64:' -Quiet)) {
    docker compose exec -T backend php artisan key:generate
    $needRestart = $true
}
if (-not (Select-String -Path backend/.env -Pattern '^JWT_SECRET=.+' -Quiet)) {
    docker compose exec -T backend php artisan jwt:secret --force
    $needRestart = $true
}
if ($needRestart) {
    docker compose restart backend scheduler
    do {
        docker compose exec -T backend php artisan --version 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) { break }
        Start-Sleep -Seconds 3
    } while ($true)
}

# 6. Migrations
docker compose exec -T backend php artisan migrate --force

Write-Host ""
Write-Host "DataShare est lance !" -ForegroundColor Green
Write-Host "   SPA : http://localhost:5173"
Write-Host "   API : http://localhost:8000/api/v1"
