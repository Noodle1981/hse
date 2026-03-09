# 🧩 Componentes Livewire - EP-Back V2.0.0.0

> **Estado:** Catálogo Completo de Componentes ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 📋 Índice de Componentes

### Por Módulo

1. [Checklists Dinámicos](#1-checklists-dinámicos-8-componentes)
2. [Capacitaciones](#2-capacitaciones-3-componentes)
3. [Constancias de Visita](#3-constancias-de-visita-3-componentes)
4. [Empresas y Sedes](#4-empresas-y-sedes-3-componentes)
5. [Hallazgos](#5-hallazgos-4-componentes)
6. [Dashboards](#6-dashboards-3-componentes)
7. [Categorías](#7-categorías-2-componentes)
8. [Usuarios](#8-usuarios-1-componente)

**Total:** 27 componentes Livewire

---

## 1. Checklists Dinámicos (8 componentes)

### ChecklistIndex
- **Ruta:** `app/Livewire/Checklists/ChecklistIndex.php`
- **Función:** Listado de checklists del catálogo
- **Características:**
  - Búsqueda por título
  - Filtros por categoría y subcategoría
  - Paginación
  - Duplicación de checklists
  - Vista de tarjetas/tabla
- **Permisos:** Admin + Técnico

### ChecklistBuilder
- **Ruta:** `app/Livewire/Checklists/ChecklistBuilder.php`
- **Función:** Crear/editar checklists
- **Características:**
  - CRUD completo
  - Gestión de secciones
  - Ordenamiento drag & drop
  - Validaciones completas
- **Permisos:** Solo Admin

### AssignmentManager
- **Ruta:** `app/Livewire/Checklists/AssignmentManager.php`
- **Función:** Gestionar asignaciones de checklists
- **Características:**
  - Asignar checklist a técnico y sede
  - Filtros por estado
  - Vista de asignaciones activas
  - Estadísticas de cumplimiento
- **Permisos:** Solo Admin

### InspectionWizard
- **Ruta:** `app/Livewire/Checklists/InspectionWizard.php`
- **Función:** Wizard para completar inspecciones
- **Características:**
  - Navegación paso a paso por secciones
  - Respuestas con evidencia fotográfica
  - Observaciones por pregunta
  - Firma dual (técnico + responsable)
  - Guardado de progreso
- **Permisos:** Admin + Técnico

### InspectionHistory
- **Ruta:** `app/Livewire/Checklists/InspectionHistory.php`
- **Función:** Historial de inspecciones completadas
- **Características:**
  - Búsqueda y filtros
  - Ver detalles de inspección
  - Descargar PDF
  - Filtrado por técnico (admin)
- **Permisos:** Admin + Técnico

### CategoryManager
- **Ruta:** `app/Livewire/Checklists/CategoryManager.php`
- **Función:** Gestión de categorías
- **Características:**
  - CRUD de categorías
  - Iconos y colores personalizados
  - Ordenamiento
- **Permisos:** Solo Admin

### SubcategoryManager
- **Ruta:** `app/Livewire/Checklists/SubcategoryManager.php`
- **Función:** Gestión de subcategorías
- **Características:**
  - CRUD de subcategorías
  - Vinculación con categorías
  - Ordenamiento
- **Permisos:** Solo Admin

### SectionBuilder
- **Ruta:** `app/Livewire/Checklists/SectionBuilder.php`
- **Función:** Constructor de secciones dentro de checklist
- **Características:**
  - Agregar/editar secciones
  - Gestión de preguntas
  - Tipos de respuesta
  - Reordenamiento
- **Permisos:** Solo Admin

---

## 2. Capacitaciones (3 componentes)

### TrainingIndex
- **Ruta:** `app/Livewire/Trainings/TrainingIndex.php`
- **Función:** Listado de capacitaciones
- **Características:**
  - Búsqueda
  - Filtros por empresa, sede, tipo
  - Eliminación con validación
- **Permisos:** Admin + Técnico

### TrainingBuilder
- **Ruta:** `app/Livewire/Trainings/TrainingBuilder.php`
- **Función:** Crear/editar capacitaciones
- **Características:**
  - CRUD completo
  - Tipos: Presencial/Virtual
  - Asignación de empresa y sede
  - Gestión de instructor
- **Permisos:** Solo Admin

### AttendanceTracker
- **Ruta:** `app/Livewire/Trainings/AttendanceTracker.php`
- **Función:** Registro de asistencias
- **Características:**
  - Agregar asistentes
  - Captura de firma digital
  - Validación de DNI
  - Prevención de duplicados
- **Permisos:** Admin + Técnico

---

## 3. Constancias de Visita (3 componentes)

### VisitCertificateManager
- **Ruta:** `app/Livewire/Visits/VisitCertificateManager.php`
- **Función:** Gestión de constancias (Admin)
- **Características:**
  - Listado completo
  - Filtros avanzados
  - Búsqueda por empleado
  - Exportar PDF
- **Permisos:** Solo Admin

### VisitCertificateWizard
- **Ruta:** `app/Livewire/Visits/VisitCertificateWizard.php`
- **Función:** Wizard de 4 pasos para crear constancia
- **Características:**
  - Paso 1: Datos de visita
  - Paso 2: Observaciones
  - Paso 3: Vincular actividades
  - Paso 4: Firmas duales
  - Sistema de token para firma remota
- **Permisos:** Admin + Técnico

### VisitCertificateHistory
- **Ruta:** `app/Livewire/Visits/VisitCertificateHistory.php`
- **Función:** Historial personal de técnico
- **Características:**
  - Solo constancias propias
  - Filtros por fecha
  - Descargar PDF
- **Permisos:** Admin + Técnico

---

## 4. Empresas y Sedes (3 componentes)

### CompanyIndex
- **Ruta:** `app/Livewire/Companies/CompanyIndex.php`
- **Función:** Listado de empresas
- **Características:**
  - Búsqueda por nombre/razón social/CUIT
  - Contador de sedes
  - Eliminación con validación
- **Permisos:** Solo Admin

### CompanyBuilder
- **Ruta:** `app/Livewire/Companies/CompanyBuilder.php`
- **Función:** Crear/editar empresas
- **Características:**
  - CRUD completo
  - Validación de CUIT único
  - Navegación a gestión de sedes
- **Permisos:** Solo Admin

### LocationManager
- **Ruta:** `app/Livewire/Companies/LocationManager.php`
- **Función:** Gestión de sedes de una empresa
- **Características:**
  - CRUD de sedes
  - Asignación de técnicos (M:M)
  - Modal de gestión de usuarios
  - Hasta 3 responsables por sede
- **Permisos:** Solo Admin

---

## 5. Hallazgos (4 componentes)

### HallazgoManager
- **Ruta:** `app/Livewire/Hallazgos/HallazgoManager.php`
- **Función:** Gestión completa de hallazgos
- **Características:**
  - Listado con filtros avanzados
  - KPIs (total, pendientes, críticos, vencidos)
  - Búsqueda
  - Ordenamiento
  - Eliminación
- **Permisos:** Solo Admin

### HallazgoWizard
- **Ruta:** `app/Livewire/Hallazgos/HallazgoWizard.php`
- **Función:** Wizard de 4 pasos para crear hallazgo
- **Características:**
  - Paso 1: Información básica
  - Paso 2: Clasificación
  - Paso 3: Origen (opcional)
  - Paso 4: Riesgo (opcional)
- **Permisos:** Admin + Técnico

### HallazgoDetail
- **Ruta:** `app/Livewire/Hallazgos/HallazgoDetail.php`
- **Función:** Ver detalle y seguimiento
- **Características:**
  - Detalle completo
  - Agregar seguimiento
  - Subir fotos
  - Registrar incidente
  - Timeline de cambios
- **Permisos:** Admin + Técnico

### MisHallazgos
- **Ruta:** `app/Livewire/Hallazgos/MisHallazgos.php`
- **Función:** Hallazgos del técnico
- **Características:**
  - Solo hallazgos propios
  - Filtros personales
  - Estadísticas personales
- **Permisos:** Técnico

## 6. Dashboards (3 componentes)

### AdminDashboard
- **Ruta:** `app/Livewire/Dashboards/AdminDashboard.php`
- **Función:** Dashboard para administrador
- **Características:**
  - KPIs globales
  - Gráficos de todos los módulos
  - Filtros por sede
  - Acciones rápidas
- **Permisos:** Solo Admin

### AnnualContractDashboard
- **Ruta:** `app/Livewire/Contracts/AnnualContractDashboard.php`
- **Función:** Dashboard de plan anual
- **Características:**
  - Matriz de planificación mensual
  - Modo edición
  - Seguimiento de cumplimiento
  - Estadísticas de frecuencias
- **Permisos:** Admin + Técnico

---

## 7. Categorías (2 componentes)

### CategoryManager
- **Ruta:** `app/Livewire/Categories/CategoryManager.php`
- **Función:** Gestión de categorías
- **Características:**
  - CRUD de categorías
  - Iconos personalizados
  - Colores personalizados
  - Ordenamiento drag & drop
- **Permisos:** Solo Admin

### SubcategoryManager
- **Ruta:** `app/Livewire/Categories/SubcategoryManager.php`
- **Función:** Gestión de subcategorías
- **Características:**
  - CRUD de subcategorías
  - Vinculación con categorías
  - Ordenamiento
- **Permisos:** Solo Admin

---

## 8. Usuarios (1 componente)

### UserManager
- **Ruta:** `app/Livewire/Users/UserManager.php`
- **Función:** Gestión de usuarios
- **Características:**
  - CRUD de usuarios
  - Asignación de roles
  - Asignación a sedes
  - Cambio de estado
- **Permisos:** Solo Admin

| Managers/Index (CRUD) | 12 |
| Wizards (Multi-step) | 3 |
| Builders (Constructores) | 4 |
| Dashboards | 3 |
| Historiales | 3 |
| Importadores | 1 |
| Trackers | 1 |
| **Total** | **27** |

### Por Complejidad

| Complejidad | Cantidad |
|-------------|----------|
| Alta (Wizards, Managers complejos) | 8 |
| Media (CRUD estándar) | 12 |
| Baja (Vistas simples) | 7 |

### Por Permisos

| Permisos | Cantidad |
|----------|----------|
| Solo Admin | 15 |
| Admin + Técnico | 12 |
| Solo Técnico | 0 |

---

## 🎯 Patrones de Diseño

### Patrón Manager
**Componentes:** 12
- Listado con paginación
- Búsqueda y filtros
- CRUD completo
- Validaciones

**Ejemplo:**
```php
class HallazgoManager extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filters = [];
    
    public function render()
    {
        return view('livewire.hallazgos.manager', [
            'items' => $this->getFilteredItems(),
        ]);
    }
}
```

### Patrón Wizard
**Componentes:** 3
- Navegación paso a paso
- Validación por paso
- Guardado de progreso
- Resumen final

**Ejemplo:**
```php
class InspectionWizard extends Component
{
    public $currentStep = 1;
    public $totalSteps = 4;
    
    public function nextStep() { /*...*/ }
    public function previousStep() { /*...*/ }
    public function save() { /*...*/ }
}
```

### Patrón Builder
**Componentes:** 4
- Construcción incremental
- Drag & drop
- Preview en tiempo real
- Validaciones complejas

**Ejemplo:**
```php
class ChecklistBuilder extends Component
{
    public $sections = [];
    
    public function addSection() { /*...*/ }
    public function updateOrder($order) { /*...*/ }
    public function save() { /*...*/ }
}
```

---

## 🔗 Comunicación Entre Componentes

### Events (Livewire)

```php
// Disparar evento
$this->dispatch('item-updated', $id);

// Escuchar evento
#[On('item-updated')]
public function handleUpdate($id) { /*...*/ }
```

### Props (Parent → Child)

```blade
<livewire:child-component :item="$item" />
```

### Query String (State Persistence)

```php
protected $queryString = [
    'search' => ['except' => ''],
    'filter' => ['except' => ''],
];
```

---

## 💡 Buenas Prácticas Implementadas

### 1. Separación de Responsabilidades
✅ Un componente = Una responsabilidad  
✅ Managers para listados  
✅ Wizards para procesos complejos  
✅ Builders para construcción  

### 2. Reutilización
✅ Componentes modulares  
✅ Traits compartidos (WithPagination, WithFileUploads)  
✅ Vistas parciales reutilizables  

### 3. Performance
✅ Lazy loading de datos  
✅ Paginación en todos los listados  
✅ Optimización de queries (eager loading)  

### 4. UX
✅ Validaciones en tiempo real  
✅ Feedback visual  
✅ Loading states  
✅ Confirmaciones para acciones destructivas  

---

## 🔜 Próximos Componentes (Roadmap)

### Corto Plazo
- [ ] NotificationCenter (Centro de notificaciones)
- [ ] ReportBuilder (Constructor de reportes)
- [ ] DashboardCustomizer (Personalización de dashboard)

### Medio Plazo
- [ ] CalendarView (Vista de calendario)
- [ ] MapView (Vista de mapa con geolocalización)
- [ ] AnalyticsDashboard (Dashboard de analíticas)

### Largo Plazo
- [ ] WorkflowBuilder (Constructor de flujos)
- [ ] PermissionManager (Gestión de permisos granular)
- [ ] ApiKeyManager (Gestión de API keys)

---

*EP-Back V2.0.0.0 | Catálogo de 27 Componentes Livewire | Diciembre 2024*
