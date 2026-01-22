# Especificación Técnica: Sistema Integrado de Visitas

**Versión**: 1.0 Definitiva  
**Fecha**: 2026-01-21  
**Estado**: Aprobado para implementación

---

## 📋 Resumen Ejecutivo

Implementar un sistema donde las **Visitas** actúan como contenedor universal de todas las actividades del técnico en una sede, integrando:
- Checklists (asignados y auto-asignados)
- Capacitaciones (planificadas y ad-hoc)
- Entregas de EPP
- Hallazgos
- Incidentes
- Constancia formal con firma digital

---

## 🎯 Objetivos

### Problema Actual
- Módulos independientes y desconectados
- Duplicación de datos (fecha, sede, técnico)
- Experiencia de usuario fragmentada
- Falta de contexto completo de la visita

### Solución Propuesta
- **Visita** como contenedor único
- Flujo integrado y cohesivo
- Trazabilidad completa
- Constancia automática con toda la información

---

## 🏗️ Arquitectura de Datos

### Modelo de Datos Unificado

``` SQLITE
-- Tabla principal: Visitas
visit_certificates
├── id
├── user_id (técnico)
├── sede_id
├── empresa_id
├── visit_date
├── start_time
├── end_time
├── status (draft, in_progress, completed, signed)
├── general_observations
├── signature
└── timestamps

-- Checklists completados en la visita
visit_checklist_completions
├── id
├── visit_certificate_id
├── checklist_assignment_id
├── is_self_assigned (boolean - si el técnico se lo auto-asignó)
├── completed_at
├── responses (JSON)
└── timestamps

-- Capacitaciones impartidas en la visita
visit_trainings
├── id
├── visit_certificate_id
├── training_id (NULL si es ad-hoc)
├── is_adhoc (boolean)
├── title
├── description
├── duration_minutes
├── attendees (JSON: [{name, position, signature}])
├── materials_delivered
├── notes
└── timestamps

-- Entregas de EPP en la visita
visit_epp_deliveries
├── id
├── visit_certificate_id
├── employee_id
├── epp_items (JSON: [{item, quantity, size}])
├── employee_signature
├── notes
└── timestamps

-- Hallazgos (vinculados opcionalmente a visita)
hallazgos
├── ... (campos existentes)
├── visit_certificate_id (NULLABLE)
├── checklist_completion_id (NULLABLE)
└── question_id (NULLABLE)

-- Incidentes (vinculados opcionalmente a visita)
incidentes
├── ... (campos existentes)
└── visit_certificate_id (NULLABLE)

-- Capacitaciones (catálogo)
trainings
├── id
├── created_by_user_id (admin o técnico)
├── assigned_by_user_id (NULL si es ad-hoc)
├── title
├── description
├── duration_minutes
├── is_template (true = admin, false = ad-hoc técnico)
├── status
└── timestamps
```

---

## 🔄 Flujo de Trabajo del Técnico

### 1. Inicio de Visita

```
Técnico llega a la sede
    ↓
Abre app → "Mis Visitas Programadas"
    ↓
Selecciona sede → "Iniciar Visita"
    ↓
Sistema crea visit_certificate (status: in_progress)
    ↓
Redirige a "Visita en Progreso"
```

### 2. Durante la Visita

El técnico puede realizar múltiples actividades:

#### A. Completar Checklists
```
Ver lista de checklists asignados
    ├─ Completar asignados
    └─ Auto-asignarse adicionales (opcional)
```

#### B. Impartir Capacitaciones
```
Opción 1: Capacitación asignada
    └─ Seleccionar de lista → Impartir → Registrar asistentes

Opción 2: Capacitación ad-hoc (urgencia)
    └─ Crear nueva → Definir tema → Impartir → Registrar
```

#### C. Entregar EPP
```
Seleccionar empleado
    ↓
Elegir elementos del catálogo
    ↓
Capturar firma del empleado
    ↓
Guardar entrega
```

#### D. Registrar Hallazgos
```
Detectar problema
    ↓
Crear hallazgo
    ├─ Vincular a checklist/pregunta (opcional)
    ├─ Agregar fotos
    └─ Definir severidad
```

#### E. Reportar Incidentes
```
Ocurre evento de seguridad
    ↓
Crear incidente
    ├─ Tipo de incidente
    ├─ Personas involucradas
    └─ Descripción detallada
```

### 3. Finalizar Visita

```
Técnico completa actividades
    ↓
Click "Finalizar Visita"
    ↓
Sistema muestra resumen:
    ├─ X checklists completados
    ├─ X capacitaciones impartidas
    ├─ X entregas de EPP
    ├─ X hallazgos registrados
    └─ X incidentes reportados
    ↓
Técnico agrega observaciones generales
    ↓
Firma digitalmente
    ↓
Sistema genera constancia completa (PDF)
    ↓
visit_certificate.status = 'signed'
```

---

## 🎨 Diseño de Interfaz

### Pantalla: Visita en Progreso

```
┌─────────────────────────────────────────┐
│ 🚧 Visita en Progreso                   │
│ Constructora ABC - Torre Norte          │
│ Inicio: 09:30 | Duración: 1h 23m        │
├─────────────────────────────────────────┤
│                                         │
│ 📋 CHECKLISTS (3)                       │
│ ✅ Control de EPP                       │
│ ⏳ Inspección Eléctrica                 │
│ ⬜ Orden y Limpieza                     │
│ [+ Auto-asignar Checklist]              │
│                                         │
│ 🎓 CAPACITACIONES (1)                   │
│ ✅ Uso de EPP - 15 asistentes           │
│ [+ Capacitación Asignada]               │
│ [+ Capacitación Ad-hoc]                 │
│                                         │
│ 🦺 ENTREGAS DE EPP (3)                  │
│ ✅ Juan Pérez - Casco, Guantes          │
│ ✅ María García - Arnés                 │
│ ✅ Pedro López - Botas                  │
│ [+ Entregar EPP]                        │
│                                         │
│ ⚠️ HALLAZGOS (2)                        │
│ ⚠️ Cable expuesto - Severidad Media     │
│ ⚠️ Extintor vencido - Severidad Alta    │
│ [+ Registrar Hallazgo]                  │
│                                         │
│ 🚨 INCIDENTES (0)                       │
│ Sin incidentes                          │
│ [+ Reportar Incidente]                  │
│                                         │
├─────────────────────────────────────────┤
│ [💾 Pausar] [✓ Finalizar Visita]       │
└─────────────────────────────────────────┘
```

---

## 🔐 Modelo de Autonomía del Técnico

| Módulo | Crear | Auto-asignar | Ejecutar | Requiere Admin |
|--------|-------|--------------|----------|----------------|
| **Checklists** | ❌ | ✅ | ✅ | Creación |
| **Capacitaciones** | ✅ (ad-hoc) | ✅ | ✅ | No |
| **Entregas EPP** | ✅ | - | ✅ | No |
| **Hallazgos** | ✅ | - | ✅ | No |
| **Incidentes** | ✅ | - | ✅ | No |

### Principios
1. **Autonomía en campo**: El técnico puede actuar sin esperar al gestor
2. **Flexibilidad**: Capacitaciones ad-hoc para urgencias
3. **Trazabilidad**: Todo queda registrado en la visita
4. **Revisión posterior**: El admin puede revisar actividades ad-hoc

---

## 💻 Implementación Técnica

### Fase 1: Base de Datos (3 horas)
- Crear migración para `visit_checklist_completions`
- Crear migración para `visit_trainings`
- Crear migración para `visit_epp_deliveries`
- Agregar campos a `hallazgos` e `incidentes`
- Actualizar `visit_certificates` y `trainings`

### Fase 2: Modelos (2 horas)
- Actualizar modelo `VisitCertificate` con relaciones
- Crear modelo `VisitChecklistCompletion`
- Crear modelo `VisitTraining`
- Crear modelo `VisitEppDelivery`
- Actualizar modelos `Hallazgo`, `Incidente`, `Training`

### Fase 3: Servicio (3 horas)
- Crear `VisitService` con métodos:
  - `startVisit()`
  - `completeChecklist()`
  - `addTraining()`
  - `deliverEpp()`
  - `finalizeVisit()`

### Fase 4: Componentes Livewire (6 horas)
- `VisitManager` - Gestión principal de visitas
- `VisitInProgress` - Vista durante la visita
- `ChecklistFiller` - Completar checklists
- `TrainingAdHoc` - Crear capacitaciones ad-hoc
- `EppDelivery` - Entregar EPP
- `VisitFinalizer` - Finalizar y firmar

### Fase 5: Vistas (4 horas)
- `start-visit.blade.php`
- `visit-in-progress.blade.php`
- `finalize-visit.blade.php`
- Actualizar rutas

### Fase 6: Testing (2 horas)
- Tests manuales de cada flujo
- Verificar compatibilidad con datos existentes

**Total estimado**: ~20 horas

---

## ✅ Criterios de Aceptación

1. ✅ El técnico puede iniciar una visita a una sede
2. ✅ Durante la visita puede completar múltiples checklists
3. ✅ Puede auto-asignarse checklists adicionales
4. ✅ Puede crear e impartir capacitaciones ad-hoc
5. ✅ Puede entregar EPP con firma del empleado
6. ✅ Puede registrar hallazgos vinculados a la visita
7. ✅ Puede reportar incidentes durante la visita
8. ✅ Al finalizar, genera constancia completa con firma
9. ✅ Los datos antiguos siguen siendo accesibles
10. ✅ El sistema funciona en móvil (responsive)

---

## 🚨 Consideraciones Importantes

### Compatibilidad con Datos Existentes
- Todos los campos nuevos son **NULLABLE**
- Los registros antiguos funcionan sin modificación
- Migración gradual al nuevo flujo

### Modelo Híbrido
- Las actividades PUEDEN vincularse a visita (ideal)
- Pero también pueden ser independientes (flexibilidad)
- `visit_certificate_id` es NULLABLE en todas las tablas

### Visitas Flexibles
- Una visita puede tener 0 checklists (solo capacitación/EPP)
- Debe tener al menos UNA actividad registrada
- Puede pausarse y continuarse después

---

## 📊 Métricas de Éxito

- Reducción del 70% en duplicación de datos
- Tiempo de registro reducido en 50%
- 100% de visitas con constancia completa
- 0 errores de inconsistencia de datos

---

## 🔄 Plan de Rollout

1. **Semana 1**: Implementar Fases 1-3 (backend)
2. **Semana 2**: Implementar Fases 4-5 (frontend)
3. **Semana 3**: Testing y ajustes
4. **Semana 4**: Despliegue gradual con usuarios piloto
5. **Semana 5**: Rollout completo

---

**Aprobado por**: Equipo de Desarrollo  
**Próximo paso**: Iniciar Fase 1 - Migraciones de Base de Datos
