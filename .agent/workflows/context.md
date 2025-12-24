---
description: Contexto del proyecto EP-Back - Sistema de Checklists Dinámicos
---

# 📖 Contexto del Proyecto EP-Back

## 🎯 Visión General

**Nombre:** EP-Back (Equipo Preventivo - Backend)
**Versión Actual:** V1.0.0.0
**Tipo:** Sistema SaaS de gestión de checklists dinámicos
**Stack:** Laravel 12 + Livewire 3 + Tailwind CSS + SQLite (dev) / MySQL|PostgreSQL (prod)

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

### 1. Código

```php
// ✅ HACER: Usar Type Hints y Return Types
public function getActiveChecklists(): Collection
{
    return Checklist::where('is_active', true)->get();
}

// ❌ EVITAR: Funciones sin tipado
public function getChecklists()
{
    return Checklist::all();
}
```

```php
// ✅ HACER: Usar Form Requests para validación
class StoreChecklistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
        ];
    }
}

// ❌ EVITAR: Validación inline en controladores
$request->validate(['title' => 'required']);
```

### 2. Componentes Livewire

```php
// ✅ HACER: Componentes pequeños y enfocados
class QuestionEditor extends Component
{
    public Question $question;
    
    protected function rules(): array
    {
        return [
            'question.label' => 'required|string|max:500',
            'question.type' => 'required|in:text,select,checkbox,radio',
        ];
    }
}

// ❌ EVITAR: Componentes monolíticos de +500 líneas
```

### 3. Base de Datos

```php
// ✅ HACER: Usar Migraciones descriptivas
public function up(): void
{
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('checklist_id')->constrained()->cascadeOnDelete();
        $table->string('label', 500);
        $table->string('type', 50); // text, select, checkbox, radio
        $table->json('options')->nullable();
        $table->integer('order')->default(0);
        $table->boolean('is_required')->default(false);
        $table->timestamps();
        
        $table->index(['checklist_id', 'order']);
    });
}

// ❌ EVITAR: Migraciones sin índices ni constraints
```

### 4. Testing

```php
// ✅ HACER: Tests descriptivos con Arrange-Act-Assert
public function test_user_can_complete_checklist(): void
{
    // Arrange
    $user = User::factory()->create();
    $checklist = Checklist::factory()
        ->has(Question::factory()->count(3))
        ->create();
    
    // Act
    $response = $this->actingAs($user)
        ->post("/inspections/{$checklist->id}", [
            'answers' => [1 => 'Sí', 2 => 'No', 3 => 'N/A']
        ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('inspections', [
        'user_id' => $user->id,
        'checklist_id' => $checklist->id,
    ]);
}
```

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
```
main            → Producción estable
├── develop     → Desarrollo activo
│   ├── feature/checklist-builder
│   ├── feature/question-editor
│   └── feature/inspection-history
└── hotfix/     → Correcciones urgentes en producción
```

### Commits (Conventional Commits)
```
feat: add dynamic question types to checklist builder
fix: resolve validation error on empty options array
docs: update API documentation for inspections
test: add unit tests for Answer model
refactor: extract question rendering to separate component
chore: update composer dependencies
```

### Pull Request Template
```markdown
## Descripción
[Describir cambios realizados]

## Tipo de cambio
- [ ] Bug fix
- [ ] Nueva feature
- [ ] Breaking change
- [ ] Documentación

## Checklist
- [ ] Tests pasan localmente
- [ ] Código sigue las convenciones del proyecto
- [ ] Documentación actualizada si aplica
```

---

## 🧪 Testing

### Comandos
```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=ChecklistTest

# Ejecutar con coverage
php artisan test --coverage

# Ejecutar tests de feature
php artisan test tests/Feature

# Ejecutar tests unitarios
php artisan test tests/Unit
```

### Estructura de Tests
```
tests/
├── Feature/
│   └── Checklists/
│       ├── ChecklistBuilderTest.php
│       ├── ChecklistFillerTest.php
│       └── InspectionTest.php
└── Unit/
    └── Models/
        ├── ChecklistTest.php
        ├── QuestionTest.php
        └── AnswerTest.php
```

### Coverage Mínimo Esperado
- Modelos: 80%
- Componentes Livewire: 70%
- Controladores: 60%
- **Global: >60%**

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

## 📚 Documentación de Referencia

### Interna
- `/doc/PRD-EP.txt` - Product Requirements Document
- `/doc/Cambio-tecnologia.txt` - Análisis de migración tecnológica
- `/doc/ROADMAP.md` - Fases del proyecto (actualizado)
- `/doc/CHECKLISTS_DINAMICOS.md` - **Documentación técnica del módulo**
- `/doc/componentes.txt` - Especificación de componentes

### Módulo de Checklists Dinámicos (V1.0.0.0)
```
URL: http://localhost:8000/checklists

Archivos principales:
├── app/Models/DynamicChecklist.php
├── app/Models/DynamicQuestion.php
├── app/Livewire/Checklists/ChecklistBuilder.php
├── app/Livewire/Checklists/ChecklistFiller.php
└── resources/views/livewire/checklists/
```

### Externa
- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Livewire 3 Docs](https://livewire.laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/start-here)

---

## 📊 Versiones

### Historial
| Versión | Fecha | Descripción |
|---------|-------|-------------|
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

*Documento actualizado: Diciembre 2024*
*Próxima revisión: Post-implementación V1.0.0.0*
