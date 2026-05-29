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
| `trainings` | Sesiones e instancias de capacitación | `id`, `empresa_id`, `sede_id`, `dynamic_checklist_id`, `title`, `description`, `type` (presencial/virtual), `date`, `duration`, `status` |
| `visit_trainings` | Bitácora y firmas de capacitación en vivo en la visita | `id`, `visit_certificate_id`, `training_id`, `title`, `description`, `notes`, `duration_minutes`, `status`, `attendees` (JSON con DNI y Firmas) |
| `annual_plans` | Planificación anual del contrato de HyS por sede | `id`, `sede_id`, `category`, `activity_name`, `checklist_id`, `duration_minutes`, `modality`, `multi_trainer`, `scheduled_months` (JSON) |

---

## ✅ Buenas Prácticas

### Backend (Laravel 12)
- ✅ **Manejo de Roles Robusto:** Toda validación de roles en la plataforma (rutas, plantillas, policies) debe apoyarse en la lógica unificada del modelo `User::hasRole()`. Evitar splitting manual ad-hoc en controladores o middleware.
- ✅ **Nombres de Parámetros de Ruta Coincidentes:** Para que la vinculación implícita de modelos (Route Model Binding) de Laravel funcione de forma transparente, el comodín de la ruta (ej. `{inspection}`) debe coincidir exactamente con el nombre de la variable inyectada en el controlador (ej. `DynamicInspection $inspection`). Evitar usar `{id}` genérico si se tipa el modelo, para prevenir inyecciones de modelos vacíos en el frontend.
- ✅ **Inmutabilidad y Blindaje de Fechas (SRE Audit):** Al finalizar una inspección, es obligatorio ignorar y sanitizar cualquier campo de fecha/hora enviado por el frontend (`completed_at`, `date`, `time`, `timestamp`). La persistencia debe forzar estrictamente `now()` en el servidor y disparar alertas detalladas (`Log::warning()`) ante cualquier discrepancia para evitar fraude de horarios.
- ✅ **Arquitectura y Ciclo de Firmas Digitales:**
  - **Firma Tipo A (Perfil/Permanente):** Trazada en canvas, decodificada de Base64 a archivo físico `.png` en storage, a la espera de auditoría visual en `PendingSignaturesList.jsx` por parte del Gestor.
  - **Firma Tipo B (Transaccional/Hojas de Firma):** Guardadas como Base64 serializados o strings directamente sobre planillas específicas en DB (e.g. `visit_trainings.attendees`).
  - **DomPDF Resolution Constraint:** Se garantiza que al exportar las plantillas o reportes PDF en Laravel, el motor DomPDF resuelva las rutas físicas absolutas locales (`public_path('storage/' . $path)`) evitando fallas de visualización (como marcos cruzados o placeholders vacíos).
- ✅ Usar Type Hints y Return Types estrictos.
- ✅ Form Requests independientes para la validación de peticiones.
- ✅ Eager Loading (`with()`) en Eloquent para evitar consultas N+1.
- ✅ **Flujo de Aprobación de Firma Digital (Auditoría):** Los cambios de firma digital del técnico entran en estado `pending` en `firma_status` y se guardan temporalmente en `firma_temp`. Los Gestores auditan y aprueban o rechazan el cambio físicamente a través de la consola de usuarios (`Users.jsx`).
- ✅ Retornar respuestas nativas de Inertia: `return Inertia::render('Componente', [...]);`.
 
### Frontend (React 19 & Inertia)
- ✅ **Diseño HUD Táctico y Modular:** Componentes HUD oscuros premium, modularizados y reutilizables. El modal `ActionSelector` es el estándar para paneles de comandos y acciones spontaneous.
- ✅ **HUD Sync Badge (Naranja Técnico):** El asistente de checklists (`Wizard.jsx`) debe mostrar arriba de la barra de progreso un badge centrado `"⌚ Tiempo Real (Sync Servidor)"` en Naranja Técnico para indicar la inmutabilidad de la fecha en tiempo real.
- ✅ **Counter Badges HUD:** Uso de insignias flotantes absolutas (`absolute -top-3 -right-3`, `rounded-2xl`, `font-black`) para indicar múltiples ejecuciones continuas en flujos activos de inspección.
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