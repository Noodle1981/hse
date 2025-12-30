# 📚 Sistema de Capacitaciones - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Registro de Asistencias ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema integral para gestionar capacitaciones presenciales y virtuales, con registro de asistencias mediante firma digital y generación automática de certificados PDF.

### URL de Acceso
```
http://localhost:8000/trainings              # Listado de capacitaciones (Admin)
http://localhost:8000/trainings/create       # Crear capacitación (Admin)
http://localhost:8000/trainings/{id}/edit    # Editar capacitación (Admin)
http://localhost:8000/trainings/{id}/attendance  # Tomar asistencia (Admin+Técnico)
http://localhost:8000/trainings/{id}/pdf     # Descargar certificado PDF
```

---

## 🏗️ Arquitectura

```
┌────────────────────────────────────────────────┐
│          LIVEWIRE COMPONENTS (3)               │
│  TrainingIndex | TrainingBuilder |             │
│  AttendanceTracker                             │
├────────────────────────────────────────────────┤
│              ELOQUENT MODELS (2)               │
│  Training | TrainingAttendance                 │
├────────────────────────────────────────────────┤
│              CONTROLLERS (1)                   │
│  TrainingPDFController                         │
├────────────────────────────────────────────────┤
│              SQLite DATABASE                   │
│  trainings | training_attendances              │
└────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Archivos

### Componentes Livewire

```
app/Livewire/Trainings/
├── TrainingIndex.php           # Listado con filtros
├── TrainingBuilder.php         # CRUD capacitaciones
└── AttendanceTracker.php       # Registro de asistencias
```

### Modelos

```
app/Models/
├── Training.php                # Capacitación
└── TrainingAttendance.php      # Asistencia
```

### Controladores

```
app/Http/Controllers/
└── TrainingPDFController.php   # Generación de PDFs
```

---

## 🔗 Rutas

| Ruta | Componente | Función | Rol |
|------|------------|---------|-----|
| `/trainings` | TrainingIndex | Listado | Admin |
| `/trainings/create` | TrainingBuilder | Crear | Admin |
| `/trainings/{id}/edit` | TrainingBuilder | Editar | Admin |
| `/trainings/{id}/attendance` | AttendanceTracker | Tomar asistencia | Admin+Técnico |
| `/trainings/{id}/pdf` | TrainingPDFController | Descargar PDF | Admin+Técnico |
| `/trainings/{id}/pdf/preview` | TrainingPDFController | Vista previa PDF | Admin+Técnico |

---

## 📊 Base de Datos

### Tabla: trainings

```sql
trainings
├── id
├── empresa_id (FK → empresas)
├── sede_id (FK → sedes)
├── user_id (FK → users, nullable) -- Técnico responsable
├── title                           -- Título de la capacitación
├── description                     -- Descripción
├── type                            -- 'presencial' | 'virtual'
├── date                            -- Fecha y hora
├── duration                        -- Duración en minutos
├── trainer_name                    -- Nombre del capacitador
├── status                          -- 'scheduled' | 'completed' | 'cancelled'
├── created_at
└── updated_at
```

### Tabla: training_attendances

```sql
training_attendances
├── id
├── training_id (FK → trainings)
├── user_id (FK → users, nullable)  -- Si es usuario del sistema
├── employee_name                   -- Nombre del asistente
├── dni                             -- DNI (7-8 dígitos)
├── signature                       -- Firma digital (Base64)
├── created_at
└── updated_at
```

---

## ✨ Características Principales

### 1. Tipos de Capacitación

**Presencial:**
- Capacitación en sede física
- Requiere asistencia presencial
- Firma digital en el momento

**Virtual:**
- Capacitación online
- Registro de asistencia remota
- Firma digital virtual

### 2. Gestión de Capacitaciones

**Campos principales:**
- Título
- Descripción
- Tipo (Presencial/Virtual)
- Fecha y hora
- Duración (minutos)
- Nombre del capacitador
- Estado (Programada/Completada/Cancelada)
- Empresa y Sede
- Técnico responsable (opcional)

**Estados:**
- `scheduled` - Programada
- `completed` - Completada
- `cancelled` - Cancelada

### 3. Registro de Asistencias

**Proceso:**
1. Técnico o Admin accede a `/trainings/{id}/attendance`
2. Ingresa datos del asistente:
   - Nombre completo
   - DNI (7-8 dígitos)
   - Firma digital
3. Sistema valida y registra
4. Previene duplicados por DNI

**Validaciones:**
- Nombre: Requerido, máximo 255 caracteres
- DNI: Requerido, formato 7-8 dígitos
- Firma: Requerida, formato Base64
- Duplicados: No permite mismo DNI en misma capacitación

### 4. Firma Digital

```javascript
// Integración con signature_pad.js
Canvas: 300x150px
Formato: Base64 PNG
Botones: Limpiar / Guardar
Almacenamiento: Campo 'signature' en BD
```

### 5. Generación de PDFs

**Características:**
- Generación automática con Spatie PDF
- Incluye datos de la capacitación
- Lista completa de asistentes con firmas
- Descarga o vista previa
- Nombre de archivo: `asistencias_{titulo}_{id}.pdf`

**Contenido del PDF:**
- Encabezado con datos de la capacitación
- Información de empresa y sede
- Fecha, hora y duración
- Capacitador
- Tabla de asistentes con:
  - Nombre
  - DNI
  - Firma digital
  - Fecha de registro

---

## 🔄 Flujo de Trabajo Completo

### 1. Administrador programa capacitación

```
Admin → /trainings/create
  ├─ Define título y descripción
  ├─ Selecciona tipo (Presencial/Virtual)
  ├─ Programa fecha y hora
  ├─ Define duración
  ├─ Ingresa nombre del capacitador
  ├─ Selecciona empresa y sede
  ├─ Asigna técnico responsable (opcional)
  └─ Guarda como "Programada"
```

### 2. Realización de la capacitación

```
Capacitador → Dicta la capacitación
  └─ En sede (presencial) o plataforma (virtual)
```

### 3. Registro de asistencias

```
Técnico/Admin → /trainings/{id}/attendance
  ├─ Visualiza datos de la capacitación
  ├─ Por cada asistente:
  │   ├─ Ingresa nombre completo
  │   ├─ Ingresa DNI
  │   ├─ Captura firma digital
  │   └─ Guarda asistencia
  └─ Repite para todos los asistentes
```

### 4. Generación de certificado

```
Usuario → /trainings/{id}/pdf
  ├─ Sistema genera PDF automáticamente
  ├─ Incluye todos los asistentes registrados
  ├─ Muestra firmas digitales
  └─ Descarga o muestra en navegador
```

### 5. Gestión posterior

```
Admin → /trainings
  ├─ Filtra por empresa, sede o búsqueda
  ├─ Ve cantidad de asistentes
  ├─ Edita capacitación (si no tiene asistencias)
  ├─ Cambia estado a "Completada"
  └─ Descarga certificados
```

---

## 🎨 Interfaz de Usuario

### TrainingIndex (Listado)

**Características:**
- Tabla con paginación (10 por página)
- Búsqueda por título
- Filtros por empresa y sede
- Contador de asistentes
- Acciones: Ver, Editar, Eliminar, Tomar Asistencia, PDF

**Columnas:**
- Título
- Tipo (Badge: Presencial/Virtual)
- Empresa
- Sede
- Fecha
- Estado (Badge: Programada/Completada/Cancelada)
- Asistentes (contador)
- Acciones

### TrainingBuilder (Crear/Editar)

**Formulario:**
```
┌─────────────────────────────────────┐
│ Datos de la Capacitación            │
├─────────────────────────────────────┤
│ Título: [________________]          │
│ Descripción: [____________]         │
│ Tipo: ○ Presencial ○ Virtual        │
│ Fecha y Hora: [____][____]          │
│ Duración (min): [____]              │
│ Capacitador: [________________]     │
│ Estado: [▼ Programada]              │
├─────────────────────────────────────┤
│ Ubicación                           │
├─────────────────────────────────────┤
│ Empresa: [▼ Seleccionar]            │
│ Sede: [▼ Seleccionar]               │
│ Técnico Responsable: [▼ Opcional]   │
├─────────────────────────────────────┤
│ [Cancelar] [Guardar Capacitación]   │
└─────────────────────────────────────┘
```

### AttendanceTracker (Tomar Asistencia)

**Secciones:**

1. **Información de la Capacitación**
   - Título, tipo, fecha
   - Empresa y sede
   - Capacitador
   - Total de asistentes registrados

2. **Registrar Nueva Asistencia**
   ```
   ┌─────────────────────────────────────┐
   │ Nombre Completo: [________________] │
   │ DNI: [________]                     │
   │ Firma: [Canvas de firma]            │
   │        [Limpiar] [Guardar]          │
   └─────────────────────────────────────┘
   ```

3. **Lista de Asistentes**
   - Tabla con nombre, DNI, firma (miniatura)
   - Ordenados por fecha de registro

---

## 🔐 Seguridad y Permisos

### Roles del Sistema

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver listado | ✅ | ❌ |
| Crear capacitación | ✅ | ❌ |
| Editar capacitación | ✅ | ❌ |
| Eliminar capacitación | ✅ (sin asistencias) | ❌ |
| Tomar asistencia | ✅ | ✅ |
| Descargar PDF | ✅ | ✅ |
| Ver PDF preview | ✅ | ✅ |

### Validaciones de Seguridad

**Eliminación:**
- Solo Admin puede eliminar
- No se puede eliminar si tiene asistencias registradas
- Mensaje de error si intenta eliminar con asistencias

**Asistencias:**
- No permite duplicados (mismo DNI en misma capacitación)
- Validación de formato de DNI argentino (7-8 dígitos)
- Firma obligatoria

---

## 📋 Funcionalidades Avanzadas

### 1. Filtros Inteligentes

**Por Empresa:**
- Selecciona empresa
- Automáticamente carga sedes de esa empresa
- Filtra capacitaciones

**Por Sede:**
- Requiere seleccionar empresa primero
- Filtra capacitaciones de esa sede específica

**Por Búsqueda:**
- Busca en título de capacitación
- Búsqueda en tiempo real

### 2. Prevención de Duplicados

```php
// Valida que el DNI no esté registrado en esta capacitación
$exists = TrainingAttendance::where('training_id', $training->id)
    ->where('dni', $dni)
    ->exists();

if ($exists) {
    return error('Esta persona ya registró su asistencia');
}
```

### 3. Contador de Asistentes

```php
// En el listado, muestra cantidad de asistentes
$trainings = Training::withCount('attendances')->get();

// Badge visual: "5 asistentes"
```

### 4. Integración con Constancias de Visita

```php
// Relación polimórfica
$training->constancias(); // Constancias que incluyen esta capacitación
```

---

## 📄 Generación de PDFs

### TrainingPDFController

**Métodos:**

1. **download($id)** - Descarga el PDF
   ```php
   return $pdf->download('asistencias_titulo_id.pdf');
   ```

2. **preview($id)** - Muestra en navegador
   ```php
   return $pdf->stream('asistencias_titulo_id.pdf');
   ```

### Vista PDF: `pdf.training-attendance`

**Estructura:**
```
┌─────────────────────────────────────┐
│ CERTIFICADO DE ASISTENCIA           │
├─────────────────────────────────────┤
│ Capacitación: {título}              │
│ Tipo: {presencial/virtual}          │
│ Fecha: {fecha}                      │
│ Duración: {minutos} minutos         │
│ Capacitador: {nombre}               │
│ Empresa: {empresa}                  │
│ Sede: {sede}                        │
├─────────────────────────────────────┤
│ LISTA DE ASISTENTES                 │
├─────────────────────────────────────┤
│ N° | Nombre | DNI | Firma           │
│ 1  | Juan   | 123 | [firma]         │
│ 2  | María  | 456 | [firma]         │
│ ...                                 │
└─────────────────────────────────────┘
```

---

## 🔄 Estados de Capacitación

### Ciclo de Vida

```
┌─────────────┐
│  scheduled  │ ← Creación inicial
└──────┬──────┘
       │
       ├─→ [Se dicta la capacitación]
       │
       ├─→ [Se toman asistencias]
       │
       ▼
┌─────────────┐
│  completed  │ ← Admin marca como completada
└─────────────┘

       O

┌─────────────┐
│  cancelled  │ ← Si se cancela
└─────────────┘
```

**Transiciones:**
- `scheduled` → `completed`: Cuando se completa
- `scheduled` → `cancelled`: Si se cancela
- `completed` → No cambia (final)
- `cancelled` → No cambia (final)

---

## 🧪 Casos de Uso

### Caso 1: Capacitación Presencial en Obra

```
1. Admin crea capacitación "Uso de EPP"
   - Tipo: Presencial
   - Empresa: Constructora ABC
   - Sede: Obra Centro
   - Fecha: 2024-12-30 09:00
   - Duración: 120 minutos

2. Técnico llega a la obra
   - Accede desde tablet/móvil
   - Abre /trainings/1/attendance

3. Dicta la capacitación

4. Al finalizar, registra asistentes:
   - Juan Pérez - DNI 12345678 - [firma]
   - María López - DNI 23456789 - [firma]
   - Carlos Ruiz - DNI 34567890 - [firma]

5. Genera PDF y entrega copia
```

### Caso 2: Capacitación Virtual

```
1. Admin crea capacitación "Seguridad en Altura"
   - Tipo: Virtual
   - Empresa: Minera XYZ
   - Sede: Oficina Central
   - Fecha: 2024-12-30 14:00
   - Duración: 90 minutos

2. Se envía link de Zoom/Meet a participantes

3. Durante o después de la capacitación:
   - Admin registra asistentes desde oficina
   - Cada uno firma digitalmente

4. PDF queda disponible para todos
```

### Caso 3: Capacitación con Múltiples Técnicos

```
1. Admin programa capacitación masiva
   - 50+ asistentes esperados

2. Asigna 3 técnicos para tomar asistencia

3. Cada técnico con tablet:
   - Registra grupo de asistentes
   - Todos se guardan en misma capacitación

4. PDF final incluye todos los asistentes
```

---

## 📊 Métricas y Reportes

### Información Disponible

**Por Capacitación:**
- Total de asistentes
- Fecha de realización
- Duración efectiva
- Estado actual

**Por Empresa:**
- Total de capacitaciones
- Total de asistentes capacitados
- Capacitaciones por tipo

**Por Sede:**
- Capacitaciones realizadas
- Promedio de asistentes

---

## 🎯 Ventajas del Sistema

| Característica | Beneficio |
|----------------|-----------|
| **Firma Digital** | Sin papeles, todo digital |
| **PDF Automático** | Certificados instantáneos |
| **Validación DNI** | Previene duplicados |
| **Tipos Flexibles** | Presencial y virtual |
| **Filtros Avanzados** | Búsqueda rápida |
| **Integración** | Con empresas, sedes, usuarios |
| **Móvil-friendly** | Registro desde tablet/móvil |
| **Trazabilidad** | Historial completo |

---

## 🔜 Roadmap Futuro

### Próximas Mejoras

1. **Notificaciones por Email**
   - Recordatorio antes de capacitación
   - Confirmación de asistencia
   - Envío automático de certificado

2. **Importación Masiva**
   - Importar lista de asistentes desde Excel
   - Pre-cargar asistentes esperados

3. **Códigos QR**
   - Generar QR para registro rápido
   - Escanear DNI automáticamente

4. **Evaluaciones**
   - Agregar test post-capacitación
   - Calificaciones y aprobación
   - Certificado solo si aprueba

5. **Estadísticas Avanzadas**
   - Dashboard de capacitaciones
   - Gráficos de tendencias
   - Reportes por período

6. **Integración con Calendario**
   - Sincronización con Google Calendar
   - Recordatorios automáticos

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists
- [CONTRATO_ANUAL.MD](./CONTRATO_ANUAL.MD) - Contrato anual

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de capacitaciones
- ✅ Tipos: Presencial y Virtual
- ✅ Registro de asistencias con firma digital
- ✅ Generación automática de PDFs
- ✅ Validación de DNI argentino
- ✅ Prevención de duplicados
- ✅ Integración con empresas y sedes
- ✅ Filtros avanzados
- ✅ Contador de asistentes
- ✅ Relación con constancias de visita

---

*EP-Back V2.0.0.0 | Sistema de Capacitaciones con Registro Digital | Diciembre 2024*
