---
description: Contexto del proyecto XamaSafe (Stack React + Inertia.js)
---

# 📖 Contexto de XamaSafe

## 🎯 Visión General

**Nombre:** XamaSafe
**Versión Actual:** V2.3.0.0 (Premium HUD)
**Tipo:** Sistema SaaS integral de Higiene y Seguridad
**Stack:** Laravel 12 + Inertia.js (React 19) + Tailwind CSS v4 + SQLite (dev) + Ziggy JS

### Propósito
Sistema escalable de checklists que permite crear, gestionar y ejecutar formularios dinámicos para:
- ✅ Higiene y Seguridad (HyS)
- ✅ Normas de Calidad (ISO, BPA)
- ✅ Seguimientos operativos
- ✅ Auditorías ambientales
- ✅ Cualquier proceso que requiera verificación por checklist

---

## 🏗️ Arquitectura del Sistema

### Patrón de Diseño
```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                        │
│   React 19 + Inertia.js (React) + Tailwind CSS v4           │
├─────────────────────────────────────────────────────────────┤
│                    APPLICATION LAYER                         │
│   Laravel 12 Controllers + Form Requests + Policies        │
├─────────────────────────────────────────────────────────────┤
│                    DOMAIN LAYER                              │
│   Eloquent Models + Relationships + Scopes                   │
├─────────────────────────────────────────────────────────────┤
│                    INFRASTRUCTURE LAYER                      │
│   Migrations + Seeders + Factories + SQLite/MySQL            │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Carpetas
```
ep-back/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controladores (devuelven Inertia::render)
│   │   └── Requests/          # Form Requests (validación)
│   ├── Models/                # Modelos Eloquent
│   ├── Policies/              # Políticas de autorización
│   └── Services/              # Lógica de negocio
├── database/
│   ├── migrations/            # Esquema de BD
│   ├── factories/             # Factories para testing
│   └── seeders/               # Datos de prueba
├── resources/
│   ├── js/
│   │   ├── Layouts/           # Layouts de React (e.g. AuthenticatedLayout.jsx)
│   │   ├── Pages/             # Páginas React resueltas por Inertia.js
│   │   │   ├── Gestor/        # Consola de mando, Usuarios, Técnicos, Corporativo
│   │   │   ├── Tecnico/       # Herramientas de campo, Misiones e Inspecciones
│   │   │   ├── Auth/          # Login, Registro y Recuperación de clave
│   │   │   ├── Profile/       # Gestión de perfiles y firmas
│   │   │   └── Admin/         # Módulos de administración
│   │   ├── app.jsx            # Inicializador principal de Inertia con React
│   │   └── bootstrap.js       # Inicializador de Axios
│   ├── css/
│   │   └── app.css            # Configuración de Tailwind CSS v4 y variables CSS custom
│   └── views/
│       └── app.blade.php      # Layout raíz de HTML para Inertia
├── routes/
│   └── web.php                # Rutas web registradas con Ziggy y mapeadas a controladores
└── tests/
    ├── Feature/               # Tests de integración
    └── Unit/                  # Tests unitarios
```

---

## 🗃️ Modelo de Datos (Checklists Dinámicos)

### Entidades Principales

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│  Checklist  │──1:N─│  Question   │      │    User     │
│  (Plantilla)│      │ (Preguntas) │      │ (Ejecutor)  │
└──────┬──────┘      └──────┬──────┘      └──────┬──────┘
       │                    │                     │
       │   ┌────────────────┼─────────────────────┘
       │   │                │
       ▼   ▼                ▼
┌─────────────┐      ┌─────────────┐
│  Inspection │──1:N─│   Answer    │
│ (Ejecución) │      │ (Respuesta) │
└─────────────┘      └─────────────┘
```

### Descripción de Tablas

| Tabla | Propósito | Campos Clave |
|-------|-----------|--------------|
| `asset_types` | Tipos de Checklist Dinámicos: unificadores de atributos de ficha técnica vinculados a un checklist operativo. Ámbito de empresa (no de sede). | `id`, `empresa_id`, `name`, `category_id`, `metadata_schema` (JSON), `photo_schema` (JSON), `group_label`, `subgroup_label` |
| `assets` | Ficha del activo físico con identificador/QR único y metadata dinámica. Agrupable jerárquicamente en dos niveles (Grupo/Subgrupo) y configurable en caliente por sede. | `id`, `empresa_id`, `sede_id`, `asset_type_id`, `name`, `identifier`, `status`, `grupo`, `subgrupo`, `metadata` |
| `audit_logs` | Registro de auditoría inmutable de cambios de accesos | `id`, `user_id`, `action`, `target_type`, `target_id`, `payload` |
| `consultoras` | Empresas consultoras de HyS que agrupan usuarios y clientes | `id`, `nombre`, `cuit`, `max_tecnicos`, `max_gestores`, `max_empresas`, `estado_suscripcion` |
| `consultora_suscripcion_logs` | Historial de transiciones de estado de suscripción de consultoras | `id`, `consultora_id`, `estado_anterior`, `estado_nuevo`, `user_id` |
| `checklists` | Plantillas de checklist | `id`, `title`, `description`, `category`, `is_active` |
| `questions` | Preguntas dinámicas | `id`, `checklist_id`, `label`, `type`, `options`, `order`, `is_required`, `vincular_a_metadata` |
| `inspections` | Ejecuciones/llenados | `id`, `checklist_id`, `user_id`, `asset_id`, `completed_at`, `notes` |
| `answers` | Respuestas específicas | `id`, `inspection_id`, `question_id`, `value` |
| `checklist_assignments` | Órdenes de trabajo técnicas asignadas (pueden ser compartidas sin user_id a nivel de sede) | `id`, `dynamic_checklist_id`, `asset_id`, `user_id` (nullable), `status`, `due_date` |
| `sedes` | Plantas físicas y control de jornada | `id`, `empresa_id`, `nombre_sede`, `dias_operativos` (JSON), `hora_apertura`, `hora_cierre`, `es_roster_24_7`, `radio_tolerancia_metros` |
| `visit_certificates` | Constancias de visita, geolocalización y auditoría horaria | `id`, `sede_id`, `user_id`, `start_time`, `end_time`, `horas_gabinete_estimadas`, `checkin_lat`, `checkin_lng`, `checkout_lat`, `checkout_lng`, `gps_verified` |
| `trainings` | Sesiones e instancias de capacitación | `id`, `empresa_id`, `sede_id`, `dynamic_checklist_id`, `title`, `description`, `type` (presencial/virtual), `date`, `duration`, `status` |
| `training_attendances` | Firmas y asistencia individual de alumnos normalizada | `id`, `training_id`, `visit_training_id`, `user_id`, `employee_name`, `dni`, `signature` |
| `visit_trainings` | Bitácora y capacitación en vivo en la visita | `id`, `visit_certificate_id`, `training_id`, `title`, `description`, `notes`, `duration_minutes`, `status` |
| `annual_plans` | Planificación anual del contrato de HyS por sede | `id`, `sede_id`, `category`, `activity_name`, `checklist_id`, `status`, `duration_minutes`, `modality`, `multi_trainer`, `scheduled_months` (JSON) |

---

## ✅ Buenas Prácticas

### Backend (Laravel 12)
- ✅ **Módulo Genérico de Activos, Metadata Dinámica y Expediente Fotográfico:** Para soportar múltiples rubros (Transporte, Seguridad, Alimentación), el sistema modela el equipamiento de forma abstracta (`assets` y `asset_types`) y guarda los atributos específicos en un campo JSON `metadata` y su expediente de fotos en `photos`. Permite configurar esquemas de fotos por categoría (`photo_schema`), alta dinámica multi-activo con prevención de identificadores duplicados (`show_in_create`, `distinct`), galería de evidencias de siniestros (`incidentes.asset_id`) y exportación a PDF A4.
- ✅ **Agrupación Jerárquica L1/L2 y Normalización de Escritura de Activos:**
  - Los activos admiten estructuración anidada mediante los campos `grupo` (Nivel 1, ej: *Planta*) y `subgrupo` (Nivel 2, ej: *Sector*), con etiquetas editables a nivel de tipo de activo.
  - El listado principal y la barra lateral de creación agrupan los activos jerárquicamente en árbol mediante doble bucle y contenedores de relieve HUD.
  - Para asegurar la consistencia y unicidad, el input ofrece sugerencias en caliente y auto-ajusta (on-blur snapping) las diferencias de mayúsculas comparándolas de forma insensible a mayúsculas con los grupos existentes en la base de datos de esa Sede, mostrando distintivos dinámicos de nuevo/existente.
  - Admite edición en caliente de nombre, identificador, grupo y subgrupo desde la barra lateral con validación asíncrona de unicidad del ID.
- ✅ **Tipos de Checklist Dinámicos (`AssetType`) — Arquitectura, Roles y Política:**
  - El `AssetType` actúa como **unificador de atributos de ficha técnica**, no como un tipo físico de activo. Se asocia a un checklist operativo existente para importar sus preguntas como atributos estructurados del activo.
  - **Roles de Atributo y Pre-llenado Inteligente:** Los campos en `metadata_schema` pueden tener un `role` específico para guiar la inicialización de respuestas en el Wizard de Inspección:
    - `identificador` (Identificador único, ej: N° Interno) → Pre-llenado en el Wizard: SÍ (editable/trazable).
    - `dato_estatico` (Atributo fijo del activo, ej: Tipo de Agente) → Pre-llenado en el Wizard: SÍ.
    - `dato_con_regla` (Métricas sujetas a reglas SRE, ej: Próxima Carga) → Pre-llenado en el Wizard: SÍ (punto de partida para actualizar).
    - `verificacion` (Comprobación en terreno, ej: manómetro en zona verde) → Pre-llenado en el Wizard: NO (siempre se limpia).
  - **Ámbito de Empresa:** Pertenece a `empresa_id`, siendo una plantilla compartida por todas las sedes. El `Asset` individual es el que se asocia a `sede_id`.
  - **Reemplazo Exclusivo al Re-importar:** Al cambiar el checklist asociado e importar nuevamente, el sistema borra automáticamente los atributos del checklist anterior (`field._imported = true`) y conserva solo los campos creados manualmente.
  - **Inmutabilidad de Atributos Importados:** Los campos con `_imported: true` son de solo lectura en el configurador. No se puede editar su etiqueta, clave ni eliminarlos. Esto preserva la trazabilidad de PDFs e informes generados con esa estructura.
  - **Política de Versionado de Checklists (Regla de Oro):** Los checklists son documentos controlados. Si se necesita corregir uno, se crea una nueva versión del checklist y se re-importa el tipo de activo de forma deliberada. El checklist original sigue funcionando normalmente en inspecciones de todas las sedes sin conflictos con el AssetType.
  - **Selector de Tipo Dark:** El selector de tipo de dato usa `[color-scheme:dark]` + `bg-[#16162a]` en las opciones para respetar la estética premium en todos los navegadores. 
- ✅ **Motor de Mantenimiento Autónomo, Asignación Automática y Bloqueo Operativo (Interlock SRE):**
  - **Constructor SRE:** Permite a los gestores configurar múltiples reglas preventivas de ciclo infinito por atributo del tipo de activo (frecuencia, margen de pre-alerta y checklist de resolución).
  - **Asignación Correctiva (SRE):** Al superar la frecuencia límite, el activo cambia a `fuera_de_servicio`, abre un hallazgo crítico y auto-asigna la orden de trabajo (`ChecklistAssignment` enlazada vía `asset_id`).
  - **Asignaciones Compartidas y Auto-asignación Dinámica:** Las órdenes de trabajo preventivas autogeneradas por SRE se crean sin un técnico específico (`user_id = null`), permitiendo que cualquier técnico autorizado de la sede las visualice como tareas pendientes en su consola. Al iniciar la ejecución de la tarea en el Wizard, esta se auto-asigna dinámicamente a su `user_id` y cambia a `in_progress`, ocultándola del panel de otros técnicos de la sede para evitar colisiones.
  - **Bloqueo Terreno (Interlock):** Si un activo se encuentra `fuera_de_servicio`, se bloquea cualquier inspección regular en el backend (`InspectionController.php`) y se despliega un banner de error rojo crítico en la interfaz (`Wizard.jsx`), a menos que la inspección ejecute su checklist de resolución asignado.
  - **Auto-Resolución y Reset:** Al completar con éxito el checklist resolutor, `SyncAssetMetadata` recalcula el próximo objetivo (`proximo_val = lectura_actual + frecuencia`), cierra el hallazgo, completa la orden técnica y devuelve el activo a `operativo`.
  - **Sincronización de Metadata y Logs de Cambio:** Al finalizar una inspección, un Listener (`SyncAssetMetadata`) intercepta las respuestas cuyas preguntas estén enlazadas mediante `vincular_a_metadata` (ej: `kilometraje`) y actualiza automáticamente la ficha del activo en caliente. Además, realiza una comparación del valor previo y nuevo, insertando un log auditor en la tabla `asset_metadata_logs` si el valor difiere. Esto permite registrar cronológicamente quién y en qué inspección se alteró el valor de cada atributo.
  - **Advertencia de Re-inspección (Modal Ámbar):** Si el técnico intenta seleccionar un activo que ya fue auditado en la visita actual (estado `✓ Auditado`), el Wizard Paso 0 detiene la selección inmediata y despliega un modal de confirmación táctico premium en color ámbar/dorado para advertir sobre la duplicación legal del reporte.
  - **Hover Tooltip de Alertas en Index:** Al pasar el mouse sobre el estado "Alerta" del activo en la tabla general del gestor, se despliega hacia abajo (`top-full mt-2`) un tooltip oscuro premium detallando cada campo que causó la alerta (ej: "Vencimiento Carga" o "Prueba Hidráulica") con la fecha exacta y días restantes, ocultando campos técnicos redundantes.
  - **Baja Lógica y Preservación Histórica (Soft Deletes):** El borrado de activos realiza una baja lógica (`SoftDeletes`) renombrándose en la UI a "Dar de Baja". Para garantizar que el historial legal de inspecciones e incidentes pasados de un activo de baja no quede huérfano, las relaciones en `DynamicInspection` e `Incidente` cargan los activos incluyendo registros eliminados (`->withTrashed()`).
- ✅ **Parámetros por Defecto en Contrato Anual:** `ContractController` establece el mes presente en tiempo real (`now()->format('Y-m')`) como el inicio por defecto de la grilla táctica PPD, evitando bloquear la navegación en meses antiguos de ejecuciones históricas.
- ✅ **Manejo de Roles Robusto:** Toda validación de roles en la plataforma (rutas, plantillas, policies) debe apoyarse en la lógica unificada del modelo `User::hasRole()`. Evitar splitting manual ad-hoc en controladores o middleware.
- ✅ **Nombres de Parámetros de Ruta Coincidentes:** Para que la vinculación implícita de modelos (Route Model Binding) de Laravel funcione de forma transparente, el comodín de la ruta (ej. `{inspection}`) debe coincidir exactamente con el nombre de la variable inyectada en el controlador (ej. `DynamicInspection $inspection`). Evitar usar `{id}` genérico si se tipa el modelo, para prevenir inyecciones de modelos vacíos en el frontend.
- ✅ **Hardening en Informes Maestros (`MasterReport`):** El controlador `MasterReportController` debe usar Route Model Binding nativo `{masterReport}`. Se inyectan FormRequests estrictos (`StoreMasterReportRequest` y `UpdateMasterReportRequest`) para validar y sanitizar el payload (excluyendo campos sensibles del sistema mediante `safeData()` o `validatedWithSafeTime()`). Se debe llamar obligatoriamente a la política del framework `$this->authorize('update', $masterReport)` para asegurar multi-tenancy.
- ✅ **Máquina de Estados y Aprobaciones en Informes**: El controlador debe procesar la transición segura del estado del informe (`'solicitado'`, `'en_redaccion'`, `'finalizado'`, `'aprobado'`) evaluando las acciones del gestor (`action === 'approve'` o `'observe'`). En aprobación, limpia el campo `observacion_revision`, mientras que en observación, guarda las anotaciones del gestor y devuelve el estado a `'en_redaccion'`.
- ✅ **Inmutabilidad y Blindaje de Fechas (SRE Audit):** Al finalizar una inspección, es obligatorio ignorar y sanitizar cualquier campo de fecha/hora enviado por el frontend (`completed_at`, `date`, `time`, `timestamp`). La persistencia debe forzar estrictamente `now()` en el servidor y disparar alertas detalladas (`Log::warning()`) ante cualquier discrepancia para evitar fraude de horarios.
- ✅ **Resolución de Sede y Redirección Contextual en Visita (Seguridad de Datos):** Para reportes de hallazgos e incidentes iniciados desde una visita activa, el backend debe resolver el `initial_sede_id` cruzando el `visit_id` provisto y validar que coincida. Al guardar, el sistema debe evaluar el rol del usuario y el contexto de visita para redirigirlo dinámicamente a la visita activa (`tecnico.visits.in-progress`) en lugar de rutas globales de gestor o vistas externas.
- ✅ **Arquitectura y Ciclo de Firmas Digitales (Optimización Storage vs Base64):**
  - **Firma Tipo A (Perfil/Permanente / Instructor Especialista):** Trazada en canvas React, transmitida como Base64 URL en el request, decodificada en backend y guardada como archivo físico `.png` en storage público. En base de datos se guarda únicamente el path relativo (ej. `signatures/instructor_xxxx.png`) para evitar el "bloat" de Base64 largos en tablas relacionales. Para técnicos, entra en estado `'pending'` a la espera de auditoría visual en `PendingSignaturesList.jsx` por parte del Gestor antes de copiarse al path permanente final.
  - **Firma Tipo B (Transaccional/Hojas de Firma/Socio):** Guardadas como Base64 serializados o strings directamente sobre planillas específicas en DB (e.g. `training_attendances`).
  - **DomPDF Resolution Constraint:** Se garantiza que al exportar las plantillas o reportes PDF en Laravel, el motor DomPDF resuelva las rutas físicas absolutas locales (`public_path('storage/' . $path)`) evitando fallas de visualización (como marcos cruzados o placeholders vacíos).
- ✅ Usar Type Hints y Return Types estrictos.
- ✅ Form Requests independientes para la validación de peticiones.
- ✅ Eager Loading (`with()`) en Eloquent para evitar consultas N+1.
- ✅ **Flujo de Aprobación de Firma Digital (Auditoría):** Los cambios de firma digital del técnico entran en estado `pending` en `firma_status` y se guardan temporalmente en `firma_temp`. Los Gestores auditan y aprueban o rechazan el cambio físicamente a través de la consola de usuarios (`Users.jsx`).
- ✅ **Separación de Roles Globales (`super_admin` vs `vendedor`):**
  - **Súper Administrador (`super_admin`):** control absoluto. Gestiona el personal global (CRUD de staff global), puede eliminar consultoras, y administra el personal interno de las consultoras.
  - **Vendedor (`vendedor`):** rol comercial y de soporte. Administra consultoras (dar de alta, editar información/branding/colores, configurar límites de licencias de técnicos, gestores y empresas, y estado de suscripción). **Restringido:** bloqueado de eliminar consultoras, de ver/modificar personal de consultoras, y de la consola de staff global (recibe `403` HTTP y elementos ocultos en la UI).
  - **Helpers en modelo `User`:** se añaden constantes de rol y helpers (`isVendedor()`, `isGlobalUser()`) y se protegen las rutas en `routes/web.php` de forma segmentada.
- ✅ **Auditoría Inmutable (Audit Log):**
  - Registra de forma automática e inalterable qué usuario global (`super_admin` o `vendedor`) modificó los límites o suscripciones de una consultora.
  - El modelo `AuditLog` está blindado a nivel de base de datos interceptando los eventos Eloquent `updating` y `deleting` para lanzar excepciones.
  - Las transacciones se disparan automáticamente usando eventos Laravel (`ClientAccessModified`) y oyentes (`LogClientAccess`) vinculados en `AppServiceProvider`.
- ✅ **Aislamiento de Súper Administración:** Las rutas de súper administración se protegen con middleware `role:super_admin,vendedor` o `role:super_admin` según la jerarquía, e invocan `$this->ensureSuperAdminOrVendedor()` o `$this->ensureSuperAdmin()` en `AdministrationController` para convalidar el rol en backend.
- ✅ **Validación de Jerarquía Multi-tenant en Consultoras:** Para evitar accesos cruzados ilegítimos o manipulados en endpoints parametrizados de operadores, se valida explícitamente en backend (`abort_unless($user->consultora_id === $consultant->id, 403)`) la pertenencia estricta del usuario objetivo a la consultora máster inyectada.
- ✅ **Lógica de Deshabilitación Unificada (Preservación de Trazabilidad):** Las bajas de personal global y de operadores de consultoras sustituyen el borrado físico (`delete()`) por un cambio lógico de estado (`'inactivo'`). Esto mantiene la integridad de auditorías de inspección, capacitaciones e incidentes históricos relacionados al usuario en la base de datos.
- ✅ **Re-vinculación de Activos y Restauración de Metadata Histórica:** Se proporciona la capacidad de re-vincular o desvincular activos en inspecciones completadas. El backend (`InspectionController@relinkAsset`) utiliza la tabla `asset_metadata_logs` para revertir de manera automática y precisa los valores previos de los metadatos en el activo de origen, y sincroniza las respuestas en el nuevo activo de destino. Adicionalmente, se fuerza el recálculo y actualización de alertas del motor SRE para mantener los estados operativos sincronizados en caliente.
- ✅ Retornar respuestas nativas de Inertia: `return Inertia::render('Componente', [...]);`.
 
### Frontend (React 19 & Inertia)
- ✅ **Menú Lateral para Usuarios Globales (`super_admin` / `vendedor`):**
  - El layout `AuthenticatedLayout.jsx` evalúa la pertenencia global (`user.rol`) para renderizar un menú administrativo minimalista.
  - Para `super_admin`, incluye: "Dashboard Global", "Consultoras", y "Personal Global" (ABM de staff global).
  - Para `vendedor`, incluye: "Dashboard Global" y "Consultoras", agregando un distintivo de marca "Panel Vendedor" en la barra lateral, y ocultando las rutas de administración de personal global.
- ✅ **Línea de Tiempo de Auditoría (Edit.jsx):**
  - En la vista de edición de Consultoras (`Edit.jsx`), se integra una pestaña de **"Auditoría"** que renderiza una línea de tiempo táctica de cambios de accesos.
  - Cada entrada calcula la diferencia visual de los límites (técnicos, gestores, empresas y estado de suscripción) indicando los valores anteriores y nuevos, y el usuario autor de la modificación.
- ✅ **ABM de Staff Global (Index.jsx en Admin/Staff):**
  - Interfaz premium en modo oscuro que permite a los súper administradores gestionar el staff interno, crear nuevos usuarios de soporte, deshabilitarlos (cambiando a `inactivo` mediante un modal React táctico) o blanquear sus contraseñas.
- ✅ **Modales de Confirmación Tácticos React (Frontend):**
  - Reemplazo de las advertencias nativas de JavaScript `confirm()` por modales React premium integrados en `Pages/Admin/Staff/Index.jsx`, `Pages/Admin/Consultants/Index.jsx` y `Pages/Admin/Consultants/Users.jsx`.
  - Diseñados en base a la paleta premium oscura de XamaSafe (`bg-[#16162a]`, `border-white/10`, efectos glassmorphism `backdrop-blur-md` y botones tácticos estilizados de color rojo/gris).
- ✅ **Diseño HUD Táctico y Modular:** Componentes HUD oscuros premium, modularizados y reutilizables. El modal `ActionSelector` es el estándar para paneles de comandos y acciones spontaneous.
- ✅ **HUD Sync Badge (Naranja Técnico):** El asistente de checklists (`Wizard.jsx`) debe mostrar arriba de la barra de progreso un badge centrado `"⌚ Tiempo Real (Sync Servidor)"` en Naranja Técnico para indicar la inmutabilidad de la fecha en tiempo real.
- ✅ **Intercepción y Auto-completado por Falla Crítica en Asistente (`Wizard.jsx`):** Evaluar en caliente el desvío crítico al navegar entre secciones del checklist. Si el técnico decide "Rechazar y Finalizar", el frontend debe mutar de forma masiva los campos no contestados de secciones posteriores a `"No evaluado por falla crítica"` y saltar al paso de firmas de inmediato para cerrar la auditoría.
- ✅ **Carga Acumulativa Secuencial y Remoción (React Files):** Al subir evidencias fotográficas en formularios de campo, la función `handleFileChange` debe concatenar (usando el operador spread `...`) la nueva selección con los archivos previamente acumulados en `data.fotos`, en lugar de sobreescribir el arreglo. Cada archivo debe renderizar una previsualización con un botón flotante individual "✕" para remover fotos de forma independiente antes del envío.
- ✅ **Soporte de Entradas Temporales (Wizard):** El wizard debe renderizar inputs específicos de tipo `time` y `datetime-local` adaptados a pantallas táctiles, acompañados de botones rápidos ("Hora Actual" / "Ahora") que inyectan el timestamp local del dispositivo al vuelo.
- ✅ **Validación Táctica con Firmas de Perfil (Wizard.jsx):** La sección 5 del wizard lee la firma de perfil (`report.tecnico.firma`) del técnico de forma inmutable, presentándola con filtro inverso de color (`brightness-0 invert opacity-90`) y un glow esmeralda de aprobación. Si falta la firma, bloquea el envío y despliega un Glassmorphic Alert Box con enlace a su Perfil.
- ✅ **Flujo de Revisión e Inmutabilidad de Inputs**: En el frontend (`Wizard.jsx`), los inputs de redacción técnica deben aplicar la propiedad `readOnly` de forma condicional basándose en el flag `isReadOnly` (que es `true` si el usuario no es técnico o si el estado es `'finalizado'` o `'aprobado'`).
- ✅ **Botonera de Acción Flotante Inteligente**: La barra inferior de comandos del wizard debe alternar dinámicamente sus botones por rol y estado: pastilla verde esmeralda para `'aprobado'`, botones de `Aprobar` / `Observar` para el Gestor si está `'finalizado'`, botones de `Guardar` / `Generar` para el Técnico si está en borrador, y badge pulsante azul de lectura en otros estados.
- ✅ **Sección 6: Evaluación del Gestor**: Visible únicamente para gestores y solo bajo el estado `'finalizado'`. Debe contener el textarea acoplado a `data.observacion_revision` para que el gestor redacte sus correcciones.
- ✅ **Inmutabilidad en el Envío de useForm**: Al enviar estados transitorios o flags dinámicos en handlers de Inertia (como `finalize`, `action`), se debe registrar de forma inmutable el callback mediante transform: `form.transform(data => ({ ...data, action: 'approve' }))` y disparar el método `put` en la siguiente instrucción independiente para evitar congelamiento de UI en React.
- ✅ **Counter Badges HUD:** Uso de insignias flotantes absolutas (`absolute -top-3 -right-3`, `rounded-2xl`, `font-black`) para indicar múltiples ejecuciones continuas en flujos activos de inspección.
- ✅ **Responsividad y Adaptabilidad Móvil/Notebook:**
  - **Márgenes y Relleno de Celdas:** El contenedor general debe usar márgenes fluidos (`p-4 sm:p-6 lg:p-8 xl:p-12`) y las celdas de las tablas deben ser lo más compactas posibles (`px-4 lg:px-6 xl:px-8 py-6 lg:py-8`) para evitar el scroll horizontal innecesario en laptops comunes de 13/15 pulgadas.
  - **Transición Automática Tabla-Card:** Para pantallas con un ancho inferior a `1024px` (`lg` en Tailwind), ocultar las tablas anchas (`lg:block hidden`) y renderizar una lista ordenada de tarjetas interactivas/táctiles (`lg:hidden block`) que estructuren la información de forma vertical.
  - **Scrollbars de Marca (`.custom-scrollbar`):** Cuando la tabla necesite desplazamiento horizontal, se debe envolver en un div con `overflow-x-auto custom-scrollbar` usando los colores oficiales del pilar correspondiente (Naranja para checklists y contrato anual, Índigo para hallazgos y capacitación, y Rojo para incidentes).
  - **Columnas Fijas Dinámicas:** En grillas matriciales complejas, la columna fija izquierda (como el nombre de la actividad) debe tener un ancho adaptable en base al tamaño de la pantalla (ej: `w-[140px] sm:w-[220px] md:w-[300px]`) con relleno compacto en móviles.
- ✅ **Seguridad en Rutas e Inertia (`auth` reference safety):** Al estructurar o validar componentes React que definen rutas dinámicas o botones según el rol del usuario, siempre se debe importar `usePage` y destructurar la propiedad `auth` de forma segura (`const { auth } = usePage().props`) para evitar runtime crash exceptions si el objeto no está definido en el contexto global.
- ✅ Componentes modulares, pequeños y altamente enfocados.
- ✅ Control de estado mediante hooks nativos de React (`useState`, `useEffect`, `useMemo`).
- ✅ Enlace con backend y envío de formularios usando el hook `useForm` de Inertia.
- ✅ Cero código remanente o muerto de Livewire (totalmente purgado).

### Base de Datos
- ✅ Migraciones con índices, claves foráneas estructuradas y constraints de borrado cascada.
- ✅ Optimización de consultas complejas.
- ⚠️ **ADVERTENCIA MCP SQLite:** No utilizar la herramienta MCP de SQLite del entorno, ya que apunta a una base de datos global genérica del sistema. Para cualquier interacción o consulta con la base de datos de XamaSafe, utilizar siempre **Laravel Tinker** (`php artisan tinker`) o scripts PHP locales en la carpeta `scratch/` que apunten a `database/database.sqlite`.

---

## 📌 Convenciones de Nombrado

### Laravel/PHP
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Modelos | PascalCase singular | `Checklist`, `Question` |
| Controllers | PascalCase + Controller | `ChecklistController` |
| Migrations | snake_case con fecha | `2024_12_23_create_checklists_table` |
| Métodos | camelCase | `getActiveQuestions()` |
| Variables | camelCase | `$checklistId` |
| Constantes | UPPER_SNAKE | `STATUS_ACTIVE` |

### React & Frontend
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Componentes / Páginas | PascalCase | `AuthenticatedLayout.jsx`, `Dashboard.jsx` |
| Hooks Personalizados | camelCase prefijada por `use` | `useCustomValidation` |
| CSS Variables (Tema HUD) | kebab-case con prefijo `--color-ep-` | `--color-ep-primary` |

---

## 🔄 Git Workflow

### Ramas
- `feature-inertia-react` → Rama activa de desarrollo (React 19 + Inertia + Laravel 12).
- `version-premium` → Desarrollo premium legacy (Livewire).
- `version-minina` → Rama estable MVP.
- `main` → Producción estable/base.

### Commits (Conventional)
- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Documentación
- `test:` Tests
- `refactor:` Refactorización o limpieza de código

---

## 🧪 Testing

### Comandos
```bash
php artisan test                    # Todos los tests
php artisan test --filter=Name      # Tests específicos
```

---

## 🚀 Instalación Local

### Requisitos
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- npm o pnpm

### Pasos
```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno (si no existe .env)
cp .env.example .env
php artisan key:generate

# 3. Crear base de datos SQLite (si no existe)
touch database/database.sqlite

# 4. Ejecutar migraciones
php artisan migrate

# 5. Cargar datos de prueba / Seeders
php artisan db:seed

# 6. Instalar dependencias JS
npm install

# 7. Compilar assets & Iniciar entorno de desarrollo
npm run dev
```

---

*Documento actualizado: 7 Julio 2026*
*Versión del Sistema: V2.3.0.0 Premium (Inertia + React)*
*Especialidad: Monolito Laravel con UI React táctica y moderna (Tactical Premium Dark)*