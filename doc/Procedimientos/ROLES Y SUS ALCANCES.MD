# 👥 Sistema de Roles y Permisos - EP-Back V2.0.0.0

> **Estado:** Sistema Completo con Control de Acceso ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Descripción

Sistema de control de acceso basado en roles (RBAC) que define permisos granulares para cada funcionalidad del sistema. Actualmente implementa dos roles principales: **Administrador** y **Técnico**, con alcances específicos para cada módulo.

---

## 👤 Roles del Sistema

### 1. Administrador

**Descripción:**
- Rol con acceso completo al sistema
- Gestión de configuración y datos maestros
- Supervisión de todas las operaciones
- Acceso a reportes y estadísticas globales

**Características:**
- ✅ CRUD completo en todos los módulos
- ✅ Gestión de usuarios y asignaciones
- ✅ Configuración del sistema
- ✅ Acceso a todos los dashboards
- ✅ Exportación de reportes
- ✅ Gestión de empresas y sedes

### 2. Técnico

**Descripción:**
- Rol operativo para trabajo en campo
- Ejecución de inspecciones y capacitaciones
- Registro de entregas y hallazgos
- Acceso limitado a sus sedes asignadas

**Características:**
- ✅ Ejecución de inspecciones
- ✅ Registro de capacitaciones
- ✅ Entrega de EPP
- ✅ Creación de hallazgos
- ✅ Constancias de visita
- ❌ Sin acceso a configuración
- ❌ Sin gestión de usuarios
- ❌ Solo ve datos de sus sedes

---

## 📊 Matriz de Permisos por Módulo

### 1. Dashboard

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver dashboard admin | ✅ | ❌ |
| Ver dashboard técnico | ✅ | ✅ |
| Ver métricas globales | ✅ | ❌ |
| Ver métricas personales | ✅ | ✅ |
| Filtrar por sede | ✅ (todas) | ✅ (solo asignadas) |

### 2. Empresas y Sedes

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver empresas | ✅ | ❌ |
| Crear empresa | ✅ | ❌ |
| Editar empresa | ✅ | ❌ |
| Eliminar empresa | ✅ | ❌ |
| Ver sedes | ✅ (todas) | ✅ (solo asignadas) |
| Crear sede | ✅ | ❌ |
| Editar sede | ✅ | ❌ |
| Eliminar sede | ✅ | ❌ |
| Asignar técnicos a sede | ✅ | ❌ |

### 3. Usuarios

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver usuarios | ✅ | ❌ |
| Crear usuario | ✅ | ❌ |
| Editar usuario | ✅ | ❌ |
| Eliminar usuario | ✅ | ❌ |
| Cambiar rol | ✅ | ❌ |
| Asignar a sedes | ✅ | ❌ |
| Ver perfil propio | ✅ | ✅ |
| Editar perfil propio | ✅ | ✅ |

### 4. Checklists Dinámicos

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver catálogo | ✅ | ✅ |
| Crear checklist | ✅ | ❌ |
| Editar checklist | ✅ | ❌ |
| Eliminar checklist | ✅ | ❌ |
| Duplicar checklist | ✅ | ❌ |
| Ver asignaciones | ✅ (todas) | ✅ (solo propias) |
| Crear asignación | ✅ | ❌ |
| Completar inspección | ✅ | ✅ |
| Ver historial | ✅ (todas) | ✅ (solo propias) |
| Descargar PDF | ✅ | ✅ |

### 5. Capacitaciones

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver capacitaciones | ✅ (todas) | ✅ (de sus sedes) |
| Crear capacitación | ✅ | ❌ |
| Editar capacitación | ✅ | ❌ |
| Eliminar capacitación | ✅ | ❌ |
| Tomar asistencia | ✅ | ✅ |
| Descargar PDF | ✅ | ✅ |
| Ver historial | ✅ (todas) | ✅ (solo propias) |

### 6. Constancias de Visita

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver todas las constancias | ✅ | ❌ |
| Ver mi historial | ✅ | ✅ |
| Crear constancia | ✅ | ✅ |
| Vincular actividades | ✅ | ✅ |
| Firmar como técnico | ✅ | ✅ |
| Descargar PDF | ✅ | ✅ |
| Filtrar por técnico | ✅ | ❌ (solo propias) |

### 7. Entrega de EPP

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver catálogo EPP | ✅ | ❌ |
| CRUD catálogo EPP | ✅ | ❌ |
| Ver empleados | ✅ (todos) | ✅ (de sus sedes) |
| CRUD empleados | ✅ | ✅ |
| Importar empleados | ✅ | ❌ |
| Realizar entrega | ✅ | ✅ |
| Ver historial completo | ✅ | ❌ (solo propias) |
| Descargar PDF | ✅ | ✅ |

### 8. Hallazgos

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver todos los hallazgos | ✅ | ❌ |
| Ver mis hallazgos | ✅ | ✅ |
| Crear hallazgo | ✅ | ✅ |
| Editar hallazgo | ✅ | ✅ (solo propios) |
| Eliminar hallazgo | ✅ | ❌ |
| Agregar seguimiento | ✅ | ✅ |
| Subir fotos | ✅ | ✅ |
| Registrar incidente | ✅ | ✅ |
| Ver KPIs completos | ✅ | ❌ |

### 9. Matriz de Riesgos

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver matrices | ✅ (todas) | ✅ (de sus sedes) |
| CRUD matrices | ✅ | ❌ |
| Gestionar riesgos | ✅ | ❌ |
| Evaluar riesgos | ✅ | ✅ |
| Gestionar controles | ✅ | ❌ |
| Duplicar matrices | ✅ | ❌ |
| Ver plan anual | ✅ | ✅ |

### 10. Contrato Anual

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver dashboard | ✅ | ✅ |
| Seleccionar cualquier sede | ✅ | ❌ (solo asignadas) |
| Activar modo edición | ✅ | ❌ |
| Agregar actividades | ✅ | ❌ |
| Eliminar actividades | ✅ | ❌ |
| Marcar/desmarcar meses | ✅ | ❌ |
| Ver cumplimiento | ✅ | ✅ |
| Exportar PDF | ✅ | ✅ |

### 11. Categorías

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver categorías | ✅ | ✅ |
| Crear categoría | ✅ | ❌ |
| Editar categoría | ✅ | ❌ |
| Eliminar categoría | ✅ | ❌ |
| Gestionar subcategorías | ✅ | ❌ |
| Reordenar | ✅ | ❌ |

### 12. Incidentes

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver incidentes | ✅ (todos) | ✅ (de sus sedes) |
| Registrar incidente | ✅ | ✅ |
| Editar incidente | ✅ | ✅ (solo propios) |
| Eliminar incidente | ✅ | ❌ |
| Ver estadísticas | ✅ | ❌ |

### 13. Auditoría EDUGE

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Ver auditorías | ✅ | ❌ |
| Crear auditoría | ✅ | ❌ |
| Validar datos | ✅ | ❌ |
| Modificar datos | ✅ | ❌ |
| Exportar reportes | ✅ | ❌ |

### 14. Configuración

| Funcionalidad | Administrador | Técnico |
|---------------|---------------|---------|
| Configuración general | ✅ | ❌ |
| Gestión de permisos | ✅ | ❌ |
| Logs del sistema | ✅ | ❌ |
| Backup/Restore | ✅ | ❌ |

---

## 🔐 Implementación Técnica

### Middleware de Roles

```php
// routes/web.php

// Solo Administrador
Route::middleware(['role:Administrador'])->group(function () {
    Route::get('/companies', CompanyIndex::class);
    Route::get('/users', UserManager::class);
    // ...
});

// Administrador y Técnico
Route::middleware(['role:Administrador,Técnico'])->group(function () {
    Route::get('/checklists/fill/{id}', InspectionWizard::class);
    Route::get('/trainings/{id}/attendance', AttendanceTracker::class);
    // ...
});
```

### Verificación en Componentes

```php
// En Livewire Components
public function mount()
{
    $role = strtolower(auth()->user()->rol);
    
    if ($role !== 'administrador' && $role !== 'admin') {
        abort(403, 'No autorizado');
    }
}
```

### Filtrado por Sede

```php
// Técnicos solo ven datos de sus sedes
if (auth()->user()->hasRole('Técnico')) {
    $query->whereHas('sede', function($q) {
        $q->whereHas('users', fn($sq) => 
            $sq->where('users.id', auth()->id())
        );
    });
}
```

---

## 📋 Usuarios Demo

### Administrador

```
Email: admin@ep-back.com
Password: password
Rol: Administrador
Acceso: Completo
```

**Puede:**
- Gestionar todo el sistema
- Ver todas las empresas y sedes
- Crear y asignar usuarios
- Configurar el sistema
- Acceder a todos los reportes

### Técnico

```
Email: tecnico@ep-back.com
Password: password
Rol: Técnico
Sedes asignadas: Según configuración
```

**Puede:**
- Ejecutar inspecciones
- Registrar capacitaciones
- Entregar EPP
- Crear hallazgos
- Ver solo sus sedes

---

## 🔄 Flujos de Trabajo por Rol

### Flujo Administrador

```
1. Login como Admin
   └─ Accede a dashboard completo

2. Configura sistema
   ├─ Crea empresas y sedes
   ├─ Crea usuarios técnicos
   └─ Asigna técnicos a sedes

3. Configura operaciones
   ├─ Crea catálogo de checklists
   ├─ Crea catálogo de EPP
   ├─ Define matrices de riesgo
   └─ Configura plan anual

4. Asigna trabajo
   ├─ Asigna checklists a técnicos
   ├─ Programa capacitaciones
   └─ Define responsables

5. Supervisa
   ├─ Ve dashboard global
   ├─ Revisa cumplimiento
   ├─ Genera reportes
   └─ Toma decisiones
```

### Flujo Técnico

```
1. Login como Técnico
   └─ Accede a dashboard personal

2. Ve asignaciones
   ├─ Checklists pendientes
   ├─ Capacitaciones programadas
   └─ Hallazgos abiertos

3. Ejecuta trabajo en campo
   ├─ Completa inspecciones
   ├─ Toma asistencia en capacitaciones
   ├─ Entrega EPP a empleados
   └─ Registra hallazgos

4. Documenta
   ├─ Sube fotos de evidencia
   ├─ Completa observaciones
   ├─ Captura firmas
   └─ Genera constancias

5. Reporta
   └─ Constancias de visita
   └─ PDFs de entregas
   └─ Seguimiento de hallazgos
```

---

## 🎯 Casos de Uso por Rol

### Caso 1: Admin - Configuración Inicial

```
Objetivo: Configurar sistema para nueva empresa

1. Crea empresa
   └─ "Constructora ABC S.A."

2. Crea sedes
   ├─ Obra Centro
   ├─ Obra Norte
   └─ Oficina Central

3. Crea usuarios técnicos
   ├─ Técnico A
   ├─ Técnico B
   └─ Técnico C

4. Asigna técnicos a sedes
   ├─ Técnico A → Obra Centro
   ├─ Técnico B → Obra Norte
   └─ Técnico C → Oficina Central

5. Configura checklists
   └─ Crea catálogo de inspecciones

6. Sistema listo para operar
```

### Caso 2: Técnico - Día de Trabajo

```
Objetivo: Completar asignaciones del día

1. Login y revisa dashboard
   └─ 3 inspecciones pendientes

2. Va a Obra Centro
   └─ Ejecuta inspección de EPP

3. Detecta hallazgo
   └─ Registra "Arnés vencido"

4. Dicta capacitación
   └─ Toma asistencia de 15 empleados

5. Entrega EPP
   └─ Entrega arnés nuevo a empleado

6. Genera constancia de visita
   └─ Vincula todas las actividades

7. Fin del día
   └─ Todas las asignaciones completadas
```

### Caso 3: Admin - Supervisión

```
Objetivo: Revisar cumplimiento mensual

1. Accede a dashboard admin
   └─ Ve métricas globales

2. Revisa cumplimiento
   ├─ Inspecciones: 95%
   ├─ Capacitaciones: 100%
   └─ Hallazgos: 3 críticos abiertos

3. Analiza hallazgos críticos
   └─ Asigna prioridad

4. Revisa plan anual
   └─ Verifica cumplimiento

5. Genera reportes
   └─ Exporta PDFs para cliente

6. Toma decisiones
   └─ Programa auditoría
```

---

## 🔒 Seguridad y Validaciones

### Validaciones de Acceso

```php
// Verificar rol
if (!auth()->user()->hasRole('Administrador')) {
    abort(403);
}

// Verificar sede asignada
if (!auth()->user()->sedes->contains($sede->id)) {
    abort(403, 'No tienes acceso a esta sede');
}

// Verificar propiedad
if ($hallazgo->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
    abort(403, 'No puedes editar este hallazgo');
}
```

### Protección de Rutas

```php
// Middleware en rutas
Route::middleware(['auth', 'role:Administrador'])->group(function () {
    // Rutas protegidas
});

// Gates
Gate::define('manage-users', function ($user) {
    return $user->hasRole('Administrador');
});

// Policies
public function update(User $user, Hallazgo $hallazgo)
{
    return $user->id === $hallazgo->user_id || $user->isAdmin();
}
```

---

## 📊 Comparativa de Roles

| Aspecto | Administrador | Técnico |
|---------|---------------|---------|
| **Alcance** | Global | Por sede |
| **Configuración** | ✅ Completa | ❌ Ninguna |
| **Gestión de usuarios** | ✅ Sí | ❌ No |
| **Ejecución de trabajo** | ✅ Sí | ✅ Sí |
| **Reportes** | ✅ Globales | ✅ Personales |
| **Dashboards** | ✅ Admin + Técnico | ✅ Solo Técnico |
| **Exportación** | ✅ Todo | ✅ Solo propio |
| **Eliminación** | ✅ Sí | ❌ No |

---

## 🔜 Roadmap Futuro

### Próximos Roles

1. **Supervisor**
   - Supervisión de técnicos
   - Aprobación de hallazgos
   - Reportes por área

2. **Cliente**
   - Vista de solo lectura
   - Reportes de su empresa
   - Dashboard personalizado

3. **Auditor**
   - Acceso a auditorías
   - Reportes de cumplimiento
   - Sin edición

### Mejoras de Permisos

1. **Permisos Granulares**
   - Permisos por módulo
   - Permisos por acción
   - Configuración flexible

2. **Roles Personalizados**
   - Crear roles custom
   - Asignar permisos específicos
   - Plantillas de roles

3. **Grupos de Usuarios**
   - Agrupar por departamento
   - Permisos por grupo
   - Jerarquías

---

## 📚 Referencias

- [ARQUITECTURA.md](../ARQUITECTURA.md) - Arquitectura completa del sistema
- [CHECKLISTS_DINAMICOS.md](./CHECKLISTS_DINAMICOS.md) - Sistema de checklists
- [EMPRESAS_SEDE_EMPLEADOS.MD](./EMPRESAS_SEDE_EMPLEADOS.MD) - Empresas y sedes

---

## 🔄 Changelog

### V2.0.0.0 (30 Diciembre 2024)
- ✅ Sistema completo de roles y permisos
- ✅ 2 roles principales: Administrador y Técnico
- ✅ Control de acceso por middleware
- ✅ Filtrado automático por sede
- ✅ Validaciones de propiedad
- ✅ Protección de rutas
- ✅ Permisos granulares por módulo
- ✅ Matriz completa de permisos
- ✅ Usuarios demo configurados

---

*EP-Back V2.0.0.0 | Sistema de Roles y Permisos con Control de Acceso | Diciembre 2024*
