# 05 — Contratos Inertia.js, Endpoints Internos y API Sanctum

- [ ] **Sincronización de rutas Ziggy ↔ `routes/web.php`**: Toda llamada `route('...')` en los componentes React de `resources/js/` corresponde a una ruta existente y activa en `routes/web.php`, con parámetros coincidentes.
- [ ] **Consolidación de rutas compartidas Gestor / Técnico**: Evitar duplicación innecesaria de definiciones entre los prefijos `/gestor/...` y `/tecnico/...` cuando apuntan al mismo controlador y método.
- [ ] **Protección de endpoints JSON internos (`/gestor/api/*`)**: Los endpoints JSON consumidos por modales React (`/gestor/api/companies/{empresa}/sites` y `/gestor/api/technicians/{user}/assignments`) validan pertenencia del recurso a la `consultora_id` del usuario autenticado.
- [ ] **Validación estricta de payloads (Anti Mass-Assignment)**: Peticiones `POST`/`PUT` validadas con reglas explícitas antes de persistir, rechazando campos no autorizados con HTTP `422`.
- [ ] **Especificación OpenAPI y visor Scalar**: Inicializar contrato `public/openapi.json` documentando los endpoints JSON/API (`/api/*` y `/gestor/api/*`) y montar el visor interactivo **Scalar** en una ruta protegida (`/docs` o `/scalar`) para el equipo y futuras integraciones móviles.
- [ ] **Protección de documentación en producción**: El visor de contratos OpenAPI/Scalar debe requerir autenticación de usuario global (`super_admin`) en entornos productivos.
