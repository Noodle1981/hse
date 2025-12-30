# 🗺️ ROADMAP: Módulo de Hallazgos y Seguimiento

## 🎯 Objetivo Principal

Implementar un sistema centralizado de **Hallazgos y Seguimiento** que conecte:
- Visitas/Inspecciones
- Checklists
- Matriz de Riesgos
- Registro de Incidentes/Accidentes

**Sin romper nada del código existente** ✅

---

## 📋 Estrategia de Implementación

### Principio: **Adición, No Modificación**
- ✅ Agregar nuevas tablas (no modificar existentes)
- ✅ Agregar nuevas relaciones (opcionales)
- ✅ Crear nuevos componentes Livewire
- ✅ Mantener funcionalidad actual intacta
- ✅ Implementación por fases independientes

---

## 🏗️ FASE 1: Base de Datos (Sin Impacto)

### Nuevas Tablas

#### 1.1 `hallazgos` (Findings)
```sql
- id
- sede_id (FK)
- user_id (técnico que lo detectó)
- titulo (ej: "Escalones oxidados")
- descripcion
- ubicacion_especifica (ej: "Escalera principal, 2do piso")
- criticidad (bajo, medio, alto, critico)
- estado (pendiente, en_proceso, resuelto, verificado, cerrado)
- plazo_correccion (date)
- responsable_correccion (string)
- fecha_deteccion
- fecha_resolucion (nullable)
- origen_type (polymorphic: DynamicInspection, GeneradorConstancia, Manual)
- origen_id
- risk_id (FK nullable - vincula con matriz de riesgo)
- created_at, updated_at
```

#### 1.2 `hallazgo_seguimientos` (Follow-ups)
```sql
- id
- hallazgo_id (FK)
- user_id
- estado_anterior
- estado_nuevo
- comentario
- fecha_seguimiento
- created_at, updated_at
```

#### 1.3 `hallazgo_fotos` (Photos)
```sql
- id
- hallazgo_id (FK)
- tipo (antes, durante, despues)
- ruta_archivo
- descripcion
- created_at, updated_at
```

#### 1.4 `incidentes` (Incidents/Accidents)
```sql
- id
- hallazgo_id (FK nullable - si fue causado por un hallazgo)
- sede_id (FK)
- user_id (quien reporta)
- tipo (accidente, incidente, cuasi_accidente)
- gravedad (leve, moderado, grave, fatal)
- fecha_ocurrencia
- descripcion
- personas_afectadas (int)
- dias_perdidos (int nullable)
- medidas_tomadas (text)
- created_at, updated_at
```

**✅ Impacto: CERO** - Son tablas nuevas, no tocan nada existente

---

## 🔧 FASE 2: Modelos y Relaciones (Opcional)

### 2.1 Nuevos Modelos
- `Hallazgo.php`
- `HallazgoSeguimiento.php`
- `HallazgoFoto.php`
- `Incidente.php`

### 2.2 Relaciones Opcionales en Modelos Existentes

#### En `DynamicInspection.php`
```php
// OPCIONAL - no rompe nada si no se usa
public function hallazgos()
{
    return $this->morphMany(Hallazgo::class, 'origen');
}
```

#### En `GeneradorConstancia.php`
```php
// OPCIONAL
public function hallazgos()
{
    return $this->morphMany(Hallazgo::class, 'origen');
}
```

#### En `Risk.php`
```php
// OPCIONAL
public function hallazgos()
{
    return $this->hasMany(Hallazgo::class);
}
```

**✅ Impacto: CERO** - Son relaciones opcionales que no afectan funcionalidad existente

---

## 🎨 FASE 3: Componentes Livewire (Independientes)

### 3.1 Componentes Nuevos
- `HallazgoManager.php` - Listado para Admin
- `HallazgoWizard.php` - Crear/Editar hallazgo
- `HallazgoDetail.php` - Ver detalle con seguimiento
- `MisHallazgos.php` - Vista del técnico
- `IncidenteManager.php` - Gestión de incidentes

### 3.2 Rutas Nuevas
```php
// Nuevas rutas - no tocan las existentes
Route::prefix('hallazgos')->group(function() {
    Route::get('/', HallazgoManager::class)->name('hallazgos.index');
    Route::get('/crear', HallazgoWizard::class)->name('hallazgos.create');
    Route::get('/{id}', HallazgoDetail::class)->name('hallazgos.show');
    Route::get('/mis-hallazgos', MisHallazgos::class)->name('hallazgos.my');
});

Route::prefix('incidentes')->group(function() {
    Route::get('/', IncidenteManager::class)->name('incidentes.index');
});
```

**✅ Impacto: CERO** - Rutas nuevas, no modifican las existentes

---

## 🔗 FASE 4: Integración con Módulos Existentes (Opcional)

### 4.1 Desde Inspecciones
**Agregar botón opcional** en `InspectionWizard`:
```blade
<!-- NUEVO - no rompe nada -->
<button wire:click="crearHallazgo">
    Registrar Hallazgo
</button>
```

### 4.2 Desde Constancias
**Agregar paso opcional** en `VisitCertificateWizard`:
```php
// Paso 5 (opcional): Hallazgos detectados
public $hallazgos_detectados = [];
```

### 4.3 Desde Matriz de Riesgo
**Agregar botón** en evaluación de riesgo:
```blade
<!-- Si el riesgo es Alto/Crítico -->
<button wire:click="crearHallazgoDesdeRiesgo">
    Crear Hallazgo de Seguimiento
</button>
```

**✅ Impacto: MÍNIMO** - Son botones/opciones nuevas que no afectan el flujo actual

---

## 📊 FASE 5: Dashboard y Reportes

### 5.1 Nuevas Métricas en Dashboards

#### Admin Dashboard
```php
'hallazgos' => [
    'total_pendientes' => Hallazgo::where('estado', 'pendiente')->count(),
    'criticos_abiertos' => Hallazgo::where('criticidad', 'critico')
                                   ->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
    'vencidos' => Hallazgo::where('plazo_correccion', '<', now())
                          ->whereNotIn('estado', ['resuelto', 'cerrado'])->count(),
]
```

#### Technician Dashboard
```php
'mis_hallazgos' => [
    'pendientes' => Hallazgo::where('user_id', auth()->id())
                            ->where('estado', 'pendiente')->count(),
    'en_seguimiento' => Hallazgo::where('user_id', auth()->id())
                                ->where('estado', 'en_proceso')->count(),
]
```

**✅ Impacto: CERO** - Solo agrega métricas, no modifica las existentes

---

## 🎯 FASE 6: Flujo Completo Integrado

### Ejemplo: Escalones Oxidados

```
1. DETECCIÓN (Visita/Inspección)
   ↓
   Técnico crea hallazgo:
   - Título: "Escalones oxidados"
   - Criticidad: Alto
   - Plazo: 15 días
   - Fotos: [antes.jpg]
   ↓
2. VINCULACIÓN CON RIESGO
   ↓
   Sistema pregunta: "¿Crear/Actualizar riesgo en matriz?"
   - Sí → Crea riesgo "Caída por escalones oxidados" (Nivel: Alto)
   ↓
3. SEGUIMIENTO
   ↓
   Responsable actualiza:
   - Estado: "En proceso"
   - Comentario: "Presupuesto aprobado, inicio en 3 días"
   ↓
4. INCIDENTE (si ocurre)
   ↓
   Se registra accidente:
   - Vinculado al hallazgo
   - Gravedad: Moderado
   - Días perdidos: 2
   ↓
5. RESOLUCIÓN
   ↓
   Técnico verifica:
   - Estado: "Resuelto"
   - Fotos: [despues.jpg]
   ↓
6. ACTUALIZACIÓN DE RIESGO
   ↓
   Sistema actualiza automáticamente:
   - Riesgo "Caída por escalones" → Nivel: Bajo
   - Justificación: "Hallazgo #123 resuelto"
   ↓
7. CIERRE
   ↓
   Admin verifica y cierra hallazgo
```

---

## 📅 Cronograma Estimado

| Fase | Tiempo | Riesgo de Romper |
|------|--------|------------------|
| Fase 1: Base de Datos | 2 horas | 0% |
| Fase 2: Modelos | 1 hora | 0% |
| Fase 3: Componentes | 6 horas | 0% |
| Fase 4: Integración | 4 horas | 5% (mínimo) |
| Fase 5: Dashboard | 2 horas | 0% |
| Fase 6: Testing | 3 horas | 0% |
| **TOTAL** | **18 horas** | **<5%** |

---

## ✅ Garantías de No Ruptura

### 1. Tablas Independientes
- No se modifican tablas existentes
- Solo se agregan nuevas tablas

### 2. Relaciones Opcionales
- Las relaciones polimórficas son opcionales
- Si no se usan, no afectan nada

### 3. Rutas Separadas
- Nuevas rutas con prefijo `/hallazgos`
- No tocan rutas existentes

### 4. Componentes Nuevos
- Componentes Livewire completamente nuevos
- No modifican componentes existentes

### 5. Migraciones Reversibles
- Todas las migraciones tienen `down()`
- Se puede revertir sin problemas

---

## 🚀 Plan de Implementación Seguro

### Paso 1: Crear Branch
```bash
git checkout -b feature/hallazgos-seguimiento
```

### Paso 2: Implementar por Fases
- Hacer commit después de cada fase
- Probar antes de continuar

### Paso 3: Testing
- Verificar que módulos existentes funcionan
- Probar nuevas funcionalidades

### Paso 4: Merge
- Solo cuando todo esté probado
- Merge a develop/main

---

## 📝 Notas Importantes

### Lo que NO se toca:
- ❌ Tablas existentes
- ❌ Migraciones anteriores
- ❌ Componentes Livewire actuales
- ❌ Rutas existentes
- ❌ Lógica de negocio actual

### Lo que SÍ se agrega:
- ✅ Nuevas tablas
- ✅ Nuevos modelos
- ✅ Nuevos componentes
- ✅ Nuevas rutas
- ✅ Relaciones opcionales

---

## 🎯 Beneficios del Módulo

1. **Trazabilidad Completa**
   - Desde detección hasta resolución
   - Historial de cambios

2. **Gestión Proactiva**
   - Alertas de vencimientos
   - Priorización por criticidad

3. **Integración Total**
   - Conecta inspecciones, visitas, riesgos
   - Flujo unificado

4. **Cumplimiento Legal**
   - Registro de incidentes
   - Evidencia fotográfica
   - Auditoría completa

5. **Mejora Continua**
   - Estadísticas de hallazgos
   - Tiempos de resolución
   - Análisis de tendencias

---

## ❓ Preguntas Frecuentes

**Q: ¿Se romperá algo del código actual?**  
A: No. Todo es nuevo y opcional.

**Q: ¿Puedo seguir usando inspecciones sin hallazgos?**  
A: Sí, totalmente. El módulo de hallazgos es opcional.

**Q: ¿Qué pasa si no quiero vincular un hallazgo con un riesgo?**  
A: No hay problema, el campo `risk_id` es nullable.

**Q: ¿Puedo desactivar el módulo si no me gusta?**  
A: Sí, simplemente no uses las rutas `/hallazgos`.

---

## 🎉 Conclusión

Este módulo es el **corazón del sistema de Higiene y Seguridad** y se puede implementar de forma **100% segura** sin romper nada existente.

¿Procedemos con la implementación? 🚀
