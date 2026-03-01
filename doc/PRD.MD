# PRD — EPP SaaS + EPP Exchange (Nexo)
**Versión:** 0.4 — Trazabilidad Legal (Res. 18/2025)  
**Fecha:** 2026-03-01  
**Autor:** Producto XamaSafe  

---

## 1. Visión General

### 1.1 Descripción del Ecosistema

Este documento define dos plataformas interdependientes que forman un ecosistema completo:

| Plataforma | Nombre de trabajo | Rol |
|---|---|---|
| **Plataforma A** | EPP SaaS (este repositorio) | Gestión de seguridad laboral: checklists, visitas, capacitaciones, entrega de EPP, hallazgos |
| **Plataforma B** | EPP Exchange | Marketplace de conexión entre empresas que necesitan EPP y proveedores certificados. **Sin stock propio.** |

### 1.2 El "Nexo"

La Plataforma A es el **generador de demanda calificada**. Cuando el técnico de H&S detecta en campo que un EPP está fuera de norma, el sistema convierte ese diagnóstico técnico en una **señal de compra legalmente validada** que alimenta la Plataforma B.

La intención de compra no viene de marketing, viene de cumplimiento legal. Eso cambia radicalmente la conversión.

```
Plataforma A (EPP SaaS)
    │
    │  Emite evento: { epp_item, cantidad, urgencia, empresa, sede, motivo }
    │
    ▼
[ API de Integración / Webhook ]
    │
    ▼
Plataforma B (EPP Exchange)
    │
    ├── Proveedores certificados reciben la solicitud de cotización
    ├── Empresa recibe comparativa de precios aprobados por el técnico
    └── La compra registrada en B cierra el ciclo de trazabilidad en A
```

---

## 2. Plataforma A — EPP SaaS (MVP Actual)

### 2.1 Funcionalidades del MVP

- ✅ Gestión de sedes y empresas
- ✅ Checklists dinámicos con respuestas y hallazgos
- ✅ Visitas con constancias PDF firmadas digitalmente
- ✅ Capacitaciones y asistencias
- ✅ Entrega de EPP digitalizada (Res. SRT 299/11)
- ✅ Historial de EPP por empleado
- ✅ Hallazgos con seguimiento y fotos
- ✅ Contrato anual

### 2.2 Módulo de EPP — Estado actual de la base de datos

#### Tabla `epp_items` (Catálogo de EPP)

| Campo actual | Estado | Nota |
|---|---|---|
| `nombre` | ✅ Existe | |
| `tipo` | ✅ Existe | Cabeza, Ocular, Auditiva, etc. |
| `vida_util_meses` | ✅ Existe | Vida útil por uso, en meses |
| `codigo_norma` | ✅ Existe | Ej: IRAM 3620 |
| `descripcion` | ✅ Existe | |
| `vida_util_fabricante_anios` | ❌ **Falta** | Vencimiento desde fabricación (Ej: arnés 10 años sin usar) |
| `stock_actual` | ✅ Existe | Stock en almacén interno de la empresa/consultora |
| `stock_seguridad` | ❌ **Falta** | Punto de pedido: si el stock baja de esto, sugiere comprar |
| `exchange_product_id` | ❌ **Falta** | ID del producto en EPP Exchange (FK externa, nullable) |
| `unidad_medida` | ❌ **Falta** | Par, Unidad, Caja |
| `requiere_control_critico` | ❌ **Falta** | Bool: si este EPP activa baja obligatoria ante impacto/caída |

#### Tabla `epp_delivery_items` (EPP entregado por acto)

| Campo actual | Estado | Nota |
|---|---|---|
| `epp_delivery_id` | ✅ Existe | |
| `epp_item_id` | ✅ Existe | |
| `cantidad` | ✅ Existe | |
| `tipo_entrega` | ✅ Existe | inicial/reposicion_rotura/reposicion_fecha |
| `lote` | ❌ **Falta** | Número de lote del fabricante |
| `fecha_fabricacion` | ❌ **Falta** | Base para calcular vida útil de fábrica |
| `fecha_vencimiento_fabricante` | ❌ **Falta** | Fecha dura de vencimiento (Res. 18/2025) |
| `codigo_qr_trazabilidad` | ❌ **Falta** | Código único para escaneo en visitas (Res. 18/2025) |
| `estado` | ❌ **Falta** | enum: activo / dado_de_baja / vencido / reemplazado |
| `motivo_baja` | ❌ **Falta** | nullable: impacto / vencimiento / deterioro / otro |
| `fecha_baja` | ❌ **Falta** | nullable |
| `exchange_order_id` | ❌ **Falta** | nullable: ID del pedido en EPP Exchange que repuso este ítem |

### 2.3 Nuevos campos requeridos — Migración propuesta

Los campos faltantes se agregarán mediante migraciones en dos fases:

**Fase 1 — MVP (necesarios para los triggers del Nexo y Control de Inventario):**
- `epp_items`: `vida_util_fabricante_anios`, `requiere_control_critico`, `unidad_medida`, `stock_seguridad`
- `epp_delivery_items`: `lote`, `fecha_fabricacion`, `fecha_vencimiento_fabricante`, `estado`, `motivo_baja`, `fecha_baja`

**Fase 2 — Integración Exchange:**
- `epp_items`: `exchange_product_id`
- `epp_delivery_items`: `codigo_qr_trazabilidad`, `exchange_order_id`

---

## 3. Lógica de Triggers — Cuándo el Sistema Notifica

El sistema generará una **Alerta de Reemplazo** ante cualquiera de estos tres criterios:

### Trigger 1 — Diagnóstico del Técnico (más prioritario)

**Origen:** Checklist con ítem de tipo crítico marcado "No Conforme".  
**Condición:** El ítem del checklist tiene vinculado un `epp_item_id`.  
**Acción:** El `epp_delivery_item` correspondiente al empleado pasa a `estado = 'dado_de_baja'`.

### Trigger 2 — Vencimiento de Vida Útil

El sistema evalúa diariamente dos fechas:

```
vencimiento_por_uso = delivery_date + (epp_items.vida_util_meses * 30 días)
vencimiento_de_fabrica = epp_delivery_items.fecha_fabricacion + (epp_items.vida_util_fabricante_anios * 365 días)
fecha_vencimiento = MIN(vencimiento_por_uso, vencimiento_de_fabrica, epp_delivery_items.fecha_vencimiento_fabricante)
```

**Alerta anticipada:** 30 días antes de la fecha de vencimiento más próxima.

### Trigger 3 — Evento Crítico (Incidente)

**Origen:** Registro de incidente donde se documenta uso de EPP durante el evento.  
**Condición:** `epp_items.requiere_control_critico = true` (ej: arnés, casco clase B).  
**Acción:** Baja automática del lote + Verificación de stock.

### Paso Intermedio: Validación de Inventario Local (Stock)

Antes de que un Trigger genere una Solicitud a la Tienda (EPP Exchange), el sistema EPP SaaS DEBE evaluar el inventario interno:

1. **La necesidad:** El sistema calcula la cantidad de EPP a reponer (ej: 20 cascos dados de baja).
2. **Evaluación de Depósito:** Revisa `epp_items.stock_actual` vs. `epp_items.stock_seguridad`.
3. **Decisión:**
   - **Escenario A (Stock Suficiente):** Si (`stock_actual` - necesidad) >= `stock_seguridad`: El sistema simplemente descuenta el stock local y genera el comprobante de entrega, notificando al pañolero interno. No contacta a la Tienda.
   - **Escenario B (Quiebre de Stock inminente):** Si hay stock para cubrir la necesidad pero el depósito quedaría por debajo del `stock_seguridad`, el sistema envía el EPP pero **también dispara** un Trigger de reposición a EPP Exchange para alertar al comprador que "El stock llegó a nivel crítico".
   - **Escenario C (Sin Stock):** No hay stock para cubrir la necesidad. El sistema bloquea el uso (informando la falta de EPP) y **dispara inmediatamente** el pedido a EPP Exchange.

---

## 3.5 Marco Legal de Trazabilidad — Resolución 18/2025

> **Vigencia:** 1° de octubre de 2025. Reemplaza el antiguo "Sello S" de IRAM.

### Qué exige la norma

Todo EPP que ingresa al mercado argentino **a partir del 1/10/2025** debe incluir un **código QR visible e indeleble** en el producto o su envase primario, colocado antes de la comercialización. El QR debe dar acceso inmediato a:

| Dato | Descripción |
|------|-------------|
| **Declaración Jurada de Conformidad** | Del fabricante o importador |
| **Ficha técnica completa** | Norma aplicada, tipo de riesgo cubierto |
| **Certificación vigente** | Qué certifica, emitida por qué organismo |
| **Número de lote** | Identificación del lote de fabricación |
| **Fechas de fabricación y vencimiento** | Para proyección de vida útil |

> **Importante:** Los productos fabricados **antes** de la norma aún pueden circular sin QR hasta agotar stock. La plataforma debe soportar ambos modos: registro por QR (norma nueva) y carga manual de sello S (norma vieja).

### Dos dimensiones de QR en el Nexo

La plataforma maneja **dos tipos de QR complementarios**:

```
QR del fabricante (Res. 18/2025)              QR de XamaSafe (por acto de entrega)
─────────────────────────────────             ──────────────────────────────────────
• Identifica el PRODUCTO                       • Identifica la ASIGNACIÓN al trabajador
• Contiene lote, norma, certificación          • Contiene delivery_item_id único (UUID)
• Lo coloca el fabricante/importador           • Lo genera la plataforma al hacer entrega
• Alimenta la ficha del EPP Exchange           • Se imprime en la constancia SRT 299/11
• Permanece en el producto siempre             • El técnico lo escanea en visitas futuras
```

### Cómo aprovecha el Nexo este QR

```
EPP en campo (casco, arnés, etc.)
    └── QR Res. 18/2025 (del fabricante)  ← Ya viene en el producto
         └── Técnico escanea con app XamaSafe
              └── Sistema valida:
                   ├── ¿Producto original? (cotejando DB Exchange / fabricante)
                   ├── ¿Estado del lote? (¿alguna unidad del lote fue dada de baja?)
                   ├── ¿Vida útil vigente? (fecha fabricación + vida_util_fabricante_anios)
                   └── ¿Está asignado a este trabajador? (cross con epp_delivery_items)
```

### Marcas relevantes ya alineadas

| Marca | Tecnología | Relevancia para el Nexo |
|-------|-----------|------------------------|
| **Libus** | QR en documentación y brochures | Permite descargar certificaciones |
| **MSA Safety** | MSA id Tag (tag digital por dispositivo) | Asignación digital por trabajador, sincronizable con nuestra API |
| **3M** | Sensor Uvicator (cambio de color UV) | Indicador físico de degradación; nuestro sistema puede capturarlo como dato |

---

## 4. Plataforma B — EPP Exchange (Contrato que debe cumplir)

### 4.1 Modelo de negocio

- EPP Exchange es un **marketplace broker**: conecta empresas con proveedores certificados.
- **No maneja stock propio.**
- Monetiza por comisión sobre transacciones cerradas o por suscripción a proveedores.

### 4.2 Contrato de datos que EPP Exchange DEBE proveer a EPP SaaS

Para que el ciclo de trazabilidad cierre en la Plataforma A, cuando una compra se realiza en EPP Exchange, esta DEBE enviar (vía webhook o API response) los siguientes datos:

```json
{
  "exchange_order_id": "EX-2026-00441",
  "empresa_id_externo": "XS-EMP-12",
  "epp_items": [
    {
      "exchange_product_id": "PROD-CASCO-CLASS-B-001",
      "nombre": "Casco Clase B IRAM 3620",
      "tipo": "Cabeza",
      "codigo_norma": "IRAM 3620",
      "vida_util_meses": 36,
      "vida_util_fabricante_anios": 5,
      "requiere_control_critico": false,
      "unidad_medida": "Unidad",
      "cantidad_entregada": 20,
      "lote": "LOT-2025-11-MSA",
      "fecha_fabricacion": "2025-11-01",
      "fecha_vencimiento_fabricante": "2030-11-01",
      "proveedor": {
        "id": "PROV-001",
        "nombre": "Proveedora Segura S.A.",
        "sello_conformidad": true,
        "certificaciones": ["IRAM 3620", "ANSI Z89.1"]
      }
    }
  ],
  "fecha_orden": "2026-03-01T15:00:00Z"
}
```

### 4.3 Lo que EPP SaaS ENVIARÁ a EPP Exchange al generar la alerta

```json
{
  "source": "eppsaas",
  "trigger_type": "vencimiento_vida_util | diagnostico_tecnico | evento_critico",
  "empresa": {
    "id": 12,
    "nombre": "Industrias XYZ S.A.",
    "cuit": "30-12345678-9"
  },
  "sede": {
    "id": 5,
    "nombre": "Planta Norte",
    "provincia": "Córdoba"
  },
  "epp_requerido": {
    "epp_item_id": 3,
    "nombre": "Casco Clase B",
    "tipo": "Cabeza",
    "codigo_norma": "IRAM 3620",
    "cantidad_estimada": 20,
    "urgencia": "alta",
    "motivo": "Vencimiento de fibras UV — detectado por técnico en visita #88"
  },
  "tecnico_responsable": {
    "nombre": "Omar Olivera",
    "matricula": "HyS-1234"
  },
  "timestamp": "2026-03-01T13:00:00-03:00"
}
```

---

## 5. Mock de EPP Exchange para el MVP

Durante el desarrollo del MVP, EPP Exchange no existirá. El sistema leerá un archivo JSON local que simula las respuestas de la API de la tienda. Esto permite desarrollar y probar la UI completa sin depender de la Plataforma B.

### 5.1 Ubicación del mock

```
storage/app/mock_exchange/
  ├── products.json       ← Catálogo de productos disponibles en la tienda
  ├── providers.json      ← Proveedores certificados  
  └── quotes.json         ← Respuestas de cotización simuladas
```

### 5.2 `products.json` — Catálogo simulado de EPP Exchange

```json
{
  "products": [
    {
      "exchange_product_id": "PROD-CASCO-CLASS-B-001",
      "nombre": "Casco Seguridad Clase B - Hard Hat",
      "marca": "MSA Safety",
      "tipo": "Cabeza",
      "codigo_norma": "IRAM 3620",
      "vida_util_meses": 36,
      "vida_util_fabricante_anios": 5,
      "requiere_control_critico": false,
      "unidad_medida": "Unidad",
      "precio_referencia_ars": 12500,
      "imagen_url": "https://placehold.co/200x200?text=Casco",
      "certificaciones_vigentes": ["IRAM 3620", "ANSI Z89.1-2014"],
      "sello_conformidad_iram": true,
      "proveedores_disponibles": ["PROV-001", "PROV-003"]
    },
    {
      "exchange_product_id": "PROD-ARNES-001",
      "nombre": "Arnés Anticaída Cuerpo Completo",
      "marca": "Petzl",
      "tipo": "Anticaída",
      "codigo_norma": "IRAM 3625",
      "vida_util_meses": 60,
      "vida_util_fabricante_anios": 10,
      "requiere_control_critico": true,
      "unidad_medida": "Unidad",
      "precio_referencia_ars": 87000,
      "imagen_url": "https://placehold.co/200x200?text=Arnes",
      "certificaciones_vigentes": ["IRAM 3625", "EN 361:2002"],
      "sello_conformidad_iram": true,
      "proveedores_disponibles": ["PROV-002"]
    },
    {
      "exchange_product_id": "PROD-CALZADO-001",
      "nombre": "Bota de Seguridad Punta de Acero",
      "marca": "Seguropie",
      "tipo": "Pies",
      "codigo_norma": "IRAM 3609",
      "vida_util_meses": 12,
      "vida_util_fabricante_anios": 3,
      "requiere_control_critico": false,
      "unidad_medida": "Par",
      "precio_referencia_ars": 35000,
      "imagen_url": "https://placehold.co/200x200?text=Bota",
      "certificaciones_vigentes": ["IRAM 3609"],
      "sello_conformidad_iram": true,
      "proveedores_disponibles": ["PROV-001", "PROV-002", "PROV-003"]
    },
    {
      "exchange_product_id": "PROD-GUANTE-NITRILO-001",
      "nombre": "Guante de Nitrilo Resistencia Química",
      "marca": "KleenGuard",
      "tipo": "Manos",
      "codigo_norma": "IRAM 3608",
      "vida_util_meses": 6,
      "vida_util_fabricante_anios": 2,
      "requiere_control_critico": false,
      "unidad_medida": "Par",
      "precio_referencia_ars": 4200,
      "imagen_url": "https://placehold.co/200x200?text=Guante",
      "certificaciones_vigentes": ["IRAM 3608", "EN 374"],
      "sello_conformidad_iram": false,
      "proveedores_disponibles": ["PROV-001"]
    }
  ]
}
```

### 5.3 `providers.json` — Proveedores simulados

```json
{
  "providers": [
    {
      "id": "PROV-001",
      "nombre": "Proveedora Segura S.A.",
      "cuit": "30-70123456-8",
      "provincia": "Buenos Aires",
      "contacto_email": "ventas@provegs.com.ar",
      "sello_conformidad": true,
      "scorecard": {
        "calidad_tecnica": 90,
        "confiabilidad_entrega": 85,
        "costo_beneficio": 80,
        "total": 86
      },
      "certificaciones": ["IRAM 3620", "IRAM 3609", "IRAM 3608"],
      "tiempo_entrega_dias": 3
    },
    {
      "id": "PROV-002",
      "nombre": "Equipo y Seguridad Andina",
      "cuit": "30-88765432-1",
      "provincia": "Córdoba",
      "contacto_email": "comercial@esandina.com",
      "sello_conformidad": true,
      "scorecard": {
        "calidad_tecnica": 95,
        "confiabilidad_entrega": 78,
        "costo_beneficio": 70,
        "total": 82
      },
      "certificaciones": ["IRAM 3625", "IRAM 3609", "EN 361:2002"],
      "tiempo_entrega_dias": 5
    },
    {
      "id": "PROV-003",
      "nombre": "Distribuidora EPP Norte",
      "cuit": "30-55443322-7",
      "provincia": "Tucumán",
      "contacto_email": "info@eppnorte.com.ar",
      "sello_conformidad": false,
      "scorecard": {
        "calidad_tecnica": 70,
        "confiabilidad_entrega": 90,
        "costo_beneficio": 92,
        "total": 84
      },
      "certificaciones": ["IRAM 3620"],
      "tiempo_entrega_dias": 2
    }
  ]
}
```

### 5.4 Clase de servicio para consumir el mock (y futura API real)

La integración se implementará mediante un **Service** de Laravel que abstrae la fuente de datos. En MVP leerá el JSON; en producción apuntará a la API real de EPP Exchange sin cambiar el código que lo consume:

```php
// app/Services/EppExchangeService.php

interface EppExchangeContract {
    public function getProducts(): Collection;
    public function getProviders(): Collection;
    public function submitReplacementRequest(array $data): array;
}

// MVP: Lee desde JSON mock
class MockEppExchangeService implements EppExchangeContract { ... }

// Producción: Llama a la API real
class ApiEppExchangeService implements EppExchangeContract { ... }
```

La selección entre uno u otro se hace mediante una variable de entorno:
```env
EPP_EXCHANGE_MODE=mock        # usa JSON local
EPP_EXCHANGE_MODE=api         # llama a la API real
EPP_EXCHANGE_API_URL=https://api.eppexchange.com
EPP_EXCHANGE_API_KEY=xxxxx
```

---

## 6. Flujo completo del Trigger a Cotización (MVP con Mock)

```
1. Técnico hace visita y marca ítem crítico "No Conforme" en checklist
   [OPCIÓN A] → Escanea QR del EPP con la app (Res. 18/2025) ← **Néxo legal**
   [OPCIÓN B] → Marca manualmente el epp_item vinculado al checklist (Sello S legacy)
       ↓
2. Sistema detecta el epp_item_id vinculado al ítem de checklist
   └── Verifica lote: ¿otras unidades del mismo lote también están en campo? ← Alerta masiva de lote
       ↓
3. Módulo de Inventario evalúa:
   ¿Hay `stock_actual` para cubrir la baja sin perforar el `stock_seguridad`?
   ├── SÍ → Se repone del pañol / depósito local. Fin.
   └── NO → Pasa al paso 4...
       ↓
4. Se crea EppReplacementAlert {
     epp_delivery_item_id, motivo, urgencia, cantidad_requerida_para_comprar
   }
       ↓
5. EppExchangeService::submitReplacementRequest() es llamado
   [MVP] → Devuelve datos del products.json + providers.json filtrados
   [PROD] → Llama a POST /api/v1/replacement-requests en EPP Exchange
       ↓
6. La empresa ve en su panel:
   "Se necesitan comprar 50 cascos (30 para reposición inmediata + 20 para stock de seguridad) — 3 proveedores disponibles"
   con precios referenciales, scorecard y enlace a certificación Res. 18/2025 de cada proveedor
       ↓
7. Empresa aprueba la cotización → (MVP: simula confirmación)
                                    (PROD: genera orden en EPP Exchange)
       ↓
8. EPP Exchange envía webhook a EPP SaaS con datos de la compra
   └── Incluye: exchange_order_id, lote, fecha_fabricacion, fecha_vencimiento_fabricante
       ↓
9. Al recibir la mercancía, el Gestor registra la entrega:
   └── Se generan nuevos epp_delivery_items con `lote`, `exchange_order_id`, y `codigo_qr_trazabilidad`
   └── El tecnico puede ahora entregar y el PDF de constancia incluye el QR ← Cierra ciclo legal
```

---

## 7. Roadmap de Implementación

### ✅ Fase 1 — Base de datos extendida *(Completada)*

- [x] Migración: campos faltantes en `epp_delivery_items` (lote, fechas, estado, motivo_baja)
- [x] Migración: campos faltantes en `epp_items` (vida_util_fabricante_anios, requiere_control_critico, unidad_medida)
- [x] Crear modelo `EppReplacementAlert` con sus relaciones

### ✅ Fase 2 — Motor de Triggers *(Completada)*

- [x] Job `EvaluarVencimientosEppJob` diario: evalúa vencimientos y genera alertas
- [ ] Vinculación de checklist ítem → epp_item (campo en DynamicQuestion o en la respuesta) ← *Pendiente Fase 2b*
- [ ] Disparador desde registro de incidente crítico ← *Pendiente Fase 2b*

### Fase 2b — Trazabilidad Legal QR (Res. 18/2025) ← **Siguiente sprint**

> Ancla legal de la plataforma. Sin esto, la trazabilidad es incompleta.

- [ ] Campo `codigo_qr_trazabilidad` en `epp_delivery_items` (UUID único auto-generado al entregar)
- [ ] QR incluido en el PDF de constancia de entrega SRT 299/11
- [ ] Vista pública `/qr/{token}` para escaneo en campo: muestra estado del ítem, norma, vencimiento
- [ ] Modo de escaneo en wizard de visita: técnico escanea QR del EPP y el sistema vincula automáticamente el `epp_delivery_item`
- [ ] Soporte para carga manual de datos (Sello S legacy): productos pre-norma sin QR físico
- [ ] Alerta de lote cruzado: si un ítem del lote X falla, alertar sobre todas las unidades del lote X en campo

### ✅ Fase 3 — Integración Mock Exchange + UI *(Completada)*

- [x] Crear `storage/app/mock_exchange/` con los JSON definidos en este PRD
- [x] Implementar `MockEppExchangeService` con interfaz `EppExchangeContract`
- [x] UI en panel de gestor: vista `/gestor/epp/alerts` — Alertas con cotizaciones del Exchange

### Fase 4 — Integración API Real *(Post-lanzamiento Exchange)*

- [ ] Implementar `ApiEppExchangeService`
- [ ] Configurar webhook receiver para confirmar compras
- [ ] Sistema de scoring/ranking de proveedores en tiempo real

---

## 8. Reglas de Negocio Clave

| Regla | Descripción |
|---|—-|
| **El técnico aprueba, no vende** | Ninguna cotización llega a la empresa sin validación del técnico de H&S. |
| **Un item bajado nunca se reactiva** | Una vez `motivo_baja` registrado, el `epp_delivery_item` es inmutable. Solo se crea uno nuevo. |
| **QR es obligatorio (post Oct-2025)** | Todo EPP entregado desde la plataforma debe generar `codigo_qr_trazabilidad`. Para productos pre-norma con Sello S: carga manual habilitada. |
| **Alerta de lote cruzado** | Si un operario reporta falla en lote X, el sistema alerta sobre TODAS las unidades del lote X activas en campo, no solo la del afectado. |
| **El proveedor sin QR vigente se marca** | El sistema indica si el proveedor en EPP Exchange comercializa bajo norma nueva (QR) o sello S legacy, pero no lo oculta. |
| **La reposición cierra el ciclo** | Un ítem dado de baja con `exchange_order_id` se considera trazado. Sin ese campo, la auditoría queda incompleta. |
| **Lectura bidireccional del QR** | El QR del fabricante (Res. 18/2025) alimenta la ficha del producto en la plataforma. El QR de XamaSafe (por entrega) acredita la asignación al trabajador. Son dos dimensiones complementarias. |
