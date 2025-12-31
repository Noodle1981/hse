# 🚀 ROADMAP: Performance y Estabilidad

> **Nivel de dependencia:** 🏗️ FUNDACIÓN (NIVEL 0)  
> **Prerequisitos:** Ninguno (ES prerequisito de otros)  
> **Prioridad:** 🔴 CRÍTICA  
> **Duración:** Continuo (Sprint inicial: 2-3 semanas)  
> **Sprint sugerido:** Sprint 1

---

## 📌 Propósito

Establecer fundamentos sólidos de performance y estabilidad que permitan escalar el sistema sin degradar la experiencia de usuario. **Este roadmap es prerequisito para todos los demás.**

## 🎯 Objetivos Medibles

| Métrica | Actual | Objetivo | Crítico |
|---------|--------|----------|---------|
| Tiempo de carga (páginas) | ? | < 2s | ✅ |
| Tiempo de respuesta API | ? | < 500ms | ✅ |
| Queries N+1 | ? | 0 en rutas críticas | ✅ |
| Disponibilidad | ? | > 99.5% | ⚠️ |
| Error rate | ? | < 0.1% | ⚠️ |
| Uso de memoria | ? | < 512MB por request | ⚪ |

## 🛠️ Implementación

### Fase 1: Monitoreo (Semana 1)

#### 1.1 Laravel Pulse
```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
php artisan migrate
```

**Configurar en `.env`:**
```env
PULSE_ENABLED=true
PULSE_INGEST_DRIVER=database
PULSE_STORE_DRIVER=database
```

**Acceso:** `/pulse` (solo admin)

#### 1.2 Query Logging
```php
// config/database.php - Solo en development
'connections' => [
    'mysql' => [
        'options' => [
            PDO::ATTR_EMULATE_PREPARES => true,
        ],
        'strict' => true,
        'engine' => null,
    ],
],
```

**Instalar Debugbar:**
```bash
composer require barryvdh/laravel-debugbar --dev
```

---

### Fase 2: Optimización Base de Datos (Semana 1-2)

#### 2.1 Identificar Queries N+1
**Comando de auditoría:**
```bash
php artisan debugbar:enable
# Navegar manualmente por rutas críticas:
# - /admin/dashboard
# - /tecnico/dashboard
# - /checklists/fill/{id}
# - /edificios
```

**Rutas a revisar:**
- Lista de inspecciones
- Dashboard de técnico
- Wizard de checklist
- Listado de edificios/establecimientos

#### 2.2 Eager Loading Obligatorio
```php
// Ejemplo: app/Http/Livewire/InspectionsList.php
public function render()
{
    $inspections = Inspection::with([
        'checklist',
        'tecnico',
        'edificio.establecimiento',
        'respuestas.pregunta'
    ])
    ->whereDate('created_at', '>=', $this->dateFrom)
    ->get();

    return view('livewire.inspections-list', compact('inspections'));
}
```

#### 2.3 Índices Críticos
```php
// database/migrations/2025_12_31_add_performance_indexes.php
Schema::table('inspections', function (Blueprint $table) {
    $table->index(['tecnico_id', 'created_at']);
    $table->index(['edificio_id', 'status']);
    $table->index('created_at');
});

Schema::table('checklist_responses', function (Blueprint $table) {
    $table->index(['inspection_id', 'question_id']);
});

Schema::table('edificios', function (Blueprint $table) {
    $table->index('establecimiento_id');
    $table->index(['lat', 'lng']); // Para mapas
});
```

---

### Fase 3: Caché Estratégico (Semana 2)

#### 3.1 Dashboard Stats
```php
// app/Http/Livewire/DashboardStats.php
public function mount()
{
    $userId = auth()->id();
    
    $this->stats = Cache::remember(
        "dashboard_stats_{$userId}",
        now()->addMinutes(5),
        function () use ($userId) {
            return [
                'inspections_today' => Inspection::whereDate('created_at', today())
                    ->where('tecnico_id', $userId)
                    ->count(),
                'pending' => Inspection::where('status', 'pending')
                    ->where('tecnico_id', $userId)
                    ->count(),
                'completed_month' => Inspection::whereMonth('created_at', now()->month)
                    ->where('tecnico_id', $userId)
                    ->where('status', 'completed')
                    ->count(),
            ];
        }
    );
}
```

#### 3.2 Templates de Checklists
```php
// app/Services/ChecklistService.php
public function getTemplates()
{
    return Cache::rememberForever('checklist_templates', function () {
        return ChecklistTemplate::with('preguntas')->get();
    });
}

// Invalidar cuando se edite un template
public function updateTemplate($id, $data)
{
    $template = ChecklistTemplate::findOrFail($id);
    $template->update($data);
    
    Cache::forget('checklist_templates');
    
    return $template;
}
```

#### 3.3 Listados Estáticos
```php
// Modalidades, tipos de edificio, etc.
public function getModalidades()
{
    return Cache::remember('modalidades', now()->addDay(), function () {
        return Modalidad::orderBy('nombre')->get();
    });
}
```

---

### Fase 4: Optimización de Assets (Semana 2)

#### 4.1 Vite Production Build
```bash
npm run build
```

**Verificar en `vite.config.js`:**
```javascript
export default defineConfig({
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
});
```

#### 4.2 Lazy Loading de Imágenes
```blade
<!-- resources/views/components/image.blade.php -->
<img 
    src="{{ $src }}" 
    alt="{{ $alt }}"
    loading="lazy"
    decoding="async"
    class="{{ $class }}"
>
```

**Uso:**
```blade
<x-image src="/storage/fotos/edificio.jpg" alt="Edificio" />
```

---

### Fase 5: Monitoreo Continuo (Semana 3+)

#### 5.1 Comando de Reporte Semanal
```php
// app/Console/Commands/PerformanceReport.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PerformanceReport extends Command
{
    protected $signature = 'performance:report';
    protected $description = 'Generate weekly performance report';

    public function handle()
    {
        // Queries lentas de la semana
        $slowQueries = DB::table('pulse_entries')
            ->where('type', 'slow_query')
            ->where('created_at', '>=', now()->subWeek())
            ->get();

        // Errores
        $errors = DB::table('pulse_entries')
            ->where('type', 'exception')
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        // Enviar reporte
        Mail::to(config('app.admin_email'))
            ->send(new \App\Mail\WeeklyPerformanceReport([
                'slow_queries' => $slowQueries,
                'error_count' => $errors,
            ]));

        $this->info('Performance report sent!');
    }
}
```

**Programar en `app/Console/Kernel.php`:**
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('performance:report')->weekly()->mondays()->at('09:00');
}
```

#### 5.2 Alertas Automáticas
```php
// config/pulse.php
'recorders' => [
    \Laravel\Pulse\Recorders\Exceptions::class => [
        'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
        'sample_rate' => 1,
        'capture' => [
            //
        ],
    ],
    \Laravel\Pulse\Recorders\SlowQueries::class => [
        'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
        'threshold' => 100, // ms
    ],
],
```

---

## ✅ Criterios de Completitud

### Sprint 1 (80% para desbloquear siguientes)
- [x] Laravel Pulse instalado y configurado
- [x] 0 queries N+1 en rutas críticas
- [x] Índices de base de datos implementados
- [x] Caché en dashboards principales
- [x] Tiempo de carga < 2s verificado

### Continuo
- [ ] Monitoreo semanal activo
- [ ] Alertas de queries lentas
- [ ] Revisión mensual de métricas

---

## 🚫 Bloquea a

Si este roadmap NO se completa al **80%**, los siguientes están bloqueados:

- ❌ PWA Offline (Sprint 2)
- ❌ Usuarios Empresariales (Sprint 4)
- ❌ IA y Automaciones (Sprint 5)

**Solo puede proceder en paralelo:**
- ✅ Rediseño Sidebar (Sprint 3) - No requiere prerequisitos

---

## 📊 Métricas de Progreso

```
Fase 1 (Monitoreo):           [ ░░░░░░░░░░ ] 0%
Fase 2 (BD Optimization):     [ ░░░░░░░░░░ ] 0%
Fase 3 (Caché):               [ ░░░░░░░░░░ ] 0%
Fase 4 (Assets):              [ ░░░░░░░░░░ ] 0%
Fase 5 (Continuo):            [ ░░░░░░░░░░ ] 0%

TOTAL:                        [ ░░░░░░░░░░ ] 0%
```

---

## 📝 Notas

**Actualización:** 2025-12-31  
**Responsable:** Equipo desarrollo  
**Revisión:** Semanal después de Sprint 1
