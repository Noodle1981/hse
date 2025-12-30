# 🏢 Sistema de Empresas, Sedes y Empleados - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Gestión Jerárquica ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema de gestión jerárquica de empresas, sedes y asignación de usuarios técnicos. Permite organizar la estructura de clientes, sus ubicaciones físicas, y asignar técnicos a sedes específicas para control de acceso y trazabilidad.

### URL de Acceso
```
http://localhost:8000/companies                    # Listado de empresas (Admin)
http://localhost:8000/companies/create             # Crear empresa (Admin)
http://localhost:8000/companies/{id}/edit          # Editar empresa (Admin)
http://localhost:8000/companies/{id}/locations     # Gestionar sedes (Admin)
http://localhost:8000/epp/employees                # Gestionar empleados (Admin+Técnico)
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (3)               │
│  CompanyIndex | CompanyBuilder |               │
│  LocationManager                               │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (3)               │
│  Empresa | Sede | Employee                     │
├────────────────────────────────────────────────┤
│              PIVOT TABLE                       │
│  sede_users (Many-to-Many)                     │
├────────────────────────────────────────────────┤
│         ESTRUCTURA JERÁRQUICA                  │
│  Empresa → Sedes → Empleados                   │
│  Sede ↔ Users (Técnicos)                       │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Companies/
├── CompanyIndex.php            # Listado de empresas
├── CompanyBuilder.php          # CRUD empresas
└── LocationManager.php         # CRUD sedes + asignación usuarios
```

### Modelos

```
app/Models/
├── Empresa.php                 # Empresa/Cliente
├── Sede.php                    # Sede/Sucursal
└── Employee.php                # Empleado
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/companies` | CompanyIndex | Listado empresas | Admin |
| `/companies/create` | CompanyBuilder | Crear empresa | Admin |
| `/companies/{id}/edit` | CompanyBuilder | Editar empresa | Admin |
| `/companies/{empresa}/locations` | LocationManager | Gestionar sedes | Admin |

---

## 📊 Base de Datos

### Tabla: empresas

```sql
empresas
├── id
├── nombre                      -- Nombre comercial
├── razon_social                -- Razón social
├── cuit                        -- CUIT (único)
├── created_at
└── updated_at
```

### Tabla: sedes

```sql
sedes
├── id
├── empresa_id (FK → empresas)
├── nombre_sede                 -- Nombre de la sede
├── direccion                   -- Dirección física
├── correo                      -- Email de contacto
├── telefono                    -- Teléfono
├── tipo_sede                   -- Tipo (Obra, Oficina, Planta, etc.)
├── localidad                   -- Localidad/Ciudad
├── responsable_1_nombre        -- Responsable principal
├── responsable_1_cargo         -- Cargo del responsable
├── responsable_1_email         -- Email del responsable
├── responsable_2_nombre        -- Responsable secundario
├── responsable_2_cargo
├── responsable_2_email
├── responsable_3_nombre        -- Responsable terciario
├── responsable_3_cargo
├── responsable_3_email
├── created_at
└── updated_at
```

### Tabla: sede_users (Pivot)

```sql
sede_users
├── id
├── sede_id (FK → sedes)
├── user_id (FK → users)
├── created_at
└── updated_at
```

**Relación:** Many-to-Many
- Una sede puede tener múltiples técnicos asignados
- Un técnico puede estar asignado a múltiples sedes

---

## ✨ Características Principales

### 1. Gestión de Empresas

**Datos de la empresa:**
- Nombre comercial
- Razón social
- CUIT (validación de unicidad)

**Funcionalidades:**
- CRUD completo
- Búsqueda por nombre, razón social o CUIT
- Contador de sedes asociadas
- Validación: No se puede eliminar si tiene sedes

### 2. Gestión de Sedes

**Datos de la sede:**
- Nombre de la sede
- Dirección
- Tipo de sede
- Localidad
- Contacto (email, teléfono)
- Hasta 3 responsables con cargo y email

**Funcionalidades:**
- CRUD completo dentro de la empresa
- Edición inline
- Asignación de técnicos
- Contador de usuarios asignados

### 3. Asignación de Técnicos a Sedes

**Características:**
- Modal de gestión de usuarios
- Lista de técnicos disponibles
- Toggle para asignar/desasignar
- Actualización en tiempo real
- Contador de técnicos por sede

**Beneficios:**
- Control de acceso por sede
- Filtrado automático en otros módulos
- Trazabilidad de responsables

### 4. Estructura Jerárquica

```
Empresa (Cliente)
  └── Sede 1 (Obra Centro)
      ├── Técnico A
      ├── Técnico B
      └── Empleados
          ├── Empleado 1
          ├── Empleado 2
          └── ...
  └── Sede 2 (Oficina Norte)
      ├── Técnico C
      └── Empleados
          └── ...
```

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Nuevo Cliente con Múltiples Sedes

```
1. Admin crea empresa
   └─ /companies/create
   ├─ Nombre: "Constructora ABC S.A."
   ├─ Razón Social: "Constructora ABC Sociedad Anónima"
   └─ CUIT: 30-12345678-9

2. Admin gestiona sedes
   └─ /companies/1/locations

3. Crea Sede 1: Obra Centro
   ├─ Nombre: "Obra Centro"
   ├─ Tipo: "Obra en construcción"
   ├─ Dirección: "Av. Principal 123"
   ├─ Localidad: "Buenos Aires"
   ├─ Responsable 1:
   │   ├─ Nombre: "Juan Pérez"
   │   ├─ Cargo: "Jefe de Obra"
   │   └─ Email: juan.perez@abc.com
   └─ Asigna técnicos:
       ├─ ✅ Técnico A
       └─ ✅ Técnico B

4. Crea Sede 2: Oficina Norte
   ├─ Nombre: "Oficina Norte"
   ├─ Tipo: "Oficina administrativa"
   ├─ Dirección: "Calle Secundaria 456"
   └─ Asigna técnicos:
       └─ ✅ Técnico C

5. Resultado:
   └─ Empresa con 2 sedes
   └─ 3 técnicos asignados
   └─ Listo para operaciones
```

### Escenario 2: Asignación de Técnico a Nueva Sede

```
1. Admin accede a gestión de sedes
   └─ /companies/1/locations

2. Selecciona "Obra Centro"
   └─ Click en "Gestionar Usuarios"

3. Modal muestra:
   ├─ Técnicos asignados:
   │   ├─ ✅ Técnico A
   │   └─ ✅ Técnico B
   └─ Técnicos disponibles:
       ├─ ☐ Técnico C
       ├─ ☐ Técnico D
       └─ ☐ Técnico E

4. Asigna Técnico D
   └─ Click en checkbox

5. Sistema actualiza
   ├─ Técnico D ahora asignado a Obra Centro
   ├─ Contador actualizado: 3 usuarios
   └─ Técnico D ve Obra Centro en sus filtros
```

### Escenario 3: Reorganización de Sedes

```
1. Empresa cierra Sede 2
   └─ Admin elimina "Oficina Norte"

2. Sistema verifica dependencias
   ├─ ¿Tiene inspecciones? No
   ├─ ¿Tiene capacitaciones? No
   ├─ ¿Tiene empleados? Sí (5 empleados)
   └─ Alerta: "Tiene 5 empleados asociados"

3. Admin reasigna empleados
   └─ Mueve empleados a Sede 1

4. Elimina sede
   └─ Técnico C queda sin asignación a esa sede

5. Resultado:
   └─ Empresa ahora con 1 sede
   └─ Todos los empleados reasignados
```

---

## 🎨 Interfaz de Usuario

### CompanyIndex (Listado de Empresas)

```
┌─────────────────────────────────────┐
│ [Buscar...] [+ Nueva Empresa]       │
├─────────────────────────────────────┤
│ Nombre       │ CUIT │ Sedes │ Acciones │
├──────────────┼──────┼───────┼──────────┤
│ Constr. ABC  │ 30-  │ 2     │ ✏️ 📍 🗑️  │
│ Minera XYZ   │ 30-  │ 5     │ ✏️ 📍 🗑️  │
│ Bodega 123   │ 30-  │ 1     │ ✏️ 📍 🗑️  │
└─────────────────────────────────────┘

Leyenda:
✏️ = Editar
📍 = Gestionar Sedes
🗑️ = Eliminar
```

### CompanyBuilder (Crear/Editar Empresa)

```
┌─────────────────────────────────────┐
│ Nueva Empresa                       │
├─────────────────────────────────────┤
│ Nombre: *                           │
│ [_________________________________] │
│                                     │
│ Razón Social: *                     │
│ [_________________________________] │
│                                     │
│ CUIT: * (formato: XX-XXXXXXXX-X)    │
│ [_________________________________] │
├─────────────────────────────────────┤
│ [Cancelar] [Guardar Empresa]        │
└─────────────────────────────────────┘
```

### LocationManager (Gestionar Sedes)

**Vista Principal:**
```
┌─────────────────────────────────────┐
│ Sedes de: Constructora ABC S.A.     │
│ [+ Nueva Sede]                      │
├─────────────────────────────────────┤
│ Obra Centro                         │
│ 📍 Av. Principal 123, Buenos Aires  │
│ 👥 3 técnicos asignados             │
│ [Editar] [Gestionar Usuarios] [🗑️]  │
├─────────────────────────────────────┤
│ Oficina Norte                       │
│ 📍 Calle Secundaria 456, Rosario    │
│ 👥 1 técnico asignado               │
│ [Editar] [Gestionar Usuarios] [🗑️]  │
└─────────────────────────────────────┘
```

**Formulario Nueva Sede:**
```
┌─────────────────────────────────────┐
│ Nueva Sede                          │
├─────────────────────────────────────┤
│ Nombre de la Sede: *                │
│ [_________________________________] │
│                                     │
│ Tipo de Sede:                       │
│ [_________________________________] │
│                                     │
│ Dirección:                          │
│ [_________________________________] │
│                                     │
│ Localidad:                          │
│ [_________________________________] │
│                                     │
│ Teléfono:                           │
│ [_________________________________] │
│                                     │
│ Correo:                             │
│ [_________________________________] │
│                                     │
│ Responsable Principal:              │
│ Nombre: [_________________________] │
│ Cargo: [__________________________] │
│ Email: [__________________________] │
├─────────────────────────────────────┤
│ [Cancelar] [Guardar Sede]           │
└─────────────────────────────────────┘
```

**Modal Gestionar Usuarios:**
```
┌─────────────────────────────────────┐
│ Asignar Técnicos a: Obra Centro     │
├─────────────────────────────────────┤
│ ✅ Técnico A (Juan Pérez)           │
│ ✅ Técnico B (María López)          │
│ ✅ Técnico D (Carlos Ruiz)          │
│ ☐ Técnico C (Ana García)            │
│ ☐ Técnico E (Luis Martínez)         │
├─────────────────────────────────────┤
│ [Cerrar]                            │
└─────────────────────────────────────┘
```

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver empresas | ✅ | ❌ |
| CRUD empresas | ✅ | ❌ |
| Ver sedes | ✅ | ✅ (solo asignadas) |
| CRUD sedes | ✅ | ❌ |
| Asignar técnicos | ✅ | ❌ |
| Ver empleados | ✅ | ✅ (solo de sus sedes) |

### Validaciones

**Empresas:**
- Nombre: Requerido, máximo 255 caracteres
- Razón social: Requerida, máximo 255 caracteres
- CUIT: Requerido, único, máximo 13 caracteres
- Eliminación: Solo si no tiene sedes

**Sedes:**
- Nombre: Requerido, máximo 255 caracteres
- Empresa: Requerida (relación)
- Otros campos: Opcionales

---

## 🔄 Integración con Otros Módulos

### 1. Checklists/Inspecciones

```php
// Asignación de checklist a sede
$assignment->sede_id = $sede->id;

// Técnico solo ve asignaciones de sus sedes
$assignments = ChecklistAssignment::whereHas('sede', function($q) {
    $q->whereHas('users', fn($sq) => $sq->where('users.id', auth()->id()));
})->get();
```

### 2. Capacitaciones

```php
// Capacitación en sede específica
$training->sede_id = $sede->id;

// Filtrar por sedes del técnico
$trainings = Training::whereIn('sede_id', auth()->user()->sedes->pluck('id'));
```

### 3. EPP

```php
// Empleados por sede
$employees = Employee::where('sede_id', $sede->id)->get();

// Entregas en sede
$deliveries = EppDelivery::whereHas('employee', fn($q) => 
    $q->where('sede_id', $sede->id)
);
```

### 4. Constancias de Visita

```php
// Constancia en sede
$constancia->sede_id = $sede->id;

// Técnico solo ve constancias de sus sedes
$constancias = GeneradorConstancia::whereIn('sede_id', 
    auth()->user()->sedes->pluck('id')
);
```

### 5. Matriz de Riesgo

```php
// Matriz de riesgo por sede
$matrix->sede_id = $sede->id;

// Plan anual por sede
$plan = AnnualPlan::where('sede_id', $sede->id)->get();
```

---

## 📊 Relaciones del Modelo Sede

```php
// Sede tiene muchas relaciones
$sede->empresa;              // Empresa padre
$sede->users;                // Técnicos asignados (M:M)
$sede->employees;            // Empleados
$sede->inspections;          // Inspecciones
$sede->trainings;            // Capacitaciones
$sede->constancias;          // Constancias de visita
$sede->assignments;          // Asignaciones de checklists
$sede->riskMatrices;         // Matrices de riesgo
$sede->annualPlans;          // Planes anuales
```

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Estructura Jerárquica** | Organización clara y escalable |
| **Asignación Flexible** | Técnicos en múltiples sedes |
| **Control de Acceso** | Filtrado automático por sede |
| **Trazabilidad** | Responsables documentados |
| **Validaciones** | Previene eliminaciones incorrectas |
| **Integración Total** | Todos los módulos usan sedes |
| **Multi-responsable** | Hasta 3 responsables por sede |

---

## 📈 Casos de Uso Comunes

### Caso 1: Empresa Constructora

```
Constructora ABC S.A.
├── Obra Centro (En construcción)
│   ├── Técnico A (Seguridad)
│   ├── Técnico B (Higiene)
│   └── 50 empleados
├── Obra Norte (En construcción)
│   ├── Técnico A (Seguridad)
│   └── 30 empleados
└── Oficina Central (Administrativa)
    ├── Técnico C (Auditorías)
    └── 10 empleados
```

### Caso 2: Empresa Minera

```
Minera XYZ S.A.
├── Mina Principal
│   ├── Técnico D (Turno mañana)
│   ├── Técnico E (Turno tarde)
│   └── 200 empleados
├── Planta de Procesamiento
│   ├── Técnico F
│   └── 80 empleados
└── Campamento
    ├── Técnico G
    └── 50 empleados
```

### Caso 3: Bodega

```
Bodega 123 S.A.
├── Viñedo Sur
│   ├── Técnico H
│   └── 40 empleados
├── Planta Embotelladora
│   ├── Técnico I
│   └── 60 empleados
└── Centro de Distribución
    ├── Técnico J
    └── 25 empleados
```

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Geolocalización**
   - Coordenadas GPS de sedes
   - Mapa interactivo
   - Distancias entre sedes

2. **Horarios de Operación**
   - Horarios por sede
   - Turnos de trabajo
   - Días no laborables

3. **Documentos por Sede**
   - Habilitaciones
   - Certificados
   - Planos
   - Vencimientos

4. **Estadísticas por Sede**
   - Dashboard de actividad
   - Comparativas entre sedes
   - Ranking de cumplimiento

5. **Importación Masiva**
   - Importar empresas desde Excel
   - Importar sedes desde Excel
   - Plantillas predefinidas

6. **Jerarquía Extendida**
   - Áreas dentro de sedes
   - Sectores
   - Niveles

7. **Contactos Adicionales**
   - Más de 3 responsables
   - Contactos de emergencia
   - Directorio completo

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [ENTREGA_EPP.MD](./ENTREGA_EPP.MD) - Sistema de EPP
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de empresas y sedes
- ✅ CRUD de empresas con validación CUIT
- ✅ CRUD de sedes con múltiples responsables
- ✅ Asignación Many-to-Many de técnicos a sedes
- ✅ Modal de gestión de usuarios
- ✅ Contador de sedes y usuarios
- ✅ Validaciones de eliminación
- ✅ Integración con todos los módulos
- ✅ Estructura jerárquica completa
- ✅ Filtrado automático por sedes asignadas

---

*EP-Back V2.0.0.0 | Sistema de Empresas, Sedes y Asignación de Técnicos | Diciembre 2024*
