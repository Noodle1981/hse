# 👥 ROADMAP: Rol Usuarios Empresariales

> **Nivel de dependencia:** 📱 EXPERIENCIA USUARIO (NIVEL 2)  
> **Prerequisitos:** Performance estable (80%) + PWA Offline (recomendado)  
> **Prioridad:** 🟡 MEDIA  
> **Duración:** 3-4 semanas  
> **Sprint sugerido:** Sprint 4

---

## 📌 Propósito

Crear un rol para clientes empresariales que puedan revisar trabajos realizados  en sus establecimientos sin necesidad de emails, con acceso directo a informes y sistema de notificaciones.

## 🎯 Objetivos

- Evitar sobrecarga de emails
- Control directo de trabajos por parte del cliente
- Segmentación: cliente solo ve SUS empresas
- Sistema de notificaciones bidireccional (admin/técnico ← → cliente)

## 🛠️ Implementación

### Fase 1: Base de Datos (Semana 1)

#### 1.1 Migración: Pivot usuarios-empresas
```php
// database/migrations/2025_12_31_create_usuario_empresa_table.php
Schema::create('usuario_empresa', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('empresa_id')->constrained('establecimientos')->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['user_id', 'empresa_id']);
    $table->index('user_id');
    $table->index('empresa_id');
});
```

#### 1.2 Actualizar roles
```php
// database/seeders/RoleSeeder.php
$roles = [
    'admin',
    'administrativo',
    'tecnico',
    'usuario_empresarial', // NUEVO
];

foreach ($roles as $role) {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
}
```

---

### Fase 2: Modelos y Relaciones (Semana 1)

#### 2.1 Modelo User
```php
// app/Models/User.php
public function empresasAsignadas()
{
    return $this->belongsToMany(Establecimiento::class, 'usuario_empresa', 'user_id', 'empresa_id')
        ->withTimestamps();
}

public function canViewEmpresa($empresaId)
{
    if ($this->hasRole(['admin', 'administrativo'])) {
        return true;
    }
    
    return $this->empresasAsignadas()->where('id', $empresaId)->exists();
}
```

#### 2.2 Scope Global
```php
// app/Models/Inspection.php
use Illuminate\Database\Eloquent\Builder;

protected static function booted()
{
    static::addGlobalScope('empresarial', function (Builder $builder) {
        $user = auth()->user();
        
        if ($user && $user->hasRole('usuario_empresarial')) {
            $empresaIds = $user->empresasAsignadas()->pluck('id');
            
            $builder->whereHas('edificio', function ($query) use ($empresaIds) {
                $query->whereIn('establecimiento_id', $empresaIds);
            });
        }
    });
}
```

---

### Fase 3: Dashboard Cliente (Semana 2)

#### 3.1 Livewire Component
```php
// app/Http/Livewire/ClienteDashboard.php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Inspection;
use App\Models\Establecimiento;

class ClienteDashboard extends Component
{
    public $empresaSeleccionada = null;
    public $filterFecha = null;
    
    public function mount()
    {
        $empresas = auth()->user()->empresasAsignadas;
        $this->empresaSeleccionada = $empresas->first()?->id;
    }
    
    public function render()
    {
        $empresas = auth()->user()->empresasAsignadas;
        
        $query = Inspection::with(['checklist', 'tecnico', 'edificio'])
            ->where('status', 'completed');
        
        if ($this->empresaSeleccionada) {
            $query->whereHas('edificio', function ($q) {
                $q->where('establecimiento_id', $this->empresaSeleccionada);
            });
        }
        
        if ($this->filterFecha) {
            $query->whereDate('created_at', $this->filterFecha);
        }
        
        $inspecciones = $query->latest()->paginate(15);
        
        return view('livewire.cliente-dashboard', [
            'empresas' => $empresas,
            'inspecciones' => $inspecciones,
        ]);
    }
}
```

#### 3.2 Vista
```blade
<!-- resources/views/livewire/cliente-dashboard.blade.php -->
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Mis Inspecciones</h2>
    
    <div class="flex gap-4 mb-6">
        <select wire:model.live="empresaSeleccionada" class="form-select">
            <option value="">Todas las empresas</option>
            @foreach($empresas as $empresa)
                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
            @endforeach
        </select>
        
        <input type="date" wire:model.live="filterFecha" class="form-input">
    </div>
    
    <div class="space-y-4">
        @foreach($inspecciones as $inspeccion)
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">{{ $inspeccion->checklist->nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ $inspeccion->edificio->nombre }}</p>
                        <p class="text-xs text-gray-500">
                            Técnico: {{ $inspeccion->tecnico->name }} • 
                            {{ $inspeccion->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    
                    <div class="flex gap-2">
                        @if(!$inspeccion->visto_por_cliente)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">NUEVO</span>
                        @endif
                        
                        <a href="{{ route('cliente.inspeccion.show', $inspeccion) }}" 
                           class="btn btn-sm btn-primary">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
```

---

### Fase 4: Sistema de Vistos (Semana 2-3)

#### 4.1 Migración
```php
// database/migrations/2025_12_31_add_visto_fields_to_inspections.php
Schema::table('inspections', function (Blueprint $table) {
    $table->boolean('visto_por_cliente')->default(false);
    $table->timestamp('visto_cliente_at')->nullable();
    $table->foreignId('visto_por_user_id')->nullable()->constrained('users');
});
```

#### 4.2 Marcar como visto
```php
// app/Http/Controllers/ClienteInspeccionController.php
public function show(Inspection $inspection)
{
    // Verificar permiso
    abort_unless(auth()->user()->canViewEmpresa($inspection->edificio->establecimiento_id), 403);
    
    // Marcar como visto
    if (!$inspection->visto_por_cliente) {
        $inspection->update([
            'visto_por_cliente' => true,
            'visto_cliente_at' => now(),
            'visto_por_user_id' => auth()->id(),
        ]);
        
        // Notificar al técnico y admin
        Notification::send(
            [$inspection->tecnico, User::role('admin')->first()],
            new InspeccionVistaNotification($inspection, auth()->user())
        );
    }
    
    return view('cliente.inspeccion.show', compact('inspection'));
}
```

---

### Fase 5: Notificaciones (Semana 3)

#### 5.1 Notification Model
```php
// app/Notifications/NuevaInspeccionClienteNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NuevaInspeccionClienteNotification extends Notification
{
    public function __construct(public $inspection) {}
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Nueva inspección disponible',
            'message' => "Inspección '{$this->inspection->checklist->nombre}' completada en {$this->inspection->edificio->nombre}",
            'url' => route('cliente.inspeccion.show', $this->inspection),
            'inspection_id' => $this->inspection->id,
        ];
    }
}
```

#### 5.2 Disparar al completar inspección
```php
// app/Http/Livewire/ChecklistWizard.php
public function finalizarInspeccion()
{
    $this->inspection->update(['status' => 'completed']);
    
    // Notificar a usuarios empresariales de esta empresa
    $empresa = $this->inspection->edificio->establecimiento;
    $clientes = $empresa->usuarios; // Relación usuarios del establecimiento
    
    Notification::send($clientes, new NuevaInspeccionClienteNotification($this->inspection));
    
    session()->flash('success', 'Inspección finalizada');
    return redirect()->route('tecnico.dashboard');
}
```

#### 5.3 Componente de Notificaciones
```blade
<!-- resources/views/components/notification-bell.blade.php -->
<div x-data="{ open: false, count: {{ auth()->user()->unreadNotifications->count() }} }">
    <button @click="open = !open" class="relative">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
        </svg>
        <span x-show="count > 0" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
              x-text="count"></span>
    </button>
    
    <div x-show="open" @click.away="open = false" 
         class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg">
        @foreach(auth()->user()->unreadNotifications as $notification)
            <a href="{{ $notification->data['url'] }}" 
               class="block p-4 hover:bg-gray-50 border-b">
                <p class="font-semibold text-sm">{{ $notification->data['title'] }}</p>
                <p class="text-xs text-gray-600">{{ $notification->data['message'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </a>
        @endforeach
    </div>
</div>
```

---

### Fase 6: Permisos y Rutas (Semana 3-4)

#### 6.1 Middleware
```php
// app/Http/Middleware/EnsureUserCanViewEmpresa.php
public function handle($request, Closure $next)
{
    $empresaId = $request->route('empresa') ?? $request->input('empresa_id');
    
    if ($empresaId && !auth()->user()->canViewEmpresa($empresaId)) {
        abort(403, 'No tienes acceso a esta empresa');
    }
    
    return $next($request);
}
```

#### 6.2 Rutas
```php
// routes/web.php
Route::middleware(['auth', 'role:usuario_empresarial'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');
    Route::get('/inspeccion/{inspection}', [ClienteInspeccionController::class, 'show'])->name('inspeccion.show');
    Route::get('/reportes', [ClienteReportesController::class, 'index'])->name('reportes');
    Route::get('/certificados', [ClienteCertificadosController::class, 'index'])->name('certificados');
});
```

---

## ✅ Criterios de Completitud

- [x] Tabla pivot `usuario_empresa` creada
- [x] Rol `usuario_empresarial` configurado
- [x] Dashboard cliente funcional
- [x] Cliente solo ve sus empresas asignadas
- [x] Sistema de vistos implementado
- [x] Notificaciones bidireccionales funcionando
- [x] Permisos granulares verificados
- [x] Testing: Cliente A no ve datos de Cliente B

---

## 🚫 Prerequisitos

**BLOQUEADO hasta:**
- ✅ Performance (NIVEL 0) al 80%
- ⚠️ PWA Offline (NIVEL 1) - Recomendado (clientes móviles)

**Justificación:**
- Nuevos roles agregan carga al sistema
- Clientes accederán desde móviles, PWA mejora experiencia

---

## 📊 Métricas de Progreso

```
Fase 1 (BD):                   [ ░░░░░░░░░░ ] 0%
Fase 2 (Modelos):              [ ░░░░░░░░░░ ] 0%
Fase 3 (Dashboard):            [ ░░░░░░░░░░ ] 0%
Fase 4 (Vistos):               [ ░░░░░░░░░░ ] 0%
Fase 5 (Notificaciones):       [ ░░░░░░░░░░ ] 0%
Fase 6 (Permisos):             [ ░░░░░░░░░░ ] 0%

TOTAL:                         [ ░░░░░░░░░░ ] 0%
```

---

## 📝 Testing

### Manual
1. Crear usuario con rol `usuario_empresarial`
2. Asignar 2 empresas específicas
3. Verificar dashboard muestra solo esas empresas
4. Completar inspección como técnico
5. Verificar notificación en cliente
6. Abrir inspección como cliente
7. Verificar campo `visto_por_cliente` = true
8. Verificar técnico recibe notificación de visto

### Seguridad
1. Intentar acceder a empresa no asignada vía URL
2. Intentar cambiar `empresa_id` en request
3. Verificar 403 Forbidden en ambos casos
