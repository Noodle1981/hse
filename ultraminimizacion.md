# Roadmap: Ultra-Minimización del Proyecto

> Objetivo: eliminar el módulo EPP completo, limpiar la base de datos, renombrar namespaces legacy y detectar código muerto.

---

## Etapa 1 — Quitar vistas EPP del perfil Gestor

**URLs a eliminar:**
- `http://127.0.0.1:8000/gestor/epp/history`
- `http://127.0.0.1:8000/gestor/epp/alerts`

### Rutas a eliminar en `routes/web.php` (bloque gestor, líneas 98–107)

```php
// Eliminar SOLO estas dos rutas del bloque gestor/epp:
Route::get('/alerts', \App\Livewire\Epp\EppAlerts::class)->name('alerts');
Route::get('/history', \App\Livewire\Epp\EppHistory::class)->name('history');
```

> ⚠️ No tocar todavía el bloque completo `gestor/epp` porque `catalog`, `deliver`, `employees` e `import-employees` pueden seguir siendo necesarios hasta la Etapa 3.

### Navbar lateral

- Buscar en `resources/views/components/layouts/app.blade.php` cualquier enlace que apunte a `gestor.epp.alerts` o `gestor.epp.history` y comentar/eliminar.
- Verificar también en componentes Livewire con método `render()` si construyen el menú dinámicamente.

### Verificación

```bash
php artisan route:list | grep "gestor/epp"
# Las rutas alerts e history ya no deben aparecer
```

---

## Etapa 2 — Quitar vista EPP del perfil Técnico

**URL a eliminar:**
- `http://127.0.0.1:8000/tecnico/epp/deliver`

### Rutas a eliminar en `routes/web.php` (bloque técnico, líneas 222–229)

```php
// Eliminar del bloque tecnico/epp:
Route::get('/deliver', \App\Livewire\Epp\EppDeliveryWizard::class)->name('deliver');
```

> ⚠️ El bloque `tecnico/epp` también contiene `/history` y `/employees`. Eliminar solo `/deliver` por ahora.

### Navbar lateral

- Buscar en `app.blade.php` el enlace `tecnico.epp.deliver` y eliminar.

### Verificación

```bash
php artisan route:list | grep "tecnico/epp/deliver"
# No debe aparecer
```

---

## Etapa 3 — Borrar controladores y modelos EPP

Una vez sin rutas activas hacia ellos, se pueden eliminar de forma segura.

### Controladores a eliminar

| Archivo | Ruta |
|---|---|
| `EppPDFController.php` | `app/Http/Controllers/EppPDFController.php` |
| `EppQrController.php` | `app/Http/Controllers/EppQrController.php` |

### Componentes Livewire a eliminar

| Clase | Archivo PHP | Vista Blade |
|---|---|---|
| `EppAlerts` | `app/Livewire/Epp/EppAlerts.php` | `resources/views/livewire/epp/epp-alerts.blade.php` |
| `EppHistory` | `app/Livewire/Epp/EppHistory.php` | `resources/views/livewire/epp/epp-history.blade.php` |
| `EppDeliveryWizard` | `app/Livewire/Epp/EppDeliveryWizard.php` | `resources/views/livewire/epp/epp-delivery-wizard.blade.php` |
| `EppCatalog` | `app/Livewire/Epp/EppCatalog.php` | `resources/views/livewire/epp/epp-catalog.blade.php` |
| `EmployeeManager` | `app/Livewire/Epp/EmployeeManager.php` | *(sin vista dedicada visible)* |
| `EmployeeImporter` | `app/Livewire/Epp/EmployeeImporter.php` | *(sin vista dedicada visible)* |

> Verificar si `EmployeeManager` o `EmployeeImporter` son referenciados desde `VisitInProgress` u otros componentes antes de eliminar.

### Modelos a eliminar

| Modelo | Archivo |
|---|---|
| `EppDelivery` | `app/Models/EppDelivery.php` |
| `EppDeliveryItem` | `app/Models/EppDeliveryItem.php` |
| `EppDeliveryRequest` | `app/Models/EppDeliveryRequest.php` |
| `EppItem` | `app/Models/EppItem.php` |
| `EppReplacementAlert` | `app/Models/EppReplacementAlert.php` |
| `VisitEppDelivery` | `app/Models/VisitEppDelivery.php` |

### Vistas blade adicionales a eliminar

- `resources/views/pdf/epp-delivery.blade.php`
- `resources/views/components/pdf/epp-delivery.blade.php`
- `resources/views/visits/epp-delivery.blade.php`
- `resources/views/public/epp-qr-scan.blade.php`

### Quitar rutas restantes de EPP

Eliminar los bloques completos en `web.php`:

```php
// En gestor (líneas ~98–107):
Route::prefix('epp')->name('epp.')->group(function () { ... });

// En técnico (líneas ~222–229):
Route::prefix('epp')->name('epp.')->group(function () { ... });
```

### Verificación

```bash
grep -rn "EppDelivery\|EppItem\|EppAlert\|EppQr\|EppCatalog\|EppHistory\|EppDeliveryWizard" app/ --include="*.php"
# No debe haber referencias fuera de los archivos a eliminar
php artisan route:list | grep epp
# No debe haber rutas EPP
```

---

## Etapa 4 — Limpiar base de datos (Migraciones EPP)

Crear una **nueva migración de rollback limpio** en lugar de eliminar las migraciones originales (para mantener el historial de Git).

### Migración de limpieza a crear

```php
// database/migrations/2026_03_06_drop_epp_tables.php
Schema::dropIfExists('epp_replacement_alerts');
Schema::dropIfExists('epp_delivery_requests');
Schema::dropIfExists('epp_delivery_items');
Schema::dropIfExists('epp_deliveries');
Schema::dropIfExists('epp_items');
Schema::dropIfExists('visit_epp_deliveries'); // si existe como tabla separada
```

### Migraciones originales a archivar (no eliminar del historial)

| Migración |
|---|
| `2025_12_28_021704_create_epp_items_table.php` |
| `2025_12_28_021704_create_epp_deliveries_table.php` |
| `2025_12_28_021705_create_epp_delivery_items_table.php` |
| `2026_01_22_191035_create_epp_delivery_requests_table.php` |
| `2026_01_27_183156_add_stock_to_epp_items_table.php` |
| `2026_03_01_164031_add_exchange_fields_to_epp_items_table.php` |
| `2026_03_01_164033_add_tracking_fields_to_epp_delivery_items_table.php` |
| `2026_03_01_170702_create_epp_replacement_alerts_table.php` |
| `2026_03_01_192256_add_qr_fields_to_epp_delivery_items_table.php` |
| `2026_03_01_193459_add_exchange_order_id_to_epp_replacement_alerts_table.php` |

### Verificación

```bash
php artisan migrate
php artisan db:show --counts
# Las tablas EPP no deben aparecer
```

---

## Etapa 5 — Renombrar carpeta `risk` → `contrato-anual` y revisar código muerto

### 5a — Renombrar namespace `Risks` → `Contracts` (o `ContratosAnuales`)

**Componentes afectados:**

| Archivo actual | Nuevo namespace propuesto |
|---|---|
| `app/Livewire/Risks/AdvancedExecutionsHub.php` | `App\Livewire\Contracts\AdvancedExecutionsHub` |
| `app/Livewire/Risks/AnnualContractDashboard.php` | `App\Livewire\Contracts\AnnualContractDashboard` |
| `app/Http/Controllers/Risks/AnnualContractReportController.php` | `App\Http\Controllers\Contracts\AnnualContractReportController` |
| `resources/views/livewire/risks/` | `resources/views/livewire/contracts/` |

**Pasos:**
1. Renombrar carpetas físicas en filesystem.
2. Actualizar `namespace` en cada archivo PHP.
3. Actualizar `use` statements en `web.php` (líneas 165–169 en gestor, líneas 219–220 en técnico).
4. Ejecutar `php artisan view:clear && php artisan cache:clear`.

**Verificación:**

```bash
grep -rn "Livewire\\\\Risks\|Controllers\\\\Risks" app/ routes/ --include="*.php"
# No debe haber referencias al namespace Risks
```

---

### 5b — Revisar `AngularController`

**Situación actual:** `app/Http/Controllers/AngularController.php` renderiza:
- `view('landing')` → método `index()`
- `view('angular')` → método `panel()`

**No tiene rutas registradas en `web.php`.**

**Verificación:**

```bash
grep -rn "AngularController" routes/ app/ --include="*.php"
```

**Decisión:** Si no hay referencias en rutas ni en ningún otro archivo → **eliminar el controlador** y las vistas `landing.blade.php` y `angular.blade.php` si existen.

---

## Resumen de archivos a eliminar (total)

| Tipo | Cantidad | Estado |
|---|---|---|
| Rutas en `web.php` | ~12 líneas en 2 bloques | Etapas 1, 2 y 3 |
| Componentes Livewire PHP | 6 archivos | Etapa 3 |
| Vistas Blade Livewire | 4 archivos | Etapa 3 |
| Vistas Blade PDF/otros | 4 archivos | Etapa 3 |
| Modelos | 6 archivos | Etapa 3 |
| Controladores | 2 archivos | Etapa 3 |
| Migraciones (drop) | 1 migración nueva | Etapa 4 |
| Renombrar carpeta/namespace | 3 archivos | Etapa 5a |
| Posible baja de `AngularController` | 1 archivo | Etapa 5b |

---

## Orden de ejecución recomendado

```
Etapa 1 → Etapa 2 → verificar que la app no rompe → Etapa 3 → Etapa 4 → Etapa 5
```

> 💡 Hacer un commit después de cada etapa para poder hacer rollback limpio si algo falla.
