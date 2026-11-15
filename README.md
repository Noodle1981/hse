# XamaSafe / HSE SaaS — Plataforma Integral de Seguridad e Higiene

> **Versión:** `v2.3.0`  
> **Stack:** Laravel 12 + Inertia.js 3 + React 19 + Tailwind CSS 4  
> **Estado:** Pre-Producción Verificada

Plataforma SaaS multi-tenant para la gestión operativa, auditoría y cumplimiento normativo en Higiene y Seguridad en el Trabajo (HSE), con operación de campo mobile-first, trazabilidad GPS, firmas digitales y motor de activos SRE.

---

## Características Principales

- **Multi-Tenancy Aislado por Consultora (`consultant_id`)**: Gestión independiente de empresas cliente, sedes (`locations`), personal (`employees`) y usuarios.
- **Visitas e Inspecciones en Campo**: Flujo completo de inicio/cierre con geolocalización GPS, checklists dinámicos y generación automática de certificados/informes en PDF (`DomPDF`).
- **Hallazgos e Incidentes**: Registro rápido en terreno con evidencias fotográficas y seguimiento hasta su cierre.
- **Capacitaciones y Asistencia**: Registro de sesiones y captura de firmas digitales de asistentes (`signature_pad`).
- **Motor de Activos SRE**: Control de vencimientos, bloqueos y alertas programadas (`assets:check-alerts`).
- **Planes Anuales e Informes Maestros**: Planificación anual por sede/empresa y consolidación ejecutiva de reportes.
- **White-Label / Branding**: Personalización visual por consultora con fallback centralizado en `config('app.branding.*')`.
- **Documentación API OpenAPI + Scalar**: Especificación en `public/openapi.json` y visor `/docs` protegido para `super_admin`.
- **Centro de Ayuda In-App**: 5 manuales y procedimientos descargables en PDF vía `HelpCenterController` (`/tutoriales/{key}/pdf`).

---

## Stack Tecnológico

| Capa | Tecnología | Versión |
|------|------------|---------|
| Backend | PHP + Laravel Framework | PHP >= 8.2 (probado en 8.4/8.5) · Laravel 12 |
| Puente SPA | Inertia.js + Ziggy | `inertiajs/inertia-laravel` 3.0 · `tightenco/ziggy` 2.6 |
| Frontend | React + Vite + Tailwind CSS | React 19 · Vite 8 · Tailwind CSS 4 |
| Base de Datos | SQLite (dev/test) / MySQL (prod) | SQLite 3 / MySQL 8 |
| Autenticación & API | Sesiones Web + Laravel Sanctum | `laravel/sanctum` 4.0 |
| Reportes & Utilidades | DomPDF · SimpleXLSX · SimpleQRCode | `barryvdh/laravel-dompdf` 3.1 |

---

## Requisitos

- **PHP >= 8.2** (compatible con PHP 8.4 / 8.5) con extensiones estándar de Laravel (`pdo_sqlite` / `pdo_mysql`, `mbstring`, `gd`, `zip`, `fileinfo`)
- **Composer 2.x**
- **Node.js >= 20** y **npm**

---

## Instalación y Puesta en Marcha (Local)

```bash
# 1. Clonar el repositorio
git clone https://github.com/Noodle1981/hse.git
cd hse

# 2. Instalar dependencias PHP y JS
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Crear base de datos SQLite (si usas SQLite en local)
# Linux/Mac: touch database/database.sqlite
# Windows PowerShell: New-Item database/database.sqlite -ItemType File
touch database/database.sqlite

# 5. Enlace simbólico para archivos públicos (logos, firmas, evidencias)
php artisan storage:link

# 6. Ejecutar migraciones y seeders iniciales
php artisan migrate --seed

# 7. Compilar assets o iniciar servidor de desarrollo
npm run dev
# En otra terminal:
php artisan serve
```

> Para iniciar servidor HTTP, queue listener y Vite en un solo comando: `composer run dev`.

---

## Pruebas y Calidad

```bash
# Ejecutar suite de pruebas PHPUnit (SQLite en memoria — 56 tests / 163 aserciones)
php artisan test

# Compilación de producción frontend
npm run build
```

---

## Despliegue a Producción

El script [`deploy.sh`](deploy.sh) automatiza el flujo de despliegue:

1. `composer install --no-dev --optimize-autoloader --no-interaction`
2. `npm ci && npm run build`
3. `php artisan migrate --force`
4. `php artisan storage:link`
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. `php artisan queue:restart`

Asegurar en el servidor productivo:
- `.env` con `APP_ENV=production`, `APP_DEBUG=false`, credenciales reales de BD/SMTP y `BRAND_*`.
- `php.ini` con `upload_max_filesize >= 20M` y `post_max_size >= 20M` (y `client_max_body_size 20M;` en Nginx).
- Cron ejecutando `php artisan schedule:run` cada minuto (`assets:check-alerts` corre diariamente a las `07:00`).
- Supervisor manteniendo activo `php artisan queue:work` y respaldos periódicos de base de datos + `storage/app/public`.
