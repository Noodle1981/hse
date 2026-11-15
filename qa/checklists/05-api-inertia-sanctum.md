# 05 — Contratos Inertia.js, Endpoints Internos y API Sanctum

- [x] **Sincronización de rutas Ziggy ↔ `routes/web.php`**: Toda llamada `route('...')` en los componentes React de `resources/js/` corresponde a una ruta existente y activa en `routes/web.php`, con parámetros coincidentes.
- [x] **Consolidación de rutas compartidas Gestor / Técnico**: Evitar duplicación innecesaria de definiciones entre los prefijos `/gestor/...` y `/tecnico/...` cuando apuntan al mismo controlador y método.
- [x] **Protección de endpoints JSON internos (`/gestor/api/*`)**: Los endpoints JSON consumidos por modales React (`/gestor/api/companies/{empresa}/sites` y `/gestor/api/technicians/{user}/assignments`) validan pertenencia del recurso a la `consultora_id` del usuario autenticado (`AdministrationController.php`).
- [x] **Validación estricta de payloads (Anti Mass-Assignment)**: Peticiones `POST`/`PUT` validadas con reglas explícitas antes de persistir, rechazando campos no autorizados con HTTP `422`.
- [x] **Especificación OpenAPI y visor Scalar**: Contrato `public/openapi.json` creado documentando los endpoints JSON/API (`/api/*` y `/gestor/api/*`) y visor interactivo **Scalar** (`resources/views/scalar.blade.php`) montado en `/docs`.
- [x] **Protección de documentación en producción**: El visor `/docs` requiere autenticación y rol `super_admin` (`middleware(['auth', 'role:super_admin'])`).
