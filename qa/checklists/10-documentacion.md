# 10 — Documentación Técnica y de Negocio

- [x] **Actualizar `README.md` principal a la versión V2.3 (React 19 + Inertia 3)**: Reemplazada la descripción obsoleta de `V1.2.0.0` en `README.md` para reflejar la arquitectura actual (Laravel 12 + Inertia.js 3 + React 19 + Tailwind CSS 4 + Multi-Tenancy + Motor de Activos SRE).
- [x] **Frescura de la documentación de arquitectura**: Verificado `.agent/workflows/notebooklm/arquitectura.md` actualizado con el stack Laravel 12 + React 19 + Inertia.js + Tailwind v4.
- [x] **Variables de entorno (`.env.example`) completas**: Documentadas en `.env.example` las variables requeridas (incluyendo `MYSQL_ATTR_SSL_CA` y variables `BRAND_*`) sin exponer credenciales reales.
- [x] **Guía de instalación y puesta en marcha verificada**: Instrucciones paso a paso probadas con `composer install`, `npm install`, `php artisan migrate --seed` y `npm run build` / `npm run dev` documentadas en `README.md`.
- [x] **Manuales y Centro de Ayuda In-App (`HelpCenterController`)**: Verificado `HelpCenterController` con los 5 manuales y procedimientos descargables en PDF (`/tutoriales/{key}/pdf`).
