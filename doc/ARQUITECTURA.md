# 🏗️ Arquitectura del Sistema - EP-Back

**Versión:** V1.2.0.0  
**Stack:** TALL (Tailwind, Alpine, Livewire, Laravel 12)  
**Última actualización:** 24 Diciembre 2024

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

| Tecnología | Uso | Ubicación |
|------------|-----|-----------|
| **Tailwind CSS** | Estilos utility-first | CDN en layouts |
| **Alpine.js** | Interactividad JS | Incluido en Livewire |
| **Livewire 3** | Componentes reactivos | `app/Livewire/` |
| **Laravel 12** | Backend framework | Core |

### Estructura de Componentes Livewire

```
app/Livewire/
├── Auth/
│   └── Login.php                # Login web (sesiones)
└── Checklists/
    ├── Dashboard.php            # Estadísticas
    ├── ChecklistIndex.php       # Listado
    ├── ChecklistBuilder.php     # CRUD
    ├── ChecklistFiller.php      # Llenar + firma
    ├── InspectionHistory.php    # Historial
    └── InspectionShow.php       # Detalle
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
│   └── UserController.php       # CRUD usuarios + login API
├── InspectionPDFController.php  # PDF generation
└── (otros controladores legacy)
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

### Tablas del Sistema de Checklists

```sql
users
├── id
├── email (unique)
├── password
├── rol (Administrador, Técnico)
└── ...

dynamic_checklists
├── id
├── title
├── category
├── is_active
└── is_draft

dynamic_questions
├── id
├── dynamic_checklist_id (FK)
├── label
├── type (text, radio, signature, etc)
└── options (JSON)

dynamic_inspections
├── id
├── dynamic_checklist_id (FK)
├── user_id (FK, nullable)
├── completed_at
└── status

dynamic_answers
├── id
├── dynamic_inspection_id (FK)
├── dynamic_question_id (FK)
└── value
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

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver dashboard | ✅ | ✅ |
| Ver checklists | ✅ | ✅ |
| Crear checklist | ✅ | ❌ |
| Editar checklist | ✅ | ❌ |
| Llenar checklist | ✅ | ✅ |
| Ver historial | ✅ | ✅ |

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

## 📡 Integraciones Futuras

### API Ready para:

1. **Aplicación Móvil**
   ```
   POST /api/login
   GET /api/listar_checklists
   POST /api/crear_inspeccion
   ```

2. **Third-Party Apps**
   ```
   Authorization: Bearer {token}
   Accept: application/json
   ```

3. **Webhooks**
   ```
   POST /api/webhooks/inspection-completed
   ```

---

## 🚀 Rutas del Sistema

### Web (TALL Stack)

| Ruta | Componente | Auth | Role |
|------|-----------|------|------|
| `/login` | Login | - | - |
| `/dashboard` | Dashboard | ✅ | Todos |
| `/checklists` | ChecklistIndex | ✅ | Todos |
| `/checklists/create` | ChecklistBuilder | ✅ | Admin |
| `/checklists/{id}/edit` | ChecklistBuilder | ✅ | Admin |
| `/checklists/{id}/fill` | ChecklistFiller | ✅ | Admin+Técnico |
| `/inspections` | InspectionHistory | ✅ | Todos |
| `/inspections/{id}` | InspectionShow | ✅ | Todos |

### API (Token-based)

| Ruta | Método | Auth |
|------|--------|------|
| `/api/login` | POST | - |
| `/api/listar_colaboradores_admin` | GET | Sanctum |
| `/api/registro_colaborador_admin` | POST | Sanctum |

---

## 📦 Dependencias

```json
{
  "laravel/framework": "^12.0",
  "livewire/livewire": "^3.7",
  "laravel/sanctum": "^4.0"
}
```

### Frontend
- Tailwind CSS (CDN)
- Alpine.js (vía Livewire)
- signature_pad.js (NPM)

---

## 🔄 Flujo de Trabajo

### Crear y Llenar Checklist

```
1. Admin → /checklists/create
   └─ Crea plantilla con preguntas

2. Técnico → /checklists
   └─ Ve plantillas activas

3. Técnico → /checklists/{id}/fill
   └─ Completa inspección (con firma)

4. Sistema → Guarda en dynamic_inspections
   └─ Genera PDF automático

5. Usuario → /inspections
   └─ Ve historial completo
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

---

*Arquitectura EP-Back V1.2.0.0 - TALL Stack + API REST*
