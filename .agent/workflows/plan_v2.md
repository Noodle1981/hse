# XamaSafe v2.0.0 — Plan de Implementación
## "Escalar lo que ya fue validado"

**Prerequisito:** Criterios de validación de v1.0.0 cumplidos (≥ 3 de 4)
**Branch:** `feature/assets-v2` desde `produccion`
**Principio:** Cada feature de esta versión tiene una pregunta de validación que la habilitó.

---

## Qué cambia entre v1 y v2

```
v1.0.0                          v2.0.0
──────                          ──────
"El Gestor ve sus activos"  →   "El Gestor reporta y el cliente se entera solo"
"Las alertas están en-app"  →   "Las alertas llegan por email sin entrar al sistema"
"El Técnico selecciona      →   "El Técnico escanea el QR y listo"
  el activo manualmente"
"Una tabla simple de flota" →   "Dashboard con semáforo por tipo y sede"
```

---

## Sprint 1 — Reportes de Cumplimiento (6h)
*Se habilita cuando: el Gestor preguntó "¿puedo exportar esto?" o "¿puedo mandárselo al cliente?"*

### B9 — Reporte de Cumplimiento por AssetType

**`app/Http/Controllers/AssetReportController.php`** — nuevo:
```php
// Columnas dinámicas según metadata_schema del tipo seleccionado
public function complianceReport(AssetType $type, Request $request): JsonResponse
// Filtros: sede_id, empresa_id, status, alert_status
// Output: JSON para tabla + stream CSV + PDF via DomPDF/Browsershot
```

**`resources/js/Pages/Gestor/Assets/Report.jsx`** — nuevo:
- Selector de `AssetType` → tabla se regenera con columnas del schema
- Semáforo por fila: 🟢 OK / 🟡 próximo a vencer / 🔴 vencido / ⚫ fuera de servicio
- KPIs en header: total / en alerta / fuera de servicio / próximos a vencer (30d)
- Botón `Exportar CSV` → descarga con columnas dinámicas
- Botón `Exportar PDF` → PDF con logo de la consultora + fecha de generación

**Rutas:**
```php
Route::get('/activos/reporte',     [AssetReportController::class, 'index'])->name('gestor.assets.report');
Route::get('/activos/reporte/csv', [AssetReportController::class, 'exportCsv'])->name('gestor.assets.report.csv');
Route::get('/activos/reporte/pdf', [AssetReportController::class, 'exportPdf'])->name('gestor.assets.report.pdf');
```

**Ejemplo de reporte generado para Matafuegos:**

| ID | Nombre | Sede | Presión | Vencimiento | Días restantes | Estado |
|---|---|---|---|---|---|---|
| MF-001 | Sector Depósito | Norte | 12 bar | 15/09/2026 | 76 días | 🟢 OK |
| MF-007 | Oficina Central | Sur | 8 bar | 20/07/2026 | 19 días | 🟡 Próximo |
| MF-012 | Depósito B | Norte | — | 01/06/2026 | -30 días | 🔴 Vencido |

---

## Sprint 2 — Dashboard de Flota (6h)
*Se habilita cuando: el cliente tiene ≥ 2 tipos de activos distintos cargados*

### B10 — Vista de Flota por Tipo

**`resources/js/Pages/Gestor/Assets/Index.jsx`** — modificar (hoy es tabla plana):
- Agregar cards por `AssetType` en la parte superior:
  ```
  ┌─────────────────┐  ┌──────────────────┐  ┌──────────────────┐
  │   Matafuegos    │  │  Baños Químicos  │  │  Camionetas 4x4  │
  │  24 activos     │  │   8 activos      │  │   12 activos     │
  │  🟢 20 / 🟡 3  │  │  🟢 5 / 🔴 3    │  │  🟢 10 / 🟡 2   │
  └─────────────────┘  └──────────────────┘  └──────────────────┘
  ```
- Click en card → tabla filtrada de esa flota con columnas del schema
- Vista global (todas) conservada como tab o toggle

**Mapa de calor por sede** (si la sede tiene coordenadas):
- Marker en mapa con color según cantidad de activos en alerta en esa sede
- Solo activar si la Sede tiene `latitud/longitud` seteados

---

## Sprint 3 — Ficha Pública del Activo (5h)
*Se habilita cuando: el Gestor quiere que el cliente final vea el historial sin acceso al panel*

### B11 — AssetPublicController

**`app/Http/Controllers/AssetPublicController.php`** — nuevo:
```php
// Generar token firmado al crear/editar activo
public function generatePublicUrl(Asset $asset): string
{
    return URL::signedRoute('asset.public.show', ['token' => encrypt($asset->id)]);
}

// Resolver el token y mostrar la ficha
public function show(string $token): Response
// Sin login requerido
// Solo lectura: metadata actual, estado, timeline de inspecciones
// No expone IDs internos ni info de otras empresas
```

**`resources/js/Pages/Public/AssetCard.jsx`** — nuevo (página pública):
- Header: nombre del activo, tipo, sede, estado (semáforo grande)
- Metadata actual en cards: cada campo del schema con su valor
- Timeline de inspecciones: fecha, técnico, resultado, hallazgos
- Gráfico de evolución de métricas numéricas (ej: presión a lo largo del tiempo)
- Footer: branding XamaSafe + logo de la consultora

**Ruta:**
```php
// Sin middleware auth
Route::get('/activos/publico/{token}', [AssetPublicController::class, 'show'])
     ->name('asset.public.show');
```

**Cómo llega el cliente a esta URL:**
- El Gestor copia el link desde la ficha del activo en `Assets/Show.jsx`
- O el sistema lo incluye automáticamente en el email de alerta (B12)

---

## Sprint 4 — Notificaciones Proactivas (4h)
*Se habilita cuando: el job de alertas (B8) genera Findings que nadie ve en-app a tiempo*

### B12 — AssetAlertNotification

**`app/Notifications/AssetAlertNotification.php`** — nuevo:
```php
// Canal: mail (+ broadcast si hay PWA)
public function toMail($notifiable): MailMessage
{
    return (new MailMessage)
        ->subject("⚠️ Alerta: {$this->asset->name} requiere atención")
        ->line("El campo **{$this->fieldLabel}** está en estado de alerta.")
        ->line("Valor actual: {$this->currentValue} | Límite: {$this->threshold}")
        ->action('Ver ficha del activo', $this->publicUrl); // link B11
}
```

**`app/Console/Commands/CheckAssetAlerts.php`** — modificar (ya existe de v1):
- Al crear un Finding → disparar `AssetAlertNotification` al Gestor
- Si `assetType.notification_emails` tiene emails externos → notificar también

**`resources/js/Pages/Gestor/AssetTypes/Index.jsx`** — modificar:
- Sección "Notificaciones": agregar/quitar emails externos que recibirán alertas
- Toggle: notificar también al Gestor propietario (default: on)

**Casos de uso:**
```
Matafuego vence en 30 días
  → Finding automático en XamaSafe (ya existe desde B8 v1.0.0)
  → Email a gestor@consultora.com (nuevo en B12)
  → Email a seguridad@clienteempresa.com (nuevo en B12, si está en notification_emails)
  → Email incluye link a ficha pública del activo (nuevo en B11)
```

---

## Sprint 5 — QR Scanning (6h) [OPTATIVO]
*Se habilita cuando: los técnicos se quejan de tener que buscar el activo en el selector*

### C1 — Dependencia
```bash
npm install html5-qrcode
```

### C2 — QrScannerModal.jsx
**`resources/js/Components/QrScannerModal.jsx`** — nuevo:
- Modal HUD oscuro premium que activa la cámara trasera
- Al detectar QR → resuelve `asset_id` o `employee_id` según tipo del código
- Timeout de 30 segundos con cierre automático
- Fallback: "Ingresar código manualmente"

### C3 — Endpoint de resolución QR
**`app/Http/Controllers/QrController.php`** — nuevo:
```php
// GET /scan/asset/{identifier} → JsonResponse con datos del activo
// GET /scan/employee/{id}      → JsonResponse con datos del empleado
```

### C4 — Integración QR en el Wizard
**`Wizard.jsx`** — Paso 0:
- Botón "⬛ ESCANEAR QR DEL EQUIPO" → abre QrScannerModal
- Al recibir `asset_id`: autoseleccionar y avanzar al Paso 1

### C5 — QR en Asistencia de Capacitaciones
**`InProgress.jsx`:**
- Botón "ESCANEAR CREDENCIAL" en panel de asistencia
- Al leer QR de empleado → pre-llenar nombre y DNI
- Flash verde de confirmación para velocidad en campo

---

## Orden de implementación v2.0.0

```
SPRINT 1 (Reportes ~ 6h)
  B9: AssetReportController (columnas dinámicas + filtros)
  B9: Report.jsx (tabla + semáforo + KPIs)
  B9: Export CSV + PDF

SPRINT 2 (Dashboard Flota ~ 6h)
  B10: Index.jsx → cards por AssetType + vista filtrada
  B10: Mapa de calor por sede (si hay coords)

SPRINT 3 (Ficha Pública ~ 5h)
  B11: AssetPublicController (token firmado)
  B11: AssetCard.jsx (página pública sin login)
  B11: Link en Assets/Show.jsx para copiar URL

SPRINT 4 (Notificaciones ~ 4h)
  B12: AssetAlertNotification (email)
  B12: Integración con CheckAssetAlerts
  B12: notification_emails en UI de AssetType

SPRINT 5 (QR ~ 6h) [OPTATIVO]
  C1: npm install html5-qrcode
  C2: QrScannerModal.jsx
  C3: QrController + rutas /scan/*
  C4: Integración QR en Wizard
  C5: Integración QR en asistencia de capacitaciones
```

---

## Checklist de Validación v2.0.0

### Reportes
- [ ] `Report.jsx` muestra columnas dinámicas correctas para Matafuego (presión, vencimiento)
- [ ] `Report.jsx` muestra columnas dinámicas correctas para Camioneta (km, VTV, seguro)
- [ ] Export CSV contiene todas las columnas del schema
- [ ] PDF generado incluye logo de la consultora y fecha

### Dashboard Flota
- [ ] Cards por AssetType muestran conteos correctos
- [ ] Click en card filtra la tabla al tipo seleccionado
- [ ] Semáforo refleja estado real según reglas del schema

### Ficha Pública
- [ ] URL firmada es accesible sin login
- [ ] URL no expone IDs internos
- [ ] URL expirada retorna 403
- [ ] Timeline muestra inspecciones en orden cronológico

### Notificaciones
- [ ] Email llega al Gestor cuando `CheckAssetAlerts` detecta una alerta
- [ ] Email llega a emails externos configurados en `notification_emails`
- [ ] Email contiene link a ficha pública (B11)
- [ ] No se duplica el email si el Finding ya estaba abierto

### QR (Optativo)
- [ ] QR de activo resuelve y pre-selecciona en Wizard
- [ ] QR de empleado pre-llena asistencia

### General
- [ ] `php artisan test` en verde
- [ ] Ningún endpoint nuevo expone datos de otra consultora

---

## Lo que v2.0.0 habilita comercialmente

| Feature | Propuesta de valor para el cliente |
|---|---|
| Reporte de cumplimiento PDF | "Acá está tu informe de matafuegos firmado digitalmente para la auditoría" |
| Dashboard de flota | "Tenés 3 baños con limpieza vencida en la sede Norte" |
| Ficha pública | "Escaneá este QR del matafuego para ver su historial completo" |
| Notificaciones | "Te avisamos 30 días antes sin que tengas que entrar al sistema" |
| QR scanning | "El técnico escanea y arranca la inspección en 2 segundos" |

---

*XamaSafe v2.0.0 — Escalar lo que fue validado*
*"No construir lo que no se validó es la decisión de ingeniería más importante."*
