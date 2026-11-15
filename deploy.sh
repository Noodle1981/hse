#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# HSE SaaS — Script de Despliegue a Producción (Laravel 12 + Vite + React 19)
# ==============================================================================
# Requisitos de entorno / servidor:
#   - PHP >= 8.2 con php.ini: upload_max_filesize >= 20M, post_max_size >= 20M
#   - Nginx: client_max_body_size 20M;
#   - Cron: * * * * * cd /var/www/hse && php artisan schedule:run >> /dev/null 2>&1
#     (incluye `assets:check-alerts` diario a las 07:00)
#   - Supervisor: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
#   - Backup diario: Base de datos + `storage/app/public` (firmas/evidencias)
# ==============================================================================

echo ">> [1/7] Activando modo mantenimiento..."
php artisan down --retry=60 || true

trap 'php artisan up || true' EXIT

echo ">> [2/7] Instalando dependencias PHP (producción)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ">> [3/7] Instalando dependencias Node y compilando assets..."
npm ci
npm run build

echo ">> [4/7] Ejecutando migraciones..."
php artisan migrate --force

echo ">> [5/7] Verificando enlace simbólico de almacenamiento..."
php artisan storage:link || true

echo ">> [6/7] Generando cachés de producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> [7/7] Reiniciando workers de cola..."
php artisan queue:restart

echo ">> Despliegue completado con éxito."
