# 🎯 Sistema de Matriz de Riesgos - EP-Back V2.0.0.0

> **Estado:** Propuesta de Diseño Técnico - Para Debate en Sprint 🚧  
> **Fecha de creación:** 26 Diciembre 2024  
> **Autor:** Equipo EP-Back

---

## 📋 Índice

1. [Visión General](#-visión-general)
2. [Justificación del Negocio](#-justificación-del-negocio)
3. [Arquitectura del Sistema](#-arquitectura-del-sistema)
4. [Modelo de Datos](#-modelo-de-datos)
5. [Escalas y Cálculos de Riesgo](#-escalas-y-cálculos-de-riesgo)
6. [Componentes y Vistas](#-componentes-y-vistas)
7. [Integración con Checklists](#-integración-con-checklists)
8. [Roadmap de Implementación](#-roadmap-de-implementación)
9. [Estimaciones y Complejidad](#-estimaciones-y-complejidad)
10. [Preguntas Abiertas para el Sprint](#-preguntas-abiertas-para-el-sprint)

---

## 🎯 Visión General

### ¿Qué es una Matriz de Riesgos?

Una **Matriz de Riesgos** es una herramienta de gestión que permite:

1. **Identificar** riesgos laborales, operativos o de cumplimiento
2. **Evaluar** cada riesgo según su **Probabilidad** e **Impacto**
3. **Priorizar** riesgos según su nivel de severidad
4. **Definir controles** y medidas de mitigación
5. **Monitorear** la evolución de los riesgos en el tiempo

### Objetivo del Módulo

Complementar el sistema de **Checklists Dinámicos** con una herramienta de gestión de riesgos que permita:

- ✅ Crear matrices de riesgo personalizadas por proceso, área o proyecto
- ✅ Evaluar riesgos usando escalas estandarizadas (ISO 31000, AS/NZS 4360)
- ✅ Vincular riesgos con checklists de verificación
- ✅ Generar reportes de cumplimiento normativo
- ✅ Visualizar riesgos en heatmaps (mapas de calor)

---

## 💼 Justificación del Negocio

### Valor Agregado

| Beneficio | Descripción | Impacto |
|-----------|-------------|---------|
| **Cumplimiento Normativo** | Cumple con ISO 45001, Ley de Riesgos del Trabajo (Argentina), ISO 31000 | 🔴 Crítico |
| **Diferenciación Competitiva** | Pocos SaaS de checklists incluyen gestión de riesgos | 🟡 Alto |
| **Retención de Clientes** | Clientes de HyS necesitan ambas herramientas | 🟡 Alto |
| **Reducción de Costos** | Evita multas por incumplimiento normativo | 🟢 Medio |
| **Upselling** | Módulo premium para clientes existentes | 🟢 Medio |

### Usuarios Objetivo

1. **Responsables de Higiene y Seguridad (HyS)** 🎯 Principal
2. Auditores de calidad (ISO 9001, ISO 14001)
3. Gerentes de operaciones (riesgos operativos)
4. Equipos de compliance (riesgos legales)

---

## 🏗️ Arquitectura del Sistema

### Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                   PRESENTATION LAYER                         │
│   Livewire Components (RiskMatrixBuilder, RiskMatrixView)   │
├─────────────────────────────────────────────────────────────┤
│                   APPLICATION LAYER                          │
│   Services (RiskCalculatorService, RiskReportService)       │
├─────────────────────────────────────────────────────────────┤
│                   DOMAIN LAYER                               │
│   Models (RiskMatrix, Risk, Control, RiskEvaluation)        │
├─────────────────────────────────────────────────────────────┤
│                   INFRASTRUCTURE LAYER                       │
│   Migrations, Seeders, SQLite/MySQL                         │
└─────────────────────────────────────────────────────────────┘
```

### Relación con Sistema Existente

```
┌─────────────────┐      ┌─────────────────┐
│  DynamicChecklist  │ ◄───┤   RiskMatrix    │
│  (Verificación)    │      │  (Evaluación)   │
└────────┬──────────┘      └────────┬────────┘
         │                          │
         ▼                          ▼
┌─────────────────┐      ┌─────────────────┐
│  DynamicInspection │      │   RiskEvaluation │
│  (Ejecución)       │      │  (Evaluación)    │
└───────────────────┘      └─────────────────┘
```

---

## 🗃️ Modelo de Datos

### Propuesta A: Modelo Separado (Recomendado)

**Ventajas:**
- ✅ Separación de concerns (checklist ≠ matriz)
- ✅ Más fácil de mantener
- ✅ Permite escalar independientemente

**Desventajas:**
- ❌ Duplicación de lógica de preguntas dinámicas
- ❌ Mayor complejidad inicial

#### Tablas Nuevas

##### 1. `risk_matrices`
Representa una matriz de riesgos completa.

| Campo | Tipo | Descripción | Valores |
|-------|------|-------------|---------|
| `id` | bigint | PK autoincremental | - |
| `title` | varchar(255) | Título de la matriz | "Riesgos Planta Industrial 2024" |
| `description` | text | Descripción detallada | - |
| `area` | varchar(100) | Área/Departamento | "Producción", "Almacén" |
| `process` | varchar(100) | Proceso asociado | "Corte de metales" |
| `responsible_user_id` | bigint | FK a `users` | Usuario responsable |
| `status` | enum | Estado actual | `draft`, `active`, `archived` |
| `probability_scale_id` | bigint | FK a `risk_scales` | Escala de probabilidad |
| `impact_scale_id` | bigint | FK a `risk_scales` | Escala de impacto |
| `created_at` | timestamp | - | - |
| `updated_at` | timestamp | - | - |

**Índices:**
- `status`
- `area`
- `responsible_user_id`

---

##### 2. `risks`
Representa un riesgo específico dentro de una matriz.

| Campo | Tipo | Descripción | Valores |
|-------|------|-------------|---------|
| `id` | bigint | PK | - |
| `risk_matrix_id` | bigint | FK a `risk_matrices` | - |
| `code` | varchar(50) | Código único del riesgo | "R-PLA-001" |
| `name` | varchar(255) | Nombre del riesgo | "Corte con sierra circular" |
| `description` | text | Descripción detallada | - |
| `category` | varchar(100) | Categoría del riesgo | "Mecánico", "Químico", "Ergonómico" |
| `source` | varchar(255) | Fuente del riesgo | "Uso de maquinaria sin resguardos" |
| `affected_workers` | int | Cantidad de trabajadores expuestos | 5 |
| `order` | int | Orden de visualización | - |
| `created_at` | timestamp | - | - |
| `updated_at` | timestamp | - | - |

**Índices:**
- `(risk_matrix_id, order)`
- `code` UNIQUE
- `category`

---

##### 3. `risk_evaluations`
Evaluación de un riesgo en un momento específico.

| Campo | Tipo | Descripción | Valores |
|-------|------|-------------|---------|
| `id` | bigint | PK | - |
| `risk_id` | bigint | FK a `risks` | - |
| `evaluated_by` | bigint | FK a `users` | - |
| `probability` | int | Nivel de probabilidad | 1-5 |
| `impact` | int | Nivel de impacto | 1-5 |
| `risk_level` | int | Calculado: P × I | 1-25 |
| `risk_category` | enum | Categoría calculada | `low`, `medium`, `high`, `critical` |
| `justification` | text | Justificación de la evaluación | - |
| `evaluated_at` | timestamp | Fecha de evaluación | - |
| `created_at` | timestamp | - | - |

**Índices:**
- `(risk_id, evaluated_at)`
- `risk_category`

---

##### 4. `controls`
Controles/medidas de mitigación para riesgos.

| Campo | Tipo | Descripción | Valores |
|-------|------|-------------|---------|
| `id` | bigint | PK | - |
| `risk_id` | bigint | FK a `risks` | - |
| `type` | enum | Tipo de control | `elimination`, `substitution`, `engineering`, `administrative`, `ppe` |
| `description` | text | Descripción del control | "Instalar resguardo en sierra" |
| `status` | enum | Estado de implementación | `planned`, `implementing`, `implemented` |
| `responsible_user_id` | bigint | FK a `users` | - |
| `deadline` | date | Fecha límite | - |
| `verification_checklist_id` | bigint | FK a `dynamic_checklists` (nullable) | Checklist para verificar |
| `created_at` | timestamp | - | - |
| `updated_at` | timestamp | - | - |

**Relación clave:** `verification_checklist_id` conecta controles con checklists.

---

##### 5. `risk_scales`
Escalas configurables de probabilidad e impacto.

| Campo | Tipo | Descripción | Valores |
|-------|------|-------------|---------|
| `id` | bigint | PK | - |
| `name` | varchar(255) | Nombre de la escala | "Probabilidad ISO 31000" |
| `type` | enum | Tipo de escala | `probability`, `impact` |
| `levels` | json | Niveles de la escala | `[{"value": 1, "label": "Muy bajo"}, ...]` |
| `is_default` | boolean | Escala por defecto | - |
| `created_at` | timestamp | - | - |

**Ejemplo de `levels` JSON:**
```json
[
  {"value": 1, "label": "Muy bajo", "description": "Ocurre < 1 vez al año"},
  {"value": 2, "label": "Bajo", "description": "Ocurre 1-3 veces al año"},
  {"value": 3, "label": "Medio", "description": "Ocurre mensualmente"},
  {"value": 4, "label": "Alto", "description": "Ocurre semanalmente"},
  {"value": 5, "label": "Muy alto", "description": "Ocurre diariamente"}
]
```

---

### Propuesta B: Extender Modelo Existente

Agregar campo `type` a `dynamic_checklists`:
- `type = 'checklist'` → Checklist normal
- `type = 'risk_matrix'` → Matriz de riesgos

**Ventajas:**
- ✅ Reutiliza todo el sistema de preguntas dinámicas
- ✅ Menos código nuevo

**Desventajas:**
- ❌ Acoplamiento fuerte
- ❌ Complejidad creciente en `DynamicChecklist`
- ❌ Dificulta la escalabilidad futura

> **Recomendación:** Propuesta A (Modelo Separado)

---

## 📐 Escalas y Cálculos de Riesgo

### Escala Estándar 5×5 (ISO 31000)

#### Probabilidad (P)

| Nivel | Etiqueta | Descripción | Frecuencia Estimada |
|-------|----------|-------------|---------------------|
| 1 | Muy Bajo | Raro, casi imposible | < 1 vez/año |
| 2 | Bajo | Poco probable | 1-3 veces/año |
| 3 | Medio | Posible | Mensual |
| 4 | Alto | Probable | Semanal |
| 5 | Muy Alto | Casi seguro | Diario |

#### Impacto (I)

| Nivel | Etiqueta | Descripción | Consecuencias |
|-------|----------|-------------|---------------|
| 1 | Insignificante | Sin lesiones | Daños < $1000 |
| 2 | Menor | Lesión leve | Daños $1000-$10.000 |
| 3 | Moderado | Lesión con baja laboral | Daños $10.000-$50.000 |
| 4 | Mayor | Lesión grave | Daños $50.000-$200.000 |
| 5 | Catastrófico | Muerte o incapacidad permanente | Daños > $200.000 |

### Fórmula de Cálculo

```
Nivel de Riesgo = Probabilidad × Impacto
```

**Resultados posibles:** 1 a 25

### Categorización de Riesgo

| Nivel Calculado | Categoría | Color | Acción Requerida |
|-----------------|-----------|-------|------------------|
| 1-4 | Bajo | 🟢 Verde | Monitoreo periódico |
| 5-9 | Medio | 🟡 Amarillo | Plan de control en 6 meses |
| 10-15 | Alto | 🟠 Naranja | Plan de control en 1 mes |
| 16-25 | Crítico | 🔴 Rojo | Acción inmediata (< 1 semana) |

### Matriz Visual (Heatmap)

```
IMPACTO
    5│ 🟡  🟠  🟠  🔴  🔴
    4│ 🟡  🟡  🟠  🟠  🔴
    3│ 🟢  🟡  🟡  🟠  🟠
    2│ 🟢  🟢  🟡  🟡  🟠
    1│ 🟢  🟢  🟢  🟡  🟡
     └─────────────────────
       1   2   3   4   5  PROBABILIDAD
```

---

## 🖥️ Componentes y Vistas

### Rutas Propuestas

| Ruta | Componente | Función |
|------|------------|---------|
| `/risk-matrices` | `RiskMatrixIndex` | Listado de matrices |
| `/risk-matrices/create` | `RiskMatrixBuilder` | Crear nueva matriz |
| `/risk-matrices/{id}/edit` | `RiskMatrixBuilder` | Editar matriz |
| `/risk-matrices/{id}` | `RiskMatrixView` | Ver matriz (tabla + heatmap) |
| `/risk-matrices/{id}/risks/create` | `RiskEditor` | Agregar riesgo |
| `/risk-matrices/{id}/risks/{riskId}` | `RiskDetail` | Detalle de riesgo + controles |
| `/risk-matrices/{id}/evaluate` | `RiskEvaluator` | Evaluar todos los riesgos |
| `/risk-matrices/{id}/report` | `RiskReportPDF` | Exportar PDF |

### Componentes Livewire

#### 1. `RiskMatrixIndex.php`
- **Función:** Listado de matrices con filtros (área, estado, responsable)
- **Vistas:** Tabla + tarjetas
- **Acciones:** Crear, editar, duplicar, archivar, ver

#### 2. `RiskMatrixBuilder.php`
- **Función:** CRUD de matrices
- **Campos:**
  - Información general (título, área, proceso)
  - Selección de escalas (probabilidad/impacto)
  - Asignación de responsable
  - Gestión de riesgos (agregar/editar/eliminar)

#### 3. `RiskMatrixView.php`
- **Función:** Visualización de matriz completa
- **Secciones:**
  - Información de la matriz
  - Tabla de riesgos (con nivel calculado)
  - Heatmap interactivo
  - Lista de controles pendientes

#### 4. `RiskEvaluator.php`
- **Función:** Evaluar riesgos (asignar P e I)
- **Características:**
  - Formulario para cada riesgo
  - Cálculo automático de nivel
  - Historial de evaluaciones
  - Comparación con evaluaciones previas

#### 5. `RiskDetail.php`
- **Función:** Detalle de un riesgo específico
- **Secciones:**
  - Información del riesgo
  - Evaluaciones históricas (timeline)
  - Controles asociados
  - Checklist de verificación (si existe)

### UI/UX - Componentes Visuales

#### Heatmap (Mapa de Calor)
```html
<!-- Tabla 5x5 interactiva con Alpine.js -->
<div class="grid grid-cols-6 gap-1">
  <!-- Eje Y: Impacto -->
  <div>Impacto</div>
  
  <!-- Celdas coloreadas según P×I -->
  @for($i = 5; $i >= 1; $i--)
    <div>{{ $i }}</div>
    @for($p = 1; $p <= 5; $p++)
      <div class="risk-cell" 
           :class="getRiskColor({{ $i }}, {{ $p }})"
           @click="filterRisksByLevel({{ $i * $p }})">
        <!-- Mostrar cantidad de riesgos en esta celda -->
        <span>{{ count }}</span>
      </div>
    @endfor
  @endfor
  
  <!-- Eje X: Probabilidad -->
  <div></div>
  @for($p = 1; $p <= 5; $p++)
    <div>{{ $p }}</div>
  @endfor
  <div>Probabilidad</div>
</div>
```

#### Badge de Categoría de Riesgo
```html
<span class="px-3 py-1 rounded-full text-sm font-semibold
             {{ match($risk->category) {
               'low' => 'bg-green-100 text-green-800',
               'medium' => 'bg-yellow-100 text-yellow-800',
               'high' => 'bg-orange-100 text-orange-800',
               'critical' => 'bg-red-100 text-red-800'
             } }}">
  {{ strtoupper($risk->category) }}
</span>
```

---

## 🔗 Integración con Checklists

### Casos de Uso de Integración

#### 1. Verificación de Controles
```
Flujo:
1. Usuario crea riesgo "Corte con sierra"
2. Agrega control "Instalar resguardo"
3. Enlaza checklist "Verificación de resguardos"
4. Al completar checklist → actualiza estado del control
```

**Implementación:**
```php
// En la tabla controls
'verification_checklist_id' => foreignId('dynamic_checklists')
```

#### 2. Generación Automática de Matriz desde Checklist

```
Flujo:
1. Usuario completa checklist "Inspección de Planta"
2. Checklist tiene preguntas tipo "¿Se detectó algún riesgo?"
3. Sistema sugiere: "Generar Matriz de Riesgos"
4. Crea matriz prellenada con riesgos identificados
```

**Implementación:**
- Nueva tabla: `checklist_risk_mappings`
- Mapea preguntas de checklist → campos de riesgo

#### 3. Enlace Bidireccional

```
Matriz de Riesgos ↔ Checklist de Verificación
```

**UI:**
- En `RiskMatrixView`: botón "Ver Checklists Relacionados"
- En `ChecklistIndex`: filtro "Solo checklists vinculados a riesgos"

### Tabla de Relación (Opcional)

##### `checklist_risk_matrix_relations`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | PK |
| `risk_matrix_id` | bigint | FK a `risk_matrices` |
| `dynamic_checklist_id` | bigint | FK a `dynamic_checklists` |
| `relation_type` | enum | `verification`, `source`, `other` |
| `created_at` | timestamp | - |

---

## 🛣️ Roadmap de Implementación

### Fase 1: MVP - Matriz Standalone (4-6 semanas)

#### Sprint 1: Modelo de Datos (1 semana)
- [x] Debate y aprobación de diseño técnico
- [ ] Crear migraciones (`risk_matrices`, `risks`, `risk_evaluations`, `controls`, `risk_scales`)
- [ ] Crear modelos Eloquent + relaciones
- [ ] Crear factories para testing
- [ ] Seeder con datos demo (1 matriz con 10 riesgos)
- [ ] Tests unitarios de modelos

**Entregables:**
- ✅ Migraciones ejecutadas
- ✅ Seeder con datos de prueba

#### Sprint 2: CRUD de Matrices (1-2 semanas)
- [ ] `RiskMatrixIndex.php` - Listado
- [ ] `RiskMatrixBuilder.php` - Crear/Editar
- [ ] Vistas Blade correspondientes
- [ ] Validaciones (Form Requests)
- [ ] Tests de feature (crear, editar, eliminar)

**Entregables:**
- ✅ CRUD funcional de matrices
- ✅ Navegación entre vistas

#### Sprint 3: Gestión de Riesgos (1-2 semanas)
- [ ] Agregar/editar riesgos dentro de matriz
- [ ] `RiskEvaluator.php` - Evaluar P e I
- [ ] Cálculo automático de nivel de riesgo
- [ ] `RiskDetail.php` - Detalle de riesgo
- [ ] Tests de cálculos de riesgo

**Entregables:**
- ✅ Evaluación de riesgos funcional
- ✅ Cálculos automáticos correctos

#### Sprint 4: Visualización (1-2 semanas)
- [ ] `RiskMatrixView.php` - Vista completa
- [ ] Heatmap interactivo (Alpine.js)
- [ ] Filtros por categoría/nivel
- [ ] Gráficos (distribución de riesgos)
- [ ] Exportar PDF básico

**Entregables:**
- ✅ Heatmap funcional
- ✅ PDF exportable

### Fase 2: Controles y Verificación (2-3 semanas)

#### Sprint 5: Gestión de Controles
- [ ] CRUD de controles
- [ ] Asignación de responsables
- [ ] Dashboard de controles pendientes
- [ ] Notificaciones por deadline

#### Sprint 6: Integración con Checklists
- [ ] Enlace control ↔ checklist
- [ ] Actualización automática de estado
- [ ] Vista "Checklists Relacionados" en matriz

### Fase 3: Avanzado (3-4 semanas)

#### Sprint 7: Historial y Auditoría
- [ ] Timeline de evaluaciones
- [ ] Comparación de evaluaciones
- [ ] Registro de cambios (audit log)

#### Sprint 8: Reportes y Analytics
- [ ] Reportes avanzados (PDF/Excel)
- [ ] Dashboard de riesgos (KPIs)
- [ ] Tendencias de riesgo

#### Sprint 9: Escalas Personalizadas
- [ ] CRUD de `risk_scales`
- [ ] Configuración por cliente
- [ ] Migración de matrices existentes

---

## 💰 Estimaciones y Complejidad

### Estimación de Esfuerzo (Desarrollador Senior)

| Fase | Sprints | Semanas | Horas | Story Points |
|------|---------|---------|-------|--------------|
| Fase 1: MVP | 4 | 4-6 | 120-160h | 34 |
| Fase 2: Controles | 2 | 2-3 | 60-80h | 13 |
| Fase 3: Avanzado | 3 | 3-4 | 80-100h | 21 |
| **TOTAL** | **9** | **9-13** | **260-340h** | **68** |

### Complejidad Técnica

| Componente | Complejidad | Riesgo | Notas |
|------------|-------------|--------|-------|
| Modelo de datos | 🟡 Media | 🟢 Bajo | Similar a checklists |
| Cálculos de riesgo | 🟢 Baja | 🟢 Bajo | Fórmulas simples |
| Heatmap interactivo | 🟡 Media | 🟡 Medio | Requiere Alpine.js avanzado |
| Integración checklists | 🔴 Alta | 🟡 Medio | Complejidad en relaciones |
| PDF con gráficos | 🟡 Media | 🟡 Medio | Uso de librerías (DomPDF) |
| Escalas personalizables | 🔴 Alta | 🟠 Alto | JSON dinámico + validaciones |

### Dependencias Técnicas

- **Librerías PHP:**
  - `barryvdh/laravel-dompdf` (PDF)
  - Laravel Excel (exportar datos)

- **JavaScript:**
  - Chart.js (gráficos)
  - Alpine.js (interactividad)

---

## ❓ Preguntas Abiertas para el Sprint

### 1. Modelo de Datos
- [ ] **¿Aprobamos Propuesta A (Modelo Separado)?**
  - Alternativa: Propuesta B (Extender `dynamic_checklists`)
  
- [ ] **¿Usamos escalas configurables desde el inicio?**
  - Alternativa: Hardcodear escala 5×5 en Fase 1

### 2. Integración con Checklists
- [ ] **¿Qué tipo de integración priorizamos?**
  - Opción A: Solo enlace manual (control → checklist)
  - Opción B: Generación automática desde checklist
  - Opción C: Bidireccional completa

- [ ] **¿Permitimos múltiples checklists por control?**

### 3. UI/UX
- [ ] **¿Heatmap en Fase 1 o Fase 2?**
  - Heatmap requiere ~20h adicionales

- [ ] **¿Diseño responsive (móvil) es crítico?**
  - Matrices suelen usarse en desktop

### 4. Alcance del MVP
- [ ] **¿Incluimos PDF en Fase 1?**
  - PDF básico: +10-15h
  - PDF con gráficos: +25-30h

- [ ] **¿Notificaciones por email de controles vencidos?**
  - Requiere configuración de colas/jobs

### 5. Datos Demo
- [ ] **¿Cuántas matrices de ejemplo en el seeder?**
  - Propuesta: 2-3 matrices (HyS, Ambiental, Calidad)

### 6. Permisos y Roles
- [ ] **¿Restricciones por rol?**
  - Admin: CRUD completo
  - Responsable HyS: Crear/editar solo sus matrices
  - Técnico: Solo evaluar riesgos asignados

### 7. Nomenclatura
- [ ] **¿Nombres de rutas en español o inglés?**
  - `/risk-matrices` vs `/matrices-riesgo`
  - Recomendación: inglés (consistencia con código)

---

## 📚 Referencias

### Normativas y Estándares
- [ISO 31000:2018](https://www.iso.org/iso-31000-risk-management.html) - Gestión de Riesgos
- [ISO 45001:2018](https://www.iso.org/iso-45001-occupational-health-and-safety.html) - Salud y Seguridad Ocupacional
- [AS/NZS 4360:2004](https://www.standards.org.au/) - Gestión de Riesgos (Australia/Nueva Zelanda)

### Documentación Interna
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema actual
- [ROADMAP.md](./ROADMAP.md) - Roadmap general
- [context.md](../.agent/workflows/context.md) - Contexto del proyecto

### Literatura y Recursos
- *Risk Matrix: A Primer* - ASSE (American Society of Safety Engineers)
- *Fundamentals of Risk Assessment* - W. David Yates

---

## 📊 Métricas de Éxito

### Indicadores de Adopción
- [ ] ≥ 50% de clientes de HyS crean al menos 1 matriz en primer mes
- [ ] ≥ 70% de matrices tienen controles asociados
- [ ] ≥ 30% de controles están vinculados a checklists

### Indicadores Técnicos
- [ ] Coverage de tests ≥ 70%
- [ ] Tiempo de carga de matriz con 50 riesgos < 2 segundos
- [ ] Exportación de PDF < 5 segundos

### Indicadores de Negocio
- [ ] Retención de clientes +15% (con módulo de riesgos)
- [ ] Upselling ≥ 20% de base actual
- [ ] NPS ≥ 8/10 para módulo de riesgos

---

## ✅ Próximos Pasos

### Antes del Sprint Planning
1. **Revisar y debatir este documento en reunión de equipo**
2. **Responder preguntas abiertas (Sección 10)**
3. **Priorizar features (MVP vs. Nice-to-have)**
4. **Asignar story points definitivos**

### Durante el Sprint Planning
1. **Aprobar Propuesta A o B de modelo de datos**
2. **Definir alcance de Fase 1 (4-6 semanas)**
3. **Crear tickets en Jira/Linear/GitHub Issues**
4. **Asignar responsables**

### Post-Sprint Planning
1. **Actualizar `ROADMAP.md` con Matriz de Riesgos**
2. **Crear rama `feature/risk-matrix`**
3. **Iniciar Sprint 1: Modelo de Datos**

---

**Documento preparado para debate en Sprint Planning**  
**Fecha:** 26 Diciembre 2024  
**Versión:** 1.0 - Propuesta Inicial

> [!IMPORTANT]
> Este documento es una propuesta de diseño técnico. **No implementar código hasta aprobar el diseño en el sprint planning.**

> [!TIP]
> Para facilitar el debate, cada sección tiene preguntas específicas que requieren decisión del equipo.
