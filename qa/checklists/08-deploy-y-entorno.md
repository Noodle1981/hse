# 08 — Deploy y Entorno (Laravel 12 + Vite 6 + PHP 8.4/8.5)

- [x] **Compilación reproducible de Frontend y Backend**: `composer install --no-dev --optimize-autoloader` y `npm ci && npm run build` ejecutan sin errores ni advertencias críticas.
- [x] **Variables de entorno obligatorias definidas**: `.env` productivo configurado con `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, credenciales de base de datos, correo SMTP y variables de marca (`BRAND_*` documentadas en `.env.example`).
- [x] **Enlace simbólico de almacenamiento (`storage:link`)**: `php artisan storage:link` ejecutado y verificado (`public/storage` presente) para servir logos de consultoras (`storage/logos/`), firmas (`storage/signatures/`) y evidencias fotográficas (`storage/hallazgos/`, `storage/incidentes/`).
- [x] **Límites de subida de imágenes móviles en `php.ini` / Nginx**: Documentado en `deploy.sh` (`upload_max_filesize >= 20M`, `post_max_size >= 20M`, Nginx `client_max_body_size 20M;`) para el servidor productivo (en entorno local CLI: `2M`/`8M`).
- [x] **Caché de optimización de Laravel en producción**: Ejecución de `php artisan config:cache`, `php artisan route:cache` y `php artisan view:cache` verificada sin errores e incluida en `deploy.sh`.
- [x] **Ejecución programada de alertas SRE (`schedule:run`)**: `Schedule::command('assets:check-alerts')->dailyAt('07:00')` definido en `routes/console.php` y cron documentado en `deploy.sh`.
- [x] **Worker de colas activo (`queue:work`)**: `QUEUE_CONNECTION=database` configurado, `php artisan queue:restart` en `deploy.sh` y comando Supervisor documentado.
- [x] **Respaldo periódico de Base de Datos y `storage/app/public`**: Política de backup automatizado documentada en `deploy.sh` para la base de datos y la carpeta de firmas/fotos de auditoría legal.
