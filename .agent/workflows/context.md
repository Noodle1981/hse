---
description: Contexto del proyecto XamaSafe
---

# 📖 Contexto del XamaSafe

## 🎯 Visión General

**Nombre:** XamaSafe
**Versión Actual:** V2.1.0.0 (Premium HUD)
**Tipo:** Sistema SaaS integral de Higiene y Seguridad
**Stack:** Laravel 12 + Livewire 3.7 + Tailwind CSS v4 + SQLite (dev)

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
│  Livewire Components + Blade Views + Tailwind CSS           │
├─────────────────────────────────────────────────────────────┤
│                    APPLICATION LAYER                         │
│  Services + Form Requests + Policies                        │
├─────────────────────────────────────────────────────────────┤
│                    DOMAIN LAYER                              │
│  Eloquent Models + Relationships + Scopes                   │
├─────────────────────────────────────────────────────────────┤
│                    INFRASTRUCTURE LAYER                      │
│  Migrations + Seeders + Factories + SQLite/MySQL            │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Carpetas
```
ep-back/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controladores MVC
│   │   └── Requests/          # Form Requests (validación)
│   ├── Livewire/
│   │   └── Checklists/        # Componentes del módulo
│   ├── Models/                # Modelos Eloquent
│   ├── Policies/              # Políticas de autorización
│   └── Services/              # Lógica de negocio
├── database/
│   ├── migrations/            # Esquema de BD
│   ├── factories/             # Factories para testing
│   └── seeders/               # Datos de prueba
├── resources/
│   └── views/
│       ├── livewire/          # Vistas de componentes
│       ├── components/        # Componentes Blade reutilizables
│       └── layouts/           # Layouts principales
├── routes/
│   └── web.php                # Rutas principales
├── tests/
│   ├── Feature/               # Tests de integración
│   └── Unit/                  # Tests unitarios
└── doc/                       # Documentación del proyecto
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
│ Inspection  │──1:N─│   Answer    │
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

---

## ✅ Buenas Prácticas

### Código
- ✅ Usar Type Hints y Return Types
- ✅ Form Requests para validación
- ✅ Eager Loading para prevenir N+1
- ❌ Evitar código sin tipado

### Componentes Livewire
- ✅ Componentes pequeños y enfocados
- ✅ Validación por propiedades
- ❌ Evitar componentes >500 líneas

### Base de Datos
- ✅ Migraciones con índices y constraints
- ✅ foreignId()->constrained()->cascadeOnDelete()
- ✅ Índices en columnas frecuentemente consultadas

### Testing
- ✅ Patrón Arrange-Act-Assert
- ✅ Factories para datos de prueba
- ✅ Tests descriptivos (test_user_can_...)

---

## 📌 Convenciones de Nombrado

### PHP/Laravel
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Modelos | PascalCase singular | `Checklist`, `Question` |
| Controllers | PascalCase + Controller | `ChecklistController` |
| Migrations | snake_case con fecha | `2024_12_23_create_checklists_table` |
| Métodos | camelCase | `getActiveQuestions()` |
| Variables | camelCase | `$checklistId` |
| Constantes | UPPER_SNAKE | `STATUS_ACTIVE` |

### Blade/Views
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Vistas | kebab-case | `checklist-form.blade.php` |
| Componentes | kebab-case | `x-question-input` |
| Layouts | kebab-case | `app-layout.blade.php` |

### CSS/Tailwind (Visual Premium Dark)
- **Estética:** HUD Futurista / Dashboard Premium.
- **Paleta:** Escala de grises profunda, acentos vibrantes (Cyan, Violet, Emerald).
- **Efectos:** Glassmorphism, bordes con degradado, micro-animaciones HUD.
- **Tipografía:** Inter / Outfit para legibilidad técnica.
- **Componentes:** Basados en el sistema "Terminal Táctica".

---

## 🔄 Git Workflow

### Ramas
- `main` → Producción estable
- `version-premium` → Desarrollo actual (Rama principal activa)
- `version-minina` → Rama estable MVP
- `feature/*` → Nuevas funcionalidades

### Commits (Conventional)
- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Documentación
- `test:` Tests
- `refactor:` Refactorización

---

## 🧪 Testing

### Comandos
```bash
php artisan test                    # Todos los tests
php artisan test --filter=Name      # Tests específicos
php artisan test --coverage         # Con coverage
```

### Coverage Esperado
- Modelos: 80% | Livewire: 70% | Controllers: 60% | **Global: >60%**

---

## 🚀 Instalación Local

### Requisitos
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- npm/yarn

### Pasos (proyecto existente)
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

# 5. (Opcional) Cargar datos de prueba
php artisan db:seed

# 6. Instalar dependencias JS
npm install

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
```

### Variables de Entorno Clave
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
```

---

## 📚 Documentación Completa del Sistema

### 📖 Procedimientos (9)
- `/doc/Lógica_del_diseño/ARQUITECTURA.md` - Arquitectura V2.0
- `/doc/Procedimientos/TAREAS.MD` - Estado y roadmap
- `/doc/Procedimientos/CHECKLISTS_DINAMICOS.md`
- `/doc/Procedimientos/CAPACITACIONES.MD`
- `/doc/Procedimientos/CONTANCIAS_DE_VISITAS.MD`
- `/doc/Procedimientos/EMPRESAS_SEDE_EMPLEADOS.MD`
- `/doc/Procedimientos/HALLAZGOS.md`
- `/doc/Procedimientos/CONTRATO_ANUAL.MD`
- `/doc/Procedimientos/ROLES Y SUS ALCANCES.MD`

### 🎨 Lógica del Diseño (4)
- `/doc/Lógica_del_diseño/COMPONENTES_LIVEWARE.MD` - 27 componentes
- `/doc/Lógica_del_diseño/TABLAS.MD` - 38 tablas
- `/doc/Lógica_del_diseño/ESCALABILIDAD.MD`
- `/doc/Lógica_del_diseño/VISUAL.MD`

### 📊 Estadísticas
**13 documentos** | 400+ páginas | 27 componentes | 38 tablas | 20+ casos de uso

### 🌐 Referencias
- [Laravel 12](https://laravel.com/docs/12.x) | [Livewire 3](https://livewire.laravel.com/docs)
- [Tailwind CSS v4](https://tailwindcss.com/docs) | [Alpine.js](https://alpinejs.dev)

---

## 📊 Versiones

### Historial
| Versión | Fecha | Descripción |
|---------|-------|-------------|
| V1.0.0.0 | Marzo 2026 | Sistema MVP Ultra-minimizado - 8 módulos iniciales |
| V2.1.0.0 | Abril 2026 | **Versión Premium HUD** - Rediseño total, Control de Contratos y Hallazgos |

### Versionado Semántico
```
MAJOR.MINOR.PATCH.BUILD
  │     │     │     └─ Build interno (incrementa por commit)
  │     │     └─────── Correcciones de bugs
  │     └───────────── Nuevas funcionalidades compatibles
  └─────────────────── Cambios incompatibles con versiones anteriores
```

---

*Documento actualizado: 06 Abril 2026*
*Versión del Sistema: V2.1.0.0 Premium*
*Documentación Completa: 13 Archivos Técnicos*