---
description: Contexto del proyecto EP-Back - Sistema de Checklists Dinámicos
---

# 📖 Contexto del Proyecto EP-Back

## 🎯 Visión General

**Nombre:** EP-Back (Equipo Preventivo - Backend)
**Versión Actual:** V2.0.0.0
**Tipo:** Sistema SaaS integral de Higiene y Seguridad
**Stack:** Laravel 11 + Livewire 3 + Tailwind CSS + SQLite (dev) / MySQL|PostgreSQL (prod)

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

### CSS/Tailwind
- Usar clases utilitarias de Tailwind
- Evitar CSS personalizado salvo casos justificados
- Componentizar con `@apply` solo en casos de mucha repetición

---

## 🔄 Git Workflow

### Ramas
- `main` → Producción estable
- `develop` → Desarrollo activo
- `feature/*` → Nuevas funcionalidades
- `hotfix/*` → Correcciones urgentes

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

### 📖 Procedimientos (11)
- `/doc/Lógica_del_diseño/ARQUITECTURA.md` - Arquitectura V2.0
- `/doc/Procedimientos/TAREAS.MD` - Estado y roadmap
- `/doc/Procedimientos/CHECKLISTS_DINAMICOS.md`
- `/doc/Procedimientos/CAPACITACIONES.MD`
- `/doc/Procedimientos/CONTANCIAS_DE_VISITAS.MD`
- `/doc/Procedimientos/ENTREGA_EPP.MD`
- `/doc/Procedimientos/EMPRESAS_SEDE_EMPLEADOS.MD`
- `/doc/Procedimientos/HALLAZGOS.md`
- `/doc/Procedimientos/MATRIZ_RIESGOS.md`
- `/doc/Procedimientos/CONTRATO_ANUAL.MD`
- `/doc/Procedimientos/ROLES Y SUS ALCANCES.MD`

### 🎨 Lógica del Diseño (4)
- `/doc/Lógica_del_diseño/COMPONENTES_LIVEWARE.MD` - 41 componentes
- `/doc/Lógica_del_diseño/TABLAS.MD` - 46 tablas
- `/doc/Lógica_del_diseño/ESCALABILIDAD.MD`
- `/doc/Lógica_del_diseño/VISUAL.MD`

### 📊 Estadísticas
**15 documentos** | 500+ páginas | 41 componentes | 46 tablas | 30+ casos de uso

### 🌐 Referencias
- [Laravel 11](https://laravel.com/docs/11.x) | [Livewire 3](https://livewire.laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs) | [Alpine.js](https://alpinejs.dev)

---

## 📊 Versiones

### Historial
| Versión | Fecha | Descripción |
|---------|-------|-------------|
| V2.0.0.0 | Dic 2024 | Sistema completo - 14 módulos funcionales implementados |
| V1.0.0.0 | Dic 2024 | Versión inicial - Sistema de checklists dinámicos |

### Versionado Semántico
```
MAJOR.MINOR.PATCH.BUILD
  │     │     │     └─ Build interno (incrementa por commit)
  │     │     └─────── Correcciones de bugs
  │     └───────────── Nuevas funcionalidades compatibles
  └─────────────────── Cambios incompatibles con versiones anteriores
```

---

*Documento actualizado: 30 Diciembre 2024*
*Versión del Sistema: V2.0.0.0*
*Documentación Completa: 15 Archivos Técnicos*
