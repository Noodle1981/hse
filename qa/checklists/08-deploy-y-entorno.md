# 08 — Deploy y Entorno (Laravel 12 + Vite 6 + PHP 8.4/8.5)

- [ ] **Compilación reproducible de Frontend y Backend**: `composer install --no-dev --optimize-autoloader` y `npm ci && npm run build` ejecutan sin errores ni advertencias críticas.
- [ ] **Variables de entorno obligatorias definidas**: `.env` productivo configurado con `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, credenciales de base de datos, correo SMTP y variables de marca.
- [ ] **Enlace simbólico de almacenamiento (`storage:link`)**: `php artisan storage:link` ejecutado y verificado para servir logos de consultoras (`storage/logos/`), firmas (`storage/signatures/`) y evidencias fotográficas (`storage/hallazgos/`, `storage/incidentes/`).
- [ ] **Límites de subida de imágenes móviles en `php.ini` / Nginx**: Configuración verificada con `upload_max_filesize >= 20M` y `post_max_size >= 20M` para soportar fotos de cámaras de celulares en campo.
- [ ] **Caché de optimización de Laravel en producción**: Ejecución de `php artisan config:cache`, `php artisan route:cache` y `php artisan view:cache` en el script de despliegue.
- [ ] **Ejecución programada de alertas SRE (`schedule:run`)**: Cron del servidor configurado para ejecutar `php artisan schedule:run` (incluyendo el comando `CheckAssetAlerts` para vencimientos de activos).
- [ ] **Worker de colas activo (`queue:work`)**: Proceso supervisor configurado para procesar jobs en segundo plano como `ImportEmployeesJob` y envíos de correo.
- [ ] **Respaldo periódico de Base de Datos y `storage/app/public`**: Política de backup automatizado para la base de datos y la carpeta de firmas/fotos de auditoría legal.
