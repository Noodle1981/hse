# 📝 Sistema de Constancias de Visitas - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Vinculación de Actividades ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema integral para generar constancias de visitas técnicas con firmas digitales duales (técnico + responsable), vinculación automática de actividades realizadas durante la visita, y generación de certificados PDF profesionales.

### URL de Acceso
```
http://localhost:8000/visit-certificates              # Todas las constancias (Admin)
http://localhost:8000/visit-certificates/create       # Crear constancia (Admin+Técnico)
http://localhost:8000/visit-certificates/my-history   # Mi historial (Admin+Técnico)
http://localhost:8000/visit-certificates/{id}/pdf     # Descargar PDF
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (3)               │
│  VisitCertificateManager |                     │
│  VisitCertificateWizard (4 pasos) |            │
│  VisitCertificateHistory                       │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (1)               │
│  GeneradorConstancia                           │
├────────────────────────────────────────────────┤
│              CONTROLLERS (1)                   │
│  VisitCertificatePDFController                 │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  generador_constancias |                       │
│  constancia_actividades (pivot polimórfica)    │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Visits/
├── VisitCertificateManager.php    # Gestión completa (Admin)
├── VisitCertificateWizard.php     # Wizard 4 pasos
└── VisitCertificateHistory.php    # Historial personal
```

### Modelos

```
app/Models/
└── GeneradorConstancia.php        # Constancia de visita
```

### Controladores

```
app/Http/Controllers/
└── VisitCertificatePDFController.php   # Generación de PDFs
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/visit-certificates` | VisitCertificateManager | Gestión completa | Admin |
| `/visit-certificates/create` | VisitCertificateWizard | Crear constancia | Admin+Técnico |
| `/visit-certificates/my-history` | VisitCertificateHistory | Mi historial | Admin+Técnico |
| `/visit-certificates/{id}/pdf` | VisitCertificatePDFController | Descargar PDF | Admin+Técnico |
| `/visit-certificates/{id}/pdf/preview` | VisitCertificatePDFController | Vista previa | Admin+Técnico |

---

## 📊 Base de Datos

### Tabla: generador_constancias

```sql
generador_constancias
├── id
├── sede_id (FK → sedes)
├── user_id (FK → users)              -- Técnico que realizó la visita
├── fecha_visita                      -- Fecha de la visita
├── tipo_visita                       -- Tipo de visita
├── observacion                       -- Observaciones/actividades
├── firma_instructor                  -- Firma del técnico (Base64)
├── date_firma_instructor             -- Fecha firma técnico
├── firma_responsable                 -- Firma del responsable (Base64)
├── date_firma_responsable            -- Fecha firma responsable
├── aclaracion_responsable            -- Nombre del responsable
├── email_responsable                 -- Email del responsable
├── token_responsable                 -- Token para firma remota
├── code                              -- Código de 4 dígitos
├── justificacion_firma               -- Justificación si no hay firma
├── duration                          -- Duración de la visita
├── created_at
└── updated_at
```

### Tabla: constancia_actividades (Pivot Polimórfica)

```sql
constancia_actividades
├── id
├── generador_constancia_id (FK → generador_constancias)
├── actividad_id                      -- ID de la actividad
├── actividad_type                    -- Tipo: DynamicInspection, Training, etc.
├── created_at
└── updated_at
```

**Tipos de actividades vinculables:**
- `DynamicInspection` - Inspecciones/Checklists
- `Training` - Capacitaciones
- `Hallazgo` - Hallazgos detectados

---

## ✨ Características Principales

### 1. Wizard de 4 Pasos

**Paso 1: Datos de la Visita**
- Selección de sede
- Tipo de visita
- Fecha de visita

**Paso 2: Observaciones**
- Descripción detallada de actividades realizadas
- Observaciones generales
- Máximo 3000 caracteres

**Paso 3: Vinculación de Actividades** ✨
- Inspecciones/Checklists realizados
- Capacitaciones dictadas
- Hallazgos detectados

**Paso 4: Firmas**
- Firma del técnico (obligatoria)
- Datos del responsable:
  - Email (opcional)
  - Nombre/Aclaración (opcional)
- Opción: "Firma no disponible"
  - Justificación requerida

### 2. Sistema de Firmas Duales

**Firma del Técnico:**
- Obligatoria al crear la constancia
- Captura digital con signature_pad.js
- Almacenada en Base64
- Fecha y hora automática

**Firma del Responsable:**
- Opcional al crear
- Puede firmarse después vía email
- Token único de seguridad
- Código de 4 dígitos
- Justificación si no está disponible

### 3. Vinculación de Actividades

**Relaciones Polimórficas:**
```php
// La constancia puede vincular múltiples tipos de actividades
$constancia->inspections()      // Checklists completados
$constancia->trainings()         // Capacitaciones dictadas
$constancia->hallazgosActividades() // Hallazgos detectados
```

**Beneficios:**
- Trazabilidad completa
- Evidencia de trabajo realizado
- Integración con contrato anual
- Reportes automáticos

### 4. Filtros Avanzados

**VisitCertificateManager (Admin):**
- Búsqueda por texto
- Estado (Pendiente/Completada)
- Técnico
- Empresa
- Sede
- Tipo de visita
- Rango de fechas

**VisitCertificateHistory (Técnico):**
- Solo sus constancias
- Búsqueda por texto
- Estado
- Empresa y Sede
- Rango de fechas

### 5. Estados de Constancia

**Pendiente:**
- `firma_responsable` es NULL
- Esperando firma del responsable
- Badge amarillo

**Completada:**
- `firma_responsable` tiene valor
- Ambas firmas presentes
- Badge verde

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Firma Inmediata

```
1. Técnico realiza visita a sede
   └─ Ejecuta actividades (checklists, capacitaciones, etc.)

2. Técnico crea constancia
   └─ /visit-certificates/create

3. Paso 1: Datos de la Visita
   ├─ Selecciona sede
   ├─ Define tipo: "Visita de Rutina"
   └─ Fecha: Hoy

4. Paso 2: Observaciones
   └─ Describe actividades realizadas

5. Paso 3: Vincula Actividades
   ├─ ✅ Checklist "Inspección EPP" (ID: 45)
   └─ ✅ Capacitación "Uso de Arnés" (ID: 12)

6. Paso 4: Firmas
   ├─ Firma técnico: [captura digital]
   ├─ Responsable presente: Sí
   ├─ Nombre: "Juan Pérez"
   └─ Firma responsable: [captura digital]

7. Sistema guarda constancia
   └─ Estado: Completada (ambas firmas)

8. PDF disponible inmediatamente
   └─ Incluye todas las actividades vinculadas
```

### Escenario 2: Firma Diferida (Responsable Ausente)

```
1. Técnico realiza visita
   └─ Responsable no está disponible

2. Técnico crea constancia
   └─ Completa Pasos 1-3 normalmente

3. Paso 4: Firmas
   ├─ Firma técnico: [captura digital]
   ├─ ☑️ "Firma no disponible"
   ├─ Justificación: "Responsable en reunión"
   ├─ Email: responsable@empresa.com
   └─ Nombre: "María López"

4. Sistema guarda constancia
   ├─ Estado: Pendiente
   ├─ Genera token único
   ├─ Genera código de 4 dígitos
   └─ TODO: Envía email al responsable

5. Responsable recibe email
   ├─ Link único con token
   └─ Código de seguridad

6. Responsable firma remotamente
   └─ Estado cambia a: Completada

7. PDF actualizado con ambas firmas
```

### Escenario 3: Firma Nunca Disponible

```
1. Técnico crea constancia
   └─ Responsable no firmará

2. Paso 4: Firmas
   ├─ Firma técnico: [captura digital]
   ├─ ☑️ "Firma no disponible"
   └─ Justificación: "Empresa sin representante en sede"

3. Sistema guarda constancia
   └─ Estado: Pendiente (pero justificado)

4. PDF incluye justificación
   └─ Válido sin firma del responsable
```

---

## 🎨 Interfaz de Usuario

### VisitCertificateManager (Admin)

**Características:**
- Tabla con paginación (15 por página)
- Vista de todas las constancias del sistema
- Filtros múltiples
- Acciones: Ver PDF, Editar (futuro)

**Columnas:**
- ID
- Técnico
- Empresa - Sede
- Tipo de Visita
- Fecha
- Estado (Badge)
- Actividades vinculadas (contador)
- Acciones

### VisitCertificateWizard (Crear)

**Paso 1: Datos de la Visita**
```
┌─────────────────────────────────────┐
│ Paso 1 de 4: Datos de la Visita    │
├─────────────────────────────────────┤
│ Sede: [▼ Seleccionar]               │
│ Tipo de Visita: [________________]  │
│ Fecha: [____-__-__]                 │
├─────────────────────────────────────┤
│                    [Siguiente →]     │
└─────────────────────────────────────┘
```

**Paso 2: Observaciones**
```
┌─────────────────────────────────────┐
│ Paso 2 de 4: Observaciones          │
├─────────────────────────────────────┤
│ Actividades Realizadas:             │
│ [_________________________________] │
│ [_________________________________] │
│ [_________________________________] │
│                                     │
│ 0/3000 caracteres                   │
├─────────────────────────────────────┤
│ [← Anterior]         [Siguiente →]  │
└─────────────────────────────────────┘
```

**Paso 3: Vinculación de Actividades**
```
┌─────────────────────────────────────┐
│ Paso 3 de 4: Actividades Realizadas │
├─────────────────────────────────────┤
│ Inspecciones/Checklists:            │
│ ☐ Inspección EPP - 2024-12-30       │
│ ☐ Checklist Vehículos - 2024-12-29  │
│                                     │
│ Capacitaciones:                     │
│ ☐ Uso de Arnés - 2024-12-30         │
│                                     │
│                                     │
│ Hallazgos:                          │
│ ☐ Escalera dañada - Gravedad Alta   │
├─────────────────────────────────────┤
│ [← Anterior]         [Siguiente →]  │
└─────────────────────────────────────┘
```

**Paso 4: Firmas**
```
┌─────────────────────────────────────┐
│ Paso 4 de 4: Firmas                 │
├─────────────────────────────────────┤
│ Firma del Técnico: *                │
│ [Canvas de firma]                   │
│ [Limpiar] [Guardar]                 │
├─────────────────────────────────────┤
│ Datos del Responsable:              │
│ Email: [________________] (opcional)│
│ Nombre: [________________] (opcional)│
│                                     │
│ ☐ Firma no disponible               │
│   Justificación: [________________] │
├─────────────────────────────────────┤
│ [← Anterior]    [Crear Constancia]  │
└─────────────────────────────────────┘
```

### VisitCertificateHistory (Mi Historial)

**Características:**
- Solo constancias del técnico autenticado
- Filtros personales
- Vista rápida de estado
- Descarga de PDFs

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver todas las constancias | ✅ | ❌ |
| Ver mi historial | ✅ | ✅ |
| Crear constancia | ✅ | ✅ |
| Vincular actividades | ✅ | ✅ |
| Descargar PDF | ✅ | ✅ |
| Filtrar por técnico | ✅ | ❌ (solo propias) |

### Validaciones

**Al crear:**
- Sede: Requerida, debe existir
- Tipo de visita: Requerido, máximo 255 caracteres
- Fecha: Requerida, no puede ser futura
- Observaciones: Requeridas, máximo 3000 caracteres
- Firma técnico: Requerida, formato Base64
- Justificación: Requerida si "firma no disponible"

**Seguridad:**
- Token único de 60 caracteres para firma remota
- Código de 4 dígitos adicional
- Gate authorization para ver PDFs
- Solo el técnico puede ver sus constancias en historial

---

## 📄 Generación de PDFs

### VisitCertificatePDFController

**Métodos:**

1. **download($id)** - Descarga el PDF
   ```php
   Gate::authorize('view', $constancia);
   return $pdf->download('constancia_visita_id_fecha.pdf');
   ```

2. **preview($id)** - Vista previa HTML
   ```php
   return view('pdf.visit-certificate-report', $data);
   ```

### Vista PDF: `pdf.visit-certificate-report`

**Estructura:**
```
┌─────────────────────────────────────┐
│ CONSTANCIA DE VISITA TÉCNICA        │
├─────────────────────────────────────┤
│ Constancia N°: {id}                 │
│ Empresa: {empresa}                  │
│ Sede: {sede}                        │
│ Tipo de Visita: {tipo}              │
│ Fecha: {fecha}                      │
├─────────────────────────────────────┤
│ ACTIVIDADES REALIZADAS              │
├─────────────────────────────────────┤
│ {observaciones}                     │
│                                     │
│ Actividades Vinculadas:             │
│ • Inspecciones: 2                   │
│ • Capacitaciones: 1                 │
│ • Hallazgos: 1                      │
├─────────────────────────────────────┤
│ DETALLE DE ACTIVIDADES              │
├─────────────────────────────────────┤
│ Inspecciones:                       │
│ - Inspección EPP (2024-12-30)       │
│ - Checklist Vehículos (2024-12-29)  │
│                                     │
│ Capacitaciones:                     │
│ - Uso de Arnés (2024-12-30)         │
│   Asistentes: 15                    │
├─────────────────────────────────────┤
│ FIRMAS                              │
├─────────────────────────────────────┤
│ Técnico:                            │
│ [Firma digital]                     │
│ {nombre técnico}                    │
│ Matrícula: {matricula}              │
│ Fecha: {fecha_firma}                │
│                                     │
│ Responsable:                        │
│ [Firma digital] o [Justificación]   │
│ {nombre responsable}                │
│ Fecha: {fecha_firma}                │
└─────────────────────────────────────┘
```

---

## 🔄 Integración con Otros Módulos

### 1. Contrato Anual

```php
// Las actividades vinculadas a constancias
// se contabilizan en el cumplimiento del contrato anual
$constancia->inspections; // Cuenta para objetivo de inspecciones
$constancia->trainings;   // Cuenta para objetivo de capacitaciones
```

### 2. Checklists/Inspecciones

```php
// Al completar una inspección, puede vincularse a constancia
$inspection = DynamicInspection::find($id);
$constancia->inspections()->attach($inspection->id);
```

### 3. Capacitaciones

```php
// Capacitación dictada durante la visita
$training = Training::find($id);
$constancia->trainings()->attach($training->id);
```


### 4. Hallazgos

```php
// Hallazgos detectados durante la visita
$hallazgo = Hallazgo::find($id);
$constancia->hallazgosActividades()->attach($hallazgo->id);
```

---

## 📊 Scopes y Helpers

### Scopes en GeneradorConstancia

```php
// Constancias pendientes de firma
GeneradorConstancia::pendingSignature()->get();

// Constancias completadas
GeneradorConstancia::completed()->get();
```

### Helper: allActivities()

```php
// Obtener todas las actividades vinculadas
$activities = $constancia->allActivities();
// Retorna array con todas las relaciones cargadas
```

---

## 🎯 Casos de Uso

### Caso 1: Visita de Rutina Completa

```
Contexto:
- Técnico visita obra de construcción
- Realiza múltiples actividades
- Responsable presente

Flujo:
1. Técnico ejecuta durante el día:
   - Inspección de EPP (checklist)
   - Capacitación sobre arnés (15 asistentes)
   - Detecta hallazgo (escalera dañada)

2. Al finalizar, crea constancia:
   - Sede: Obra Centro
   - Tipo: "Visita de Rutina Mensual"
   - Fecha: Hoy
   - Observaciones: Detalla actividades

3. Vincula actividades:
   - ✅ Inspección EPP
   - ✅ Capacitación Arnés
   - ✅ Hallazgo escalera

4. Firmas:
   - Técnico firma
   - Responsable (capataz) firma

5. PDF generado incluye:
   - Todas las actividades
   - Ambas firmas
   - Evidencia completa de la visita
```

### Caso 2: Visita de Emergencia

```
Contexto:
- Incidente en sede
- Técnico acude de urgencia
- Responsable no disponible

Flujo:
1. Técnico atiende emergencia:
   - Registra hallazgo crítico
   - Realiza inspección de seguridad

2. Crea constancia:
   - Tipo: "Visita de Emergencia"
   - Observaciones: Detalle del incidente

3. Vincula:
   - ✅ Hallazgo crítico
   - ✅ Inspección de seguridad

4. Firmas:
   - Técnico firma
   - ☑️ Firma no disponible
   - Justificación: "Emergencia, responsable evacuado"
   - Email: gerente@empresa.com

5. Sistema:
   - Guarda como pendiente
   - TODO: Envía email al gerente
   - PDF válido con justificación
```

### Caso 3: Auditoría Programada

```
Contexto:
- Auditoría trimestral
- Múltiples actividades planificadas
- Responsable coordinado

Flujo:
1. Técnico ejecuta plan de auditoría:
   - 5 inspecciones diferentes
   - 2 capacitaciones

2. Crea constancia:
   - Tipo: "Auditoría Trimestral Q4"
   - Observaciones: Resumen ejecutivo

3. Vincula todas las actividades:
   - ✅ 5 inspecciones
   - ✅ 2 capacitaciones

4. Firmas:
   - Técnico firma
   - Gerente de RRHH firma

5. PDF completo:
   - Evidencia de toda la auditoría
   - Cumplimiento del contrato anual
   - Trazabilidad total
```

---

## 📈 Métricas y Reportes

### Información Disponible

**Por Constancia:**
- Total de actividades vinculadas
- Estado de firma
- Técnico responsable
- Sede y empresa

**Por Técnico:**
- Total de visitas realizadas
- Visitas pendientes de firma
- Promedio de actividades por visita

**Por Empresa/Sede:**
- Frecuencia de visitas
- Tipos de visitas más comunes
- Actividades más realizadas

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Firmas Duales** | Validación técnico + responsable |
| **Vinculación de Actividades** | Trazabilidad completa |
| **Firma Diferida** | Flexibilidad para responsables ausentes |
| **Justificación** | Válido sin firma si está justificado |
| **PDF Automático** | Certificado profesional instantáneo |
| **Relaciones Polimórficas** | Vincula cualquier tipo de actividad |
| **Filtros Avanzados** | Búsqueda rápida y precisa |
| **Integración Total** | Conecta con todos los módulos |
| **Token Seguro** | Firma remota protegida |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Notificaciones por Email** ✨
   - Email al responsable con link de firma
   - Recordatorios automáticos
   - Confirmación de firma completada

2. **Firma Remota Completa**
   - Portal público para firmar
   - Validación de código de 4 dígitos
   - Historial de intentos

3. **Plantillas de Observaciones**
   - Plantillas predefinidas por tipo de visita
   - Autocompletado inteligente
   - Biblioteca de textos comunes

4. **Geolocalización**
   - Captura automática de ubicación
   - Validación de presencia en sede
   - Mapa en PDF

5. **Fotos de Evidencia**
   - Adjuntar fotos a la constancia
   - Galería en PDF
   - Compresión automática

6. **Firma Manuscrita Escaneada**
   - Opción de subir firma escaneada
   - Validación de formato
   - Alternativa a firma digital

7. **Estadísticas Avanzadas**
   - Dashboard de constancias
   - Gráficos de tendencias
   - Reportes por período

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists
- [CAPACITACIONES.MD](./CAPACITACIONES.MD) - Sistema de capacitaciones
- [CONTRATO_ANUAL.MD](./CONTRATO_ANUAL.MD) - Contrato anual

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de constancias de visitas
- ✅ Wizard de 4 pasos
- ✅ Firmas digitales duales
- ✅ Vinculación polimórfica de actividades:
  - Inspecciones/Checklists
  - Capacitaciones
  - Hallazgos
  - Hallazgos
- ✅ Firma diferida con token y código
- ✅ Justificación de firma no disponible
- ✅ Generación automática de PDFs
- ✅ Filtros avanzados
- ✅ Historial personal para técnicos
- ✅ Gestión completa para administradores
- ✅ Integración con contrato anual

---

*EP-Back V2.0.0.0 | Sistema de Constancias de Visitas con Vinculación de Actividades | Diciembre 2024*
