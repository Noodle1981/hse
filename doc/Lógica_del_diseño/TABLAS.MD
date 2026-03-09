# 🗄️ Esquema de Base de Datos - EP-Back V2.0.0.0

> **Estado:** Documentación Completa de Tablas ✅  
> **Última actualización:** 30 Diciembre 2024  
> **Total de Tablas:** 46

---

## 📋 Índice de Tablas

### Tablas del Sistema (Laravel)
1. [users](#1-users) - Usuarios del sistema
2. [cache](#2-cache) - Caché del sistema
3. [jobs](#3-jobs) - Cola de trabajos
4. [personal_access_tokens](#4-personal_access_tokens) - Tokens de API

### Empresas y Sedes
5. [empresas](#5-empresas) - Empresas/Clientes
6. [sedes](#6-sedes) - Sedes/Sucursales
7. [sede_users](#7-sede_users) - Asignación técnicos-sedes

### Checklists Dinámicos
8. [categories](#8-categories) - Categorías
9. [subcategories](#9-subcategories) - Subcategorías
10. [dynamic_checklists](#10-dynamic_checklists) - Checklists
11. [checklist_sections](#11-checklist_sections) - Secciones
12. [dynamic_questions](#12-dynamic_questions) - Preguntas
13. [checklist_assignments](#13-checklist_assignments) - Asignaciones
14. [dynamic_inspections](#14-dynamic_inspections) - Inspecciones
15. [dynamic_answers](#15-dynamic_answers) - Respuestas

### Capacitaciones
16. [trainings](#16-trainings) - Capacitaciones
17. [training_attendances](#17-training_attendances) - Asistencias
18. [capacitacion_presencials](#18-capacitacion_presencials) - (Legacy)
19. [capacitacion_viruals](#19-capacitacion_viruals) - (Legacy)
20. [capacitacion_virual_asistentes](#20-capacitacion_virual_asistentes) - (Legacy)

### Constancias de Visita
21. [generador_constancias](#21-generador_constancias) - Constancias
22. [constancia_actividades](#22-constancia_actividades) - Actividades vinculadas

### Otros
35. [annual_plans](#27-annual_plans) - Planes anuales
36. [informes](#28-informes) - Informes (Legacy)
37. [items](#29-items) - Items (Legacy)
38. [registros](#30-registros) - Registros (Legacy)

---

## 📊 Detalle de Tablas

### 1. users

**Descripción:** Usuarios del sistema (Administradores y Técnicos)

```sql
users
├── id (PK)
├── nombres
├── apellidos
├── email (UNIQUE)
├── email_verified_at
├── password
├── rol                    -- 'Administrador' | 'Técnico'
├── is_active
├── remember_token
├── created_at
└── updated_at
```

**Relaciones:**
- `hasMany` → sedes (via sede_users)
- `hasMany` → inspections
- `hasMany` → trainings
- `hasMany` → hallazgos

---

### 2. cache

**Descripción:** Sistema de caché de Laravel

```sql
cache
├── key (PK)
├── value
└── expiration
```

---

### 3. jobs

**Descripción:** Cola de trabajos en background

```sql
jobs
├── id (PK)
├── queue
├── payload
├── attempts
├── reserved_at
├── available_at
└── created_at
```

---

### 4. personal_access_tokens

**Descripción:** Tokens Sanctum para API

```sql
personal_access_tokens
├── id (PK)
├── tokenable_type
├── tokenable_id
├── name
├── token (UNIQUE)
├── abilities
├── last_used_at
├── expires_at
├── created_at
└── updated_at
```

---

### 5. empresas

**Descripción:** Empresas/Clientes del sistema

```sql
empresas
├── id (PK)
├── nombre
├── razon_social
├── cuit (UNIQUE)
├── created_at
└── updated_at
```

**Relaciones:**
- `hasMany` → sedes
- `hasMany` → employees

**Índices:**
- UNIQUE(cuit)

---

### 6. sedes

**Descripción:** Sedes/Sucursales de las empresas

```sql
sedes
├── id (PK)
├── empresa_id (FK → empresas)
├── nombre_sede
├── direccion
├── correo
├── telefono
├── tipo_sede
├── localidad
├── responsable_1_nombre
├── responsable_1_cargo
├── responsable_1_email
├── responsable_2_nombre
├── responsable_2_cargo
├── responsable_2_email
├── responsable_3_nombre
├── responsable_3_cargo
├── responsable_3_email
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → empresa
- `belongsToMany` → users (via sede_users)
- `hasMany` → inspections
- `hasMany` → trainings
- `hasMany` → employees

**Índices:**
- FK(empresa_id)

---

### 7. sede_users

**Descripción:** Tabla pivot - Asignación de técnicos a sedes (Many-to-Many)

```sql
sede_users
├── id (PK)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── created_at
└── updated_at
```

**Índices:**
- FK(sede_id)
- FK(user_id)
- UNIQUE(sede_id, user_id)

---

### 8. categories

**Descripción:** Categorías principales de checklists

```sql
categories
├── id (PK)
├── name
├── icon
├── color
├── order
├── created_at
└── updated_at
```

**Relaciones:**
- `hasMany` → subcategories
- `hasMany` → checklists

---

### 9. subcategories

**Descripción:** Subcategorías de checklists

```sql
subcategories
├── id (PK)
├── category_id (FK → categories)
├── name
├── order
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → category
- `hasMany` → checklists

---

### 10. dynamic_checklists

**Descripción:** Catálogo de checklists

```sql
dynamic_checklists
├── id (PK)
├── title
├── description
├── category_id (FK → categories)
├── subcategory_id (FK → subcategories)
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → category
- `belongsTo` → subcategory
- `hasMany` → sections
- `hasMany` → assignments
- `hasMany` → inspections

---

### 11. checklist_sections

**Descripción:** Secciones dentro de un checklist

```sql
checklist_sections
├── id (PK)
├── checklist_id (FK → dynamic_checklists)
├── title
├── order
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → checklist
- `hasMany` → questions

---

### 12. dynamic_questions

**Descripción:** Preguntas dentro de secciones

```sql
dynamic_questions
├── id (PK)
├── section_id (FK → checklist_sections)
├── question_text
├── question_type     -- text|textarea|select|checkbox|radio|file
├── options          -- JSON para select/radio/checkbox
├── is_required
├── order
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → section
- `hasMany` → answers

---

### 13. checklist_assignments

**Descripción:** Asignaciones de checklists a técnicos

```sql
checklist_assignments
├── id (PK)
├── checklist_id (FK → dynamic_checklists)
├── user_id (FK → users)
├── sede_id (FK → sedes)
├── status              -- pending|in_progress|completed
├── start_date
├── end_date
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → checklist
- `belongsTo` → user
- `belongsTo` → sede

---

### 14. dynamic_inspections

**Descripción:** Inspecciones completadas

```sql
dynamic_inspections
├── id (PK)
├── dynamic_checklist_id (FK → dynamic_checklists)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── status                -- in_progress|completed
├── firma_tecnico
├── fecha_firma_tecnico
├── firma_responsable
├── fecha_firma_responsable
├── firma_no_disponible
├── justificacion_firma
├── completed_at
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → checklist
- `belongsTo` → sede
- `belongsTo` → user
- `hasMany` → answers
- `morphToMany` → constancias (via constancia_actividades)

**Índices:**
- FK(dynamic_checklist_id)
- FK(sede_id)
- FK(user_id)
- INDEX(status)
- INDEX(completed_at)

---

### 15. dynamic_answers

**Descripción:** Respuestas de inspecciones

```sql
dynamic_answers
├── id (PK)
├── inspection_id (FK → dynamic_inspections)
├── question_id (FK → dynamic_questions)
├── answer_value
├── observation
├── photo_path
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → inspection
- `belongsTo` → question

---

### 16. trainings

**Descripción:** Capacitaciones (nuevo sistema)

```sql
trainings
├── id (PK)
├── empresa_id (FK → empresas)
├── sede_id (FK → sedes)
├── user_id (FK → users, responsable)
├── created_by_user_id (FK → users, creador)
├── title
├── description
├── type                 -- presencial|virtual
├── notes
├── is_template          -- boolean
├── dynamic_checklist_id (FK → checklists, nullable)
├── training_date
├── duration
├── trainer_name
├── status              -- pending|completed
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → empresa
- `belongsTo` → sede
- `belongsTo` → user (responsable)
- `hasMany` → attendances
- `morphToMany` → constancias

---

### 17. training_attendances

**Descripción:** Asistencias a capacitaciones

```sql
training_attendances
├── id (PK)
├── training_id (FK → trainings)
├── user_id (FK → users, nullable)
├── employee_name
├── dni
├── signature          -- Base64
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → training
- `belongsTo` → user (opcional)

---

### 18-20. capacitacion_* (Legacy)

**Descripción:** Tablas legacy del sistema anterior de capacitaciones

**Estado:** Mantenidas por compatibilidad, no se usan en V2.0

---

### 21. generador_constancias

**Descripción:** Constancias de visita

```sql
generador_constancias
├── id (PK)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── fecha_visita
├── tipo_visita
├── observacion
├── firma_instructor
├── date_firma_instructor
├── firma_responsable
├── date_firma_responsable
├── justificacion_firma
├── email_responsable
├── aclaracion_responsable
├── token_responsable
├── code
├── duration
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → sede
- `belongsTo` → user
- `morphToMany` → inspections
- `morphToMany` → trainings
- `morphToMany` → hallazgos

---

### 22. constancia_actividades

**Descripción:** Tabla pivot polimórfica - Actividades vinculadas a constancias

```sql
constancia_actividades
├── id (PK)
├── generador_constancia_id (FK → generador_constancias)
├── actividad_type      -- DynamicInspection|Training|RiskEvaluation|...
├── actividad_id
├── created_at
└── updated_at
```

**Índices:**
- FK(generador_constancia_id)
- INDEX(actividad_type, actividad_id)

---



### 31. hallazgos

**Descripción:** Hallazgos de seguridad

```sql
hallazgos
├── id (PK)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── titulo
├── descripcion
├── ubicacion_especifica
├── criticidad          -- bajo|medio|alto|critico
├── estado             -- pendiente|en_proceso|resuelto|verificado|cerrado
├── plazo_correccion
├── responsable_correccion
├── fecha_deteccion
├── fecha_resolucion
├── origen_type        -- Polimórfico
├── origen_id
├── visit_certificate_id (FK → visit_certificates, nullable)
├── checklist_completion_id (FK → visit_checklist_completions, nullable)
├── question_id (FK → dynamic_questions, nullable)
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → sede
- `belongsTo` → user
- `morphTo` → origen
- `hasMany` → seguimientos
- `hasMany` → fotos
- `hasMany` → incidentes
- `morphToMany` → constancias

**Índices:**
- INDEX(criticidad)
- INDEX(estado)
- INDEX(plazo_correccion)

---

### 32. hallazgo_seguimientos

**Descripción:** Seguimiento de hallazgos

```sql
hallazgo_seguimientos
├── id (PK)
├── hallazgo_id (FK → hallazgos)
├── user_id (FK → users)
├── fecha_seguimiento
├── descripcion
├── estado_anterior
├── estado_nuevo
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → hallazgo
- `belongsTo` → user

---

### 33. hallazgo_fotos

**Descripción:** Fotos de evidencia de hallazgos

```sql
hallazgo_fotos
├── id (PK)
├── hallazgo_id (FK → hallazgos)
├── tipo_foto           -- antes|durante|despues
├── ruta_archivo
├── descripcion
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → hallazgo

---

### 34. incidentes

**Descripción:** Incidentes relacionados con hallazgos

```sql
incidentes
├── id (PK)
├── hallazgo_id (FK → hallazgos)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── fecha_incidente
├── tipo_incidente
├── gravedad            -- leve|moderado|grave|fatal
├── descripcion
├── personas_afectadas
├── acciones_inmediatas
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → hallazgo
- `belongsTo` → sede
- `belongsTo` → user

---

### 35. annual_plans

**Descripción:** Planes anuales de contrato

```sql
annual_plans
├── id (PK)
├── sede_id (FK → sedes)
├── category            -- Capacitación|Inspección|Registro
├── activity_name
├── activity_code
├── checklist_id (FK → dynamic_checklists, nullable)
├── year
├── frequency            -- enum(monthly, quarterly, bi_annual, annual, ad_hoc)
├── january (boolean)
├── february (boolean)
├── march (boolean)
├── april (boolean)
├── may (boolean)
├── june (boolean)
├── july (boolean)
├── august (boolean)
├── september (boolean)
├── october (boolean)
├── november (boolean)
├── december (boolean)
├── created_at
└── updated_at
```

**Relaciones:**
- `belongsTo` → sede
- `belongsTo` → checklist

---

### 36-38. informes, items, registros (Legacy)

**Descripción:** Tablas legacy del sistema anterior

**Estado:** Mantenidas por compatibilidad

---

## 📊 Estadísticas de Base de Datos

### Resumen

| Categoría | Cantidad |
|-----------|----------|
| **Total de Tablas** | 46 |
| Sistema | 4 |
| Empresas/Sedes | 3 |
| Checklists | 8 |
| Capacitaciones | 5 (2 nuevas + 3 legacy) |
| EPP | 4 |
| Riesgos | 4 |
| Hallazgos | 4 |
| Constancias | 2 |
| Otros | 3 |
| Legacy | 9 |

### Relaciones

| Tipo de Relación | Cantidad |
|------------------|----------|
| One-to-Many | 45+ |
| Many-to-Many | 4 |
| Polimórficas | 2 |

### Índices

- **Primary Keys:** 46
- **Foreign Keys:** 60+
- **Unique Constraints:** 5
- **Índices Adicionales:** 15+

---

## 🔗 Diagrama de Relaciones Principales

```
users
  ├── One-to-Many → inspections
  ├── One-to-Many → trainings
  ├── One-to-Many → hallazgos
  └── Many-to-Many → sedes (via sede_users)

empresas
  ├── One-to-Many → sedes
  ├── One-to-Many → employees
  └── One-to-Many → risk_matrices

sedes
  ├── Many-to-Many → users (via sede_users)
  ├── One-to-Many → inspections
  ├── One-to-Many → trainings
  ├── One-to-Many → hallazgos
  └── One-to-Many → annual_plans

dynamic_checklists
  ├── One-to-Many → sections
  ├── One-to-Many → assignments
  └── One-to-Many → inspections

dynamic_inspections
  ├── One-to-Many → answers
  └── Morph-to-Many → constancias

hallazgos
  ├── One-to-Many → seguimientos
  ├── One-to-Many → fotos
  ├── One-to-Many → incidentes
  └── Morph-to → origen (inspections, constancias, etc.)
```

---

## 💾 Tamaño Estimado

### Por Módulo (10,000 registros)

| Tabla | Tamaño Estimado |
|-------|-----------------|
| users | ~500 KB |
| empresas | ~200 KB |
| sedes | ~1 MB |
| dynamic_checklists | ~2 MB |
| dynamic_inspections | ~15 MB |
| dynamic_answers | ~50 MB |
| trainings | ~5 MB |
| hallazgos | ~10 MB |
| epp_deliveries | ~8 MB |

**Total estimado (100K inspecciones):** ~500 MB - 1 GB

---

## 🔜 Migraciones Futuras (Roadmap)

### Próximas Tablas

1. **notifications**
   - Sistema de notificaciones
   - Email/Push/In-app

2. **audit_logs**
   - Registro de cambios
   - Trazabilidad completa

3. **settings**
   - Configuración del sistema
   - Por empresa/sede

4. **templates**
   - Plantillas de checklists
   - Plantillas de reportes

---

*EP-Back V2.0.0.0 | Esquema Completo de 46 Tablas | Diciembre 2024*
