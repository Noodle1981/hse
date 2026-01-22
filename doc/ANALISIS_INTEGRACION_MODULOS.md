# Análisis de Integración: Checklists, Visitas, Hallazgos e Incidentes

**Fecha**: 2026-01-21  
**Módulos analizados**: Checklists, Constancias de Visita, Hallazgos, Incidentes

---

## 🎯 Pregunta Clave

**¿Cómo se relacionan estos módulos en el flujo de trabajo del técnico?**

Cuando un técnico completa un checklist en `/checklists/my-assignments`:
1. ¿Se genera automáticamente una Constancia de Visita?
2. ¿Puede registrar Hallazgos encontrados durante la inspección?
3. ¿Puede reportar Incidentes en el mismo flujo?

---

## 📊 Estado Actual de los Módulos

### 1. Checklists (`/checklists/my-assignments`)
**Propósito**: Técnico completa checklists asignados  
**Estado**: ✅ Implementado (asignaciones)  
**Pendiente**: ⏳ Vista de completación

### 2. Constancias de Visita (`/visit-certificates/my-history`)
**Propósito**: Registro formal de visitas a sedes  
**Estado**: ✅ Implementado  
**Campos**: Fecha, sede, técnico, observaciones, firma

### 3. Hallazgos (`/hallazgos` o `/mis-hallazgos`)
**Propósito**: Registrar no conformidades o problemas encontrados  
**Estado**: ✅ Implementado  
**Campos**: Descripción, severidad, ubicación, fotos, estado

### 4. Incidentes (`/incidentes`)
**Propósito**: Reportar eventos de seguridad o emergencias  
**Estado**: ✅ Implementado  
**Campos**: Tipo, descripción, fecha/hora, personas involucradas

---

## 🔄 Flujos de Trabajo Posibles

### Opción A: Módulos Independientes (Actual)
```
Técnico va a sede
    ↓
Completa checklist → Guarda en BD
    ↓
(Por separado) Crea constancia de visita
    ↓
(Por separado) Registra hallazgos si los hay
    ↓
(Por separado) Reporta incidentes si ocurren
```

**Ventajas**:
- Flexibilidad
- Módulos desacoplados
- Fácil mantenimiento

**Desventajas**:
- ❌ Duplicación de datos (fecha, sede, técnico)
- ❌ Múltiples pasos manuales
- ❌ Posible inconsistencia
- ❌ Experiencia de usuario fragmentada

---

### Opción B: Flujo Integrado con Visita como Contenedor (Recomendado)

```
Técnico inicia "Nueva Visita" a sede
    ↓
Sistema crea Constancia de Visita (borrador)
    ↓
Técnico completa checklists asignados
    │
    ├─→ Encuentra hallazgo → Registra en la visita
    │
    ├─→ Ocurre incidente → Reporta en la visita
    │
    └─→ Completa todos los checklists
    ↓
Técnico finaliza visita (firma digital)
    ↓
Sistema genera constancia completa con:
    - Checklists completados
    - Hallazgos encontrados
    - Incidentes reportados
    - Firma y timestamp
```

**Ventajas**:
- ✅ Datos centralizados
- ✅ Contexto completo de la visita
- ✅ Mejor UX para el técnico
- ✅ Trazabilidad completa
- ✅ Generación automática de documentos

**Desventajas**:
- Requiere refactorización
- Mayor complejidad inicial

---

### Opción C: Flujo Híbrido

```
Técnico completa checklist
    ↓
Al finalizar, sistema pregunta:
    "¿Deseas crear constancia de visita?"
    ↓
    ├─→ SÍ: Abre modal con datos pre-llenados
    │        - Fecha: hoy
    │        - Sede: de la asignación
    │        - Checklist: adjunto automáticamente
    │        - Opción: "Agregar hallazgos"
    │        - Opción: "Reportar incidente"
    │
    └─→ NO: Solo guarda el checklist completado
```

---

## 🏗️ Propuesta de Arquitectura Integrada

### Modelo de Datos Unificado

```
visit_certificates (Contenedor principal)
├── id
├── user_id (técnico)
├── sede_id
├── empresa_id
├── visit_date
├── start_time
├── end_time
├── status (draft, completed, signed)
├── signature
├── general_observations
└── created_at

visit_checklist_completions (Relación N:N)
├── id
├── visit_certificate_id
├── checklist_assignment_id
├── completed_at
└── responses (JSON)

hallazgos (Vinculados a visita)
├── id
├── visit_certificate_id ← NUEVO
├── checklist_completion_id ← NUEVO (opcional)
├── question_id ← NUEVO (opcional - hallazgo en pregunta específica)
├── descripcion
├── severidad
├── ubicacion
├── fotos
└── estado

incidentes (Vinculados a visita)
├── id
├── visit_certificate_id ← NUEVO
├── tipo
├── descripcion
├── fecha_hora
└── personas_involucradas
```

---

## 📱 Propuesta de Interfaz de Usuario

### Flujo del Técnico

#### 1. Pantalla Inicial: "Mis Visitas Programadas"
```
┌─────────────────────────────────────┐
│ 📅 Visitas Programadas - Hoy        │
├─────────────────────────────────────┤
│ 🏢 Constructora ABC                 │
│ 📍 Obra Central - Torre Norte       │
│ ✓ 3 checklists pendientes           │
│ [Iniciar Visita] ──────────────────►│
└─────────────────────────────────────┘
```

#### 2. Durante la Visita
```
┌─────────────────────────────────────┐
│ 🚧 Visita en Progreso               │
│ Constructora ABC - Torre Norte      │
│ Inicio: 09:30                       │
├─────────────────────────────────────┤
│ Checklists:                         │
│ ✅ Control de EPP (completado)      │
│ ⏳ Inspección Eléctrica (en curso)  │
│ ⬜ Orden y Limpieza (pendiente)     │
├─────────────────────────────────────┤
│ [+ Registrar Hallazgo]              │
│ [⚠️ Reportar Incidente]             │
│                                     │
│ [Finalizar Visita]                  │
└─────────────────────────────────────┘
```

#### 3. Finalizar Visita
```
┌─────────────────────────────────────┐
│ ✓ Finalizar Visita                  │
├─────────────────────────────────────┤
│ Resumen:                            │
│ • 3 checklists completados          │
│ • 2 hallazgos registrados           │
│ • 0 incidentes                      │
│                                     │
│ Observaciones generales:            │
│ [________________________]          │
│                                     │
│ Firma digital:                      │
│ [Área de firma]                     │
│                                     │
│ [Cancelar] [Guardar y Firmar]       │
└─────────────────────────────────────┘
```

---

## 🎯 Recomendación

### Implementar **Opción B: Flujo Integrado**

**Razones**:
1. **Mejor experiencia de usuario**: Todo en un solo flujo
2. **Datos más completos**: Contexto completo de la visita
3. **Cumplimiento normativo**: Constancia formal con toda la información
4. **Trazabilidad**: Relación clara entre checklists, hallazgos e incidentes
5. **Eficiencia**: Menos duplicación de datos

---

## 📋 Plan de Implementación

### Fase 1: Preparación
- [ ] Crear migración para agregar `visit_certificate_id` a hallazgos e incidentes
- [ ] Crear tabla `visit_checklist_completions`
- [ ] Actualizar modelos con relaciones

### Fase 2: Backend
- [ ] Crear servicio `VisitService` para manejar el flujo completo
- [ ] Implementar lógica de inicio de visita
- [ ] Implementar completación de checklists dentro de visita
- [ ] Implementar registro de hallazgos/incidentes vinculados

### Fase 3: Frontend
- [ ] Crear vista "Iniciar Visita"
- [ ] Crear vista "Visita en Progreso" con checklists
- [ ] Integrar modales de hallazgos/incidentes
- [ ] Crear vista "Finalizar Visita" con firma

### Fase 4: Migración
- [ ] Migrar datos existentes (opcional)
- [ ] Mantener compatibilidad con flujo antiguo (temporal)

---

## ❓ Preguntas para Definir

1. **¿Una visita puede tener múltiples checklists?**
   - Respuesta sugerida: SÍ (un técnico completa varios checklists en una visita)

2. **¿Se pueden registrar hallazgos sin visita formal?**
   - Respuesta sugerida: SÍ (mantener flexibilidad)

3. **¿Los incidentes siempre están vinculados a una visita?**
   - Respuesta sugerida: NO (pueden ocurrir fuera de visitas programadas)

4. **¿Qué pasa si el técnico no completa todos los checklists?**
   - Respuesta sugerida: Puede guardar visita como "parcial" y continuar después

5. **¿Se requiere firma digital obligatoria?**
   - Respuesta sugerida: SÍ para visitas formales, NO para registros internos

---

## 🔗 Referencias

- Modelo actual: `app/Models/VisitCertificate.php`
- Modelo actual: `app/Models/ChecklistAssignment.php`
- Vista actual: `resources/views/livewire/visit-certificates/my-history.blade.php`

---

**Próximo paso**: Discutir con el equipo y decidir el enfoque a implementar
