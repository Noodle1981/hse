# XamaSafe v1.0.0 — Plan de Implementación
## "Demostrar valor antes de escalar"

**Fecha:** 1 de Julio 2026
**Branch:** `feature/fixes` → merge a `produccion`
**Criterio de éxito:** Un cliente real carga sus matafuegos, un técnico los inspecciona, y el sistema dispara una alerta de vencimiento automáticamente.

---

## Qué problema resuelve esta versión

Un cliente necesita saber qué matafuegos tiene, cuándo vencen, y recibir un aviso antes de que pase.
Hoy no puede hacer eso en XamaSafe. Con v1.0.0, puede.

---

## Sprint 1 — GPS (4h)
*Prerequisito: habilita proof-of-presence que los clientes ya preguntaron*

### A1 — Backend GPS
**`app/Services/TimeTrackingService.php`**
- Hacer `calculateHaversineDistance()` público y activarlo en `verifyGpsPresence()`
- No bloqueante: si supera el radio → `gps_verified = false` + `Log::warning()`

**`app/Services/VisitService.php`**
- Aceptar `checkin_lat`, `checkin_lng` al crear `VisitCertificate`
- Persistir + calcular `gps_verified` en el check-in

**`app/Http/Controllers/OperationController.php`**
- `startQuickExecution()`: aceptar `checkin_lat`, `checkin_lng` (nullable)
- Nuevo `checkout()`: `POST /in-progress/{visit}/checkout` con `checkout_lat`, `checkout_lng`

### A2 — Frontend GPS
**`resources/js/Pages/Tecnico/MyAssignments.jsx`**
- Solicitar GPS via `navigator.geolocation.getCurrentPosition()` al iniciar
- Si se deniega → continuar con advertencia visual, enviar `null`

**`resources/js/Pages/Tecnico/Visits/InProgress.jsx`**
- Badge dinámico: `gps_verified=true` → 🟠 pulsante / `false` → 🔴 / sin coords → ⚫
- Al finalizar visita: capturar GPS antes de abrir modal de firma

### A3 — Ruta
```php
Route::post('/in-progress/{visit}/checkout', [OperationController::class, 'checkout'])
     ->name('checkout');
```

---

## Sprint 2 — Motor de Activos: Schema + Backend (8h)
*El núcleo. Todo lo demás depende de esto.*

### B0 — Schema-Driven AssetType ⭐ CRÍTICO
**Migración:**
```php
$table->json('metadata_schema')->nullable();
$table->json('notification_emails')->nullable();
```

**`app/Models/AssetType.php`** — nuevo modelo con:
```php
protected $casts = [
    'metadata_schema'     => 'array',
    'notification_emails' => 'array',
];
```

**`resources/js/Pages/Gestor/AssetTypes/Index.jsx`** — CRUD con schema builder:
- Agregar campos: nombre, tipo (`text` / `number` / `date` / `select` / `boolean`), unidad, `alert_days` o `alert_threshold`
- Preview del formulario generado en tiempo real

> **¿Por qué esto primero?** Sin schema, el formulario de carga de activos es hardcodeado.
> Con schema, matafuego, baño y camioneta entran solos.

### B1 — Migración: asset_id en dynamic_inspections
```php
$table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
```

### B2 — Modelos Eloquent
**`app/Models/Asset.php`** — nuevo:
```php
fillable: empresa_id, sede_id, asset_type_id, name, identifier, status, metadata
casts:    metadata => 'array'
traits:   SoftDeletes
relaciones: belongsTo(Sede), belongsTo(Empresa), belongsTo(AssetType), hasMany(DynamicInspection)
```

**`app/Models/AssetType.php`** — ya creado en B0, agregar:
```php
relaciones: hasMany(Asset), belongsTo(Category)
```

**`app/Models/DynamicInspection.php`** — modificar:
- Agregar `asset_id` a `fillable`
- Relación `belongsTo(Asset)`

### B3 — Event + Listener: Sync de Metadata
**`app/Events/InspectionCompleted.php`** — nuevo
**`app/Listeners/SyncAssetMetadata.php`** — nuevo:
- Iterar `answers()->with('question')->get()`
- Si `vincular_a_metadata != null` → JSON merge en `assets.metadata`

**`AppServiceProvider` o `EventServiceProvider`:**
```php
InspectionCompleted::class => [SyncAssetMetadata::class]
```

**`InspectionController::save()`** — disparar el evento al completar

### B4 — AssetObserver (Audit Trail)
**`app/Observers/AssetObserver.php`** — nuevo:
- `updated`: guardar en `audit_logs` con `getOriginal()` vs `getDirty()` + `user_id` + `asset_id`

**`AppServiceProvider::boot()`:**
```php
Asset::observe(AssetObserver::class);
```

### B5 — Transición Automática de Estado
**`InspectionController::save()` o en el Listener:**
- Si inspección falla y tiene `asset_id` → `assets.status = 'fuera_de_servicio'`
- Registrar en `audit_logs` con `action = 'auto_status_change'`

### B8 — Job de Alertas ⭐ CRÍTICO PARA CLIENTE MATAFUEGOS
**`app/Console/Commands/CheckAssetAlerts.php`** — nuevo:
- `php artisan assets:check-alerts`
- Iterar activos activos con `status != 'fuera_de_servicio'`
- Leer `assetType.metadata_schema` → buscar campos con `alert_days` o `alert_threshold`
- `type=date` + días restantes ≤ `alert_days` → crear Finding automático
- `type=number` + valor ≥ `alert_threshold` → crear Finding automático
- No duplicar: verificar si ya existe Finding abierto del mismo tipo

**`app/Console/Kernel.php`:**
```php
$schedule->command('assets:check-alerts')->dailyAt('07:00');
```

---

## Sprint 3 — CRUD de Activos + Wizard (8h)
*Permite al Gestor cargar activos y al Técnico inspeccionarlos*

### B6 — AssetController + UI básica
**`app/Http/Controllers/AssetController.php`** — nuevo (CRUD completo):
- `index()`: paginado, filtrable por sede/empresa, `with(['assetType', 'sede'])`
- `store()`: formulario dinámico según `metadata_schema` del tipo elegido
- `show()`: ficha con inspecciones anteriores y metadata actual
- `update()` / `destroy()` (soft delete)
- Multi-tenancy: filtrar por `consultora_id`

**`resources/js/Pages/Gestor/Assets/Index.jsx`** — nuevo:
- Tabla con KPIs: operativos / en alerta / fuera de servicio
- Columnas: Nombre, Tipo, Sede, Estado (semáforo), Última inspección

**`resources/js/Pages/Gestor/Assets/Show.jsx`** — nuevo:
- Metadata actual (campos del schema con sus valores)
- Timeline de inspecciones vinculadas
- Estado actual + badge de alerta si aplica

**`resources/js/Pages/Gestor/Assets/Create.jsx`** — nuevo:
- Selector de `AssetType` → formulario renderizado dinámicamente desde `metadata_schema`
- Sin hardcodear campos: el schema define qué mostrar

**Rutas:**
```php
Route::prefix('activos')->name('gestor.assets.')->group(function () {
    Route::get('/',              [AssetController::class, 'index'])->name('index');
    Route::get('/crear',         [AssetController::class, 'create'])->name('create');
    Route::post('/',             [AssetController::class, 'store'])->name('store');
    Route::get('/{asset}',       [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/editar',[AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}',       [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}',    [AssetController::class, 'destroy'])->name('destroy');
});
```

### B7 — Selección de Activo en el Wizard
**`InspectionController::wizard()`:**
- Si el checklist tiene `asset_type_id` o la sede tiene activos → pasar lista al prop

**`resources/js/Pages/Tecnico/Inspections/Wizard.jsx`:**
- Paso 0: selector de activo con buscador reactivo
- Si no hay activos vinculables → saltar automáticamente
- Incluir `asset_id` en el payload de guardado

---

## Checklist de Validación v1.0.0

### Funcional
- [ ] Gestor crea un `AssetType` "Matafuego" con campos: `presion (number)`, `vencimiento (date, alert_days=30)`, `agente (select)`
- [ ] Gestor carga el activo "MF-001 - Sector Depósito" con esos datos
- [ ] Técnico entra al Wizard y ve el Paso 0 con el selector de activo
- [ ] Técnico completa la inspección → `asset_id` queda guardado en `dynamic_inspections`
- [ ] `SyncAssetMetadata` actualiza `assets.metadata` con las respuestas vinculadas
- [ ] `AssetObserver` registra el cambio en `audit_logs`
- [ ] `php artisan assets:check-alerts` genera un Finding si `vencimiento` está dentro de 30 días
- [ ] No se duplica el Finding si ya existe uno abierto
- [ ] GPS check-in/out se guarda y badge aparece en `InProgress.jsx`

### Multi-tenancy
- [ ] Ningún endpoint expone activos de otra consultora
- [ ] SuperAdmin puede ver todo

### Técnico
- [ ] `php artisan test` en verde

---

## Qué NO entra en v1.0.0 (esperando validación)

| Feature | Por qué espera |
|---|---|
| Reporte de cumplimiento (B9) | Primero validar que el Gestor usa la ficha básica |
| Dashboard de flota (B10) | Necesita datos reales de múltiples activos para ser útil |
| Ficha pública con token (B11) | Validar si el cliente quiere autoservicio o que el Gestor le mande el PDF |
| Notificaciones por email (B12) | El Finding automático ya avisa en-app; validar si necesitan email también |
| QR scanning (C) | Feature premium post-validación |

---

## Criterios para pasar a v2.0.0

> Avanzar cuando se cumplan **al menos 3 de estos 4**:

1. ✅ Un cliente cargó ≥ 10 activos reales y los usa en inspecciones
2. ✅ El job de alertas generó Findings que el Gestor actuó sobre ellos
3. ✅ El Gestor preguntó "¿puedo exportar esto?" o "¿puedo mandárselo al cliente?"
4. ✅ Otro cliente quiere usar el módulo para un tipo de activo diferente (camioneta, baño, etc.)

---

*XamaSafe v1.0.0 — Motor de Activos MVP*
*"Cargar activos → inspeccionarlos → recibir alertas. Nada más, nada menos."*
