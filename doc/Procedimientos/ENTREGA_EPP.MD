# 🦺 Sistema de Entrega de EPP - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Wizard de Entrega ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema integral para gestionar el catálogo de Equipos de Protección Personal (EPP), empleados, y realizar entregas en campo con firma digital y generación automática de constancias PDF.

### URL de Acceso
```
http://localhost:8000/epp/catalog              # Catálogo EPP (Admin)
http://localhost:8000/epp/employees            # Gestión empleados (Admin+Técnico)
http://localhost:8000/epp/import-employees     # Importar empleados (Admin)
http://localhost:8000/epp/deliver              # Wizard entrega (Técnico)
http://localhost:8000/epp/history              # Historial entregas (Admin+Técnico)
http://localhost:8000/epp/delivery/{id}/pdf    # Descargar constancia PDF
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (5)               │
│  EppCatalog | EmployeeManager |                │
│  EmployeeImporter | EppDeliveryWizard |        │
│  EppHistory                                    │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (3)               │
│  EppItem | Employee | EppDelivery              │
├────────────────────────────────────────────────┤
│              CONTROLLERS (1)                   │
│  EppPDFController                              │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  epp_items | employees | epp_deliveries |      │
│  epp_delivery_items (pivot)                    │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Epp/
├── EppCatalog.php              # CRUD catálogo EPP
├── EmployeeManager.php         # CRUD empleados
├── EmployeeImporter.php        # Importar desde Excel
├── EppDeliveryWizard.php       # Wizard 3 pasos
└── EppHistory.php              # Historial de entregas
```

### Modelos

```
app/Models/
├── EppItem.php                 # Elemento EPP
├── Employee.php                # Empleado
└── EppDelivery.php             # Entrega de EPP
```

### Controladores

```
app/Http/Controllers/
└── EppPDFController.php        # Generación de PDFs
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/epp/catalog` | EppCatalog | Gestión catálogo | Admin |
| `/epp/employees` | EmployeeManager | Gestión empleados | Admin+Técnico |
| `/epp/import-employees` | EmployeeImporter | Importar Excel | Admin |
| `/epp/deliver` | EppDeliveryWizard | Wizard entrega | Técnico |
| `/epp/history` | EppHistory | Historial | Admin+Técnico |
| `/epp/delivery/{id}/pdf` | EppPDFController | Descargar PDF | Admin+Técnico |
| `/epp/delivery/{id}/pdf/preview` | EppPDFController | Vista previa | Admin+Técnico |

---

## 📊 Base de Datos

### Tabla: epp_items

```sql
epp_items
├── id
├── nombre                      -- Nombre del EPP
├── descripcion                 -- Descripción
├── tipo                        -- Tipo de EPP (Casco, Guantes, etc.)
├── vida_util_meses             -- Vida útil en meses
├── codigo_norma                -- Código de norma (ej: ANSI Z87.1)
├── is_active (boolean)         -- Activo/Inactivo
├── created_at
└── updated_at
```

### Tabla: employees

```sql
employees
├── id
├── name                        -- Nombre
├── last_name                   -- Apellido
├── dni                         -- DNI
├── cuil                        -- CUIL
├── puesto                      -- Puesto de trabajo
├── empresa_id (FK → empresas)
├── sede_id (FK → sedes)
├── created_at
└── updated_at
```

### Tabla: epp_deliveries

```sql
epp_deliveries
├── id
├── employee_id (FK → employees)
├── user_id (FK → users)        -- Técnico que entregó
├── delivery_date               -- Fecha de entrega
├── signature                   -- Firma del empleado (Base64)
├── observaciones               -- Observaciones
├── created_at
└── updated_at
```

### Tabla: epp_delivery_items (Pivot)

```sql
epp_delivery_items
├── id
├── epp_delivery_id (FK → epp_deliveries)
├── epp_item_id (FK → epp_items)
├── cantidad                    -- Cantidad entregada
├── tipo_entrega                -- 'inicial' | 'reposición' | 'renovación'
├── created_at
└── updated_at
```

---

## ✨ Características Principales

### 1. Catálogo de EPP (Admin)

**Gestión de elementos:**
- Nombre del EPP
- Descripción
- Tipo (Casco, Guantes, Botas, Arnés, etc.)
- Vida útil en meses
- Código de norma (ANSI, ISO, etc.)
- Estado activo/inactivo

**Funcionalidades:**
- CRUD completo
- Búsqueda por nombre o tipo
- Paginación
- Modal de creación/edición

### 2. Gestión de Empleados

**Datos del empleado:**
- Nombre y apellido
- DNI
- CUIL
- Puesto de trabajo
- Empresa
- Sede

**Funcionalidades:**
- CRUD completo
- Búsqueda por nombre, apellido o DNI
- Filtros por empresa y sede
- Importación masiva desde Excel

### 3. Importación de Empleados (Excel)

**Formato esperado:**
```
| Nombre | Apellido | DNI | CUIL | Puesto | Empresa | Sede |
|--------|----------|-----|------|--------|---------|------|
| Juan   | Pérez    | 123 | 456  | Obrero | ABC     | Obra |
```

**Características:**
- Validación de datos
- Prevención de duplicados
- Asignación automática de empresa/sede
- Reporte de errores

### 4. Wizard de Entrega (3 Pasos)

**Paso 1: Seleccionar Empleado**
- Filtro por empresa y sede
- Búsqueda por nombre
- Lista de empleados disponibles

**Paso 2: Seleccionar EPP**
- Catálogo de EPP activos
- Selección múltiple
- Por cada item:
  - Tipo de entrega (inicial/reposición/renovación)
  - Cantidad

**Paso 3: Firma y Confirmación**
- Firma digital del empleado
- Observaciones opcionales
- Resumen de la entrega

### 5. Historial de Entregas

**Visualización:**
- Lista de todas las entregas
- Filtros por empresa, sede, empleado
- Búsqueda por nombre de empleado
- Paginación

**Información mostrada:**
- Empleado
- Fecha de entrega
- Técnico que entregó
- Items entregados (cantidad)
- Acciones: Ver PDF

**Permisos:**
- Admin: Ve todas las entregas
- Técnico: Solo sus entregas

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Primera Entrega a Empleado Nuevo

```
1. Admin crea empleado
   └─ /epp/employees
   ├─ Nombre: Juan Pérez
   ├─ DNI: 12345678
   ├─ Puesto: Operario
   ├─ Empresa: Constructora ABC
   └─ Sede: Obra Centro

2. Técnico va a obra
   └─ Accede desde tablet/móvil

3. Inicia wizard de entrega
   └─ /epp/deliver

4. Paso 1: Selecciona empleado
   ├─ Filtra por: Obra Centro
   ├─ Busca: "Juan"
   └─ Selecciona: Juan Pérez

5. Paso 2: Selecciona EPP
   ├─ ✅ Casco (Tipo: Inicial, Cant: 1)
   ├─ ✅ Guantes (Tipo: Inicial, Cant: 2)
   ├─ ✅ Botas (Tipo: Inicial, Cant: 1)
   ├─ ✅ Arnés (Tipo: Inicial, Cant: 1)
   └─ ✅ Lentes (Tipo: Inicial, Cant: 1)

6. Paso 3: Firma
   ├─ Empleado firma en tablet
   ├─ Observaciones: "Entrega inicial completa"
   └─ Confirma entrega

7. Sistema guarda
   ├─ Crea registro de entrega
   ├─ Vincula 5 items
   └─ Genera PDF automático

8. PDF disponible
   └─ Constancia de entrega con firma
```

### Escenario 2: Reposición de EPP Dañado

```
1. Empleado reporta guantes dañados
   └─ Técnico debe reponer

2. Técnico inicia wizard
   └─ /epp/deliver

3. Paso 1: Selecciona empleado
   └─ Juan Pérez (ya existe)

4. Paso 2: Selecciona EPP
   └─ ✅ Guantes (Tipo: Reposición, Cant: 2)

5. Paso 3: Firma
   ├─ Observaciones: "Reposición por desgaste"
   └─ Confirma

6. Sistema registra
   └─ Nueva entrega de reposición

7. Historial actualizado
   └─ Juan Pérez ahora tiene 2 entregas
```

### Escenario 3: Importación Masiva de Empleados

```
1. Admin recibe Excel con 50 empleados
   └─ Nuevos trabajadores en obra

2. Accede a importador
   └─ /epp/import-employees

3. Selecciona archivo Excel
   └─ empleados_obra_centro.xlsx

4. Sistema valida
   ├─ Verifica formato
   ├─ Valida DNIs
   ├─ Busca duplicados
   └─ Asigna empresa/sede

5. Importación exitosa
   └─ 50 empleados creados

6. Técnico puede entregar EPP
   └─ Todos los empleados disponibles
```

---

## 🎨 Interfaz de Usuario

### EppCatalog (Catálogo)

**Lista de EPP:**
```
┌─────────────────────────────────────┐
│ [Buscar...] [+ Nuevo EPP]           │
├─────────────────────────────────────┤
│ Nombre    │ Tipo    │ Vida Útil │ Acciones │
├───────────┼─────────┼───────────┼──────────┤
│ Casco     │ Cabeza  │ 24 meses  │ ✏️ 🗑️    │
│ Guantes   │ Manos   │ 6 meses   │ ✏️ 🗑️    │
│ Botas     │ Pies    │ 12 meses  │ ✏️ 🗑️    │
└─────────────────────────────────────┘
```

**Modal Crear/Editar:**
```
┌─────────────────────────────────────┐
│ Nuevo Elemento EPP                  │
├─────────────────────────────────────┤
│ Nombre: [________________________]  │
│ Tipo: [________________________]    │
│ Descripción: [__________________]   │
│ Vida Útil (meses): [____]           │
│ Código Norma: [_________________]   │
│ ☑ Activo                            │
├─────────────────────────────────────┤
│ [Cancelar] [Guardar]                │
└─────────────────────────────────────┘
```

### EmployeeManager (Empleados)

**Lista de Empleados:**
```
┌─────────────────────────────────────┐
│ [Buscar...] [+ Nuevo Empleado]      │
│ Empresa: [▼] Sede: [▼]              │
├─────────────────────────────────────┤
│ Nombre      │ DNI  │ Empresa │ Acciones │
├─────────────┼──────┼─────────┼──────────┤
│ Juan Pérez  │ 123  │ ABC     │ ✏️ 🗑️    │
│ María López │ 456  │ ABC     │ ✏️ 🗑️    │
└─────────────────────────────────────┘
```

### EppDeliveryWizard (Wizard de Entrega)

**Paso 1: Seleccionar Empleado**
```
┌─────────────────────────────────────┐
│ Paso 1 de 3: Seleccionar Empleado  │
├─────────────────────────────────────┤
│ Empresa: [▼ Constructora ABC]       │
│ Sede: [▼ Obra Centro]               │
│ Buscar: [________________]          │
├─────────────────────────────────────┤
│ ○ Juan Pérez - DNI: 12345678        │
│   Puesto: Operario                  │
│                                     │
│ ○ María López - DNI: 23456789       │
│   Puesto: Supervisora               │
└─────────────────────────────────────┘
```

**Paso 2: Seleccionar EPP**
```
┌─────────────────────────────────────┐
│ Paso 2 de 3: Seleccionar EPP        │
│ Empleado: Juan Pérez                │
├─────────────────────────────────────┤
│ ☑ Casco                             │
│   Tipo: ○ Inicial ○ Reposición      │
│   Cantidad: [1]                     │
│                                     │
│ ☑ Guantes                           │
│   Tipo: ○ Inicial ○ Reposición      │
│   Cantidad: [2]                     │
│                                     │
│ ☐ Botas                             │
├─────────────────────────────────────┤
│ [← Anterior] [Siguiente →]          │
└─────────────────────────────────────┘
```

**Paso 3: Firma**
```
┌─────────────────────────────────────┐
│ Paso 3 de 3: Firma y Confirmación   │
├─────────────────────────────────────┤
│ Resumen:                            │
│ • Casco x1 (Inicial)                │
│ • Guantes x2 (Inicial)              │
│                                     │
│ Firma del Empleado: *               │
│ [Canvas de firma]                   │
│ [Limpiar] [Guardar]                 │
│                                     │
│ Observaciones:                      │
│ [_________________________________] │
├─────────────────────────────────────┤
│ [← Anterior] [Confirmar Entrega]    │
└─────────────────────────────────────┘
```

### EppHistory (Historial)

**Lista de Entregas:**
```
┌─────────────────────────────────────┐
│ [Buscar...] Empresa: [▼] Sede: [▼]  │
├─────────────────────────────────────┤
│ Fecha      │ Empleado │ Items │ PDF  │
├────────────┼──────────┼───────┼──────┤
│ 2024-12-30 │ J. Pérez │ 5     │ 📄   │
│ 2024-12-29 │ M. López │ 3     │ 📄   │
└─────────────────────────────────────┘
```

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver catálogo EPP | ✅ | ❌ |
| CRUD catálogo EPP | ✅ | ❌ |
| Ver empleados | ✅ | ✅ |
| CRUD empleados | ✅ | ✅ |
| Importar empleados | ✅ | ❌ |
| Realizar entrega | ✅ | ✅ |
| Ver historial completo | ✅ | ❌ (solo propias) |
| Descargar PDF | ✅ | ✅ |

### Validaciones

**Catálogo EPP:**
- Nombre: Requerido, máximo 255 caracteres
- Tipo: Requerido
- Vida útil: Opcional, mínimo 1 mes

**Empleados:**
- Nombre y apellido: Requeridos
- Empresa y sede: Requeridas
- DNI/CUIL: Opcionales

**Entrega:**
- Empleado: Requerido
- Al menos 1 item EPP: Requerido
- Firma: Requerida
- Cantidad: Mínimo 1

---

## 📄 Generación de PDFs

### EppPDFController

**Métodos:**

1. **download($id)** - Descarga el PDF
   ```php
   return $pdf->download('entrega_epp_{id}_{fecha}.pdf');
   ```

2. **preview($id)** - Vista previa
   ```php
   return $pdf->stream('entrega_epp_{id}_{fecha}.pdf');
   ```

### Vista PDF: `pdf.epp-delivery`

**Estructura:**
```
┌─────────────────────────────────────┐
│ CONSTANCIA DE ENTREGA DE EPP        │
├─────────────────────────────────────┤
│ Entrega N°: {id}                    │
│ Fecha: {fecha}                      │
│                                     │
│ DATOS DEL EMPLEADO                  │
│ Nombre: {nombre apellido}           │
│ DNI: {dni}                          │
│ CUIL: {cuil}                        │
│ Puesto: {puesto}                    │
│ Empresa: {empresa}                  │
│ Sede: {sede}                        │
├─────────────────────────────────────┤
│ ELEMENTOS ENTREGADOS                │
├─────────────────────────────────────┤
│ Item      │ Tipo      │ Cant │ Norma│
├───────────┼───────────┼──────┼──────┤
│ Casco     │ Inicial   │ 1    │ ANSI │
│ Guantes   │ Inicial   │ 2    │ EN   │
│ Botas     │ Inicial   │ 1    │ ISO  │
│ Arnés     │ Inicial   │ 1    │ ANSI │
│ Lentes    │ Inicial   │ 1    │ Z87  │
├─────────────────────────────────────┤
│ Observaciones:                      │
│ {observaciones}                     │
├─────────────────────────────────────┤
│ FIRMAS                              │
├─────────────────────────────────────┤
│ Empleado:                           │
│ [Firma digital]                     │
│ {nombre}                            │
│ DNI: {dni}                          │
│                                     │
│ Técnico:                            │
│ {nombre técnico}                    │
│ Fecha: {fecha}                      │
└─────────────────────────────────────┘
```

---

## 🔄 Tipos de Entrega

### 1. Inicial

**Cuándo usar:**
- Primera entrega al empleado
- Ingreso nuevo a la empresa
- Cambio de puesto que requiere nuevo EPP

**Características:**
- Entrega completa de EPP
- Documentación de inicio
- Capacitación incluida

### 2. Reposición

**Cuándo usar:**
- EPP dañado o perdido
- Antes de fin de vida útil
- Emergencia

**Características:**
- Reemplazo de item específico
- Justificación recomendada
- Puede ser parcial

### 3. Renovación

**Cuándo usar:**
- Fin de vida útil del EPP
- Mantenimiento preventivo
- Actualización de normas

**Características:**
- Reemplazo programado
- Según vida útil del catálogo
- Trazabilidad de renovaciones

---

## 📊 Trazabilidad y Control

### Historial por Empleado

```php
// Ver todas las entregas de un empleado
$employee->deliveries()
    ->with('items')
    ->latest()
    ->get();

// Último EPP entregado
$lastDelivery = $employee->deliveries()->latest()->first();
```

### Control de Vida Útil

```php
// Calcular fecha de vencimiento
$deliveryDate = $delivery->delivery_date;
$vidaUtil = $item->vida_util_meses;
$vencimiento = $deliveryDate->addMonths($vidaUtil);

// Alertar próximos vencimientos
$proximosVencer = EppDelivery::where('delivery_date', '<', now()->subMonths($vidaUtil - 1))
    ->get();
```

### Estadísticas

**Por Empleado:**
- Total de entregas recibidas
- Items actuales
- Próximos vencimientos

**Por Sede:**
- Total de entregas realizadas
- Items más entregados
- Técnicos más activos

**Por Item EPP:**
- Total entregado
- Promedio por empleado
- Tasa de reposición

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Wizard Guiado** | Proceso simple y sin errores |
| **Firma Digital** | Sin papeles, todo digital |
| **PDF Automático** | Constancia instantánea |
| **Importación Excel** | Carga masiva rápida |
| **Tipos de Entrega** | Trazabilidad completa |
| **Vida Útil** | Control de vencimientos |
| **Filtros Avanzados** | Búsqueda rápida |
| **Móvil-friendly** | Entrega en campo desde tablet |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Alertas de Vencimiento**
   - Email cuando EPP está por vencer
   - Dashboard de vencimientos
   - Programación de renovaciones

2. **Códigos QR**
   - QR por empleado
   - Escaneo rápido para entrega
   - Trazabilidad instantánea

3. **Stock de EPP**
   - Control de inventario
   - Alertas de stock bajo
   - Solicitud de compra automática

4. **Fotos de EPP**
   - Foto del item en catálogo
   - Foto de entrega
   - Evidencia visual

5. **Devoluciones**
   - Registro de devoluciones
   - EPP en mal estado
   - Reutilización

6. **Estadísticas Avanzadas**
   - Dashboard de entregas
   - Costos por empleado
   - Tendencias de uso

7. **Integración con Nómina**
   - Descuento por pérdida
   - Costo por empleado
   - Reportes contables

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CONTANCIAS_DE_VISITAS.MD](./CONTANCIAS_DE_VISITAS.MD) - Constancias de visitas
- [CONTRATO_ANUAL.MD](./CONTRATO_ANUAL.MD) - Contrato anual

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de entrega de EPP
- ✅ Catálogo de EPP con vida útil
- ✅ Gestión de empleados
- ✅ Importación masiva desde Excel
- ✅ Wizard de entrega de 3 pasos
- ✅ 3 tipos de entrega (inicial/reposición/renovación)
- ✅ Firma digital del empleado
- ✅ Generación automática de PDFs
- ✅ Historial completo de entregas
- ✅ Filtros por empresa y sede
- ✅ Permisos por rol
- ✅ Integración con constancias de visita

---

*EP-Back V2.0.0.0 | Sistema de Entrega de EPP con Wizard y Firma Digital | Diciembre 2024*
