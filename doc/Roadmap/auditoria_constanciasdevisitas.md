# Auditoría y Mejoras - Sistema de Constancias de Visita

## Contexto General

En este documento se analizan las mejoras necesarias para el proceso de visitas técnicas, desde la ejecución hasta la generación de constancias en PDF.

---

## CASOS DE USO Y FLUJOS DE VISITA

### Situaciones Identificadas

#### Caso 1: Visita Planificada con Tareas Asignadas
**Descripción**: El técnico debe cumplir una visita pactada por contrato anual o porque le asignaron tareas específicas desde los gestores.

**Flujo**:
1. Gestor asigna tareas: Checklists, Capacitaciones, Solicitudes EPP
2. Técnico ve tareas en "Mis Tareas" y en "Visita en Progreso"
3. Completa las tareas asignadas
4. Puede auto-asignarse tareas adicionales (checklists de plantillas, capacitaciones ad-hoc)
5. Finaliza con firma y observaciones

**Estado**: ✅ Funcional

**Mejoras pendientes**:
- [ ] Indicador de "Tareas Obligatorias" vs "Opcionales"
- [ ] Validar que tareas obligatorias estén completadas antes de finalizar
- [ ] Identificar checklists que deben hacerse siempre (investigar)

---

#### Caso 2: Visita con Una Sola Actividad
**Descripción**: El técnico realiza solo una tarea (ej: solo entrega EPP o solo un checklist).

**Estado**: ✅ Funcional - El PDF se genera correctamente con secciones vacías.

---

#### Caso 3: Visita Sin Poder Completar Tareas ⚠️
**Descripción**: El técnico no puede completar tareas por diversos motivos (sede cerrada, responsable ausente, falta de materiales, etc.)

**Estado**: ❌ NO IMPLEMENTADO

**Propuesta**:
Agregar al modal de finalización:
- Checkbox: "¿Hubo impedimentos para completar la visita?"
- Si marca: Campo de texto para describir impedimentos
- Mostrar en PDF si existe
- Permitir finalizar visita sin actividades si se justifica

**Prioridad**: 🟡 Media

---

#### Caso 4: Firma Pendiente por Responsable Ausente ⚠️
**Descripción**: Se completaron todas las tareas pero el responsable no está para firmar.

**Estado**: ❌ NO IMPLEMENTADO

**Propuesta**:
Sistema de firma por email con token único:

1. **Modal de finalización**:
   - Checkbox: "Firma pendiente (responsable ausente)"
   - Campo: Email del responsable
   - Genera token único

2. **Email automático**:
   - Link: `/visits/{id}/sign/{token}`
   - Vista pública (validada solo por token)
   - Permite firmar sin login

3. **Al firmar**:
   - Actualiza visit: status='completed', responsible_signature
   - Regenera PDF con firma
   - Notifica al técnico

**Campos de BD necesarios**:
```php
// Migración Laravel (compatible con SQLite)
Schema::table('visit_certificates', function (Blueprint $table) {
    $table->boolean('signature_pending')->default(false);
    $table->string('signature_token', 255)->nullable()->unique();
    $table->string('signature_email', 255)->nullable();
    $table->timestamp('signature_requested_at')->nullable();
    $table->timestamp('signature_completed_at')->nullable();
});
```

**Prioridad**: 🔴 Alta (caso frecuente en producción)

**Nota técnica - Emails en desarrollo**:
```php
// Usar mock de emails para no romper en desarrollo
// En .env:
MAIL_MAILER=log  // Los emails se guardan en storage/logs/laravel.log

// O usar Mailtrap para testing:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
```

---

## TRAZABILIDAD Y SEGUIMIENTO

### Hallazgos e Incidentes

**Reglas de negocio definidas**:
- 🔴 Alta severidad: **7 días** para resolver
- 🟡 Media severidad: **15 días** para resolver
- 🟢 Baja severidad: **30 días** para resolver

**Campos necesarios**:
```php
// Migración Laravel para hallazgos
Schema::table('hallazgos', function (Blueprint $table) {
    $table->unsignedBigInteger('assigned_to_user_id')->nullable();
    $table->date('due_date')->nullable();
    $table->timestamp('resolved_at')->nullable();
    $table->text('resolution_notes')->nullable();
    $table->unsignedBigInteger('follow_up_visit_id')->nullable();
    
    $table->foreign('assigned_to_user_id')->references('id')->on('users');
    $table->foreign('follow_up_visit_id')->references('id')->on('visit_certificates');
});

// Cálculo automático de due_date
$dueDate = match($hallazgo->severidad) {
    'alta' => now()->addDays(7),
    'media' => now()->addDays(15),
    'baja' => now()->addDays(30),
};
```

**Flujo de seguimiento**:
1. Hallazgo creado en visita → Estado: "Abierto"
2. Gestor lo ve en dashboard
3. Asigna a técnico con fecha límite
4. Técnico resuelve y adjunta evidencia
5. Gestor verifica y cierra
6. Queda registrado en historial de la sede

**Prioridad**: 🟡 Media-Alta

---

## FOTOS Y EVIDENCIA VISUAL

**Justificación**: Crítico para legalidad y credibilidad de los técnicos.

**Implementación**:
```php
// Migración Laravel para fotos
Schema::create('visit_photos', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('visit_id');
    $table->enum('type', ['hallazgo', 'incidente', 'general']);
    $table->unsignedBigInteger('related_id')->nullable();
    $table->string('file_path', 500);
    $table->text('caption')->nullable();
    $table->integer('file_size')->nullable();
    $table->string('mime_type', 50)->nullable();
    $table->timestamps();
    
    $table->foreign('visit_id')
          ->references('id')
          ->on('visit_certificates')
          ->onDelete('cascade');
});
```

**Reglas**:
- Máximo **5 fotos** por hallazgo/incidente
- Max 5MB por foto
- Formatos: JPG/PNG
- Optimizar antes de guardar (resize si >1920px)
- Captura directa desde cámara en móviles

**Prioridad**: 🔴 Alta

---

## GEOLOCALIZACIÓN (PREPARACIÓN FUTURA)

**Estrategia**: Preparar estructura de BD ahora, implementar funcionalidad después.

```php
// Migración Laravel para geolocalización
Schema::table('visit_certificates', function (Blueprint $table) {
    $table->decimal('start_latitude', 10, 8)->nullable();
    $table->decimal('start_longitude', 11, 8)->nullable();
    $table->decimal('end_latitude', 10, 8)->nullable();
    $table->decimal('end_longitude', 11, 8)->nullable();
    $table->json('location_metadata')->nullable();
});
```

**Nota**: Funciona en navegadores móviles con HTTPS. No requiere app nativa.

**Prioridad**: 🟢 Baja (implementación futura)

---

## NORMALIZACIÓN Y MEJORA DE PDFs

### Problema Identificado
Los PDFs actuales (checklists, capacitaciones, constancias de visita) tienen formatos inconsistentes y la constancia de visita carece de detalles suficientes.

### Objetivos
1. **Normalizar** todos los PDFs con colores y estructura de la plataforma EP
2. **Mejorar** la constancia de visita con información detallada de cada actividad
3. **Estandarizar** componentes reutilizables
4. **Preparar** soporte para logos personalizados por empresa

### Colores de la Plataforma EP
```css
/* Primarios */
--ep-primary: #FF6B35;      /* Naranja característico */
--ep-dark: #1E1E2D;         /* Azul oscuro */

/* Secundarios */
--ep-gray-dark: #2D3748;
--ep-gray: #718096;
--ep-gray-light: #A0AEC0;

/* Fondos */
--ep-bg-light: #F7FAFC;
--ep-bg-blue: #EBF8FF;
--ep-bg-green: #C6F6D5;
```

### Logo Dinámico (Preparación Multi-tenant)

**Estrategia**: Usar variable para logo en lugar de hardcodear "EP".

```php
// En el controlador PDF
$logoPath = $visit->empresa->logo_path ?? public_path('images/default-logo.png');

// En la vista PDF
@if(isset($logoPath) && file_exists($logoPath))
    <img src="{{ $logoPath }}" class="logo-img">
@else
    <div class="logo-box">EP</div>
@endif
```

**Preparación para futuro**:
```php
// Migración Laravel para logos personalizados (futuro)
Schema::table('empresas', function (Blueprint $table) {
    $table->string('logo_path', 500)->nullable();
    $table->string('pdf_primary_color', 7)->default('#FF6B35');
    $table->string('pdf_secondary_color', 7)->default('#1E1E2D');
});
```

**Por ahora**: Usar logo "EP" por defecto, pero estructura lista para cambiar.

### Estructura Normalizada para Todos los PDFs

#### 1. Header Estándar
```html
<table class="header-table">
    <tr>
        <td style="width: 70pt;">
            {{-- Logo dinámico --}}
            @if(isset($logoPath))
                <img src="{{ $logoPath }}" class="logo-img">
            @else
                <div class="logo-box">EP</div>
            @endif
        </td>
        <td>
            <div class="report-title">[TÍTULO]</div>
            <div class="report-metadata">[Tipo] • [Fecha]</div>
        </td>
        <td style="text-align: right;">
            <div class="status-badge">[ESTADO]</div>
            <div class="doc-id">ID: #[id]</div>
        </td>
    </tr>
</table>
```

#### 2. Secciones con Formato Consistente
- Fondo gris claro (#F7FAFC)
- Borde izquierdo naranja (#FF6B35)
- Títulos en mayúsculas

#### 3. Grids de Información
- Labels en gris claro, mayúsculas
- Valores en gris oscuro, bold

#### 4. Footer Estándar
- Texto centrado
- Información de generación
- Paginación

### Mejoras Específicas: Constancia de Visita

#### Problema Actual
❌ Solo dice: "Se completaron 3 checklists"
❌ Solo dice: "Se realizaron 2 capacitaciones"
❌ No muestra DETALLES de qué se hizo

#### Solución: Información Detallada

**1. Checklists Completados (DETALLADO)**
Para cada checklist:
- ✅ Nombre del checklist
- ✅ Categoría/Subcategoría
- ✅ Hora de inicio y fin
- ✅ Cantidad de ítems verificados
- ✅ Resultado: Conforme / No conforme
- ✅ Observaciones del técnico
- 📋 NUEVO: Desglose de ítems críticos
- 📋 NUEVO: Items marcados como "No Conforme"

**2. Capacitaciones Realizadas (DETALLADO)**
Para cada capacitación:
- ✅ Título y descripción
- ✅ Duración real
- ✅ Hora de inicio y fin
- ✅ Cantidad de asistentes
- 📋 NUEVO: Lista de asistentes (nombre + DNI)
- 📋 NUEVO: Temas tratados
- 📋 NUEVO: Materiales entregados

**3. Entregas de EPP (DETALLADO)**
Para cada entrega:
- ✅ Empleado receptor
- ✅ Fecha y hora
- 📋 NUEVO: Lista detallada de ítems entregados
- 📋 NUEVO: Talla/Tamaño de cada ítem
- 📋 NUEVO: Tipo de entrega (Nueva/Reposición)
- 📋 NUEVO: Firma del receptor (imagen)
- 📋 NUEVO: Observaciones de la entrega

**4. Hallazgos Reportados (DETALLADO)**
Para cada hallazgo:
- ✅ Título y descripción
- ✅ Severidad (con color)
- ✅ Ubicación específica
- 📋 NUEVO: Acción correctiva recomendada
- 📋 NUEVO: Plazo de resolución (7/15/30 días)
- 📋 NUEVO: Responsable asignado
- 📋 NUEVO: Fotos adjuntas (cuando se implemente)

**5. Incidentes Reportados (DETALLADO)**
- ✅ Gravedad del incidente
- ✅ Personas involucradas
- ✅ Acciones inmediatas tomadas
- 📋 NUEVO: Cronología del evento
- 📋 NUEVO: Medidas preventivas

### Nueva Estructura: Constancia de Visita

**Página 1: Portada y Resumen Ejecutivo**
- Logo (dinámico por empresa)
- Título y datos de la visita
- Resumen ejecutivo con contadores
- Duración total y estado

**Páginas 2-N: Detalles por Sección**
- Cada sección con información completa
- Tablas detalladas
- Observaciones y notas

**Última Página: Firmas y Cierre**
- Observaciones generales
- Firma digital del técnico
- Firma digital del responsable
- Fecha y hora de cierre

### Plan de Implementación

**Fase 1: Normalización de Estilos (1-2 días)**
- [ ] Crear archivo base de estilos CSS compartido
- [ ] Crear componentes reutilizables (header, footer, badges)
- [ ] Implementar logo dinámico con variable
- [ ] Actualizar training-attendance.blade.php
- [ ] Actualizar epp-delivery.blade.php
- [ ] Actualizar hallazgo-report.blade.php
- [ ] Actualizar inspection.blade.php

**Fase 2: Mejora de Constancia de Visita (2-3 días)**
- [ ] Rediseñar visit-certificate.blade.php con detalles
- [ ] Sección de checklists con desglose completo
- [ ] Sección de capacitaciones con lista de asistentes
- [ ] Sección de EPP con tabla detallada
- [ ] Sección de hallazgos con acciones correctivas
- [ ] Sección de incidentes con cronología
- [ ] Agregar resumen ejecutivo al inicio
- [ ] Agregar página de portada profesional

**Fase 3: Testing y Ajustes (1 día)**
- [ ] Probar generación de PDFs con datos reales
- [ ] Verificar paginación correcta
- [ ] Optimizar tamaño de archivo
- [ ] Validar impresión física
- [ ] Ajustar responsive para preview en navegador

---

## PRIORIZACIÓN GENERAL

### 🔴 CRÍTICO (Implementar Inmediatamente)
1. Sistema de firma pendiente por email
2. Fotos en hallazgos/incidentes

### 🟡 IMPORTANTE (Próximo Sprint)
3. Trazabilidad de hallazgos
4. Campo de impedimentos en visita
5. Normalización y mejora de PDFs

### 🟢 FUTURO (Preparar Ahora, Implementar Después)
6. Geolocalización
7. Checklists obligatorios
8. Logos personalizados por empresa

---

## NOTAS TÉCNICAS

### Emails en Desarrollo
```bash
# Opción 1: Log (más simple)
MAIL_MAILER=log

# Opción 2: Mailtrap (testing real)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### Logos Dinámicos
- Por ahora: Logo "EP" hardcodeado
- Estructura: Lista para variable `$logoPath`
- Futuro: Campo `logo_path` en tabla `empresas`

### Geolocalización
- Campos de BD: Agregar ahora
- Funcionalidad: Implementar después
- Compatible: Navegadores móviles con HTTPS