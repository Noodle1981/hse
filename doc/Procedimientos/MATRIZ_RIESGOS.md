# 🎯 Sistema de Matriz de Riesgos - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Evaluaciones y Controles ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema integral para gestionar matrices de riesgo laborales y operacionales. Permite identificar peligros, evaluar riesgos usando escalas de probabilidad e impacto, definir controles de mitigación, y hacer seguimiento de la efectividad de las medidas implementadas.

### URL de Acceso
```
http://localhost:8000/risk-matrices                    # Gestión de matrices (Admin)
http://localhost:8000/risk-matrices/create             # Crear matriz (Admin)
http://localhost:8000/risk-matrices/{id}/edit          # Editar matriz (Admin)
http://localhost:8000/risk-matrices/{id}/risks         # Gestionar riesgos (Admin)
http://localhost:8000/risk-matrices/contrato-anual     # Plan anual (Admin+Técnico)
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (4)               │
│  RiskMatrixManager | RiskManager |             │
│  RiskEvaluationManager | AnnualContractDashboard│
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (4)               │
│  RiskMatrix | Risk | RiskEvaluation |          │
│  RiskControl                                   │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  risk_matrices | risks |                       │
│  risk_evaluations | risk_controls              │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Risks/
├── RiskMatrixManager.php       # CRUD matrices
├── RiskManager.php             # CRUD riesgos
├── RiskEvaluationManager.php   # Evaluaciones
└── AnnualContractDashboard.php # Plan anual
```

### Modelos

```
app/Models/
├── RiskMatrix.php              # Matriz de riesgos
├── Risk.php                    # Riesgo individual
├── RiskEvaluation.php          # Evaluación de riesgo
└── RiskControl.php             # Control/medida
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/risk-matrices` | RiskMatrixManager | Gestión matrices | Admin |
| `/risk-matrices/create` | RiskMatrixManager | Crear matriz | Admin |
| `/risk-matrices/{id}/edit` | RiskMatrixManager | Editar matriz | Admin |
| `/risk-matrices/{id}/risks` | RiskManager | Gestionar riesgos | Admin |
| `/risk-matrices/contrato-anual` | AnnualContractDashboard | Plan anual | Admin+Técnico |

---

## 📊 Base de Datos

### Tabla: risk_matrices

```sql
risk_matrices
├── id
├── title                       -- Título de la matriz
├── description                 -- Descripción
├── area                        -- Área/Departamento
├── process                     -- Proceso asociado
├── empresa_id (FK → empresas)
├── sede_id (FK → sedes)
├── user_id (FK → users)        -- Creador
├── status                      -- 'draft' | 'active' | 'archived'
├── created_at
└── updated_at
```

### Tabla: risks

```sql
risks
├── id
├── risk_matrix_id (FK → risk_matrices)
├── hazard                      -- Peligro identificado
├── type                        -- Tipo de riesgo
├── consequence                 -- Consecuencia potencial
├── order                       -- Orden de visualización
├── created_at
└── updated_at
```

### Tabla: risk_evaluations

```sql
risk_evaluations
├── id
├── risk_id (FK → risks)
├── sede_id (FK → sedes)
├── user_id (FK → users)        -- Evaluador
├── probability                 -- Nivel de probabilidad (1-5)
├── impact                      -- Nivel de impacto (1-5)
├── risk_level                  -- Calculado: P × I
├── risk_category               -- 'trivial' | 'tolerable' | 'moderado' | 'importante' | 'intolerable'
├── existing_controls           -- Controles existentes
├── additional_controls         -- Controles adicionales necesarios
├── evaluation_date             -- Fecha de evaluación
├── created_at
└── updated_at
```

### Tabla: risk_controls

```sql
risk_controls
├── id
├── risk_id (FK → risks)
├── measure                     -- Medida de control
├── type                        -- Tipo de control
├── status                      -- Estado de implementación
├── responsible                 -- Responsable
├── deadline                    -- Fecha límite
├── created_at
└── updated_at
```

---

## ✨ Características Principales

### 1. Gestión de Matrices

**Datos de la matriz:**
- Título
- Descripción
- Área/Departamento
- Proceso asociado
- Empresa y Sede
- Estado (borrador/activa/archivada)

**Funcionalidades:**
- CRUD completo
- Duplicación de matrices
- Búsqueda y filtros
- Asignación a empresa/sede

### 2. Identificación de Riesgos

**Datos del riesgo:**
- Peligro (hazard)
- Tipo de riesgo
- Consecuencia potencial
- Orden de visualización

**Tipos de riesgo comunes:**
- Mecánico
- Químico
- Físico
- Ergonómico
- Biológico
- Psicosocial

### 3. Evaluación de Riesgos

**Escala 5×5 (Probabilidad × Impacto):**

#### Probabilidad (P)

| Nivel | Descripción | Frecuencia |
|-------|-------------|------------|
| 1 | Muy Bajo | Raro, casi imposible |
| 2 | Bajo | Poco probable |
| 3 | Medio | Posible |
| 4 | Alto | Probable |
| 5 | Muy Alto | Casi seguro |

#### Impacto (I)

| Nivel | Descripción | Consecuencias |
|-------|-------------|---------------|
| 1 | Insignificante | Sin lesiones |
| 2 | Menor | Lesión leve |
| 3 | Moderado | Lesión con baja |
| 4 | Mayor | Lesión grave |
| 5 | Catastrófico | Muerte o incapacidad |

### 4. Cálculo de Nivel de Riesgo

```
Nivel de Riesgo = Probabilidad × Impacto
```

**Categorización:**

| Nivel Calculado | Categoría | Color | Acción |
|-----------------|-----------|-------|--------|
| 1-4 | Trivial | 🟢 Verde | Monitoreo |
| 5-8 | Tolerable | 🟡 Amarillo | Control en 6 meses |
| 9-16 | Moderado | 🟠 Naranja | Control en 1 mes |
| 17-20 | Importante | 🔴 Rojo | Control inmediato |
| 21-25 | Intolerable | ⚫ Negro | Detener actividad |

### 5. Controles de Mitigación

**Jerarquía de controles:**
1. **Eliminación** - Eliminar el peligro
2. **Sustitución** - Reemplazar por algo menos peligroso
3. **Ingeniería** - Controles de ingeniería
4. **Administrativos** - Procedimientos y capacitación
5. **EPP** - Equipos de protección personal

**Datos del control:**
- Medida de control
- Tipo (según jerarquía)
- Estado (planificado/en implementación/implementado)
- Responsable
- Fecha límite

### 6. Duplicación de Matrices

**Características:**
- Copia completa de matriz
- Incluye todos los riesgos
- Incluye todos los controles
- Título automático con "(COPIA)"
- Estado inicial: Borrador
- Permite personalización antes de guardar

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Nueva Matriz de Riesgo

```
1. Admin crea matriz
   └─ /risk-matrices/create
   ├─ Título: "Matriz Planta Industrial 2024"
   ├─ Área: "Producción"
   ├─ Proceso: "Corte de metales"
   ├─ Empresa: Constructora ABC
   └─ Sede: Planta Industrial

2. Agrega riesgos
   └─ /risk-matrices/1/risks

   Riesgo 1:
   ├─ Peligro: "Sierra circular sin resguardo"
   ├─ Tipo: "Mecánico"
   └─ Consecuencia: "Corte de dedos"

   Riesgo 2:
   ├─ Peligro: "Ruido excesivo"
   ├─ Tipo: "Físico"
   └─ Consecuencia: "Pérdida auditiva"

3. Evalúa riesgos
   
   Evaluación Riesgo 1:
   ├─ Probabilidad: 4 (Alto)
   ├─ Impacto: 5 (Catastrófico)
   ├─ Nivel: 20 (Importante) 🔴
   └─ Categoría: Importante

   Evaluación Riesgo 2:
   ├─ Probabilidad: 5 (Muy Alto)
   ├─ Impacto: 3 (Moderado)
   ├─ Nivel: 15 (Moderado) 🟠
   └─ Categoría: Moderado

4. Define controles

   Para Riesgo 1:
   ├─ Control 1:
   │   ├─ Medida: "Instalar resguardo en sierra"
   │   ├─ Tipo: Ingeniería
   │   ├─ Responsable: "Mantenimiento"
   │   └─ Plazo: 7 días
   └─ Control 2:
       ├─ Medida: "Capacitación uso de sierra"
       ├─ Tipo: Administrativo
       └─ Plazo: 15 días

5. Implementa controles
   └─ Marca como "Implementado"

6. Re-evalúa riesgo
   ├─ Probabilidad: 2 (Bajo)
   ├─ Impacto: 3 (Moderado)
   ├─ Nivel: 6 (Tolerable) 🟡
   └─ Riesgo reducido exitosamente
```

### Escenario 2: Duplicación de Matriz

```
1. Admin tiene matriz exitosa
   └─ "Matriz Obra Centro 2024"

2. Necesita crear matriz para nueva obra
   └─ Click en "Duplicar"

3. Sistema crea copia
   ├─ Título: "Matriz Obra Centro 2024 (COPIA)"
   ├─ Todos los riesgos copiados
   ├─ Todos los controles copiados
   └─ Estado: Borrador

4. Admin personaliza
   ├─ Cambia título: "Matriz Obra Norte 2024"
   ├─ Cambia sede: Obra Norte
   ├─ Ajusta riesgos específicos
   └─ Guarda

5. Nueva matriz lista
   └─ Ahorro de tiempo significativo
```

### Escenario 3: Seguimiento de Controles

```
1. Control definido
   └─ "Instalar resguardo en sierra"
   └─ Plazo: 7 días

2. Responsable implementa
   └─ Instala resguardo

3. Actualiza estado
   └─ "Implementado"

4. Técnico verifica
   └─ Inspección de verificación

5. Re-evaluación
   └─ Riesgo reducido
   └─ Nueva evaluación registrada

6. Historial completo
   └─ Todas las evaluaciones guardadas
   └─ Trazabilidad total
```

---

## 🎨 Interfaz de Usuario

### RiskMatrixManager (Listado)

```
┌─────────────────────────────────────┐
│ [Buscar...] [+ Nueva Matriz]        │
├─────────────────────────────────────┤
│ Título       │ Sede │ Riesgos │ Acciones │
├──────────────┼──────┼─────────┼──────────┤
│ Planta 2024  │ Obra │ 12      │ ✏️ 📋 📄 🗑️│
│ Oficina 2024 │ Of.  │ 8       │ ✏️ 📋 📄 🗑️│
└─────────────────────────────────────┘

Leyenda:
✏️ = Editar
📋 = Gestionar Riesgos
📄 = Duplicar
🗑️ = Eliminar
```

### RiskManager (Gestionar Riesgos)

```
┌─────────────────────────────────────┐
│ Matriz: Planta Industrial 2024      │
│ [+ Agregar Riesgo]                  │
├─────────────────────────────────────┤
│ Peligro          │ Tipo │ Nivel │ Acciones │
├──────────────────┼──────┼───────┼──────────┤
│ Sierra sin res.  │ Mec. │ 🔴 20 │ 📊 ✏️ 🗑️ │
│ Ruido excesivo   │ Fís. │ 🟠 15 │ 📊 ✏️ 🗑️ │
│ Piso resbaladizo │ Mec. │ 🟡 8  │ 📊 ✏️ 🗑️ │
└─────────────────────────────────────┘

Leyenda:
📊 = Evaluar
✏️ = Editar
🗑️ = Eliminar
```

### RiskEvaluationManager (Evaluar)

```
┌─────────────────────────────────────┐
│ Evaluar: Sierra circular sin resguardo│
├─────────────────────────────────────┤
│ Probabilidad: *                     │
│ ○ 1  ○ 2  ● 3  ○ 4  ○ 5            │
│                                     │
│ Impacto: *                          │
│ ○ 1  ○ 2  ○ 3  ● 4  ○ 5            │
│                                     │
│ Nivel Calculado: 12 (Moderado) 🟠   │
│                                     │
│ Controles Existentes:               │
│ [_________________________________] │
│                                     │
│ Controles Adicionales:              │
│ [_________________________________] │
├─────────────────────────────────────┤
│ [Cancelar] [Guardar Evaluación]     │
└─────────────────────────────────────┘
```

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver matrices | ✅ | ✅ (solo de sus sedes) |
| CRUD matrices | ✅ | ❌ |
| Gestionar riesgos | ✅ | ❌ |
| Evaluar riesgos | ✅ | ✅ |
| Gestionar controles | ✅ | ❌ |
| Duplicar matrices | ✅ | ❌ |
| Ver plan anual | ✅ | ✅ |

### Validaciones

**Matrices:**
- Título: Requerido, máximo 255 caracteres
- Empresa y sede: Requeridas
- Estado: draft/active/archived

**Riesgos:**
- Peligro: Requerido
- Tipo: Requerido
- Consecuencia: Requerida

**Evaluaciones:**
- Probabilidad: Requerida, 1-5
- Impacto: Requerido, 1-5
- Nivel: Calculado automáticamente
- Categoría: Calculada automáticamente

---

## 🔄 Integración con Otros Módulos

### 1. Hallazgos

```php
// Vincular hallazgo con riesgo
$hallazgo->risk_id = $risk->id;

// Hallazgos de un riesgo
$risk->hallazgos;

// Al resolver hallazgo, re-evaluar riesgo
if ($hallazgo->estado === 'resuelto') {
    // Crear nueva evaluación con menor nivel
}
```

### 2. Plan Anual (Contrato Anual)

```php
// Inspecciones programadas para verificar controles
$plan = AnnualPlan::where('sede_id', $sede->id)
    ->where('category', 'Inspección')
    ->get();

// Vincular con matriz de riesgo
$plan->risk_matrix_id = $matrix->id;
```

### 3. Checklists

```php
// Checklist para verificar control
$control->verification_checklist_id = $checklist->id;

// Al completar checklist, actualizar control
if ($inspection->status === 'completed') {
    $control->update(['status' => 'implemented']);
}
```

---

## 📊 Matriz Visual (Heatmap)

```
IMPACTO
    5│ 🟡  🟠  🟠  🔴  ⚫
    4│ 🟡  🟡  🟠  🔴  🔴
    3│ 🟢  🟡  🟠  🟠  🔴
    2│ 🟢  🟢  🟡  🟡  🟠
    1│ 🟢  🟢  🟢  🟡  🟡
     └─────────────────────
       1   2   3   4   5  PROBABILIDAD

Leyenda:
🟢 Trivial (1-4)
🟡 Tolerable (5-8)
🟠 Moderado (9-16)
🔴 Importante (17-20)
⚫ Intolerable (21-25)
```

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Evaluación Estandarizada** | Metodología 5×5 reconocida |
| **Cálculo Automático** | Sin errores manuales |
| **Jerarquía de Controles** | Mejores prácticas |
| **Duplicación Rápida** | Ahorro de tiempo |
| **Historial Completo** | Trazabilidad de evaluaciones |
| **Integración Total** | Conecta con hallazgos y plan anual |
| **Categorización Visual** | Identificación rápida de prioridades |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Heatmap Interactivo**
   - Visualización 5×5 con colores
   - Click en celda para ver riesgos
   - Filtrado por nivel

2. **Reportes PDF**
   - Exportar matriz completa
   - Incluir evaluaciones
   - Incluir controles

3. **Dashboard de Riesgos**
   - KPIs de riesgos
   - Gráficos de distribución
   - Tendencias temporales

4. **Notificaciones**
   - Alertas de controles vencidos
   - Recordatorios de re-evaluación
   - Notificación de riesgos críticos

5. **Escalas Personalizadas**
   - Configurar escalas propias
   - Diferentes metodologías
   - Adaptación por industria

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [HALLAZGOS.md](./HALLAZGOS.md) - Sistema de hallazgos
- [CONTRATO_ANUAL.MD](./CONTRATO_ANUAL.MD) - Plan anual

### Normativas

- **ISO 31000:2018** - Gestión de Riesgos
- **ISO 45001:2018** - Salud y Seguridad Ocupacional
- **Ley 19.587** - Higiene y Seguridad en el Trabajo (Argentina)

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de matrices de riesgo
- ✅ CRUD de matrices con duplicación
- ✅ Gestión de riesgos por matriz
- ✅ Evaluación 5×5 (Probabilidad × Impacto)
- ✅ Cálculo automático de nivel
- ✅ Categorización automática (trivial a intolerable)
- ✅ Gestión de controles de mitigación
- ✅ Jerarquía de controles
- ✅ Historial de evaluaciones
- ✅ Integración con hallazgos
- ✅ Integración con plan anual
- ✅ Asignación por empresa y sede

---

*EP-Back V2.0.0.0 | Sistema de Matriz de Riesgos con Evaluación 5×5 | Diciembre 2024*
