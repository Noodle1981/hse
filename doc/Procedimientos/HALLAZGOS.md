# ⚠️ Sistema de Hallazgos y Seguimiento - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Seguimiento y Fotos ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema integral para detectar, registrar y hacer seguimiento de hallazgos de seguridad e higiene en campo. Permite vincular hallazgos con inspecciones, constancias de visita, y registrar incidentes relacionados con evidencia fotográfica.

### URL de Acceso
```
http://localhost:8000/hallazgos                    # Gestión de hallazgos (Admin)
http://localhost:8000/hallazgos/create             # Crear hallazgo (Admin+Técnico)
http://localhost:8000/hallazgos/{id}/edit          # Editar hallazgo (Admin+Técnico)
http://localhost:8000/hallazgos/{id}               # Ver detalle (Admin+Técnico)
http://localhost:8000/mis-hallazgos                # Mis hallazgos (Técnico)
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (4)               │
│  HallazgoManager | HallazgoWizard |            │
│  HallazgoDetail | MisHallazgos                 │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (4)               │
│  Hallazgo | HallazgoSeguimiento |              │
│  HallazgoFoto | Incidente                      │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  hallazgos | hallazgo_seguimientos |           │
│  hallazgo_fotos | incidentes                   │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Hallazgos/
├── HallazgoManager.php         # Gestión completa (Admin)
├── HallazgoWizard.php          # Wizard 4 pasos
├── HallazgoDetail.php          # Ver detalle + seguimiento
└── MisHallazgos.php            # Vista técnico
```

### Modelos

```
app/Models/
├── Hallazgo.php                # Hallazgo principal
├── HallazgoSeguimiento.php     # Seguimiento/actualización
├── HallazgoFoto.php            # Foto de evidencia
└── Incidente.php               # Incidente relacionado
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/hallazgos` | HallazgoManager | Gestión completa | Admin |
| `/hallazgos/create` | HallazgoWizard | Crear hallazgo | Admin+Técnico |
| `/hallazgos/{id}/edit` | HallazgoWizard | Editar hallazgo | Admin+Técnico |
| `/hallazgos/{id}` | HallazgoDetail | Ver detalle | Admin+Técnico |
| `/mis-hallazgos` | MisHallazgos | Mis hallazgos | Técnico |

---

## 📊 Base de Datos

### Tabla: hallazgos

```sql
hallazgos
├── id
├── sede_id (FK → sedes)
├── user_id (FK → users)              -- Técnico que detectó
├── titulo                            -- Título del hallazgo
├── descripcion                       -- Descripción detallada
├── ubicacion_especifica              -- Ubicación exacta
├── criticidad                        -- 'bajo' | 'medio' | 'alto' | 'critico'
├── estado                            -- 'pendiente' | 'en_proceso' | 'resuelto' | 'verificado' | 'cerrado'
├── plazo_correccion                  -- Fecha límite
├── responsable_correccion            -- Responsable de corregir
├── fecha_deteccion                   -- Cuándo se detectó
├── fecha_resolucion                  -- Cuándo se resolvió
├── origen_type                       -- Tipo de origen (polimórfico - opcional)
├── origen_id                         -- ID del origen (opcional)
├── visit_certificate_id (FK → visit_certificates, nullable) -- Vinculación directa con visita
├── checklist_completion_id (FK → visit_checklist_completions, nullable) -- Checklist finalizado
├── question_id (FK → dynamic_questions, nullable) -- Pregunta específica del checklist

├── created_at
└── updated_at
```

### Tabla: hallazgo_seguimientos

```sql
hallazgo_seguimientos
├── id
├── hallazgo_id (FK → hallazgos)
├── user_id (FK → users)              -- Quién hizo el seguimiento
├── fecha_seguimiento                 -- Fecha del seguimiento
├── descripcion                       -- Descripción de la actualización
├── estado_anterior                   -- Estado antes del cambio
├── estado_nuevo                      -- Estado después del cambio
├── created_at
└── updated_at
```

### Tabla: hallazgo_fotos

```sql
hallazgo_fotos
├── id
├── hallazgo_id (FK → hallazgos)
├── tipo_foto                         -- 'antes' | 'durante' | 'despues'
├── ruta_archivo                      -- Path del archivo
├── descripcion                       -- Descripción de la foto
├── created_at
└── updated_at
```

### Tabla: incidentes

```sql
incidentes
├── id
├── hallazgo_id (FK → hallazgos)
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── fecha_incidente                   -- Cuándo ocurrió
├── tipo_incidente                    -- Tipo de incidente
├── gravedad                          -- 'leve' | 'moderado' | 'grave' | 'fatal'
├── descripcion                       -- Descripción del incidente
├── personas_afectadas                -- Número de afectados
├── acciones_inmediatas               -- Qué se hizo
├── created_at
└── updated_at
```

---

## ✨ Características Principales

### 1. Niveles de Criticidad

**Bajo:**
- Riesgo mínimo
- Corrección no urgente
- Plazo: 30+ días

**Medio:**
- Riesgo moderado
- Corrección importante
- Plazo: 15-30 días

**Alto:**
- Riesgo significativo
- Corrección prioritaria
- Plazo: 7-15 días

**Crítico:**
- Riesgo inminente
- Corrección inmediata
- Plazo: 1-7 días

### 2. Estados del Hallazgo

```
pendiente → en_proceso → resuelto → verificado → cerrado
```

**Pendiente:**
- Recién detectado
- Sin acción tomada
- Esperando asignación

**En Proceso:**
- Asignado a responsable
- Trabajando en corrección
- Seguimiento activo

**Resuelto:**
- Corrección implementada
- Pendiente de verificación
- Requiere validación

**Verificado:**
- Corrección validada
- Efectiva
- Pendiente de cierre

**Cerrado:**
- Completamente resuelto
- Verificado y documentado
- Estado final

### 3. Wizard de 4 Pasos

**Paso 1: Información Básica**
- Título del hallazgo
- Descripción detallada
- Ubicación específica
- Sede

**Paso 2: Clasificación**
- Criticidad (bajo/medio/alto/crítico)
- Estado inicial
- Plazo de corrección
- Responsable de corrección

**Paso 3: Origen (Opcional)**
- Tipo de origen:
  - DynamicInspection (desde checklist)
  - GeneradorConstancia (desde visita)
  - Manual (detección directa)
- ID del origen


### 4. Sistema de Seguimiento

**Características:**
- Historial completo de cambios
- Registro de estado anterior y nuevo
- Usuario que realizó el cambio
- Fecha de cada actualización
- Descripción de acciones tomadas

**Beneficios:**
- Trazabilidad completa
- Auditoría de cambios
- Evidencia de gestión
- Responsabilidad clara

### 5. Evidencia Fotográfica

**Tipos de fotos:**
- **Antes:** Estado inicial del hallazgo
- **Durante:** Proceso de corrección
- **Después:** Estado final corregido

**Características:**
- Upload múltiple
- Descripción por foto
- Almacenamiento en storage
- Eliminación automática al borrar hallazgo

### 6. Gestión de Incidentes

**Vinculación:**
- Incidente relacionado con hallazgo
- Si el hallazgo causó un accidente
- Registro de consecuencias

**Datos del incidente:**
- Fecha del incidente
- Tipo de incidente
- Gravedad (leve/moderado/grave/fatal)
- Personas afectadas
- Acciones inmediatas tomadas

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Detección Durante Inspección

```
1. Técnico realiza inspección
   └─ Checklist "Inspección de Escaleras"

2. Detecta problema
   └─ Escalones oxidados y sueltos

3. Crea hallazgo desde inspección
   └─ /hallazgos/create?origen=inspection&id=45

4. Wizard Paso 1: Información
   ├─ Título: "Escalones oxidados en escalera principal"
   ├─ Descripción: "3 escalones con oxidación severa..."
   ├─ Ubicación: "Escalera acceso planta 2"
   └─ Sede: Obra Centro

5. Paso 2: Clasificación
   ├─ Criticidad: Alto
   ├─ Estado: Pendiente
   ├─ Plazo: 7 días
   └─ Responsable: "Mantenimiento - Juan Pérez"

6. Paso 3: Origen
   ├─ Tipo: DynamicInspection
   └─ ID: 45 (automático)



8. Guarda hallazgo
   └─ Sistema crea registro

9. Sube fotos
   ├─ Foto 1: Antes - Escalones oxidados
   ├─ Foto 2: Antes - Detalle de oxidación
   └─ Foto 3: Antes - Vista general

10. Hallazgo registrado
    └─ Visible en dashboard
    └─ Alerta por criticidad alta
```

### Escenario 2: Seguimiento y Resolución

```
1. Responsable recibe notificación
   └─ Hallazgo asignado: "Escalones oxidados"

2. Inicia trabajo
   └─ Accede a /hallazgos/123

3. Agrega seguimiento
   ├─ Descripción: "Iniciando reparación"
   ├─ Estado: Pendiente → En Proceso
   └─ Fecha: 2024-12-30

4. Durante reparación
   └─ Sube foto "Durante"

5. Completa reparación
   ├─ Descripción: "Escalones reemplazados"
   ├─ Estado: En Proceso → Resuelto
   ├─ Fecha resolución: 2024-12-31
   └─ Sube foto "Después"

6. Técnico verifica
   ├─ Revisa reparación
   ├─ Valida corrección
   └─ Estado: Resuelto → Verificado

7. Admin cierra
   └─ Estado: Verificado → Cerrado

8. Sistema actualiza
   └─ Estadísticas actualizadas
```

### Escenario 3: Hallazgo que Genera Incidente

```
1. Hallazgo existente
   └─ "Escalones oxidados" (ID: 123)
   └─ Estado: Pendiente (no corregido a tiempo)

2. Ocurre accidente
   └─ Empleado se cae por escalón suelto

3. Técnico registra incidente
   └─ /hallazgos/123 → "Registrar Incidente"

4. Datos del incidente
   ├─ Fecha: 2024-12-30
   ├─ Tipo: "Caída"
   ├─ Gravedad: Moderado
   ├─ Personas afectadas: 1
   ├─ Descripción: "Empleado resbaló..."
   └─ Acciones: "Primeros auxilios, traslado..."

5. Sistema actualiza
   ├─ Hallazgo marcado como "causó incidente"
   ├─ Criticidad elevada a Crítico
   ├─ Plazo reducido a inmediato
   └─ Alerta a responsables

6. Corrección urgente
   └─ Prioridad máxima

7. Investigación
   └─ Análisis de causa raíz
   └─ Medidas preventivas
```

---

## 🎨 Interfaz de Usuario

### HallazgoManager (Gestión Admin)

**KPIs:**
```
┌─────────────────────────────────────┐
│ Total: 45 | Pendientes: 12          │
│ Críticos: 3 | Vencidos: 5           │
│ Resueltos (mes): 8                  │
└─────────────────────────────────────┘
```

**Filtros:**
```
[Buscar...] Estado: [▼] Criticidad: [▼]
Sede: [▼] Técnico: [▼] Vencidos: [▼]
```

**Lista:**
```
┌─────────────────────────────────────┐
│ Título      │ Criticidad │ Estado │ Acciones │
├─────────────┼────────────┼────────┼──────────┤
│ Escalones   │ 🔴 Alto    │ ⏳ Pend│ 👁️ ✏️ 🗑️  │
│ Cable suelto│ 🟡 Medio   │ ✅ Res │ 👁️ ✏️ 🗑️  │
└─────────────────────────────────────┘
```

### HallazgoWizard (Crear/Editar)

**Paso 1:**
```
┌─────────────────────────────────────┐
│ Paso 1 de 3: Información Básica    │
├─────────────────────────────────────┤
│ Título: *                           │
│ [_________________________________] │
│                                     │
│ Descripción: *                      │
│ [_________________________________] │
│ [_________________________________] │
│                                     │
│ Ubicación Específica:               │
│ [_________________________________] │
│                                     │
│ Sede: *                             │
│ [▼ Seleccionar]                     │
├─────────────────────────────────────┤
│                    [Siguiente →]     │
└─────────────────────────────────────┘
```

**Paso 2:**
```
┌─────────────────────────────────────┐
│ Paso 2 de 3: Clasificación         │
├─────────────────────────────────────┤
│ Criticidad: *                       │
│ ○ Bajo  ○ Medio  ● Alto  ○ Crítico │
│                                     │
│ Estado: *                           │
│ ● Pendiente  ○ En Proceso           │
│                                     │
│ Plazo de Corrección: *              │
│ [____-__-__]                        │
│                                     │
│ Responsable de Corrección:          │
│ [_________________________________] │
├─────────────────────────────────────┤
│ [← Anterior]         [Siguiente →]  │
└─────────────────────────────────────┘
```

### HallazgoDetail (Ver Detalle)

```
┌─────────────────────────────────────┐
│ Hallazgo #123                       │
│ 🔴 Alto | ⏳ En Proceso             │
├─────────────────────────────────────┤
│ Escalones oxidados en escalera      │
│ Sede: Obra Centro                   │
│ Detectado: 2024-12-28               │
│ Plazo: 2025-01-04 (⚠️ 5 días)       │
├─────────────────────────────────────┤
│ FOTOS (3)                           │
│ [Antes] [Antes] [Durante]           │
├─────────────────────────────────────┤
│ SEGUIMIENTO (2)                     │
│ • 2024-12-30: Iniciando reparación  │
│ • 2024-12-28: Hallazgo detectado    │
├─────────────────────────────────────┤
│ [Agregar Seguimiento]               │
│ [Subir Foto] [Registrar Incidente]  │
└─────────────────────────────────────┘
```

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver todos los hallazgos | ✅ | ❌ |
| Ver mis hallazgos | ✅ | ✅ |
| Crear hallazgo | ✅ | ✅ |
| Editar hallazgo | ✅ | ✅ (solo propios) |
| Eliminar hallazgo | ✅ | ❌ |
| Agregar seguimiento | ✅ | ✅ |
| Subir fotos | ✅ | ✅ |
| Registrar incidente | ✅ | ✅ |
| Ver KPIs completos | ✅ | ❌ |

---

## 📊 Scopes y Helpers

### Scopes Disponibles

```php
// Estados
Hallazgo::pendientes()->get();
Hallazgo::enProceso()->get();
Hallazgo::resueltos()->get();
Hallazgo::cerrados()->get();

// Criticidad
Hallazgo::criticos()->get();
Hallazgo::altos()->get();

// Condiciones
Hallazgo::vencidos()->get();
Hallazgo::abiertos()->get();

// Filtros
Hallazgo::porSede($sedeId)->get();
Hallazgo::porTecnico($userId)->get();
```

### Métodos Auxiliares

```php
$hallazgo->estaVencido();      // bool
$hallazgo->esCritico();        // bool
$hallazgo->estaAbierto();      // bool
$hallazgo->diasRestantes();    // int
```

---

## 🔄 Integración con Otros Módulos

### 1. Checklists/Inspecciones e Integración Directa

```php
// Un hallazgo ahora puede vincularse directamente a una pregunta específica
// de la finalización de un checklist en una visita
$hallazgo->checklist_completion_id = $completion->id;
$hallazgo->question_id = $question->id;

// También se mantiene la forma por origen polimórfico (legacy o flujos simples)
$hallazgo->origen_type = 'DynamicInspection';
$hallazgo->origen_id = $inspection->id;

// Hallazgos de una inspección
$inspection->hallazgos;
```

### 2. Constancias de Visita

```php
// Crear hallazgo directamente ligado a una visita específica
$hallazgo->visit_certificate_id = $visit->id;

// O mediante origen polimórfico
$hallazgo->origen_type = 'GeneradorConstancia';
$hallazgo->origen_id = $constancia->id;

// Hallazgos de una visita
$visit->hallazgos;
```


---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Wizard Guiado** | Proceso simple y estructurado |
| **Criticidad Visual** | Identificación rápida de prioridades |
| **Seguimiento Completo** | Trazabilidad de todas las acciones |
| **Evidencia Fotográfica** | Documentación visual |
| **Origen Polimórfico** | Vinculación flexible |
| **Alertas de Vencimiento** | Prevención de incumplimientos |
| **Integración Total** | Conecta con todos los módulos |
| **Gestión de Incidentes** | Registro de consecuencias |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Notificaciones Automáticas**
   - Email cuando se crea hallazgo crítico
   - Alerta de vencimiento próximo
   - Notificación al responsable

2. **Dashboard de Hallazgos**
   - Gráficos de tendencias
   - Mapa de calor por sede
   - Ranking de criticidad

3. **Reportes Avanzados**
   - PDF de hallazgo individual
   - Reporte mensual de hallazgos
   - Análisis de recurrencia

4. **Geolocalización**
   - Coordenadas GPS del hallazgo
   - Mapa interactivo
   - Proximidad entre hallazgos

5. **Plantillas de Hallazgos**
   - Hallazgos comunes predefinidos
   - Autocompletado inteligente
   - Sugerencias basadas en historial

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists
- [CONTANCIAS_DE_VISITAS.MD](./CONTANCIAS_DE_VISITAS.MD) - Constancias de visitas


---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de hallazgos
- ✅ 4 niveles de criticidad
- ✅ 5 estados de seguimiento
- ✅ Wizard de 4 pasos
- ✅ Sistema de seguimiento con historial
- ✅ Evidencia fotográfica (antes/durante/después)
- ✅ Gestión de incidentes relacionados
- ✅ Origen polimórfico (inspecciones/visitas/manual)

- ✅ Scopes y helpers completos
- ✅ KPIs y estadísticas
- ✅ Filtros avanzados
- ✅ Alertas de vencimiento

---

*EP-Back V2.0.0.0 | Sistema de Hallazgos con Seguimiento y Evidencia Fotográfica | Diciembre 2024*
