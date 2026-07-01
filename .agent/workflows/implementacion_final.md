# Plan de Implementacion: GPS + Modulo de Activos + QR

**Version:** 1.0
**Fecha:** 1 de Julio 2026
**Branch objetivo:** `feature/gps-assets-qr`
**Contexto:** XamaSafe - Laravel 12 + Inertia (React 19) + Tailwind v4

---

## Diagnostico: Estado Actual del Codebase

### Ya implementado (no tocar)

| Componente | Estado |
|---|---|
| Columnas GPS en visit_certificates (checkin_lat, checkin_lng, checkout_lat, checkout_lng, gps_verified) | Migracion existe |
| Columnas latitud, longitud, radio_tolerancia_metros en sedes | Migracion existe |
| TimeTrackingService::calculateHaversineDistance() | Implementado (privado) |
| TimeTrackingService::verifyGpsPresence() | Esqueleto existe (TODO comentado) |
| VisitService::startVisit() | Funcional (sin GPS aun) |
| vincular_a_metadata en dynamic_questions | Columna existe, listener ausente |

### Pendiente de implementacion

| Componente | Gap |
|---|---|
| Captura de GPS en el frontend al iniciar/cerrar visita | No implementado |
| Validacion Haversine activada en verifyGpsPresence() | Esta comentada (TODO) |
| asset_id en dynamic_inspections | No existe la columna |
| Modelos Asset / AssetType en /Models | No existen como Eloquent |
| SyncAssetMetadata listener / InspectionCompleted event | No existen |
| AssetObserver para audit trail | No existe |
| UI de seleccion de activo en el Wizard de inspeccion | No implementado |
| CRUD de Activos (Gestor) | No implementado |
| Escaneo QR de activos (frontend) | No implementado |
| Badge GPS Verificado en InProgress.jsx | No implementado |

---

## MODULO A - GPS Check-in / Check-out (Proof of Presence)

**Objetivo:** Capturar las coordenadas del tecnico al abrir y cerrar una visita, validar contra las coordenadas de la sede con Haversine y mostrar el resultado visualmente.

### A1 - Backend: Activar validacion Haversine

**Archivo:** app/Services/TimeTrackingService.php

- Hacer publico calculateHaversineDistance() y activarlo en verifyGpsPresence().
- Si distancia supera radio_tolerancia_metros -> gps_verified = false pero NO bloquear; Log::warning() con distancia real.
- Retornar distancia calculada para mostrar en la UI.

**Archivo:** app/Services/VisitService.php

- Aceptar parametros opcionales ,  al crear el VisitCertificate.
- Persistir checkin_lat, checkin_lng, checkin_time = now().
- Calcular distancia y persistir gps_verified en el check-in.

**Archivo:** app/Http/Controllers/OperationController.php

- startQuickExecution(): aceptar checkin_lat, checkin_lng (nullable) en el .
- Nuevo metodo checkout(): ruta POST /in-progress/{visit}/checkout.
  - Aceptar checkout_lat, checkout_lng.
  - Calcular Haversine del checkout.
  - Guardar checkout_lat, checkout_lng, checkout_time, checkout_distance_meters, reevaluar gps_verified.

### A2 - Frontend: Captura GPS

**Archivo:** resources/js/Pages/Tecnico/MyAssignments.jsx

- Al iniciar actividad sin visita activa, solicitar GPS via navigator.geolocation.getCurrentPosition().
- Si el usuario deniega -> continuar con advertencia visual (no bloqueante), enviar checkin_lat = null.
- Enviar checkin_lat y checkin_lng al startQuickExecution.

**Archivo:** resources/js/Pages/Tecnico/Visits/InProgress.jsx

- Badge dinamico de estado GPS:
  - gps_verified = true -> Badge Naranja pulsante  GPS Verificado - XXXm de la Sede
  - gps_verified = false -> Badge rojo Fuera del Radio - XXXm
  - Sin coordenadas -> Badge gris Sin Verificacion GPS
- Al Finalizar Visita: capturar GPS (checkout) antes de abrir modal de firma.

### A3 - Ruta nueva

`php
Route::post('/in-progress/{visit}/checkout', [OperationController::class, 'checkout'])->name('checkout');
`

---

## MODULO B - Activos con Trazabilidad Dinamica (Asset-Centric Inspections)

**Objetivo:** Crear el modelo Asset como Eloquent, vincular checklists a activos, sincronizar respuestas al activo via Event Listener, exponer CRUD para el Gestor.

### B1 - Migracion: asset_id en dynamic_inspections

**[NEW]** database/migrations/..._add_asset_id_to_dynamic_inspections.php

`php
->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
`

### B2 - Modelos Eloquent

**[NEW] app/Models/Asset.php**
- fillable: empresa_id, sede_id, asset_type_id, name, identifier, status, metadata.
- casts: metadata => array.
- SoftDeletes.
- Relaciones: belongsTo(Sede), belongsTo(Empresa), belongsTo(AssetType), hasMany(DynamicInspection).

**[NEW] app/Models/AssetType.php**
- fillable: name, category_id.
- Relacion: hasMany(Asset), belongsTo(Category).

**[MODIFY] app/Models/DynamicInspection.php**
- Agregar asset_id a fillable.
- Agregar relacion belongsTo(Asset).

### B3 - Event + Listener: Sincronizacion de Metadata

**[NEW] app/Events/InspectionCompleted.php**
- Constructor: public DynamicInspection .

**[NEW] app/Listeners/SyncAssetMetadata.php**
- Iterar ->answers()->with('question')->get().
- Si ->vincular_a_metadata no es null -> JSON merge en assets.metadata.
- Guardar el activo con los campos actualizados.

**[MODIFY] EventServiceProvider (o AppServiceProvider)**
- Registrar: InspectionCompleted::class => [SyncAssetMetadata::class].

**[MODIFY] InspectionController::save()**
- Disparar event(new InspectionCompleted()) al completar.

### B4 - Observer: Audit Trail del Activo

**[NEW] app/Observers/AssetObserver.php**
- Evento updated: guardar en audit_logs payload con getOriginal() vs getDirty(), user_id autenticado y asset_id.

**[MODIFY] AppServiceProvider::boot()**
- Asset::observe(AssetObserver::class).

### B5 - Falla Critica => Transicion de Estado

**[MODIFY] InspectionController::save() o el Listener**

- Si inspeccion resulta en fallo critico y existe asset_id:
  - Actualizar assets.status = 'fuera_de_servicio'.
  - Registrar en audit_logs la transicion automatica con action = 'auto_status_change'.
  - El AssetObserver captura el cambio de forma transparente.

### B6 - CRUD de Activos (Gestor)

**[NEW] app/Http/Controllers/AssetController.php**
- index(): listado filtrable por sede/empresa, paginado, with(['assetType', 'sede']).
- create() / store(): formulario de alta con campos basicos + metadata dinamico.
- show(): ficha del activo con historial de inspecciones y evolucion de metricas.
- edit() / update(): edicion de campos y ajuste manual de metadata.
- destroy(): soft delete.
- Multi-tenancy: filtrar por consultora_id del usuario autenticado.

**[NEW] resources/js/Pages/Gestor/Assets/Index.jsx**
- Tabla premium naranja con KPIs (activos operativos, en mantenimiento, fuera de servicio).

**[NEW] resources/js/Pages/Gestor/Assets/Show.jsx**
- Ficha del activo con timeline de inspecciones y grafico de evolucion de metadata.

**[NEW] Rutas en web.php:**
`php
Route::prefix('activos')->name('gestor.assets.')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::get('/crear', [AssetController::class, 'create'])->name('create');
    Route::post('/', [AssetController::class, 'store'])->name('store');
    Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/editar', [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
});
`

### B7 - Seleccion de Activo en el Wizard

**[MODIFY] InspectionController::wizard()**
- Si el checklist tiene asset_type_id asociado o la sede tiene activos, pasar lista al prop de la vista.

**[MODIFY] resources/js/Pages/Tecnico/Inspections/Wizard.jsx**
- Agregar Paso 0 (antes de las preguntas): Seleccion de Activo.
  - Si hay activos disponibles: selector con buscador reactivo.
  - Boton Escanear QR para auto-seleccionar (Modulo C).
  - Si no hay activos vinculables: saltear el paso automaticamente.
- Incluir asset_id en el payload de guardado.

---

## MODULO C - Identificacion por Codigo QR

**Objetivo:** Leer QR de activos para iniciar inspecciones y leer QR de empleados para asistencia de capacitaciones.

### C1 - Dependencia

`ash
npm install html5-qrcode
`

### C2 - Componente: QrScannerModal.jsx

**[NEW] resources/js/Components/QrScannerModal.jsx**
- Modal HUD Premium oscuro que activa la camara trasera del dispositivo.
- Al detectar QR: resuelve asset_id o employee_id segun el tipo de codigo.
- Timeout de 30 segundos con cierre automatico.
- Boton manual Ingresar codigo a mano como fallback.

### C3 - Endpoint de resolucion QR

**[NEW] app/Http/Controllers/QrController.php**
- GET /scan/asset/{identifier} -> JsonResponse con datos del activo.
- GET /scan/employee/{id} -> JsonResponse con datos del empleado.

**[NEW] Rutas:**
`php
Route::get('/scan/asset/{identifier}', [QrController::class, 'resolveAsset'])->name('qr.asset');
Route::get('/scan/employee/{id}', [QrController::class, 'resolveEmployee'])->name('qr.employee');
`

### C4 - Integracion QR en el Wizard

**[MODIFY] Wizard.jsx**
- Boton ESCANEAR QR DEL EQUIPO en Paso 0 abre QrScannerModal.
- Al recibir asset_id: autoseleccionar y avanzar al paso 1.

### C5 - Integracion QR en Capacitaciones

**[MODIFY] InProgress.jsx**
- En panel de asistencia de capacitacion: boton ESCANEAR CREDENCIAL.
- Al leer QR de empleado: pre-llenar nombre y DNI en la fila de asistencia.
- Flash verde de confirmacion para velocidad en campo.

---

## Orden de Implementacion

`
SPRINT 1 (GPS ~ 4h)
 A1: Activar Haversine en TimeTrackingService
 A1: Actualizar VisitService::startVisit() con GPS
 A1: Nuevo metodo OperationController::checkout()
 A2: Captura GPS en MyAssignments.jsx
 A2: Badge GPS en InProgress.jsx
 A3: Ruta checkout

SPRINT 2 (Activos Backend ~ 6h)
 B1: Migracion asset_id en dynamic_inspections
 B2: Modelos Asset.php y AssetType.php
 B2: Relacion en DynamicInspection
 B3: Event InspectionCompleted + Listener SyncAssetMetadata
 B4: AssetObserver
 B5: Transicion automatica fuera_de_servicio

SPRINT 3 (Activos Frontend ~ 8h)
 B6: AssetController (CRUD + multi-tenancy)
 B6: Assets/Index.jsx (KPIs naranja)
 B6: Assets/Show.jsx (ficha + timeline)
 B7: Paso 0 en Wizard (seleccion de activo)
 B6: Rutas en web.php

SPRINT 4 (QR ~ 6h)
 C1: npm install html5-qrcode
 C2: QrScannerModal.jsx
 C3: QrController + rutas /scan/*
 C4: Integracion QR en Wizard
 C5: Integracion QR en asistencia de capacitaciones
`

---

## Reglas SRE

1. **GPS no bloqueante:** Nunca bloquear el inicio de la visita. Si se deniega el GPS: continuar, marcar gps_verified = false, registrar en logs.
2. **Inmutabilidad post-firma:** Assets y DynamicInspections vinculadas a VisitCertificate con status = signed son de solo lectura via Policy.
3. **Eager Loading obligatorio:** Asset::with(['assetType', 'sede', 'empresa']) siempre en el controlador.
4. **Multi-tenancy:** Todo endpoint filtra por consultora_id del usuario. Bypass para SuperAdmin.
5. **Audit Trail:** Toda transicion automatica de assets.status se registra en audit_logs con action = 'auto_status_change'.
6. **Color Naranja:** Todo componente del Modulo de Activos usa ep-primary / orange-500.

---

## Checklist de Verificacion Final

- [ ] GPS check-in se guarda en la DB al iniciar visita
- [ ] GPS check-out se guarda al finalizar la visita
- [ ] Badge GPS aparece en InProgress.jsx con distancia correcta
- [ ] Formula Haversine retorna distancias precisas
- [ ] asset_id se guarda en dynamic_inspections al finalizar checklist
- [ ] SyncAssetMetadata actualiza assets.metadata con respuestas vinculadas
- [ ] AssetObserver registra cambios en audit_logs
- [ ] Activos con inspeccion fallida pasan a fuera_de_servicio automaticamente
- [ ] CRUD de activos funcional con filtros de sede/empresa
- [ ] QR de activo resuelve y pre-selecciona en el Wizard
- [ ] QR de empleado pre-llena asistencia en capacitaciones
- [ ] php artisan test en verde al finalizar cada sprint

---

*Documento de planificacion tecnica - XamaSafe v2.3.0*
*Preparado para: Agente de Implementacion*
