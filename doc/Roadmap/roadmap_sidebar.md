# 🎨 ROADMAP: Rediseño Sidebar

> **Nivel de dependencia:** ⚡ OPTIMIZACIÓN (NIVEL 1)  
> **Prerequisitos:** Ninguno (puede ejecutarse en paralelo)  
> **Prioridad:** 🟡 MEDIA  
> **Duración:** 1-2 semanas  
> **Sprint sugerido:** Sprint 3

---

## 📌 Propósito

Mejorar navegación del sidebar que se ha vuelto extenso. Agrupar lógicamente procedimientos para que sea más intuitivo y fácil de usar.

## 🎯 Objetivos

- Reducir scroll vertical en sidebar
- Agrupación lógica por categorías
- Accesos rápidos a funciones frecuentes
- Búsqueda integrada en sidebar
- Menú colapsable por secciones

## 🛠️ Implementación

### Fase 1: Análisis y Estructura (Semana 1)

#### 1.1 Categorización
```php
// config/sidebar.php
return [
    'grupos' => [
        [
            'titulo' => 'Dashboard',
            'icono' => 'home',
            'items' => [
                ['nombre' => 'Inicio', 'ruta' => 'dashboard', 'roles' => ['all']],
            ]
        ],
        [
            'titulo' => 'Inspecciones',
            'icono' => 'clipboard-check',
            'colapsable' => true,
            'items' => [
                ['nombre' => 'Mis Inspecciones', 'ruta' => 'inspections.index', 'roles' => ['tecnico']],
                ['nombre' => 'Nueva Inspección', 'ruta' => 'inspections.create', 'roles' => ['tecnico']],
                ['nombre' => 'Historial', 'ruta' => 'inspections.history', 'roles' => ['tecnico', 'admin']],
            ]
        ],
        [
            'titulo' => 'Administración',
            'icono' => 'cog',
            'colapsable' => true,
            'items' => [
                ['nombre' => 'Edificios', 'ruta' => 'edificios.index', 'roles' => ['admin', 'administrativo']],
                ['nombre' => 'Establecimientos', 'ruta' => 'establecimientos.index', 'roles' => ['admin', 'administrativo']],
                ['nombre' => 'Usuarios', 'ruta' => 'users.index', 'roles' => ['admin']],
                ['nombre' => 'Checklists', 'ruta' => 'checklists.index', 'roles' => ['admin']],
            ]
        ],
        [
            'titulo' => 'Reportes',
            'icono' => 'chart-bar',
            'colapsable' => true,
            'items' => [
                ['nombre' => 'Estadísticas', 'ruta' => 'reportes.stats', 'roles' => ['admin', 'administrativo']],
                ['nombre' => 'Constancias', 'ruta' => 'constancias.index', 'roles' => ['tecnico', 'admin']],
                ['nombre' => 'Auditoría EDUGE', 'ruta' => 'auditoria.index', 'roles' => ['admin', 'administrativo']],
            ]
        ],
        [
            'titulo' => 'Configuración',
            'icono' => 'adjustments',
            'colapsable' => true,
            'items' => [
                ['nombre' => 'Modalidades', 'ruta' => 'modalidades.index', 'roles' => ['admin']],
                ['nombre' => 'Tipos de Edificio', 'ruta' => 'tipos-edificio.index', 'roles' => ['admin']],
                ['nombre' => 'Diseño', 'ruta' => 'setup.index', 'roles' => ['admin']],
            ]
        ],
    ],
    
    'favoritos' => [], // Se llena dinámicamente por usuario
];
```

---

### Fase 2: Componente Livewire (Semana 1)

#### 2.1 Sidebar Component
```php
// app/Http/Livewire/Sidebar.php
namespace App\Http\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $collapsed = [];
    public $searchQuery = '';
    public $favoritos = [];
    
    public function mount()
    {
        // Cargar favoritos del usuario
        $this->favoritos = auth()->user()->favoritos ?? [];
        
        // Estado colapsado guardado en session
        $this->collapsed = session('sidebar_collapsed', []);
    }
    
    public function toggleGroup($index)
    {
        if (in_array($index, $this->collapsed)) {
            $this->collapsed = array_diff($this->collapsed, [$index]);
        } else {
            $this->collapsed[] = $index;
        }
        
        session(['sidebar_collapsed' => $this->collapsed]);
    }
    
    public function toggleFavorito($ruta)
    {
        if (in_array($ruta, $this->favoritos)) {
            $this->favoritos = array_diff($this->favoritos, [$ruta]);
        } else {
            $this->favoritos[] = $ruta;
        }
        
        auth()->user()->update(['favoritos' => $this->favoritos]);
    }
    
    public function render()
    {
        $grupos = collect(config('sidebar.grupos'))
            ->map(function ($grupo) {
                $grupo['items'] = collect($grupo['items'])
                    ->filter(function ($item) {
                        // Filtrar por rol
                        if ($item['roles'] === ['all']) return true;
                        return auth()->user()->hasAnyRole($item['roles']);
                    })
                    ->when($this->searchQuery, function ($items) {
                        return $items->filter(function ($item) {
                            return str_contains(strtolower($item['nombre']), strtolower($this->searchQuery));
                        });
                    })
                    ->toArray();
                
                return $grupo;
            })
            ->filter(fn($grupo) => count($grupo['items']) > 0)
            ->toArray();
        
        return view('livewire.sidebar', compact('grupos'));
    }
}
```

---

### Fase 3: Vista del Sidebar (Semana 1-2)

#### 3.1 Blade Template
```blade
<!-- resources/views/livewire/sidebar.blade.php -->
<div class="h-full flex flex-col bg-gray-900 text-white">
    <!-- Logo -->
    <div class="p-4 border-b border-gray-700">
        <h1 class="text-xl font-bold">EP-Back</h1>
    </div>
    
    <!-- Búsqueda -->
    <div class="p-4">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="searchQuery"
            placeholder="Buscar..."
            class="w-full bg-gray-800 text-white rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
        >
    </div>
    
    <!-- Favoritos -->
    @if(count($favoritos) > 0 && !$searchQuery)
        <div class="px-4 mb-4">
            <p class="text-xs uppercase text-gray-400 mb-2">⭐ Favoritos</p>
            <div class="space-y-1">
                @foreach($favoritos as $ruta)
                    @php
                        $item = collect(config('sidebar.grupos'))
                            ->pluck('items')
                            ->flatten(1)
                            ->firstWhere('ruta', $ruta);
                    @endphp
                    
                    @if($item)
                        <a href="{{ route($item['ruta']) }}" 
                           class="block px-3 py-2 rounded text-sm hover:bg-gray-800 {{ request()->routeIs($item['ruta']) ? 'bg-blue-600' : '' }}">
                            {{ $item['nombre'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="border-t border-gray-700 mb-4"></div>
    @endif
    
    <!-- Grupos de navegación -->
    <nav class="flex-1 overflow-y-auto px-4 space-y-2">
        @foreach($grupos as $index => $grupo)
            <div>
                <!-- Título del grupo -->
                <button 
                    wire:click="toggleGroup({{ $index }})"
                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-semibold hover:bg-gray-800 rounded"
                >
                    <span class="flex items-center gap-2">
                        <x-icon name="{{ $grupo['icono'] }}" class="w-5 h-5" />
                        {{ $grupo['titulo'] }}
                    </span>
                    
                    @if($grupo['colapsable'] ?? false)
                        <svg 
                            class="w-4 h-4 transition-transform {{ in_array($index, $collapsed) ? '' : 'rotate-90' }}"
                            fill="currentColor" 
                            viewBox="0 0 20 20"
                        >
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
                
                <!-- Items del grupo -->
                <div class="{{ in_array($index, $collapsed) ? 'hidden' : 'block' }} pl-4 mt-1 space-y-1">
                    @foreach($grupo['items'] as $item)
                        <div class="flex items-center gap-1 group">
                            <a 
                                href="{{ route($item['ruta']) }}" 
                                class="flex-1 px-3 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs($item['ruta']) ? 'bg-blue-600' : 'text-gray-300' }}"
                            >
                                {{ $item['nombre'] }}
                            </a>
                            
                            <!-- Botón favorito -->
                            <button 
                                wire:click="toggleFavorito('{{ $item['ruta'] }}')"
                                class="opacity-0 group-hover:opacity-100 p-1 hover:text-yellow-400 transition-opacity"
                            >
                                @if(in_array($item['ruta'], $favoritos))
                                    ⭐
                                @else
                                    ☆
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
    
    <!-- Usuario -->
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->roles->first()?->name }}</p>
            </div>
        </div>
    </div>
</div>
```

---

### Fase 4: Migración de Favoritos (Semana 2)

```php
// database/migrations/2025_12_31_add_favoritos_to_users.php
Schema::table('users', function (Blueprint $table) {
    $table->json('favoritos')->nullable();
});
```

---

## ✅ Criterios de Completitud

- [x] Sidebar agrupado por categorías lógicas
- [x] Grupos colapsables funcionando
- [x] Sistema de favoritos implementado
- [x] Búsqueda en sidebar
- [x] Filtrado por roles automático
- [x] Estado colapsado persistente en session
- [x] Diseño responsive

---

## 🚫 Prerequisitos

**Ninguno** - Puede ejecutarse en **paralelo** con Sprint 1 y 2

---

## 📊 Métricas de Progreso

```
Fase 1 (Análisis):             [ ░░░░░░░░░░ ] 0%
Fase 2 (Componente):           [ ░░░░░░░░░░ ] 0%
Fase 3 (Vista):                [ ░░░░░░░░░░ ] 0%
Fase 4 (Favoritos):            [ ░░░░░░░░░░ ] 0%

TOTAL:                         [ ░░░░░░░░░░ ] 0%
```

---

## 📝 UX Testing

**Métricas a medir:**
1. Tiempo para encontrar función (antes vs después)
2. Cantidad de clics para llegar a destino
3. Satisfacción de usuarios (encuesta)

**Objetivo:** Reducir 50% el tiempo de búsqueda de funciones
