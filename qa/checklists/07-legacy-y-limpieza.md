# 07 — Legacy, Limpieza y Mantenibilidad

- [x] **Eliminar carpeta `resources/views/resources - legacy/`**: Tras migrar las 3 pantallas faltantes (`public-signature`, `assignment-manager` y `company-branding`) a React, borradas las 42 vistas Blade/Livewire antiguas almacenadas en `resources/views/resources - legacy/`.
- [x] **Remover `livewire/livewire` de `composer.json`**: Desinstalado el paquete `livewire/livewire` que ya no forma parte del stack oficial React + Inertia.
- [x] **Eliminar copias de bases de datos y archivos basura versionados**: Borrados `database/database copy.sqlite` y `--help` de la raíz del proyecto, agregando `*.sqlite` al `.gitignore`.
- [x] **Parametrización de Marca Blanca (`BrandingService` y vistas)**: Reemplazados nombres, correos, teléfonos y WhatsApp hardcodeados en `BrandingService.php` y `welcome.blade.php` por variables configurables en `config/app.php` (`app.branding.*`) y `.env`.
- [x] **Unificar dependencias duplicadas en `composer.json`**: Mantenida una sola librería de PDF (`barryvdh/laravel-dompdf`) y una sola de lectura Excel (`shuchkin/simplexlsx`), removiendo `spatie/laravel-pdf`, `maatwebsite/excel`, `phpoffice/phpspreadsheet` y `shuchkin/simplexlsxgen`.
- [x] **Código libre de `// TODO` y rutas huérfanas**: Sin `TODO` en `routes/web.php` y eliminadas las vistas Blade huérfanas en `resources/views/master-reports/`.
- [x] **Sin artefactos temporales en el repositorio**: `public/hot`, `storage/logs/*.log` y `bootstrap/cache/*.tmp` excluidos en `.gitignore`.
