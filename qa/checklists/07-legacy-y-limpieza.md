# 07 — Legacy, Limpieza y Mantenibilidad

- [ ] **Eliminar carpeta `resources/views/resources - legacy/`**: Tras migrar las 3 pantallas faltantes (`public-signature`, `assignment-manager` y `company-branding`) a React, borrar las 42 vistas Blade/Livewire antiguas almacenadas en `resources/views/resources - legacy/`.
- [ ] **Remover `livewire/livewire` de `composer.json`**: Desinstalar el paquete `livewire/livewire` (`^3.7`) que ya no forma parte del stack oficial React + Inertia y que aún inyecta middlewares en cada petición web.
- [ ] **Eliminar copias de bases de datos y archivos basura versionados**: Borrar `database/database copy.sqlite` y el archivo vacío/basura `--help` de la raíz del proyecto, agregando `*.sqlite` al `.gitignore`.
- [ ] **Parametrización de Marca Blanca (`BrandingService` y vistas)**: Reemplazar nombres, correos y teléfonos hardcodeados (`XAMASAFE`, `contacto@xamasafe.com`, `EP-Back`, WhatsApp fijo en `welcome.blade.php`) por variables configurables en `config/app.php` y `.env`.
- [ ] **Unificar dependencias duplicadas en `composer.json`**: Mantener una sola librería de generación de PDF (`barryvdh/laravel-dompdf` o `spatie/laravel-pdf`) y una sola librería de hojas de cálculo Excel (`maatwebsite/excel` vs `shuchkin/simplexlsx*`).
- [ ] **Código libre de `// TODO` y rutas huérfanas**: Resolver los bloques comentados y `TODO` en `routes/web.php` (líneas 45, 152 y 240) y limpiar vistas Blade en `resources/views/master-reports/` si ya fueron reemplazadas por `Pages/Gestor/MasterReports/`.
- [ ] **Sin artefactos temporales en el repositorio**: Mantener `public/hot`, `storage/logs/*.log` y `bootstrap/cache/*.tmp` excluidos del control de versiones.
