# Sistema de Asignaciones Recurrentes - Especificación Técnica

## Estado Actual

Actualmente el sistema de asignaciones de checklists tiene:
- **Frecuencias soportadas**: Única vez, Semanal, Mensual, Trimestral, Anual
- **Campo de fecha**: `due_date` (fecha de vencimiento) - opcional
- **Limitación**: No hay lógica automática para manejar recurrencias

## Problema Identificado

La "Fecha de vencimiento" tiene diferentes significados según la frecuencia:
- **Única vez**: Fecha límite clara para completar el checklist
- **Recurrentes** (semanal/mensual/etc.): ¿Qué representa esta fecha?
  - ¿Fecha de inicio?
  - ¿Fecha límite del ciclo completo?
  - ¿Primera fecha de vencimiento?

## Propuesta de Solución: Sistema de Recurrencia Automática

### 1. Cambios en la Base de Datos

#### Migración: Agregar campos para recurrencia

```sql
ALTER TABLE checklist_assignments ADD COLUMN start_date DATE NULL;
ALTER TABLE checklist_assignments ADD COLUMN end_date DATE NULL;
ALTER TABLE checklist_assignments ADD COLUMN next_due_date DATE NULL;
ALTER TABLE checklist_assignments ADD COLUMN last_completed_at TIMESTAMP NULL;
ALTER TABLE checklist_assignments ADD COLUMN recurrence_count INT DEFAULT 0;
ALTER TABLE checklist_assignments ADD COLUMN parent_assignment_id BIGINT NULL;
```

**Campos explicados**:
- `start_date`: Fecha de inicio de la recurrencia
- `end_date`: Fecha límite hasta cuándo se repite (opcional, NULL = indefinido)
- `next_due_date`: Próxima fecha calculada automáticamente
- `last_completed_at`: Última vez que se completó
- `recurrence_count`: Contador de veces completado
- `parent_assignment_id`: Referencia a la asignación original (para historial)

### 2. Lógica de Funcionamiento

#### Para Asignaciones de "Única vez"
- `start_date` = NULL
- `due_date` = fecha límite
- `next_due_date` = NULL
- Comportamiento actual sin cambios

#### Para Asignaciones Recurrentes

**Al crear la asignación**:
1. Usuario selecciona frecuencia (ej: Mensual)
2. Usuario ingresa `start_date` (ej: 2026-02-01)
3. Usuario opcionalmente ingresa `end_date` (ej: 2026-12-31)
4. Sistema calcula `next_due_date` = `start_date`

**Al completar el checklist**:
1. Se marca `status = 'completed'`
2. Se actualiza `last_completed_at` = ahora
3. Se incrementa `recurrence_count`
4. Sistema calcula nueva `next_due_date`:
   - **Semanal**: `next_due_date + 7 días`
   - **Mensual**: `next_due_date + 1 mes`
   - **Trimestral**: `next_due_date + 3 meses`
   - **Anual**: `next_due_date + 1 año`
5. Si `next_due_date > end_date`, marcar asignación como finalizada
6. Crear nuevo registro de asignación con:
   - `status = 'pending'`
   - `due_date = next_due_date`
   - `parent_assignment_id` = ID de la asignación actual

### 3. Cambios en la UI

#### Formulario de Nueva Asignación

**Cuando frecuencia = "Única vez"**:
```
Fecha de Vencimiento: [____/____/____] (requerido)
```

**Cuando frecuencia = Recurrente**:
```
Fecha de Inicio: [____/____/____] (requerido)
Fecha de Fin: [____/____/____] (opcional - dejar vacío para indefinido)

ℹ️ El checklist se asignará automáticamente cada [frecuencia] 
   comenzando el [fecha_inicio]
```

#### Vista de Asignaciones

Agregar columnas:
- **Próximo Vencimiento**: Muestra `next_due_date` para recurrentes
- **Completados**: Muestra `recurrence_count` (ej: "3/12" si tiene end_date)
- **Estado**: 
  - "Activo" - recurrencia en curso
  - "Finalizado" - llegó a `end_date`
  - "Pausado" - temporalmente desactivado

### 4. Comando Artisan para Generar Recurrencias

```php
php artisan assignments:generate-recurring
```

Este comando (ejecutado diariamente via cron):
1. Busca asignaciones con `frequency != 'once'` y `status = 'completed'`
2. Verifica si `next_due_date` ya pasó
3. Crea nuevas asignaciones automáticamente
4. Envía notificaciones a técnicos

### 5. Estados Adicionales

Agregar nuevos estados:
- `active_recurring`: Asignación recurrente activa
- `paused`: Recurrencia pausada temporalmente
- `finished`: Recurrencia finalizada (llegó a end_date)

### 6. Ejemplo de Flujo Completo

**Escenario**: Inspección mensual de EPP

1. **Creación** (2026-02-01):
   ```
   Checklist: Control de EPP
   Técnico: Juan Cito
   Frecuencia: Mensual
   Fecha Inicio: 2026-02-01
   Fecha Fin: 2026-12-31
   ```

2. **Sistema crea**:
   ```
   start_date: 2026-02-01
   end_date: 2026-12-31
   next_due_date: 2026-02-01
   status: pending
   ```

3. **Juan completa el 2026-02-05**:
   ```
   status: completed
   last_completed_at: 2026-02-05
   recurrence_count: 1
   next_due_date: 2026-03-01 (calculado automáticamente)
   ```

4. **Sistema crea nueva asignación**:
   ```
   due_date: 2026-03-01
   status: pending
   parent_assignment_id: [ID anterior]
   ```

5. **Continúa hasta 2026-12-31**, luego:
   ```
   status: finished
   recurrence_count: 11
   ```

## Implementación por Fases

### Fase 1: Preparación (Actual)
- ✅ Documentar especificación
- ⏳ Crear migración de base de datos
- ⏳ Actualizar modelo `ChecklistAssignment`

### Fase 2: Lógica Core
- ⏳ Implementar cálculo de `next_due_date`
- ⏳ Crear servicio `RecurringAssignmentService`
- ⏳ Implementar auto-generación al completar

### Fase 3: UI
- ⏳ Actualizar formulario de asignación
- ⏳ Mostrar información de recurrencia en tabla
- ⏳ Agregar vista de historial de recurrencias

### Fase 4: Automatización
- ⏳ Crear comando artisan
- ⏳ Configurar cron job
- ⏳ Implementar notificaciones

## Notas Técnicas

### Consideraciones
- **Zona horaria**: Usar la zona horaria de la empresa/sede
- **Días no laborables**: Considerar calendario laboral (futuro)
- **Notificaciones**: Alertar X días antes del vencimiento
- **Historial**: Mantener registro completo de todas las completaciones

### Alternativas Evaluadas

1. **Librería externa** (ej: `spatie/laravel-recurring`): Descartada por complejidad
2. **Cron jobs externos**: Descartada por dependencia externa
3. **Eventos de Laravel**: Seleccionada como solución óptima

## Referencias

- Modelo actual: `app/Models/ChecklistAssignment.php`
- Vista actual: `resources/views/livewire/assignments/assignment-manager.blade.php`
- Componente: `app/Livewire/Assignments/AssignmentManager.php`

---

**Fecha de creación**: 2026-01-21  
**Autor**: Sistema de Documentación  
**Estado**: Pendiente de implementación
