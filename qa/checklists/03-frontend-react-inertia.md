# 03 — Frontend y Experiencia de Usuario (React 19 + Inertia.js 3 + Tailwind v4)

- [x] **Límite de tamaño de vistas < 500 lín (obligatorio)**: Ningún componente de página en `resources/js/Pages/` o layout en `resources/js/Layouts/` debe superar las 500 líneas de código (crítico > 800 líneas).
- [x] **Modularidad y subcomponentes (`Partials` / `Components`)**: Pantallas complejas descomponen sus modales, tablas, tarjetas móviles y pasos de wizard en subcomponentes enfocados y reutilizables.
- [x] **Completar las 3 pantallas pendientes de migración a React**:
  - Migrar Firma Pública de Constancia (`/constancia/firmar/{token}`) a `Pages/Public/SignCertificate.jsx`.
  - Migrar Centro de Despacho de Asignaciones (`/gestor/assignments`) a `Pages/Gestor/Assignments/Index.jsx`.
  - Migrar Configuración de Branding de Consultora (`/gestor/administration/branding`) a `Pages/Gestor/Branding/Edit.jsx`.
- [x] **Uso seguro de `usePage().props` y `auth`**: Destructurar `const { auth } = usePage().props` con optional chaining (`auth?.user`) para prevenir errores de ejecución en vistas compartidas o públicas.
- [x] **Manejo de formularios con `useForm` de Inertia**: Uso de `processing` para deshabilitar botones de envío y evitar registros duplicados; uso de `form.transform(...)` inmutable antes de enviar flags de acción (`approve`, `observe`, `finalize`).
- [x] **Coherencia cromática por pilar (Regla Visual HUD)**: Naranja (`ep-primary` / `orange-500`) para Checklists y Contrato Anual; Azul/Índigo (`indigo-500`) para Capacitaciones y Hallazgos; Rojo (`red-500`) para Incidentes; Esmeralda (`emerald-500`) para estados aprobados/operativos.
- [x] **Responsividad Mobile-First y transición Tabla → Card (`lg:block hidden` / `lg:hidden block`)**: En resoluciones menores a `1024px`, las tablas anchas se transforman en tarjetas táctiles verticales, y las grillas matriciales aplican `.custom-scrollbar` y columnas fijas adaptables.
- [x] **Soporte de campo para Técnicos (PWA + Respaldo Local)**: Configuración de `manifest.json` instalable y respaldo en `localStorage` / `IndexedDB` de las respuestas en curso en `Wizard.jsx` ante cortes momentáneos de señal en planta.
- [x] **Compilación limpia con Vite y verificación**: `npm run build` ejecuta sin errores ni referencias a componentes inexistentes (`eslint` no está instalado en `node_modules`).

## Hallazgos de la medición (Vistas sobre umbral > 500 lín)

- [x] **Dividir `resources/js/Pages/Tecnico/MyAssignments.jsx` (1317 lín · 🔴 Crítico)**: Extraer las pestañas de Inspecciones, Capacitaciones, Informes y Plan Anual, los filtros superiores y las tarjetas de misión en subcomponentes dentro de `Pages/Tecnico/Partials/`.
- [x] **Dividir `resources/js/Pages/Gestor/Assets/Index.jsx` (1048 lín · 🔴 Crítico)**: Extraer el árbol jerárquico L1/L2 (`grupo`/`subgrupo`), los tooltips de alertas SRE y el panel lateral de edición rápida en componentes dedicados (`Assets/Partials/`).
- [x] **Dividir `resources/js/Pages/Tecnico/Inspections/Wizard.jsx` (1014 lín · 🔴 Crítico)**: Extraer el Paso 0 (selección de activo y modal ámbar de re-inspección), el renderizador de preguntas por tipo, el detector de falla crítica y el paso de firmas en subcomponentes (`Inspections/Partials/`).
- [x] **Dividir `resources/js/Pages/Gestor/Assets/Types/Index.jsx` (1001 lín · 🔴 Crítico)**: Extraer el constructor de esquema de metadata (`MetadataSchemaBuilder`), el configurador de reglas SRE y el configurador de esquema de fotos (`PhotoSchemaBuilder`) en partials separados.
- [x] **Dividir `resources/js/Pages/Gestor/Assets/Create.jsx` (837 lín · 🔴 Crítico)**: Extraer el formulario dinámico de alta multi-activo y la barra lateral de previsualización de grupos/subgrupos en subcomponentes.
- [x] **Dividir `resources/js/Pages/Admin/Consultants/Edit.jsx` (793 lín · 🟡 Sobre umbral)**: Extraer el formulario de límites/suscripción, la configuración del Gestor Principal y la línea de tiempo de Auditoría (`AuditTimeline`) en `Consultants/Partials/`.
- [x] **Dividir `resources/js/Pages/Gestor/MasterReports/Wizard.jsx` (675 lín · 🟡 Sobre umbral)**: Separar las secciones del informe (visitas, checklists, capacitaciones, hallazgos, matriz y evaluación del gestor) y la botonera flotante en partials.
- [x] **Dividir `resources/js/Pages/Gestor/Companies/Locations.jsx` (667 lín · 🟡 Sobre umbral)**: Extraer el formulario de sedes (días operativos, horarios, Roster 24/7 y coordenadas GPS) y el modal de asignación de usuarios en subcomponentes.
- [x] **Dividir `resources/js/Pages/Gestor/Contracts/Partials/AnnualPlanMatrix.jsx` (652 lín · 🟡 Sobre umbral)**: Extraer las filas de categoría, celdas mensuales interactivas y modales de programación de fechas en componentes más pequeños.
- [x] **Dividir `resources/js/Pages/Tecnico/Visits/InProgress.jsx` (645 lín · 🟡 Sobre umbral)**: Extraer el resumen de actividades ejecutadas en la visita y el modal de cierre/firma (`VisitCheckoutModal`) en partials.
- [x] **Dividir `resources/js/Pages/Gestor/Assets/Show.jsx` (625 lín · 🟡 Sobre umbral)**: Extraer la galería de fotos, el historial de inspecciones del activo y la bitácora `asset_metadata_logs` en subcomponentes.
- [x] **Dividir `resources/js/Pages/Gestor/Users.jsx` (596 lín · 🟡 Sobre umbral)**: Extraer el modal de creación/edición de usuario y el modal de clave temporal generada en partials junto a `PendingSignaturesList.jsx`.
- [x] **Dividir `resources/js/Pages/Tecnico/Trainings/Session.jsx` (574 lín · 🟡 Sobre umbral)**: Extraer la carga nominal de asistentes con firma y el bloque de firma del instructor especialista en subcomponentes.
- [x] **Dividir `resources/js/Pages/Gestor/Checklists/Builder.jsx` (569 lín · 🟡 Sobre umbral)**: Extraer el editor de secciones y el configurador de lógica de falla de preguntas (`failure_values` / `vincular_a_metadata`) en partials.
