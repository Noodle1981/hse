# 🗃️ Estructura de la Base de Datos - XamaSafe (Contexto para NotebookLM)

Este documento describe la estructura actual de la base de datos de **XamaSafe** para que NotebookLM (u otras herramientas de IA) puedan comprender perfectamente la arquitectura del modelo de datos, campos, tipos, restricciones y relaciones.

## 📊 Resumen del Modelo de Datos

El sistema cuenta con las siguientes entidades principales:
- **Empresas y Sedes**: Estructura organizativa (`empresas`, `sedes`, `sede_users`).
- **Usuarios y Consultoras**: Gestión de cuentas (`users`, `consultoras`).
- **Checklists Dinámicos**: Plantillas, secciones, subcategorías y lógica (`dynamic_checklists`, `dynamic_questions`, `checklist_sections`, `categories`, `subcategories`).
- **Inspecciones y Respuestas**: Registros de checklist ejecutados (`dynamic_inspections`, `dynamic_answers`, `checklist_assignments`).
- **Capacitaciones y Empleados**: Control de firmas, asistencias y personal (`employees`, `trainings`, `training_attendances`).
- **Seguridad en Campo**: Incidentes, hallazgos, fotos y certificados de visitas (`incidentes`, `hallazgos`, `hallazgo_seguimientos`, `hallazgo_fotos`, `visit_certificates`).
- **Planificación**: Plan Anual de HyS (`annual_plans`).

### 🖋️ Gestión y Representación de Firmas en Base de Datos
* **Firma Tipo A (Firma de Perfil / Identidad Permanente):**
  * Tabla `users`: Columnas `firma` (VARCHAR, ruta relativa al archivo permanente `.png` en storage, ej: `signatures/user_1.png`), `firma_temp` (VARCHAR, ruta de firma cargada por técnico a la espera de auditoría), y `firma_status` (VARCHAR, estado de la firma: `'pending'`, `'approved'`, `'rejected'`).
  * **Uso en Informes Maestros (`master_reports`):** A diferencia de las visitas, los informes maestros no almacenan firmas transaccionales Base64 independientes. El sistema hereda y vincula directamente la firma de perfil aprobada del Técnico asignado (`master_reports.user_id` -> `users.firma`). El PDF renderiza este archivo de forma dinámica e inmutable para garantizar la autenticidad e integridad del documento final.
* **Firma Tipo B (Firma Transaccional / De Visita o Planilla):**
  * Tabla `visit_certificates`: Columnas `responsible_signature` y `instructor_signature` (TEXT, datos serializados o Base64).
  * Tabla `training_attendances`: Relacionada con `visit_trainings` y `trainings`, normalizando nombres, puestos, DNI y firmas en Base64 en filas independientes en lugar de campos JSON.
  * Tabla `dynamic_inspections`: Columnas `supervisor_signature` y `technician_signature` (TEXT).

### 🔄 Control de Estados y Auditoría en Informes Maestros
* Tabla `master_reports`:
  * **Columna `estado`** (VARCHAR): Controla el ciclo de vida del reporte maestro:
    - `'solicitado'`: El Gestor solicita formalmente la redacción al técnico.
    - `'en_redaccion'`: El Técnico trabaja sobre el borrador y guarda avances.
    - `'finalizado'`: El Técnico cierra el borrador, inmutando los campos de llenado (`readOnly`) y pasándolo a la cola de revisión del Gestor.
    - `'aprobado'`: El Gestor convalida y cierra de forma definitiva el reporte.
  * **Columna `observacion_revision`** (TEXT, nullable): Almacena las observaciones detalladas del Gestor cuando el informe es observado y devuelto a `'en_redaccion'`. Si el informe es `'aprobado'`, este campo se limpia automáticamente para preservar la limpieza de datos.
  * **Sanitización de Request (`UpdateMasterReportRequest`)**: Campos transitorios y volátiles como `action` (`'approve'` o `'observe'`) y `finalize` se consumen en la capa del controlador para guiar las transiciones de estado, pero se purgan en `safeData()` antes de invocar `$masterReport->update()`, evitando errores SQL por columnas inexistentes.

---

## Tabla: `annual_plans`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `category` | `varchar` | No ❌ | *ninguno* |  |
| `activity_name` | `varchar` | No ❌ | *ninguno* |  |
| `activity_code` | `varchar` | Sí ✅ | *ninguno* |  |
| `checklist_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `frequency` | `varchar` | No ❌ | `'none'` |  |
| `period_start` | `date` | Sí ✅ | *ninguno* |  |
| `period_end` | `date` | Sí ✅ | *ninguno* |  |
| `scheduled_months` | `TEXT` | Sí ✅ | *ninguno* |  |
| `scheduled_dates` | `TEXT` | Sí ✅ | *ninguno* |  |
| `duration_minutes` | `INTEGER` | No ❌ | `15` |  |
| `modality` | `varchar` | No ❌ | `'presencial'` |  |
| `multi_trainer` | `tinyint(1)` | No ❌ | `'0'` |  |

### 🔗 Relaciones (Foreign Keys)
- `checklist_id` → vincula con `dynamic_checklists`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

---

## Tabla: `capacitacion_presencials`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tema` | `varchar` | No ❌ | *ninguno* |  |
| `foto` | `varchar` | Sí ✅ | *ninguno* |  |
| `estado` | `tinyint(1)` | No ❌ | `'0'` |  |
| `email_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

---

## Tabla: `capacitacion_virtuals`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tema` | `varchar` | No ❌ | *ninguno* |  |
| `foto` | `varchar` | Sí ✅ | *ninguno* |  |
| `link` | `varchar` | Sí ✅ | *ninguno* |  |
| `lugar` | `varchar` | Sí ✅ | *ninguno* |  |
| `instructor` | `varchar` | Sí ✅ | *ninguno* |  |
| `duracion` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `email_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

---

## Tabla: `capacitacion_virual_asistentes`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `capacitacion_virtual_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `nombre_apellido` | `varchar` | Sí ✅ | *ninguno* |  |
| `dni` | `varchar` | Sí ✅ | *ninguno* |  |
| `puesto` | `varchar` | Sí ✅ | *ninguno* |  |
| `evaluacion` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `capacitacion_virtual_id` → vincula con `capacitacion_virtuals`(`id`)

---

## Tabla: `categories`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `code` | `varchar` | No ❌ | *ninguno* |  |
| `name` | `varchar` | No ❌ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `color` | `varchar` | No ❌ | `'#6B7280'` |  |
| `icon` | `varchar` | Sí ✅ | *ninguno* |  |
| `is_active` | `tinyint(1)` | No ❌ | `'1'` |  |
| `order` | `INTEGER` | No ❌ | `'0'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🗂️ Índices
- `categories_code_unique` (Único)
- `categories_order_index`
- `categories_is_active_index`

---

## Tabla: `checklist_assignments`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `dynamic_checklist_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `empresa_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `frequency` | `varchar` | No ❌ | `'once'` |  |
| `due_date` | `date` | Sí ✅ | *ninguno* |  |
| `status` | `varchar` | No ❌ | `'pending'` |  |
| `notes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `empresa_id` → vincula con `empresas`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `user_id` → vincula con `users`(`id`)
- `dynamic_checklist_id` → vincula con `dynamic_checklists`(`id`)

### 🗂️ Índices
- `checklist_assignments_due_date_index`
- `checklist_assignments_status_index`
- `checklist_assignments_empresa_id_sede_id_index`

---

## Tabla: `checklist_sections`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `dynamic_checklist_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `title` | `varchar` | No ❌ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `order` | `INTEGER` | No ❌ | `'0'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `dynamic_checklist_id` → vincula con `dynamic_checklists`(`id`)

### 🗂️ Índices
- `checklist_sections_dynamic_checklist_id_order_index`

---

## Tabla: `constancia_actividades`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `generador_constancia_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `actividad_type` | `varchar` | No ❌ | *ninguno* |  |
| `actividad_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `generador_constancia_id` → vincula con `generador_constancias`(`id`)

### 🗂️ Índices
- `constancia_actividad_unique` (Único)
- `constancia_actividades_actividad_type_actividad_id_index`

---

## Tabla: `consultoras`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `nombre` | `varchar` | No ❌ | *ninguno* |  |
| `cuit` | `varchar` | No ❌ | *ninguno* |  |
| `max_tecnicos` | `INTEGER` | No ❌ | `'5'` |  |
| `config` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `fiscal_address` | `varchar` | Sí ✅ | *ninguno* |  |
| `email` | `varchar` | Sí ✅ | *ninguno* |  |
| `phone` | `varchar` | Sí ✅ | *ninguno* |  |
| `province` | `varchar` | Sí ✅ | *ninguno* |  |
| `locality` | `varchar` | Sí ✅ | *ninguno* |  |
| `logo_path` | `varchar` | Sí ✅ | *ninguno* |  |
| `color_primary` | `varchar` | No ❌ | `'#1e40af'` |  |
| `color_secondary` | `varchar` | No ❌ | `'#6b7280'` |  |
| `color_accent` | `varchar` | No ❌ | `'#16a34a'` |  |

### 🗂️ Índices
- `consultoras_cuit_unique` (Único)

---

## Tabla: `dynamic_answers`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `dynamic_inspection_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `dynamic_question_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `value` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `dynamic_question_id` → vincula con `dynamic_questions`(`id`)
- `dynamic_inspection_id` → vincula con `dynamic_inspections`(`id`)

### 🗂️ Índices
- `dynamic_answers_dynamic_inspection_id_dynamic_question_id_index`

---

## Tabla: `dynamic_checklists`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `title` | `varchar` | No ❌ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `category` | `varchar` | Sí ✅ | *ninguno* |  |
| `is_active` | `tinyint(1)` | No ❌ | `'1'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `is_draft` | `tinyint(1)` | No ❌ | `'0'` |  |
| `category_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `subcategory_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `registry_code` | `varchar` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `subcategory_id` → vincula con `subcategories`(`id`)
- `category_id` → vincula con `categories`(`id`)

### 🗂️ Índices
- `dynamic_checklists_registry_code_index`
- `dynamic_checklists_subcategory_id_index`
- `dynamic_checklists_category_id_index`
- `dynamic_checklists_is_active_index`
- `dynamic_checklists_category_index`

---

## Tabla: `dynamic_inspections`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `dynamic_checklist_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `completed_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `notes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `status` | `varchar` | No ❌ | `'draft'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `sede_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `checklist_assignment_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `supervisor_name` | `varchar` | Sí ✅ | *ninguno* |  |
| `supervisor_signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `technician_signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `result` | `varchar` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `checklist_assignment_id` → vincula con `checklist_assignments`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `dynamic_checklist_id` → vincula con `dynamic_checklists`(`id`)
- `user_id` → vincula con `users`(`id`)

### 🗂️ Índices
- `dynamic_inspections_status_index`
- `dynamic_inspections_dynamic_checklist_id_user_id_index`
- `dynamic_inspections_completed_at_index`

---

## Tabla: `dynamic_questions`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `dynamic_checklist_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `label` | `varchar` | No ❌ | *ninguno* |  |
| `type` | `varchar` | No ❌ | *ninguno* |  |
| `options` | `TEXT` | Sí ✅ | *ninguno* |  |
| `order` | `INTEGER` | No ❌ | `'0'` |  |
| `is_required` | `tinyint(1)` | No ❌ | `'0'` |  |
| `help_text` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `section_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `min_value` | `numeric` | Sí ✅ | *ninguno* |  |
| `max_value` | `numeric` | Sí ✅ | *ninguno* |  |
| `failure_values` | `TEXT` | Sí ✅ | *ninguno* |  |
| `failure_logic` | `varchar` | No ❌ | `'fail_if_selected'` |  |

### 🔗 Relaciones (Foreign Keys)
- `section_id` → vincula con `checklist_sections`(`id`)
- `dynamic_checklist_id` → vincula con `dynamic_checklists`(`id`)

### 🗂️ Índices
- `dynamic_questions_section_id_index`
- `dynamic_questions_dynamic_checklist_id_order_index`

---

## Tabla: `employees`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `empresa_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `name` | `varchar` | No ❌ | *ninguno* |  |
| `last_name` | `varchar` | No ❌ | *ninguno* |  |
| `dni` | `varchar` | Sí ✅ | *ninguno* |  |
| `cuil` | `varchar` | Sí ✅ | *ninguno* |  |
| `puesto` | `varchar` | Sí ✅ | *ninguno* |  |
| `is_active` | `tinyint(1)` | No ❌ | `'1'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `deleted_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `sede_id` → vincula con `sedes`(`id`)
- `empresa_id` → vincula con `empresas`(`id`)

### 🗂️ Índices
- `employees_cuil_unique` (Único)
- `employees_dni_unique` (Único)

---

## Tabla: `empresas`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `nombre` | `varchar` | No ❌ | *ninguno* |  |
| `razon_social` | `varchar` | No ❌ | *ninguno* |  |
| `cuit` | `varchar` | No ❌ | *ninguno* |  |
| `direccion` | `varchar` | Sí ✅ | *ninguno* |  |
| `email` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `consultora_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `logo_path` | `varchar` | Sí ✅ | *ninguno* |  |
| `primary_color` | `varchar` | No ❌ | `'#F69239'` |  |
| `branding_config` | `TEXT` | Sí ✅ | *ninguno* |  |
| `deleted_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `consultora_id` → vincula con `consultoras`(`id`)

---

## Tabla: `generador_constancias`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tipo_visita` | `varchar` | No ❌ | *ninguno* |  |
| `observacion` | `varchar` | No ❌ | *ninguno* |  |
| `firma_responsable` | `TEXT` | Sí ✅ | *ninguno* |  |
| `date_firma_responsable` | `date` | Sí ✅ | *ninguno* |  |
| `firma_instructor` | `TEXT` | Sí ✅ | *ninguno* |  |
| `date_firma_instructor` | `date` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `email_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `aclaracion_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `token_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `code` | `varchar` | Sí ✅ | *ninguno* |  |
| `fecha_visita` | `date` | Sí ✅ | *ninguno* |  |
| `justificacion_firma` | `TEXT` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

---

## Tabla: `hallazgo_fotos`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `hallazgo_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tipo` | `varchar` | No ❌ | `'antes'` |  |
| `ruta_archivo` | `varchar` | No ❌ | *ninguno* |  |
| `descripcion` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `hallazgo_id` → vincula con `hallazgos`(`id`)

### 🗂️ Índices
- `hallazgo_fotos_hallazgo_id_index`

---

## Tabla: `hallazgo_seguimientos`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `hallazgo_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `estado_anterior` | `varchar` | No ❌ | *ninguno* |  |
| `estado_nuevo` | `varchar` | No ❌ | *ninguno* |  |
| `comentario` | `TEXT` | Sí ✅ | *ninguno* |  |
| `fecha_seguimiento` | `datetime` | No ❌ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `hallazgo_id` → vincula con `hallazgos`(`id`)

### 🗂️ Índices
- `hallazgo_seguimientos_fecha_seguimiento_index`
- `hallazgo_seguimientos_hallazgo_id_index`

---

## Tabla: `hallazgos`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `titulo` | `varchar` | No ❌ | *ninguno* |  |
| `descripcion` | `TEXT` | No ❌ | *ninguno* |  |
| `ubicacion_especifica` | `varchar` | Sí ✅ | *ninguno* |  |
| `criticidad` | `varchar` | No ❌ | `'medio'` |  |
| `estado` | `varchar` | No ❌ | `'pendiente'` |  |
| `plazo_correccion` | `date` | Sí ✅ | *ninguno* |  |
| `responsable_correccion` | `varchar` | Sí ✅ | *ninguno* |  |
| `fecha_deteccion` | `datetime` | No ❌ | *ninguno* |  |
| `fecha_resolucion` | `datetime` | Sí ✅ | *ninguno* |  |
| `origen_type` | `varchar` | Sí ✅ | *ninguno* |  |
| `origen_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `visit_certificate_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `checklist_completion_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `question_id` | `INTEGER` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `visit_certificate_id` → vincula con `visit_certificates`(`id`)
- `checklist_completion_id` → vincula con `visit_checklist_completions`(`id`)
- `question_id` → vincula con `dynamic_questions`(`id`)

### 🗂️ Índices
- `hallazgos_origen_type_origen_id_index`
- `hallazgos_fecha_deteccion_index`
- `hallazgos_estado_criticidad_index`

---

## Tabla: `incidente_fotos`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `incidente_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `ruta_archivo` | `varchar` | No ❌ | *ninguno* |  |
| `descripcion` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `incidente_id` → vincula con `incidentes`(`id`)

---

## Tabla: `incidentes`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `hallazgo_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tipo` | `varchar` | No ❌ | `'incidente'` |  |
| `gravedad` | `varchar` | No ❌ | `'leve'` |  |
| `fecha_ocurrencia` | `datetime` | No ❌ | *ninguno* |  |
| `descripcion` | `TEXT` | No ❌ | *ninguno* |  |
| `personas_afectadas` | `INTEGER` | No ❌ | `'0'` |  |
| `dias_perdidos` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `medidas_tomadas` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `visit_certificate_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `titulo` | `varchar` | Sí ✅ | *ninguno* |  |
| `ubicacion_especifica` | `varchar` | Sí ✅ | *ninguno* |  |
| `estado` | `varchar` | No ❌ | `'reportado'` |  |

### 🔗 Relaciones (Foreign Keys)
- `visit_certificate_id` → vincula con `visit_certificates`(`id`)
- `hallazgo_id` → vincula con `hallazgos`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `user_id` → vincula con `users`(`id`)

### 🗂️ Índices
- `incidentes_tipo_gravedad_index`
- `incidentes_sede_id_index`
- `incidentes_hallazgo_id_index`
- `incidentes_fecha_ocurrencia_index`

---

## Tabla: `informes`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `titulo` | `varchar` | No ❌ | *ninguno* |  |
| `descripcion` | `varchar` | No ❌ | *ninguno* |  |
| `capacitacion_asistentes` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `capacitacion_temas` | `TEXT` | Sí ✅ | *ninguno* |  |
| `fecha` | `date` | No ❌ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

---

## Tabla: `items`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `informe_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `observacion` | `varchar` | No ❌ | *ninguno* |  |
| `recomendacion` | `varchar` | No ❌ | *ninguno* |  |
| `imagen1` | `TEXT` | Sí ✅ | *ninguno* |  |
| `imagen2` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `informe_id` → vincula con `informes`(`id`)

---

## Tabla: `job_batches`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `varchar` | No ❌ | *ninguno* | 🔑 PK |
| `name` | `varchar` | No ❌ | *ninguno* |  |
| `total_jobs` | `INTEGER` | No ❌ | *ninguno* |  |
| `pending_jobs` | `INTEGER` | No ❌ | *ninguno* |  |
| `failed_jobs` | `INTEGER` | No ❌ | *ninguno* |  |
| `failed_job_ids` | `TEXT` | No ❌ | *ninguno* |  |
| `options` | `TEXT` | Sí ✅ | *ninguno* |  |
| `cancelled_at` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `created_at` | `INTEGER` | No ❌ | *ninguno* |  |
| `finished_at` | `INTEGER` | Sí ✅ | *ninguno* |  |

### 🗂️ Índices
- `sqlite_autoindex_job_batches_1` (Único)

---

## Tabla: `master_reports`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `solicitado_por` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `titulo` | `varchar` | No ❌ | *ninguno* |  |
| `periodo` | `varchar` | No ❌ | *ninguno* |  |
| `resumen_ejecutivo` | `TEXT` | Sí ✅ | *ninguno* |  |
| `observaciones_matriz` | `TEXT` | Sí ✅ | *ninguno* |  |
| `estado` | `varchar` | No ❌ | `'solicitado'` |  |
> **Valores permitidos (CHECK constraint):** `solicitado`, `en_redaccion`, `finalizado`, `aprobado`, `archivado`
> Migración: `2026_06_03_213939_add_aprobado_estado_to_master_reports_table`
| `datos_anexo` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `observaciones_visitas` | `TEXT` | Sí ✅ | *ninguno* |  |
| `observaciones_checklists` | `TEXT` | Sí ✅ | *ninguno* |  |
| `observaciones_capacitaciones` | `TEXT` | Sí ✅ | *ninguno* |  |
| `observaciones_hallazgos_incidentes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `observacion_revision` | `TEXT` | Sí ✅ | *ninguno* |  |
| `frecuencia` | `varchar` | No ❌ | `'mensual'` |  |

### 🔗 Relaciones (Foreign Keys)
- `solicitado_por` → vincula con `users`(`id`)
- `user_id` → vincula con `users`(`id`)
- `sede_id` → vincula con `sedes`(`id`)

### 🔄 Máquina de Estados (`estado`)
```
solicitado → en_redaccion → finalizado → aprobado
                  ↑               |
                  └───────────────┘  (gestor devuelve con observacion_revision)
```
- **`solicitado`**: Informe iniciado pero no comenzado a redactar.
- **`en_redaccion`**: Técnico redactando. Si `observacion_revision` ≠ null: fue devuelto por el gestor para corrección.
- **`finalizado`**: Técnico envió al gestor para revisión. Solo lectura para técnico.
- **`aprobado`**: Gestor aprobó. Documento archivado permanentemente. Solo lectura para todos.

---

## Tabla: `personal_access_tokens`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `tokenable_type` | `varchar` | No ❌ | *ninguno* |  |
| `tokenable_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `name` | `varchar` | No ❌ | *ninguno* |  |
| `token` | `varchar` | No ❌ | *ninguno* |  |
| `abilities` | `TEXT` | Sí ✅ | *ninguno* |  |
| `last_used_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `expires_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🗂️ Índices
- `personal_access_tokens_token_unique` (Único)
- `personal_access_tokens_tokenable_type_tokenable_id_index`

---

## Tabla: `provincias`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `nombre` | `varchar` | No ❌ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🗂️ Índices
- `provincias_nombre_unique` (Único)

---

## Tabla: `registros`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `tipo_registro` | `varchar` | No ❌ | *ninguno* |  |
| `descripcion` | `varchar` | No ❌ | *ninguno* |  |
| `nombre_usuario` | `varchar` | No ❌ | *ninguno* |  |
| `link` | `varchar` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)

---

## Tabla: `sede_users`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | Sí ✅ | *ninguno* | 🔑 PK |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `user_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `created_at` | `DATETIME` | Sí ✅ | *ninguno* |  |
| `updated_at` | `DATETIME` | Sí ✅ | *ninguno* |  |

---

## Tabla: `sedes`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `nombre_sede` | `varchar` | No ❌ | *ninguno* |  |
| `tipo_sede` | `varchar` | Sí ✅ | *ninguno* |  |
| `direccion` | `varchar` | Sí ✅ | *ninguno* |  |
| `correo` | `varchar` | Sí ✅ | *ninguno* |  |
| `telefono` | `varchar` | Sí ✅ | *ninguno* |  |
| `localidad` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_1_nombre` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_1_cargo` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_1_email` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_2_nombre` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_2_cargo` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_2_email` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_3_nombre` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_3_cargo` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsable_3_email` | `varchar` | Sí ✅ | *ninguno* |  |
| `empresa_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `provincia_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `latitud` | `numeric` | Sí ✅ | *ninguno* |  |
| `longitud` | `numeric` | Sí ✅ | *ninguno* |  |
| `deleted_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `rubro` | `varchar` | Sí ✅ | *ninguno* |  |
| `dias_operativos` | `TEXT` | Sí ✅ | *ninguno* |  |
| `hora_apertura` | `time` | Sí ✅ | *ninguno* |  |
| `hora_cierre` | `time` | Sí ✅ | *ninguno* |  |
| `es_roster_24_7` | `tinyint(1)` | No ❌ | `'0'` |  |
| `radio_tolerancia_metros` | `INTEGER` | No ❌ | `'500'` |  |

### 🔗 Relaciones (Foreign Keys)
- `provincia_id` → vincula con `provincias`(`id`)
- `empresa_id` → vincula con `empresas`(`id`)

---

## Tabla: `subcategories`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `category_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `code` | `varchar` | No ❌ | *ninguno* |  |
| `name` | `varchar` | No ❌ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `is_active` | `tinyint(1)` | No ❌ | `'1'` |  |
| `order` | `INTEGER` | No ❌ | `'0'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `category_id` → vincula con `categories`(`id`)

### 🗂️ Índices
- `subcategories_code_unique` (Único)
- `subcategories_order_index`
- `subcategories_category_id_is_active_index`

---

## Tabla: `training_attendances`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `training_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `visit_training_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `employee_name` | `varchar` | Sí ✅ | *ninguno* |  |
| `dni` | `varchar` | Sí ✅ | *ninguno* |  |
| `signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `user_id` → vincula con `users`(`id`)
- `training_id` → vincula con `trainings`(`id`)
- `visit_training_id` → vincula con `visit_trainings`(`id`)

---

## Tabla: `trainings`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `empresa_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `sede_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `title` | `varchar` | No ❌ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `type` | `varchar` | No ❌ | `'presencial'` |  |
| `date` | `datetime` | No ❌ | *ninguno* |  |
| `duration` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `trainer_name` | `varchar` | Sí ✅ | *ninguno* |  |
| `status` | `varchar` | No ❌ | `'scheduled'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `created_by_user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `is_template` | `tinyint(1)` | No ❌ | `'1'` |  |
| `notes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `dynamic_checklist_id` | `INTEGER` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `dynamic_checklist_id` → vincula con `dynamic_checklists`(`id`)
- `user_id` → vincula con `users`(`id`)
- `empresa_id` → vincula con `empresas`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `created_by_user_id` → vincula con `users`(`id`)

---

## Tabla: `users`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `email` | `varchar` | No ❌ | *ninguno* |  |
| `email_verified_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `password` | `varchar` | No ❌ | *ninguno* |  |
| `remember_token` | `varchar` | Sí ✅ | *ninguno* |  |
| `nombres` | `varchar` | Sí ✅ | *ninguno* |  |
| `apellidos` | `varchar` | Sí ✅ | *ninguno* |  |
| `genero` | `varchar` | Sí ✅ | *ninguno* |  |
| `telefono` | `varchar` | Sí ✅ | *ninguno* |  |
| `n_doc` | `varchar` | Sí ✅ | *ninguno* |  |
| `rol` | `varchar` | Sí ✅ | *ninguno* |  |
| `matricula` | `varchar` | Sí ✅ | *ninguno* |  |
| `estado` | `tinyint(1)` | No ❌ | `'1'` |  |
| `firma` | `varchar` | Sí ✅ | *ninguno* |  |
| `firma_temp` | `varchar` | Sí ✅ | *ninguno* |  |
| `firma_status` | `varchar` | No ❌ | `'approved'` |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `deleted_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `consultora_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `must_change_password` | `tinyint(1)` | No ❌ | `'0'` |  |

### 🔗 Relaciones (Foreign Keys)
- `consultora_id` → vincula con `consultoras`(`id`)

### 🗂️ Índices
- `users_email_unique` (Único)

---

## Tabla: `visit_certificates`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `user_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `sede_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `empresa_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `visit_date` | `date` | No ❌ | *ninguno* |  |
| `start_time` | `datetime` | Sí ✅ | *ninguno* |  |
| `end_time` | `datetime` | Sí ✅ | *ninguno* |  |
| `status` | `varchar` | No ❌ | `'draft'` |  |
| `general_observations` | `TEXT` | Sí ✅ | *ninguno* |  |
| `signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `notes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `responsible_name` | `varchar` | Sí ✅ | *ninguno* |  |
| `responsible_signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `signature_skip_reason` | `TEXT` | Sí ✅ | *ninguno* |  |
| `email_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `token_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `code` | `varchar` | Sí ✅ | *ninguno* |  |
| `date_firma_responsable` | `datetime` | Sí ✅ | *ninguno* |  |
| `visit_type` | `varchar` | Sí ✅ | *ninguno* |  |
| `instructor_signature` | `TEXT` | Sí ✅ | *ninguno* |  |
| `instructor_signature_date` | `datetime` | Sí ✅ | *ninguno* |  |
| `aclaracion_responsable` | `varchar` | Sí ✅ | *ninguno* |  |
| `token_expires_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `token_used` | `tinyint(1)` | No ❌ | `'0'` |  |
| `horas_gabinete_estimadas` | `numeric` | Sí ✅ | *ninguno* |  |
| `checkin_lat` | `numeric` | Sí ✅ | *ninguno* |  |
| `checkin_lng` | `numeric` | Sí ✅ | *ninguno* |  |
| `checkout_lat` | `numeric` | Sí ✅ | *ninguno* |  |
| `checkout_lng` | `numeric` | Sí ✅ | *ninguno* |  |
| `gps_verified` | `tinyint(1)` | No ❌ | `'0'` |  |

### 🔗 Relaciones (Foreign Keys)
- `empresa_id` → vincula con `empresas`(`id`)
- `sede_id` → vincula con `sedes`(`id`)
- `user_id` → vincula con `users`(`id`)

---

## Tabla: `visit_checklist_completions`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `visit_certificate_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `checklist_assignment_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `is_self_assigned` | `tinyint(1)` | No ❌ | `'0'` |  |
| `completed_at` | `datetime` | No ❌ | `CURRENT_TIMESTAMP` |  |
| `responses` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `checklist_assignment_id` → vincula con `checklist_assignments`(`id`)
- `visit_certificate_id` → vincula con `visit_certificates`(`id`)

---

## Tabla: `visit_trainings`

| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |
|---|---|---|---|---|
| `id` | `INTEGER` | No ❌ | *ninguno* | 🔑 PK |
| `visit_certificate_id` | `INTEGER` | No ❌ | *ninguno* |  |
| `training_id` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `is_adhoc` | `tinyint(1)` | No ❌ | `'0'` |  |
| `title` | `varchar` | Sí ✅ | *ninguno* |  |
| `description` | `TEXT` | Sí ✅ | *ninguno* |  |
| `duration_minutes` | `INTEGER` | Sí ✅ | *ninguno* |  |
| `materials_delivered` | `TEXT` | Sí ✅ | *ninguno* |  |
| `notes` | `TEXT` | Sí ✅ | *ninguno* |  |
| `created_at` | `datetime` | Sí ✅ | *ninguno* |  |
| `updated_at` | `datetime` | Sí ✅ | *ninguno* |  |

### 🔗 Relaciones (Foreign Keys)
- `training_id` → vincula con `trainings`(`id`)
- `visit_certificate_id` → vincula con `visit_certificates`(`id`)

---

