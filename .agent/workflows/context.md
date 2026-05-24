---
description: Contexto del proyecto XamaSafe (Stack React + Inertia.js)
---

# 📖 Contexto de XamaSafe

## 🎯 Visión General

**Nombre:** XamaSafe
**Versión Actual:** V2.1.0.0 (Premium HUD)
**Tipo:** Sistema SaaS integral de Higiene y Seguridad
**Stack:** Laravel 12 + Inertia.js (React 19) + Tailwind CSS v4 + SQLite (dev) + Ziggy JS

### Propósito
Sistema escalable de checklists que permite crear, gestionar y ejecutar formularios dinámicos para:
- ✅ Higiene y Seguridad (HyS)
- ✅ Normas de Calidad (ISO, BPA)
- ✅ Seguimientos operativos
- ✅ Auditorías ambientales
- ✅ Cualquier proceso que requiera verificación por checklist

---

## 🏗️ Arquitectura del Sistema

### Patrón de Diseño
```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                        │
│   React 19 + Inertia.js (React) + Tailwind CSS v4           │
├─────────────────────────────────────────────────────────────┤
│                    APPLICATION LAYER                         │
│   Laravel 12 Controllers + Form Requests + Policies        │
├─────────────────────────────────────────────────────────────┤
│                    DOMAIN LAYER                              │
│   Eloquent Models + Relationships + Scopes                   │
├─────────────────────────────────────────────────────────────┤
│                    INFRASTRUCTURE LAYER                      │
│   Migrations + Seeders + Factories + SQLite/MySQL            │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Carpetas
```
ep-back/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controladores (devuelven Inertia::render)
│   │   └── Requests/          # Form Requests (validación)
│   ├── Models/                # Modelos Eloquent
│   ├── Policies/              # Políticas de autorización
│   └── Services/              # Lógica de negocio
├── database/
│   ├── migrations/            # Esquema de BD
│   ├── factories/             # Factories para testing
│   └── seeders/               # Datos de prueba
├── resources/
│   ├── js/
│   │   ├── Layouts/           # Layouts de React (e.g. AuthenticatedLayout.jsx)
│   │   ├── Pages/             # Páginas React resueltas por Inertia.js
│   │   │   ├── Gestor/        # Consola de mando, Usuarios, Técnicos, Corporativo
│   │   │   ├── Tecnico/       # Herramientas de campo, Misiones e Inspecciones
│   │   │   ├── Auth/          # Login, Registro y Recuperación de clave
│   │   │   ├── Profile/       # Gestión de perfiles y firmas
│   │   │   └── Admin/         # Módulos de administración
│   │   ├── app.jsx            # Inicializador principal de Inertia con React
│   │   └── bootstrap.js       # Inicializador de Axios
│   ├── css/
│   │   └── app.css            # Configuración de Tailwind CSS v4 y variables CSS custom
│   └── views/
│       └── app.blade.php      # Layout raíz de HTML para Inertia
├── routes/
│   └── web.php                # Rutas web registradas con Ziggy y mapeadas a controladores
└── tests/
    ├── Feature/               # Tests de integración
    └── Unit/                  # Tests unitarios
```

---

## 🗃️ Modelo de Datos (Checklists Dinámicos)

### Entidades Principales

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│  Checklist  │──1:N─│  Question   │      │    User     │
│  (Plantilla)│      │ (Preguntas) │      │ (Ejecutor)  │
└──────┬──────┘      └──────┬──────┘      └──────┬──────┘
       │                    │                     │
       │   ┌────────────────┼─────────────────────┘
       │   │                │
       ▼   ▼                ▼
┌─────────────┐      ┌─────────────┐
│  Inspection │──1:N─│   Answer    │
│ (Ejecución) │      │ (Respuesta) │
└─────────────┘      └─────────────┘
```

### Descripción de Tablas

| Tabla | Propósito | Campos Clave |
|-------|-----------|--------------|
| `checklists` | Plantillas de checklist | `id`, `title`, `description`, `category`, `is_active` |
| `questions` | Preguntas dinámicas | `id`, `checklist_id`, `label`, `type`, `options`, `order`, `is_required` |
| `inspections` | Ejecuciones/llenados | `id`, `checklist_id`, `user_id`, `completed_at`, `notes` |
| `answers` | Respuestas específicas | `id`, `inspection_id`, `question_id`, `value` |
| `sedes` | Plantas físicas y control de jornada | `id`, `empresa_id`, `nombre_sede`, `dias_operativos` (JSON), `hora_apertura`, `hora_cierre`, `es_roster_24_7`, `radio_tolerancia_metros` |
| `visit_certificates` | Constancias de visita, geolocalización y auditoría horaria | `id`, `sede_id`, `user_id`, `start_time`, `end_time`, `horas_gabinete_estimadas`, `checkin_lat`, `checkin_lng`, `checkout_lat`, `checkout_lng`, `gps_verified` |

---

## ✅ Buenas Prácticas

### Backend (Laravel 12)
- ✅ Usar Type Hints y Return Types estrictos.
- ✅ Form Requests independientes para la validación de peticiones.
- ✅ Eager Loading (`with()`) en Eloquent para evitar consultas N+1.
- ✅ Retornar respuestas nativas de Inertia: `return Inertia::render('Componente', [...]);`.

### Frontend (React 19 & Inertia)
- ✅ Componentes modulares, pequeños y altamente enfocados.
- ✅ Control de estado mediante hooks nativos de React (`useState`, `useEffect`, `useMemo`).
- ✅ Enlace con backend y envío de formularios usando el hook `useForm` de Inertia.
- ✅ Cero código remanente o muerto de Livewire (totalmente purgado).

### Base de Datos
- ✅ Migraciones con índices, claves foráneas estructuradas y constraints de borrado cascada.
- ✅ Optimización de consultas complejas.
- ⚠️ **ADVERTENCIA MCP SQLite:** No utilizar la herramienta MCP de SQLite del entorno, ya que apunta a una base de datos global genérica del sistema. Para cualquier interacción o consulta con la base de datos de XamaSafe, utilizar siempre **Laravel Tinker** (`php artisan tinker`) o scripts PHP locales en la carpeta `scratch/` que apunten a `database/database.sqlite`.

---

## 📌 Convenciones de Nombrado

### Laravel/PHP
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Modelos | PascalCase singular | `Checklist`, `Question` |
| Controllers | PascalCase + Controller | `ChecklistController` |
| Migrations | snake_case con fecha | `2024_12_23_create_checklists_table` |
| Métodos | camelCase | `getActiveQuestions()` |
| Variables | camelCase | `$checklistId` |
| Constantes | UPPER_SNAKE | `STATUS_ACTIVE` |

### React & Frontend
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Componentes / Páginas | PascalCase | `AuthenticatedLayout.jsx`, `Dashboard.jsx` |
| Hooks Personalizados | camelCase prefijada por `use` | `useCustomValidation` |
| CSS Variables (Tema HUD) | kebab-case con prefijo `--color-ep-` | `--color-ep-primary` |

---

## 🔄 Git Workflow

### Ramas
- `feature-inertia-react` → Rama activa de desarrollo (React 19 + Inertia + Laravel 12).
- `version-premium` → Desarrollo premium legacy (Livewire).
- `version-minina` → Rama estable MVP.
- `main` → Producción estable/base.

### Commits (Conventional)
- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Documentación
- `test:` Tests
- `refactor:` Refactorización o limpieza de código

---

## 🧪 Testing

### Comandos
```bash
php artisan test                    # Todos los tests
php artisan test --filter=Name      # Tests específicos
```

---

## 🚀 Instalación Local

### Requisitos
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- npm o pnpm

### Pasos
```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno (si no existe .env)
cp .env.example .env
php artisan key:generate

# 3. Crear base de datos SQLite (si no existe)
touch database/database.sqlite

# 4. Ejecutar migraciones
php artisan migrate

# 5. Cargar datos de prueba / Seeders
php artisan db:seed

# 6. Instalar dependencias JS
npm install

# 7. Compilar assets & Iniciar entorno de desarrollo
npm run dev
```

---

*Documento actualizado: 20 Mayo 2026*  
*Versión del Sistema: V2.1.0.0 Premium (Inertia + React)*  
*Especialidad: Monolito Laravel con UI React táctica y moderna (Tactical Premium Dark)*  