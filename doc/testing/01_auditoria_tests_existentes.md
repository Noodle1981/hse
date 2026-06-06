# 🔍 Auditoría de Tests Existentes — XamaSafe v1.0 MVP

> Fecha: 2026-03-08  
> Analizado por: Antigravity AI

---

## Resumen Ejecutivo

Los tests existentes cubren **únicamente el módulo de Checklists** y presentan varios
problemas de alineamiento con el estado actual del código. El resto de los módulos del
MVP (Visitas, Hallazgos, Capacitaciones, Informe Maestro, Contrato Anual, Roles) no
tienen ningún test.

| Archivo | Estado | Problema Principal |
|---|---|---|
| `Unit/DynamicChecklistTest.php` | ⚠️ Parcialmente válido | Usa `scopeCategory` incorrecto |
| `Feature/ChecklistBuilderTest.php` | ⚠️ Parcialmente válido | Rutas sin auth, componente sin actingAs |
| `Feature/ChecklistFillerTest.php` | ⚠️ Parcialmente válido | Ruta hardcodeada sin prefijo de rol |

---

## Problemas Detectados

### 1. `Unit/DynamicChecklistTest.php`

#### ❌ Scope de categoría incorrecto
```php
// TEST (incorrecto):
DynamicChecklist::category('hys')->get();

// MODELO REAL (scopeByCategory):
public function scopeByCategory($query, string $category) { ... }

// CORRECCIÓN:
DynamicChecklist::byCategory('hys')->get();
```

#### ✅ Lo que sí está bien
- `it_can_create_a_checklist` — válido
- `it_has_many_questions` — válido
- `scope_active_returns_only_active_checklists` — válido
- `question_options_are_cast_to_array` — válido
- `inspection_can_be_marked_as_completed` — válido
- `it_deletes_questions_when_checklist_is_deleted` — válido (si cascade está en migración)

---

### 2. `Feature/ChecklistBuilderTest.php`

#### ⚠️ Rutas sin autenticación
```php
// TEST (incorrecto — ruta sin prefijo de rol):
$response = $this->get('/checklists/create');

// RUTA REAL (requiere auth + rol Administrador):
// gestor/checklists/create → middleware: auth, role:Administrador
```
Los tests de rutas van a fallar porque la ruta `/checklists/create` no existe.
La ruta real es `/gestor/checklists/create` y requiere `actingAs($admin)`.

#### ⚠️ Falta `actingAs` en tests de Livewire
Los tests del builder no hacen `actingAs()`, lo que puede causar fallos de autorización
si el componente Livewire verifica el usuario autenticado.

#### ✅ Lo que sí está bien
- La lógica de validación (`title` requerido, al menos una pregunta) es válida
- Los tests de `addQuestion`, `removeQuestion`, `moveDown` son válidos en su lógica

---

### 3. `Feature/ChecklistFillerTest.php`

#### ⚠️ Ruta hardcodeada incorrecta
```php
// TEST (incorrecto):
$response = $this->get("/checklists/{$id}/fill");

// RUTA REAL según web.php:
// /gestor/checklists/{id}/fill/{assignment?}
// /tecnico/checklists/{id}/fill/{assignment?}
```

#### ⚠️ `UserFactory` — roles incorrectos
La `UserFactory` por default genera usuarios con `rol = 'admin' | 'usuario' | 'inspector'`.
El sistema real usa `Administrador`, `Técnico`/`tecnico`.
Esto hace que el middleware de roles falle en tests de Feature.
```php
// INCORRECTO en UserFactory:
'rol' => fake()->randomElement(['admin', 'usuario', 'inspector']),

// CORRECTO para el sistema real:
'rol' => 'Administrador'  // o 'Técnico'
```

#### ✅ Lo que sí está bien
- La lógica de guardar respuestas en DB es válida
- El test de checkbox guardado como JSON es válido

---

## Plan de Correcciones Prioritarias

### Prioridad 1 — Correcciones obligatorias para que los tests corran

1. **Corregir `UserFactory`**: agregar estados `administrador()` y `tecnico()` con los
   roles correctos del sistema.
   
2. **Corregir rutas en Feature tests**: usar las rutas reales con prefijo de rol y
   `actingAs()`.

3. **Corregir scope en Unit test**: cambiar `::category()` por `::byCategory()`.

### Prioridad 2 — Tests faltantes críticos (nuevos a escribir)

| Módulo | Test a crear |
|---|---|
| Autenticación | Login, logout, redirect por rol |
| Control de acceso | Técnico no accede a rutas de gestor |
| Visitas | Crear visita, flujo en progreso, finalizar |
| Hallazgos | Crear hallazgo dentro de visita |
| Contrato Anual | Acceso al dashboard, generación PDF |
| Informe Maestro | Crear, editar, generar PDF |

---

## Configuración de Entorno de Testing

Crear `.env.testing` en la raíz del proyecto:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

Y en `phpunit.xml`, verificar que esta configuración esté activa (ya existe en el proyecto).

### Correr los tests
```bash
# Todos los tests
php artisan test

# Solo un archivo
php artisan test tests/Unit/DynamicChecklistTest.php

# Con detalle
php artisan test --verbose
```
