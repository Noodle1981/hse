# 10 — Documentación Técnica y de Negocio

- [ ] **Actualizar `README.md` principal a la versión V2.3 (React 19 + Inertia 3)**: Reemplazar la descripción obsoleta de `V1.2.0.0` (stack TALL / Livewire de Diciembre 2024) en `README.md` para reflejar la arquitectura real actual (Laravel 12 + Inertia.js 3 + React 19 + Tailwind CSS 4 + Multi-Tenancy + Motor de Activos SRE).
- [ ] **Frescura de la documentación de arquitectura**: Mantener sincronizados `.agent/workflows/notebooklm/arquitectura.md`, `context.md`, `base_de_datos.md` y `prd.md` con los últimos cambios del código fuente. *(Medición actual: 1 commit desde la última actualización).*
- [ ] **Variables de entorno (`.env.example`) completas**: Documentar en `.env.example` todas las variables requeridas por el proyecto (incluyendo `MYSQL_ATTR_SSL_CA` y variables de branding) sin exponer credenciales reales.
- [ ] **Guía de instalación y puesta en marcha verificada**: Instrucciones paso a paso probadas en PHP 8.4/8.5 con `composer install`, `npm install`, `php artisan migrate --seed` y `npm run dev`.
- [ ] **Manuales y Centro de Ayuda In-App (`HelpCenterController`)**: Verificar que los tutoriales descargables en PDF (`/tutoriales/{key}/pdf`) estén actualizados con las pantallas actuales del sistema.
