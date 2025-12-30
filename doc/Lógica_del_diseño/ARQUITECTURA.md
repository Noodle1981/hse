# 🏗️ Arquitectura del Sistema - EP-Back

**Versión:** V2.0.0.0  
**Stack:** TALL (Tailwind, Alpine, Livewire, Laravel 12)  
**Última actualización:** 30 Diciembre 2024

---

## 📊 Visión General

```
┌─────────────────────────────────────────────────────────┐
│                    EP-Back Sistema                       │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  ┌─────────────────┐       ┌──────────────────────┐    │
│  │  TALL Stack     │       │  API REST            │    │
│  │  (Frontend Web) │       │  (Integraciones)     │    │
│  └─────────────────┘       └──────────────────────┘    │
│         │                            │                   │
│         │                            │                   │
│         ▼                            ▼                   │
│  ┌──────────────────────────────────────────────┐       │
│  │         Laravel 12 Backend                   │       │
│  │  - Eloquent ORM                              │       │
│  │  - Sanctum (API Tokens)                      │       │
│  │  - Session Auth (Web)                        │       │
│  │  - Livewire 3.7 (Reactivo)                   │       │
│  │  - DomPDF + Spatie PDF (Reportes)            │       │
│  │  - Maatwebsite Excel (Importación)           │       │
│  └──────────────────────────────────────────────┘       │
│                      │                                   │
│                      ▼                                   │
│              ┌───────────────┐                           │
│              │  SQLite DB    │                           │
│              └───────────────┘                           │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 TALL Stack (Frontend Principal)

### Componentes

| Tecnología | Versión | Uso | Ubicación |
|------------|---------|-----|-----------|
| **Tailwind CSS** | 3.x | Estilos utility-first | CDN en layouts |
| **Alpine.js** | 3.x | Interactividad JS | Incluido en Livewire |
| **Livewire** | 3.7 | Componentes reactivos | `app/Livewire/` |
| **Laravel** | 12.0 | Backend framework | Core |

### Estructura de Componentes Livewire

```
app/Livewire/
├── Admin/
│   └── AdminDashboard.php           # Dashboard administrativo
├── Auth/
│   └── Login.php                    # Login web (sesiones)
├── Assignments/
│   └── AssignmentManager.php        # Gestión de asignaciones
├── Categories/
│   ├── CategoryIndex.php            # Listado de categorías
│   ├── CategoryBuilder.php          # CRUD categorías
│   └── SubcategoryManager.php       # Gestión de subcategorías
├── Checklists/
│   ├── Dashboard.php                # Estadísticas de checklists
│   ├── ChecklistIndex.php           # Listado de checklists
│   ├── ChecklistBuilder.php         # Constructor de checklists
│   ├── ChecklistFiller.php          # Llenar checklist (legacy)
│   ├── InspectionHistory.php        # Historial de inspecciones
│   └── InspectionShow.php           # Detalle de inspección
├── Companies/
│   ├── CompanyIndex.php             # Listado de empresas
│   ├── CompanyBuilder.php           # CRUD empresas
│   └── LocationManager.php          # Gestión de sedes
├── Epp/
│   ├── EppCatalog.php               # Catálogo de EPP
│   ├── EmployeeManager.php          # Gestión de empleados
│   ├── EmployeeImporter.php         # Importador Excel empleados
│   ├── EppDeliveryWizard.php        # Wizard entrega EPP
│   └── EppHistory.php               # Historial de entregas
├── Hallazgos/
│   ├── HallazgoManager.php          # Gestión de hallazgos
│   ├── HallazgoWizard.php           # Wizard crear/editar hallazgo
│   ├── HallazgoDetail.php           # Detalle de hallazgo
│   └── MisHallazgos.php             # Hallazgos del técnico
├── Incidentes/
│   └── IncidenteManager.php         # Gestión de incidentes
├── Risks/
│   ├── RiskMatrixManager.php        # Gestión de matrices de riesgo
│   ├── RiskMatrixEditor.php         # Editor de matriz
│   ├── RiskEvaluator.php            # Evaluador de riesgos
│   ├── RiskDashboard.php            # Dashboard de riesgos
│   └── AnnualContractDashboard.php  # Dashboard contrato anual
├── Technician/
│   ├── TechnicianDashboard.php      # Dashboard técnico
│   ├── MyAssignments.php            # Mis asignaciones
│   └── InspectionWizard.php         # Wizard llenar inspección
├── Trainings/
│   ├── TrainingIndex.php            # Listado de capacitaciones
│   ├── TrainingBuilder.php          # CRUD capacitaciones
│   └── AttendanceTracker.php        # Registro de asistencia
├── Users/
│   ├── UserManager.php              # Gestión de usuarios
│   └── UserBuilder.php              # CRUD usuarios
└── Visits/
    ├── VisitCertificateManager.php  # Gestión de constancias
    ├── VisitCertificateWizard.php   # Wizard crear constancia
    └── VisitCertificateHistory.php  # Historial de constancias
```

---

## 🔌 API REST (Integraciones Externas)

### Propósito
- Conectar aplicaciones externas (móviles, third-party)
- Mantener compatibilidad legacy (Angular)
- Escalabilidad futura

### Controladores API

```
app/Http/Controllers/
├── Auth/
│   └── UserController.php              # CRUD usuarios + login API
├── Hallazgos/
│   └── HallazgoPDFController.php       # PDF hallazgos
├── Risks/
│   ├── RiskMatrixReportController.php  # PDF matriz de riesgo
│   └── AnnualContractReportController.php # PDF contrato anual
├── AngularController.php               # Controlador legacy Angular
├── CapacitacionPresencialController.php
├── CapacitacionVirualController.php
├── ChecklistAsignController.php
├── ChecklistOtController.php
├── CorreoContactoController.php
├── CorreoController.php                # Envío de correos
├── EmpresaController.php
├── EppPDFController.php                # PDF entregas EPP
├── GeneradorConstanciaController.php
├── InformeController.php
├── InspectionPDFController.php         # PDF inspecciones
├── ItemController.php
├── PDFController.php
├── RegistroController.php
├── ResponsableChecklistController.php
├── ResponsableConstanciaController.php
├── SedeController.php
├── SedeUserController.php
├── TrainingPDFController.php           # PDF capacitaciones
└── VisitCertificatePDFController.php   # PDF constancias visita
```

### Endpoints Principales

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/login` | POST | Autenticación (token Sanctum) |
| `/api/listar_colaboradores_admin` | GET | Listar usuarios |
| `/api/registro_colaborador_admin` | POST | Crear usuario |
| `/api/editar_colaborador_admin/{id}` | PUT | Editar usuario |
| `/api/eliminar_colaborador_admin/{id}` | DELETE | Eliminar usuario |

---

## 🔐 Sistema de Autenticación Dual

### 1. Web (TALL Stack)

```php
// Login: app/Livewire/Auth/Login.php
Auth::attempt(['email' => $email, 'password' => $password])

// Sessions Laravel
// Middleware: auth
```

**Rutas protegidas:**
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class);
    Route::prefix('checklists')->group(...);
});
```

---

### 2. API (Token-based)

```php
// Login: app/Http/Controllers/Auth/UserController.php
$user->createToken($email)->plainTextToken

// Laravel Sanctum
// Middleware: auth:sanctum
```

**Rutas API:**
```php
Route::post('/api/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/user', ...);
});
```

---

## 📂 Base de Datos

### Tablas Principales del Sistema

```sql
-- Usuarios y Autenticación
users
├── id
├── email (unique)
├── password
├── rol (Administrador, Técnico)
└── ...

personal_access_tokens (Sanctum)

-- Empresas y Sedes
empresas
├── id
├── nombre
└── ...

sedes
├── id
├── empresa_id (FK)
├── nombre
├── direccion
└── ...

sede_users (pivot)

-- Categorías
categories
├── id
├── name
└── ...

subcategories
├── id
├── category_id (FK)
└── name

-- Checklists Dinámicos
dynamic_checklists
├── id
├── title
├── category_id (FK)
├── subcategory_id (FK)
├── is_active
└── is_draft

dynamic_questions
├── id
├── dynamic_checklist_id (FK)
├── section_id (FK)
├── label
├── type (text, radio, signature, etc)
└── options (JSON)

checklist_sections
├── id
├── dynamic_checklist_id (FK)
└── title

dynamic_inspections
├── id
├── dynamic_checklist_id (FK)
├── user_id (FK, nullable)
├── assignment_id (FK)
├── completed_at
├── status
├── firma_tecnico
├── firma_responsable
├── nombre_responsable
└── cargo_responsable

dynamic_answers
├── id
├── dynamic_inspection_id (FK)
├── dynamic_question_id (FK)
└── value

checklist_assignments
├── id
├── dynamic_checklist_id (FK)
├── user_id (FK)
├── empresa_id (FK)
├── sede_id (FK)
├── scheduled_date
└── status

-- Capacitaciones
trainings
├── id
├── user_id (FK)
├── title
├── date
└── ...

training_attendances
├── id
├── training_id (FK)
└── ...

capacitacion_presencials
capacitacion_viruals
capacitacion_virual_asistentes

-- EPP (Equipos de Protección Personal)
employees
├── id
├── nombre
├── dni
└── ...

epp_items
├── id
├── nombre
├── descripcion
└── ...

epp_deliveries
├── id
├── employee_id (FK)
├── user_id (FK)
├── fecha_entrega
└── ...

epp_delivery_items (pivot)

-- Matriz de Riesgo
risk_matrices
├── id
├── nombre
├── empresa_id (FK)
└── ...

risks
├── id
├── risk_matrix_id (FK)
├── descripcion
└── ...

risk_evaluations
├── id
├── risk_id (FK)
├── probabilidad
├── severidad
└── nivel_riesgo

risk_controls
├── id
├── risk_id (FK)
├── medida_control
└── ...

annual_plans
├── id
├── empresa_id (FK)
├── year
├── activities (JSON)
└── ...

-- Constancias de Visita
generador_constancias
├── id
├── user_id (FK)
├── empresa_id (FK)
├── sede_id (FK)
├── fecha_visita
├── actividades_realizadas
├── firma_tecnico
└── ...

constancia_actividades (pivot)

-- Hallazgos e Incidentes
hallazgos
├── id
├── user_id (FK)
├── empresa_id (FK)
├── sede_id (FK)
├── titulo
├── descripcion
├── gravedad
├── estado
├── fecha_deteccion
└── ...

hallazgo_seguimientos
├── id
├── hallazgo_id (FK)
├── comentario
└── fecha

hallazgo_fotos
├── id
├── hallazgo_id (FK)
└── ruta_foto

incidentes
├── id
├── user_id (FK)
├── empresa_id (FK)
├── titulo
├── descripcion
├── gravedad
└── ...

-- Legacy
informes
items
registros
```

---

## 🛡️ Seguridad y Roles

### Middleware

```php
// bootstrap/app.php
$middleware->alias([
    'role' => CheckRole::class,
]);
```

### Permisos por Rol

| Módulo | Acción | Administrador | Técnico |
|--------|--------|---------------|---------|
| **Dashboard** | Ver | ✅ | ✅ |
| **Checklists** | Ver listado | ✅ | ❌ |
| | Crear/Editar | ✅ | ❌ |
| | Llenar | ✅ | ✅ |
| | Ver historial | ✅ | ✅ |
| **Asignaciones** | Gestionar | ✅ | ❌ |
| | Ver mis tareas | ✅ | ✅ |
| **Categorías** | CRUD | ✅ | ❌ |
| **Empresas** | CRUD | ✅ | ❌ |
| **Usuarios** | CRUD | ✅ | ❌ |
| **Capacitaciones** | CRUD | ✅ | ❌ |
| | Tomar asistencia | ✅ | ✅ |
| **EPP** | Catálogo | ✅ | ❌ |
| | Entregar | ❌ | ✅ |
| | Ver historial | ✅ | ✅ |
| **Matriz de Riesgo** | Diseñar | ✅ | ❌ |
| | Evaluar | ✅ | ✅ |
| | Dashboard | ✅ | ✅ |
| **Hallazgos** | Gestionar todos | ✅ | ❌ |
| | Crear/Ver propios | ✅ | ✅ |
| **Incidentes** | Gestionar | ✅ | ❌ |
| **Constancias** | Ver todas | ✅ | ❌ |
| | Crear/Ver propias | ✅ | ✅ |

---

## 🎨 Diseño UI

### Layout Principal
```
resources/views/layouts/
├── app.blade.php      # TALL stack (con sidebar)
└── guest.blade.php    # Login (sin sidebar)
```

### Colores Personalizados
```javascript
// Tailwind config
{
  'ep-primary': '#F69239',        // Naranja EP
  'ep-primary-dark': '#E07D1E',   // Naranja oscuro
  'ep-dark': '#1E1E2D',           // Sidebar
  'ep-sidebar': '#1E1E2D',        // Fondo sidebar
}
```

---

## 📡 Módulos del Sistema

### 1. Checklists Dinámicos
- **Componentes:** ChecklistBuilder, ChecklistIndex, InspectionWizard
- **Características:** 
  - Constructor visual de checklists
  - 8 tipos de preguntas
  - Firma digital
  - Generación PDF automática
  - Sistema de asignaciones

### 2. Gestión de Empresas y Sedes
- **Componentes:** CompanyIndex, CompanyBuilder, LocationManager
- **Características:**
  - CRUD empresas
  - Gestión de sedes/sucursales
  - Asignación de usuarios a sedes

### 3. Capacitaciones
- **Componentes:** TrainingIndex, TrainingBuilder, AttendanceTracker
- **Características:**
  - Capacitaciones presenciales y virtuales
  - Registro de asistencia
  - Generación de certificados PDF

### 4. EPP (Equipos de Protección Personal)
- **Componentes:** EppCatalog, EmployeeManager, EppDeliveryWizard
- **Características:**
  - Catálogo de EPP
  - Importación Excel de empleados
  - Wizard de entrega en campo
  - Historial de entregas
  - PDF de constancia

### 5. Matriz de Riesgo
- **Componentes:** RiskMatrixManager, RiskMatrixEditor, RiskEvaluator
- **Características:**
  - Diseño de matrices personalizadas
  - Evaluación de riesgos (probabilidad × severidad)
  - Controles y medidas
  - Dashboard de riesgos
  - Integración con contrato anual

### 6. Hallazgos e Incidentes
- **Componentes:** HallazgoManager, HallazgoWizard, IncidenteManager
- **Características:**
  - Registro de hallazgos con fotos
  - Sistema de seguimiento
  - Clasificación por gravedad
  - Estados de resolución
  - Gestión de incidentes

### 7. Constancias de Visita
- **Componentes:** VisitCertificateManager, VisitCertificateWizard
- **Características:**
  - Registro de visitas técnicas
  - Vinculación con actividades del contrato anual
  - Firma digital
  - Generación PDF

### 8. Usuarios y Roles
- **Componentes:** UserManager, UserBuilder
- **Características:**
  - CRUD usuarios
  - Sistema de roles (Administrador, Técnico)
  - Asignación a empresas/sedes

### 9. Categorías y Subcategorías
- **Componentes:** CategoryIndex, CategoryBuilder, SubcategoryManager
- **Características:**
  - Organización jerárquica
  - Vinculación con checklists

### 10. Dashboards
- **Componentes:** AdminDashboard, TechnicianDashboard
- **Características:**
  - KPIs y métricas
  - Gráficos de actividad
  - Vistas personalizadas por rol

---

## 🚀 Rutas del Sistema

### Web (TALL Stack)

| Ruta | Componente | Auth | Role |
|------|-----------|------|------|
| `/login` | Login | - | - |
| `/dashboard` | Redirect condicional | ✅ | Todos |
| `/admin/dashboard` | AdminDashboard | ✅ | Admin |
| `/technician/dashboard` | TechnicianDashboard | ✅ | Admin+Técnico |
| `/checklists` | ChecklistIndex | ✅ | Admin |
| `/checklists/create` | ChecklistBuilder | ✅ | Admin |
| `/checklists/{id}/edit` | ChecklistBuilder | ✅ | Admin |
| `/checklists/{id}/fill` | InspectionWizard | ✅ | Admin+Técnico |
| `/checklists/my-assignments` | MyAssignments | ✅ | Admin+Técnico |
| `/inspections` | InspectionHistory | ✅ | Todos |
| `/inspections/{id}` | InspectionShow | ✅ | Todos |
| `/categories` | CategoryIndex | ✅ | Admin |
| `/companies` | CompanyIndex | ✅ | Admin |
| `/assignments` | AssignmentManager | ✅ | Admin |
| `/users` | UserManager | ✅ | Admin |
| `/trainings` | TrainingIndex | ✅ | Admin |
| `/trainings/{id}/attendance` | AttendanceTracker | ✅ | Admin+Técnico |
| `/epp/catalog` | EppCatalog | ✅ | Admin |
| `/epp/deliver` | EppDeliveryWizard | ✅ | Técnico |
| `/epp/employees` | EmployeeManager | ✅ | Admin+Técnico |
| `/epp/history` | EppHistory | ✅ | Admin+Técnico |
| `/risk-matrices` | RiskMatrixManager | ✅ | Admin+Técnico |
| `/risk-matrices/{id}/editor` | RiskMatrixEditor | ✅ | Admin |
| `/risk-matrices/{id}/evaluate` | RiskEvaluator | ✅ | Admin+Técnico |
| `/risk-matrices/dashboard-riesgos` | RiskDashboard | ✅ | Admin+Técnico |
| `/risk-matrices/contrato-anual` | AnnualContractDashboard | ✅ | Admin+Técnico |
| `/hallazgos` | HallazgoManager | ✅ | Admin |
| `/hallazgos/crear` | HallazgoWizard | ✅ | Admin+Técnico |
| `/hallazgos/{id}` | HallazgoDetail | ✅ | Admin+Técnico |
| `/mis-hallazgos` | MisHallazgos | ✅ | Admin+Técnico |
| `/incidentes` | IncidenteManager | ✅ | Admin |
| `/visit-certificates` | VisitCertificateManager | ✅ | Admin |
| `/visit-certificates/create` | VisitCertificateWizard | ✅ | Admin+Técnico |
| `/visit-certificates/my-history` | VisitCertificateHistory | ✅ | Admin+Técnico |

### API (Token-based)

| Ruta | Método | Auth |
|------|--------|------|
| `/api/login` | POST | - |
| `/api/listar_colaboradores_admin` | GET | Sanctum |
| `/api/registro_colaborador_admin` | POST | Sanctum |
| `/api/editar_colaborador_admin/{id}` | PUT | Sanctum |
| `/api/eliminar_colaborador_admin/{id}` | DELETE | Sanctum |

---

## 📦 Dependencias

### Backend (Composer)

```json
{
  "laravel/framework": "^12.0",
  "livewire/livewire": "^3.7",
  "laravel/sanctum": "^4.0",
  "barryvdh/laravel-dompdf": "^3.1",
  "spatie/laravel-pdf": "^1.5",
  "maatwebsite/excel": "^3.1",
  "phpoffice/phpspreadsheet": "^1.30",
  "shuchkin/simplexlsx": "^1.1",
  "shuchkin/simplexlsxgen": "^1.5"
}
```

### Frontend
- Tailwind CSS (CDN)
- Alpine.js (vía Livewire)
- signature_pad.js (NPM)

---

## 🔄 Flujos de Trabajo Principales

### 1. Crear y Llenar Checklist

```
1. Admin → /checklists/create
   └─ Crea plantilla con preguntas organizadas por secciones

2. Admin → /assignments
   └─ Asigna checklist a técnico + empresa + sede + fecha

3. Técnico → /checklists/my-assignments
   └─ Ve sus tareas asignadas

4. Técnico → /checklists/{id}/fill
   └─ Completa inspección con wizard paso a paso
   └─ Captura firmas (técnico + responsable)

5. Sistema → Guarda en dynamic_inspections
   └─ Genera PDF automático

6. Usuario → /inspections
   └─ Ve historial completo con filtros
```

### 2. Entrega de EPP

```
1. Admin → /epp/import-employees
   └─ Importa empleados desde Excel

2. Técnico → /epp/deliver
   └─ Wizard de entrega en campo
   └─ Selecciona empleado + items EPP
   └─ Captura firma

3. Sistema → Guarda en epp_deliveries
   └─ Genera PDF de constancia

4. Usuario → /epp/history
   └─ Ve historial de entregas
```

### 3. Evaluación de Riesgos

```
1. Admin → /risk-matrices/{id}/editor
   └─ Diseña matriz personalizada

2. Técnico → /risk-matrices/{id}/evaluate
   └─ Identifica riesgos
   └─ Evalúa probabilidad × severidad
   └─ Define controles

3. Usuario → /risk-matrices/dashboard-riesgos
   └─ Ve dashboard con métricas de riesgo

4. Sistema → Vincula con contrato anual
   └─ /risk-matrices/contrato-anual
```

### 4. Registro de Hallazgos

```
1. Técnico → /hallazgos/crear
   └─ Wizard de registro
   └─ Carga fotos
   └─ Clasifica gravedad

2. Admin → /hallazgos
   └─ Gestiona todos los hallazgos
   └─ Agrega seguimientos

3. Técnico → /mis-hallazgos
   └─ Ve solo sus hallazgos

4. Sistema → Genera PDF
   └─ /hallazgos/{id}/pdf
```

---

## 🎯 Ventajas del Diseño Actual

| Característica | Beneficio |
|----------------|-----------|
| **TALL Stack** | SPA-like sin complejidad de Angular |
| **API REST** | Integraciones externas fáciles |
| **Dual Auth** | Web sessions + API tokens |
| **Roles** | Control granular de permisos |
| **SQLite** | Deploy simple, sin servidor DB |
| **Livewire** | Reactivo sin escribir JS |
| **Modular** | 10+ módulos independientes |
| **PDF Automático** | Reportes profesionales |
| **Excel Import** | Carga masiva de datos |
| **Wizards** | UX guiada paso a paso |

---

## 📊 Estadísticas del Proyecto

- **41 Componentes Livewire**
- **33 Modelos Eloquent**
- **46 Migraciones**
- **25+ Controladores**
- **10 Módulos Principales**
- **3 Dashboards**
- **Múltiples generadores PDF**
- **Sistema de importación Excel**

---

*Arquitectura EP-Back V2.0.0.0 - Sistema Integral de Gestión de Seguridad e Higiene*
