# PRD — XamaSafe: Plataforma de Gestión de Seguridad Laboral
**Versión:** 1.0 — MVP Mínima Viable (Post Ultra-Minimización)
**Fecha:** 2026-03-07
**Autor:** Producto XamaSafe

---

## 1. Visión General

### 1.1 ¿Qué es XamaSafe?

XamaSafe es una plataforma SaaS B2B de **gestión integral de Higiene y Seguridad Laboral** para consultoras y técnicos habilitados. Permite digitalizar el ciclo completo de la gestión de campo: visitas, checklists, capacitaciones, hallazgos, incidentes e informes.

### 1.2 Estado Actual — v1.0 MVP

El módulo EPP fue eliminado en esta versión para reducir la deuda técnica y enfocar el producto en su núcleo de valor. Toda la lógica de entrega, gestión de stock EPP y trazabilidad QR de EPP está documentada como **Ecosistema Futuro (Tienda EPP)** en la Sección 6 de este documento.

### 1.3 Arquitectura del Ecosistema Completo (Visión Futura)

```
XamaSafe (este repositorio)          Tienda EPP (plataforma separada)
────────────────────────────         ────────────────────────────────
• Gestión de visitas                 • Catálogo de EPP certificado
• Checklists dinámicos               • Proveedores verificados
• Capacitaciones                     • Órdenes de compra
• Hallazgos e incidentes             • Trazabilidad de lotes
• Informes Maestros                  • Cumplimiento Res. 18/2025
        │                                       │
        └──────────── API / Webhook ────────────┘
                  (Conexión futura planeada)
```

---

## 2. Funcionalidades del MVP v1.0 (Estado Actual)

### 2.1 Módulo de Administración (Gestor/Admin)

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Gestión de Empresas | ✅ Activo | CRUD de empresas clientes con CUI, CUIT, datos |
| Gestión de Sedes | ✅ Activo | Sedes por empresa con responsables y datos de ubicación |
| Gestión de Usuarios | ✅ Activo | Admins y técnicos por consultora |
| Gestión de Técnicos | ✅ Activo | Perfil técnico con especialidades y firma |
| Branding por Consultora | ✅ Activo | Logo y colores para PDFs personalizados |
| Contrato Anual | ✅ Activo | Dashboard de planificación anual de servicios |
| Informe Maestro | ✅ Activo | Informe mensual de gestión con PDF profesional |
| Bitácora del Sistema | ✅ Activo | Historial de cambios con filtros avanzados |

### 2.2 Módulo de Visitas Técnicas (Core del Producto)

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Gestión de Visitas | ✅ Activo | Creación y asignación de visitas a técnicos |
| Visita en Progreso | ✅ Activo | Interfaz de campo para el técnico durante la visita |
| Auto-asignación de Checklists | ✅ Activo | El técnico puede asignarse checklists en el momento |
| Capacitaciones en Visita | ✅ Activo | Registro de capacitaciones dictadas durante la visita |
| Hallazgos en Visita | ✅ Activo | Registro de hallazgos vinculados a la visita |
| Incidentes en Visita | ✅ Activo | Registro de incidentes vinculados a la visita |
| Constancia de Visita PDF | ✅ Activo | PDF con firma digital del responsable del cliente |
| Firma Remota | ✅ Activo | Envío de link por email si el responsable no está presente |
| Historial de Visitas | ✅ Activo | Filtros por estado, sede, técnico y fecha |

### 2.3 Módulo de Checklists

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Constructor de Checklists | ✅ Activo | Creación dinámica de checklists con múltiples tipos de respuesta |
| Asignación a Técnicos | ✅ Activo | Asignación por sede y usuario |
| Llenado en Campo | ✅ Activo | Wizard de inspección paso a paso |
| Historial de Inspecciones | ✅ Activo | Listado con filtros y PDF |
| Exportar Checklist a JSON | ✅ Activo | Backup de plantillas de checklists |
| Importar desde Seeder | ✅ Activo | Restauración de plantillas desde JSON |

### 2.4 Módulo de Capacitaciones

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Creación de Capacitaciones | ✅ Activo | Con detalle de tema, duración, sede |
| Asistencia de Personal | ✅ Activo | Toma de asistencia con firma |
| PDF de Capacitación | ✅ Activo | Constancia de capacitación con branding |
| Vinculación a Visitas | ✅ Activo | Las capacitaciones quedan ancladas a la visita del día |

### 2.5 Módulo de Hallazgos

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Registro de Hallazgos | ✅ Activo | Solo desde dentro de una visita técnica |
| Seguimiento de Estado | ✅ Activo | Pendiente → En Proceso → Resuelto → Verificado → Cerrado |
| Criticidad | ✅ Activo | Bajo / Medio / Alto / Crítico |
| Fotos adjuntas | ✅ Activo | Evidencia fotográfica del hallazgo |
| Vista Mis Hallazgos | ✅ Activo | Historial del técnico con KPIs personales |
| PDF de Hallazgo | ✅ Activo | Reporte individual del hallazgo |

### 2.6 Módulo de Informe Maestro

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Solicitud de Informe (Gestor) | ✅ Activo | El gestor asigna un informe mensual a un técnico |
| Redacción (Técnico) | ✅ Activo | El técnico redacta con datos mensuales pre-cargados |
| PDF Profesional | ✅ Activo | Portada, resumen, visitas, checklists, capacitaciones, hallazgos e incidentes |
| Integración con datos de Visita | ✅ Activo | Agrega automáticamente todo lo realizado en el mes para esa sede |

---

## 3. Arquitectura Técnica

### 3.1 Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend Reactivo | Livewire 4 + Alpine.js |
| CSS | Tailwind CSS |
| Base de Datos | MySQL |
| PDFs | Spatie Laravel PDF (Chromium headless) |
| Autenticación | Laravel Sanctum |
| QR | SimpleSoftware QrCode |

### 3.2 Roles del Sistema

| Rol | Descripción |
|---|---|
| `super_admin` | Acceso total al sistema XamaSafe |
| `Administrador` | Gestor de consultora: gestiona técnicos, empresas, informes |
| `Técnico` / `tecnico` | Realiza visitas, completa checklists, genera hallazgos |

### 3.3 Flujo Central de una Visita

```
Gestor crea visita y la asigna a un técnico
    ↓
Técnico ingresa a la visita (panel "En Progreso")
    ↓
Dentro de la visita puede:
    ├── Completar checklists asignados (o auto-asignarse uno nuevo)
    ├── Registrar capacitaciones dictadas en sede
    ├── Levantar hallazgos con criticidad y fotos
    └── Reportar incidentes
    ↓
Técnico finaliza visita
    ├── Ingresa observaciones generales
    ├── Nombre del responsable del cliente
    └── Firma digital en canvas (o envío remoto por email)
    ↓
Sistema genera Constancia de Visita en PDF
    └── Con datos de la empresa, técnico, firma y actividades del día
```

---

## 4. Reglas de Negocio Clave

| Regla | Descripción |
|---|---|
| **Hallazgos solo desde Visitas** | Un técnico no puede crear un hallazgo en forma aislada. Siempre debe estar dentro de una Visita en Progreso. Esto garantiza trazabilidad sellada. |
| **Informe Maestro con datos reales** | El wizard de redacción del informe pre-carga las visitas, checklists y capacitaciones del mes para esa sede. El técnico solo agrega el análisis profesional. |
| **Un técnico ve solo sus asignaciones** | Las visitas, los checklists pendientes y los informes se filtran automáticamente por `user_id`. |
| **Branding por consultora** | Cada consultora puede definir logo y colores que se aplican a todos los PDFs generados. |
| **Firma remota** | Si el responsable del cliente no está disponible al momento del cierre, el sistema envía un enlace por email para firma diferida. El informe queda en estado "pendiente de firma" hasta que se complete. |

---

## 5. Módulos Eliminados en v1.0 (Deuda Técnica Pagada)

Los siguientes módulos fueron removidos completamente para reducir la complejidad y enfocar el producto:

| Módulo | Estado | Razón |
|---|---|---|
| **Entrega de EPP** | ❌ Eliminado | Delegado a la futura Tienda EPP |
| **Gestión de Empleados** | ❌ Eliminado | Dependía del módulo EPP |
| **Catálogo de EPP** | ❌ Eliminado | Delegado a la futura Tienda EPP |
| **Alertas de Vencimiento EPP** | ❌ Eliminado | El job diario fue removido |
| **QR de Trazabilidad EPP** | ❌ Eliminado | Form parte del roadmap de la Tienda EPP |
| **Módulo de Riesgos (heatmap)** | ❌ Eliminado | Sin demanda en MVP |
| **Módulo Angular/Landing** | ❌ Eliminado | Código muerto |
| **Hub de Ejecuciones Avanzadas** | ❌ Eliminado | Pantalla intermedia innecesaria |

---

## 6. Ecosistema Futuro — Tienda EPP (Plataforma Separada)

### 6.1 Concepto

La Tienda EPP es una plataforma independiente que actuará como marketplace de EPP certificado. XamaSafe, como generador de demanda calificada desde el campo, se conectará con ella vía API cuando el técnico detecte EPP vencido o fuera de norma.

La intención de compra no vendrá de marketing, sino de **cumplimiento legal** diagnosticado por un profesional de H&S. Eso cambia radicalmente la tasa de conversión.

### 6.2 Modelo de Integración

```
XamaSafe (Plataforma A)
    │
    │  Técnico detecta EPP vencido en visita
    │  Sistema emite evento: { epp_item, cantidad, urgencia, empresa, sede, motivo }
    │
    ▼
[ Webhook / API ]
    │
    ▼
Tienda EPP (Plataforma B)
    ├── Proveedores certificados reciben solicitud de cotización
    ├── Empresa recibe comparativa validada por el técnico
    └── La compra registrada en B cierra el ciclo de trazabilidad en A
```

### 6.3 Beneficios del Consultor al Conectarse

Cuando un consultor conecte su cuenta XamaSafe a la Tienda EPP:

- **Descuentos por volumen** en proveedores verificados
- **Comisión por referidos**: cada compra generada desde un reporte de XamaSafe genera un % de comisión para la consultora
- **Historial de compras trazado**: las órdenes de la tienda cierran el ciclo de auditoría legal en XamaSafe
- **Catálogo pre-filtrado**: la tienda solo muestra productos compatibles con la norma detectada en el hallazgo

### 6.4 Datos que XamaSafe enviará a la Tienda EPP

```json
{
  "source": "xamasafe",
  "trigger_type": "vencimiento_vida_util | diagnostico_tecnico | evento_critico",
  "empresa": { "id": 12, "nombre": "Industrias XYZ S.A.", "cuit": "30-12345678-9" },
  "sede": { "id": 5, "nombre": "Planta Norte", "provincia": "Córdoba" },
  "epp_requerido": {
    "nombre": "Casco Clase B",
    "tipo": "Cabeza",
    "codigo_norma": "IRAM 3620",
    "cantidad_estimada": 20,
    "urgencia": "alta",
    "motivo": "Vencimiento detectado por técnico en visita #88"
  },
  "tecnico_responsable": { "nombre": "Omar Olivera", "matricula": "HyS-1234" },
  "timestamp": "2026-03-07T10:00:00-03:00"
}
```

### 6.5 Roadmap de Integración con Tienda EPP

| Fase | Descripción | Estado |
|---|---|---|
| **Fase A** | Re-incorporar módulo de Empleados y Catálogo EPP (base de datos) | ⏳ Futuro |
| **Fase B** | Re-incorporar entrega de EPP vinculada a visitas + PDF constancia SRT 299/11 | ⏳ Futuro |
| **Fase C** | Detección de EPP vencido desde checklists + stock interno | ⏳ Futuro |
| **Fase D** | Conexión vía API con Tienda EPP + mock para pruebas | ⏳ Futuro |
| **Fase E** | Módulo de comisiones y beneficios para consultoras conectadas | ⏳ Futuro |

---

## 7. Implementaciones Futuras — Tecnología

### 7.1 Certificación Blockchain de Constancias (Add-on Premium)

#### ¿Por qué blockchain?

La constancia de visita PDF actual es un documento editable. Con blockchain, el hash del documento queda anclado en una red descentralizada, haciendo que cualquier alteración posterior sea **indefendible en una auditoría legal o judicial**.

#### Propuesta Técnica

Al momento de **finalizar la visita y generar la constancia**, el sistema:

1. Calcula el hash **SHA-256** del PDF generado
2. Envía ese hash a la red **Polygon** (bajo costo, ~$0.01 USD por transacción)
3. Recibe el `tx_hash` de la transacción confirmada
4. Lo guarda en `visit_certificates.blockchain_tx_hash`
5. Imprime en el PDF un **QR de verificación** que apunta al explorador de bloques con el TxID

#### Modelo de Negocio (Opcional por Consultora)

El consultor elige activar esta opción desde su panel. Modalidades posibles:

| Modelo | Descripción |
|---|---|
| **Créditos prepagos** | Compra X créditos dentro de la plataforma; cada certificado anclado descuenta 1 crédito |
| **Plan Premium** | Suscripción mensual con blockchain ilimitado incluido |
| **Pay-per-use** | XamaSafe cobra por constancia anclada (ej: $0.50 USD por PDF) |

#### Cambios Técnicos Requeridos

```php
// En app/Livewire/Visits/VisitInProgress.php → finalizeVisit()

$pdfHash = hash('sha256', $pdfContent);

if ($consultora->blockchain_enabled) {
    $txHash = PolygonAnchorService::anchor($pdfHash);
    $visit->update(['blockchain_tx_hash' => $txHash]);
}
```

Tabla `visit_certificates`:
```sql
ALTER TABLE visit_certificates
    ADD COLUMN blockchain_tx_hash VARCHAR(100) NULL,
    ADD COLUMN blockchain_anchored_at TIMESTAMP NULL;
```

Tabla `consultoras`:
```sql
ALTER TABLE consultoras
    ADD COLUMN blockchain_enabled BOOLEAN DEFAULT FALSE,
    ADD COLUMN blockchain_credits INT DEFAULT 0;
```

#### Estado

| Item | Estado |
|---|---|
| Diseño técnico | ✅ Definido |
| Implementación | ⏳ Pendiente (Post-MVP) |
| Red objetivo | Polygon (MATIC) — bajo costo |
| Wallet management | A definir: billetera maestra XamaSafe o billetera propia por consultora |

### 7.2 Notificaciones Push / Email para Hallazgos

Sistema de alertas automáticas cuando un hallazgo crítico no tiene seguimiento en X días.

### 7.3 App Mobile (React Native o Flutter)

Versión nativa para técnicos con capacidades offline: 
- Completar checklists sin conexión (sincronización posterior)
- Tomar fotos de hallazgos
- Captura de firma en pantalla táctil

### 7.4 Dashboard de Auditoría para el Cliente (Empresa)

Portal de solo lectura para que las empresas clientes vean:
- Sus propios informes mensuales
- Estado de hallazgos abiertos
- Constancias de visita firmadas

---

## 8. Checklist de Auditoría del Sistema (Estado Actual)

> Esta sección sirve para validar periódicamente la integridad del sistema.

### 8.1 Rutas y Acceso

- [ ] Todas las rutas de técnico requieren autenticación + rol
- [ ] Todas las rutas de gestor requieren autenticación + rol administrador
- [ ] No existen rutas huérfanas apuntando a controladores o componentes eliminados
- [ ] Los redirects tras acciones (crear, finalizar) son correctos por rol

### 8.2 Vistas y Componentes Livewire

- [ ] No hay referencias a variables EPP en ninguna vista Blade
- [ ] No hay imports de modelos eliminados en ningún componente Livewire
- [ ] Los modales de visita en progreso no tienen secciones EPP
- [ ] Branding (logo + colores) se aplica a todos los PDFs

### 8.3 Base de Datos

- [ ] Tablas EPP han sido eliminadas (verificar en la DB del servidor)
- [ ] No hay claves foráneas rotas apuntando a tablas inexistentes
- [ ] Las migraciones se ejecutan correctamente en entorno limpio (`migrate:fresh`)

### 8.4 PDFs y Documentos

- [ ] PDF de Constancia de Visita se genera correctamente
- [ ] PDF de Informe Maestro incluye visitas, checklists, capacitaciones, hallazgos e incidentes
- [ ] PDFs de capacitaciones e inspecciones se generan con branding de la consultora

### 8.5 Seguridad

- [ ] Los técnicos solo ven sus propios informes y visitas
- [ ] Los gestores solo ven datos de su propia consultora
- [ ] No hay endpoints API sin autenticación
- [ ] Las firmas digitales se almacenan de forma segura

---

*Documento actualizado: 2026-03-07 | XamaSafe v1.0 MVP*
