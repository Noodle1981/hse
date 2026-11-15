# Registro de Deuda Técnica (fuente de verdad)

Los IDs **no cambian** entre auditorías. Estados: Abierto · En progreso · Parcial · Resuelto · Discutido · Riesgo aceptado · Regresión.
Alcance vigente: ver `alcance.md`. Última actualización: 2026-11-15 · commit `3abc3f2`.

| ID | Sev. | Área | Título | Regla | Detectado | Estado | Cierre | Tarea |
|---|---|---|---|---|---|---|---|---|
| AUD-001 | Alta | Seguridad / Multi-Tenant | Fuga de alcance Multi-Tenant en listado de empresas (`AdministrationController.php:109`) y visitas (`OperationController.php:40`) | Control horizontal (BOLA / IDOR) — `01-seguridad.md` | 2026-11-15 | Resuelto | 2026-11-15 | Filtrar `$companies` y `VisitCertificate::query()` por `consultora_id` del usuario autenticado (salvo `isSuperAdmin()`). |
| AUD-002 | Alta | Frontend / Backend | 3 flujos en `routes/web.php` pendientes de migración a React (`constancia.firmar` L44-47, `gestor.assignments` L151-153, `branding` L240-241) | Completitud funcional — `03-frontend-react-inertia.md` | 2026-11-15 | Resuelto | 2026-11-15 | Crear `Pages/Public/SignCertificate.jsx`, `Pages/Gestor/Assignments/Index.jsx` y `Pages/Gestor/Branding/Edit.jsx` con sus endpoints en controlador. |
| AUD-003 | Media | Backend (Laravel) | 11 controladores superan el umbral de 200 líneas (8 críticos > 300 lín, liderados por `AdministrationController.php:1-1331` y `OperationController.php:1-1096`) y 21 acceden directo a Eloquent/DB | Thin Controllers y separación de capas — `02-backend-laravel.md` | 2026-11-15 | Resuelto | 2026-11-15 | Dividir controladores monolíticos por dominio y mover consultas/transacciones a `app/Services/`. |
| AUD-004 | Media | Frontend (React) | 14 vistas React superan el umbral de 500 líneas (5 críticas > 800 lín, lideradas por `MyAssignments.jsx:1-1317`, `Assets/Index.jsx:1-1048` y `Inspections/Wizard.jsx:1-1014`) | Límite de tamaño de vistas y modularidad — `03-frontend-react-inertia.md` | 2026-11-15 | Resuelto | 2026-11-15 | Extraer pestañas, modales, árboles L1/L2 y pasos de wizard en subcomponentes dentro de `Partials/` o `Components/`. |
| AUD-005 | Media | Legacy y Limpieza | Residuos de Livewire (`composer.json:18`), 42 vistas viejas en `resources/views/resources - legacy/`, `database/database copy.sqlite`, archivo `--help` y librerías duplicadas de PDF/Excel | Sin tecnologías deprecadas ni copias locales — `07-legacy-y-limpieza.md` | 2026-11-15 | Abierto | — | Eliminar `resources - legacy/`, remover `livewire/livewire`, borrar archivos basura y unificar librerías de PDF/Excel. |
| AUD-006 | Baja | Documentación y Git | `README.md:1-369` documenta stack antiguo TALL v1.2 (2024) en lugar de React 19 + Inertia 3 v2.3, y el repositorio posee `0 tags` de versión | Frescura de documentación y etiquetado SemVer — `09-control-de-versiones.md` / `10-documentacion.md` | 2026-11-15 | Abierto | — | Actualizar `README.md` al stack actual V2.3 y crear tag `v2.3.0`. |

## Notas privadas para fases futuras (no se auditan hoy)
- Incorporar soporte **PWA instalable + autoguardado local (`localStorage`/`IndexedDB`)** en `Pages/Tecnico/Inspections/Wizard.jsx` para operación en plantas con baja señal.
- Parametrizar el nombre del producto y datos de contacto en `app/Services/BrandingService.php` y `resources/views/welcome.blade.php` desde `.env` para rebranding instantáneo.

## Historial de auditorías
| # | Fecha | Commit | Alcance | Semáforo | Críticas | Altas | Medias | Bajas | Resueltas |
|---|---|---|---|---|---|---|---|---|---|
| 1 | 2026-11-15 | `3abc3f2` | Setup inicial QA completo sobre HSE SaaS (`pre-produccion`) | 🟡 GO con riesgos | 0 | 2 | 3 | 1 | 1 (Fix PHP 8.5 `PDO::MYSQL_ATTR_SSL_CA` + 5 migraciones) |
