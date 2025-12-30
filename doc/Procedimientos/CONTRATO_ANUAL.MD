# 📅 Sistema de Contrato Anual - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Seguimiento de Cumplimiento ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema de planificación y seguimiento del contrato anual de servicios de seguridad e higiene. Permite definir actividades programadas mensualmente, realizar seguimiento automático del cumplimiento, y generar reportes PDF del plan anual.

### URL de Acceso
```
http://localhost:8000/risk-matrices/contrato-anual    # Dashboard del contrato
http://localhost:8000/contrato-anual/pdf              # Exportar PDF
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (1)               │
│  AnnualContractDashboard                       │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (1)               │
│  AnnualPlan                                    │
├────────────────────────────────────────────────┤
│              CONTROLLERS (1)                   │
│  AnnualContractReportController                │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  annual_plans (matriz mensual)                 │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Risks/
└── AnnualContractDashboard.php    # Dashboard principal
```

### Modelos

```
app/Models/
└── AnnualPlan.php                 # Plan anual
```

### Controladores

```
app/Http/Controllers/Risks/
└── AnnualContractReportController.php   # Exportación PDF
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/risk-matrices/contrato-anual` | AnnualContractDashboard | Dashboard | Admin+Técnico |
| `/contrato-anual/pdf` | AnnualContractReportController | Exportar PDF | Admin+Técnico |

---

## 📊 Base de Datos

### Tabla: annual_plans

```sql
annual_plans
├── id
├── sede_id (FK → sedes)
├── category                        -- 'Capacitación' | 'Inspección' | 'Registro'
├── activity_name                   -- Nombre de la actividad
├── activity_code                   -- Código de la actividad (opcional)
├── checklist_id (FK → dynamic_checklists, nullable)
├── year                            -- Año del plan
├── january (boolean)               -- Programado para enero
├── february (boolean)              -- Programado para febrero
├── march (boolean)                 -- Programado para marzo
├── april (boolean)                 -- Programado para abril
├── may (boolean)                   -- Programado para mayo
├── june (boolean)                  -- Programado para junio
├── july (boolean)                  -- Programado para julio
├── august (boolean)                -- Programado para agosto
├── september (boolean)             -- Programado para septiembre
├── october (boolean)               -- Programado para octubre
├── november (boolean)              -- Programado para noviembre
├── december (boolean)              -- Programado para diciembre
├── created_at
└── updated_at
```

**Estructura de datos:**
- Cada fila = Una actividad del plan anual
- Cada columna de mes = Boolean (true = programado, false = no programado)
- Matriz visual: Actividades × Meses

---

## ✨ Características Principales

### 1. Categorías de Actividades

**Capacitación:**
- Capacitaciones programadas
- Puede o no estar vinculada a un checklist
- Seguimiento manual o automático

**Inspección:**
- Inspecciones programadas
- **Requiere** vinculación con checklist
- Seguimiento automático de cumplimiento
- Cuenta inspecciones completadas

**Registro:**
- Registros y documentación
- Sin vinculación a checklist
- Seguimiento manual

### 2. Matriz de Planificación Mensual

**Interfaz visual:**
```
┌─────────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
│ Actividad       │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
├─────────────────┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┤
│ Cap. EPP        │ X │   │ X │   │ X │   │ X │   │ X │   │ X │   │
│ Insp. Vehículos │ X │ X │ X │ X │ X │ X │ X │ X │ X │ X │ X │ X │
│ Reg. Incidentes │ X │   │   │   │   │   │ X │   │   │   │   │   │
└─────────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘

Leyenda:
X = Programado
✓ = Completado (verde)
⚠ = Vencido (rojo)
○ = Pendiente (amarillo)
```

### 3. Seguimiento Automático de Cumplimiento

**Para Inspecciones:**
```php
// Sistema verifica automáticamente si se completó la inspección
$count = DynamicInspection::where('dynamic_checklist_id', $plan->checklist_id)
    ->where('sede_id', $sedeId)
    ->whereMonth('completed_at', $month)
    ->whereYear('completed_at', $year)
    ->where('status', 'completed')
    ->count();

if ($count > 0) {
    $status = 'completed'; // ✓ Verde
} elseif ($month < currentMonth) {
    $status = 'overdue';   // ⚠ Rojo
} else {
    $status = 'pending';   // ○ Amarillo
}
```

**Estados:**
- `none` - No programado (vacío)
- `pending` - Programado, pendiente (amarillo)
- `completed` - Completado (verde)
- `overdue` - Vencido (rojo)

### 4. Modo de Edición

**Solo Admin:**
- Toggle para activar/desactivar modo edición
- Click en celdas para marcar/desmarcar meses
- Agregar nuevas actividades
- Eliminar actividades

**Técnico:**
- Solo visualización
- No puede editar
- Puede ver cumplimiento

### 5. Filtros y Selección

**Por Sede:**
- Admin: Puede seleccionar cualquier sede
- Técnico: Solo sus sedes asignadas

**Por Año:**
- Selector de año
- Por defecto: Año actual

### 6. Exportación PDF

**Características:**
- Orientación horizontal (landscape)
- Matriz completa de actividades × meses
- Indicadores de cumplimiento
- Datos de empresa y sede
- Nombre: `plan_anual_{sede}_{año}.pdf`

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Planificación Anual (Admin)

```
1. Admin accede al dashboard
   └─ /risk-matrices/contrato-anual

2. Selecciona sede y año
   ├─ Sede: Obra Centro
   └─ Año: 2025

3. Activa modo edición
   └─ Toggle "Modo Edición"

4. Agrega actividades:
   
   a) Capacitación EPP
      ├─ Categoría: Capacitación
      ├─ Nombre: "Capacitación Uso de EPP"
      ├─ Código: "CAP-EPP-001"
      ├─ Checklist: (ninguno)
      └─ Meses: Ene, Mar, May, Jul, Sep, Nov
   
   b) Inspección Vehículos
      ├─ Categoría: Inspección
      ├─ Nombre: "Inspección de Vehículos"
      ├─ Código: "INSP-VEH-001"
      ├─ Checklist: "Checklist Vehículos" ✅
      └─ Meses: Todos (mensual)
   
   c) Registro Incidentes
      ├─ Categoría: Registro
      ├─ Nombre: "Registro de Incidentes"
      ├─ Código: "REG-INC-001"
      └─ Meses: Ene, Jul

5. Guarda cambios
   └─ Desactiva modo edición

6. Exporta PDF
   └─ Descarga plan_anual_obra_centro_2025.pdf
```

### Escenario 2: Seguimiento Mensual (Técnico)

```
1. Técnico accede al dashboard
   └─ Ve su sede asignada automáticamente

2. Visualiza matriz del año actual
   └─ Mes actual: Diciembre

3. Ve estado de actividades:
   
   Enero (pasado):
   ├─ Cap. EPP: ✓ Completado (verde)
   ├─ Insp. Vehículos: ✓ Completado (verde)
   └─ Reg. Incidentes: ⚠ Vencido (rojo)
   
   Febrero (pasado):
   └─ Insp. Vehículos: ✓ Completado (verde)
   
   Diciembre (actual):
   ├─ Insp. Vehículos: ○ Pendiente (amarillo)
   └─ Cap. EPP: ○ Pendiente (amarillo)

4. Identifica actividades vencidas
   └─ Reg. Incidentes de Enero no se completó

5. Completa actividades pendientes
   └─ Realiza inspección de vehículos

6. Sistema actualiza automáticamente
   └─ Diciembre Insp. Vehículos: ✓ Completado
```

### Escenario 3: Auditoría Trimestral

```
1. Auditor accede al dashboard
   └─ Selecciona sede a auditar

2. Revisa cumplimiento Q1 (Ene-Mar):
   
   Capacitaciones:
   ├─ Cap. EPP (Ene): ✓
   ├─ Cap. EPP (Mar): ✓
   └─ Cumplimiento: 100%
   
   Inspecciones:
   ├─ Insp. Vehículos (Ene): ✓
   ├─ Insp. Vehículos (Feb): ✓
   ├─ Insp. Vehículos (Mar): ✓
   └─ Cumplimiento: 100%
   
   Registros:
   ├─ Reg. Incidentes (Ene): ⚠ Vencido
   └─ Cumplimiento: 0%

3. Exporta PDF para evidencia
   └─ Incluye en informe de auditoría

4. Solicita justificación de vencidos
   └─ Reg. Incidentes de Enero
```

---

## 🎨 Interfaz de Usuario

### Dashboard Principal

**Sección 1: Controles**
```
┌─────────────────────────────────────┐
│ Sede: [▼ Obra Centro]               │
│ Año: [▼ 2024]                       │
│ ☐ Modo Edición (Solo Admin)         │
│ [Exportar PDF]                      │
└─────────────────────────────────────┘
```

**Sección 2: Agregar Actividad (Solo Admin en Modo Edición)**
```
┌─────────────────────────────────────┐
│ Categoría: ○ Capacitación           │
│            ○ Inspección             │
│            ○ Registro               │
│                                     │
│ Nombre: [________________________]  │
│ Código: [________________________]  │
│ Checklist: [▼ Seleccionar] (opc.)   │
│                                     │
│ [Agregar Actividad]                 │
└─────────────────────────────────────┘
```

**Sección 3: Matriz de Planificación**
```
┌──────────────────────────────────────────────────────────────┐
│ CAPACITACIONES                                               │
├────────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┤
│ Cap. EPP       │ ✓ │   │ ✓ │   │ ✓ │   │ ✓ │   │ ✓ │   │ ○ │   │
│ [Eliminar]     │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
└────────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘

┌──────────────────────────────────────────────────────────────┐
│ INSPECCIONES                                                 │
├────────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┤
│ Insp. Vehículos│ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ○ │ ○ │
│ [Eliminar]     │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
└────────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘

┌──────────────────────────────────────────────────────────────┐
│ REGISTROS                                                    │
├────────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┤
│ Reg. Incidentes│ ⚠ │   │   │   │   │   │ ✓ │   │   │   │   │   │
│ [Eliminar]     │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
└────────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘

Leyenda:
✓ = Completado    ○ = Pendiente    ⚠ = Vencido    (vacío) = No programado
```

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver dashboard | ✅ | ✅ |
| Seleccionar cualquier sede | ✅ | ❌ (solo asignadas) |
| Activar modo edición | ✅ | ❌ |
| Agregar actividades | ✅ | ❌ |
| Eliminar actividades | ✅ | ❌ |
| Marcar/desmarcar meses | ✅ | ❌ |
| Ver cumplimiento | ✅ | ✅ |
| Exportar PDF | ✅ | ✅ |

### Validaciones

**Al agregar actividad:**
- Nombre: Requerido, máximo 255 caracteres
- Categoría: Requerida, debe ser Capacitación/Inspección/Registro
- Sede: Requerida, debe existir
- Checklist: Opcional, pero recomendado para Inspecciones

**Modo edición:**
- Solo Admin puede activar
- Validación de rol en cada acción
- Prevención de edición accidental

---

## 📄 Generación de PDFs

### AnnualContractReportController

**Método: export()**
```php
// Parámetros: sede_id, year
$pdf = Pdf::loadView('reports.annual-plan', $data)
    ->setPaper('a4', 'landscape');

return $pdf->stream("plan_anual_{sede}_{year}.pdf");
```

### Vista PDF: `reports.annual-plan`

**Estructura:**
```
┌─────────────────────────────────────────────────────────────┐
│ PLAN ANUAL DE ACTIVIDADES - {año}                          │
├─────────────────────────────────────────────────────────────┤
│ Empresa: {empresa}                                          │
│ Sede: {sede}                                                │
│ Fecha de generación: {fecha}                                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ CAPACITACIONES                                              │
│ ┌────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
│ │ Actividad  │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
│ ├────────────┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┤
│ │ Cap. EPP   │ ✓ │   │ ✓ │   │ ✓ │   │ ✓ │   │ ✓ │   │ ✓ │   │
│ └────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘
│                                                             │
│ INSPECCIONES                                                │
│ ┌────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
│ │ Actividad  │ E │ F │ M │ A │ M │ J │ J │ A │ S │ O │ N │ D │
│ ├────────────┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┤
│ │ Insp. Veh. │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ✓ │ ○ │ ○ │
│ └────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘
│                                                             │
│ RESUMEN DE CUMPLIMIENTO                                     │
│ • Capacitaciones: 10/10 (100%)                              │
│ • Inspecciones: 10/12 (83%)                                 │
│ • Registros: 1/2 (50%)                                      │
│ • TOTAL: 21/24 (87.5%)                                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 Integración con Otros Módulos

### 1. Checklists/Inspecciones

```php
// Vinculación directa
$plan->checklist_id = $checklist->id;

// Seguimiento automático
$completed = DynamicInspection::where('dynamic_checklist_id', $plan->checklist_id)
    ->whereMonth('completed_at', $month)
    ->count();
```

**Beneficio:**
- Seguimiento automático de cumplimiento
- No requiere marcado manual
- Evidencia trazable

### 2. Constancias de Visitas

```php
// Las actividades vinculadas a constancias
// cuentan para el cumplimiento del plan
$constancia->inspections; // Cuenta para objetivo
$constancia->trainings;   // Cuenta para objetivo
```

**Beneficio:**
- Trazabilidad completa
- Evidencia de trabajo realizado
- Cumplimiento documentado

### 3. Capacitaciones

```php
// Capacitaciones programadas en el plan
// pueden vincularse a trainings reales
$training->created_at->month === $plannedMonth;
```

**Beneficio:**
- Planificación vs. ejecución
- Seguimiento de cumplimiento
- Reportes de capacitación

---

## 📊 Cálculo de Cumplimiento

### Algoritmo de Seguimiento

```php
function getComplianceData($plan, $month, $monthNumber) {
    // 1. Si no está programado
    if (!$plan->$month) {
        return ['status' => 'none', 'count' => 0];
    }
    
    // 2. Si es Inspección con checklist
    if ($plan->category === 'Inspección' && $plan->checklist_id) {
        $count = DynamicInspection::where('checklist_id', $plan->checklist_id)
            ->whereMonth('completed_at', $monthNumber)
            ->where('status', 'completed')
            ->count();
        
        if ($count > 0) {
            return ['status' => 'completed', 'count' => $count];
        }
    }
    
    // 3. Verificar si está vencido
    $currentMonth = date('m');
    if ($monthNumber < $currentMonth) {
        return ['status' => 'overdue', 'count' => 0];
    }
    
    // 4. Pendiente (mes actual o futuro)
    return ['status' => 'pending', 'count' => 0];
}
```

### Optimización de Queries

**Problema N+1:**
```php
// ❌ Malo: N+1 queries
foreach ($plans as $plan) {
    foreach ($months as $month) {
        $count = DynamicInspection::where(...)->count(); // Query por cada celda
    }
}
```

**Solución:**
```php
// ✅ Bueno: 1 query para todas las inspecciones
$inspections = DynamicInspection::where('sede_id', $sedeId)
    ->whereYear('completed_at', $year)
    ->get()
    ->groupBy(fn($i) => $i->checklist_id . '_' . $i->completed_at->month);

// Luego buscar en memoria
$count = $inspections[$key]->count() ?? 0;
```

---

## 📈 Métricas y Reportes

### KPIs Disponibles

**Por Sede:**
- Total de actividades programadas
- Actividades completadas
- Actividades vencidas
- Porcentaje de cumplimiento

**Por Categoría:**
- Capacitaciones: X/Y (Z%)
- Inspecciones: X/Y (Z%)
- Registros: X/Y (Z%)

**Por Mes:**
- Actividades programadas
- Actividades completadas
- Tasa de cumplimiento mensual

**Anual:**
- Cumplimiento total del contrato
- Tendencia mensual
- Actividades más/menos cumplidas

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Matriz Visual** | Planificación clara y simple |
| **Seguimiento Automático** | No requiere marcado manual para inspecciones |
| **Estados Visuales** | Identificación rápida de vencidos |
| **Vinculación Checklists** | Trazabilidad completa |
| **Modo Edición** | Previene cambios accidentales |
| **PDF Profesional** | Evidencia para auditorías |
| **Optimización** | Sin problemas N+1 |
| **Multi-sede** | Gestión centralizada |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Notificaciones Automáticas**
   - Email cuando actividad está por vencer
   - Alerta de actividades vencidas
   - Resumen semanal de cumplimiento

2. **Plantillas de Planes**
   - Plantillas predefinidas por industria
   - Clonar plan de año anterior
   - Importar/exportar planes

3. **Dashboard de Cumplimiento**
   - Gráficos de tendencias
   - Comparativa entre sedes
   - Ranking de cumplimiento

4. **Seguimiento Automático de Capacitaciones**
   - Vincular trainings al plan
   - Conteo automático como inspecciones
   - Evidencia de asistentes

5. **Comentarios y Justificaciones**
   - Justificar actividades vencidas
   - Comentarios por actividad
   - Historial de cambios

6. **Integración con Calendario**
   - Sincronización con Google Calendar
   - Recordatorios automáticos
   - Vista de calendario

7. **Reportes Avanzados**
   - Comparativa año a año
   - Análisis de tendencias
   - Predicción de cumplimiento

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists
- [CONTANCIAS_DE_VISITAS.MD](./CONTANCIAS_DE_VISITAS.MD) - Constancias de visitas
- [MATRIZ_RIESGOS.md](./MATRIZ_RIESGOS.md) - Matriz de riesgos

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de contrato anual
- ✅ Matriz de planificación mensual
- ✅ 3 categorías de actividades
- ✅ Seguimiento automático de inspecciones
- ✅ Estados visuales (completado/pendiente/vencido)
- ✅ Modo de edición para Admin
- ✅ Vinculación con checklists
- ✅ Exportación PDF landscape
- ✅ Optimización de queries (sin N+1)
- ✅ Filtros por sede y año
- ✅ Permisos por rol

---

*EP-Back V2.0.0.0 | Sistema de Contrato Anual con Seguimiento Automático | Diciembre 2024*
