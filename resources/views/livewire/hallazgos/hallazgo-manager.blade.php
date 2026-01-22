<div>
    {{-- Header con KPIs --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-ep-dark">Gestión de Hallazgos</h1>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Total Hallazgos</div>
                <div class="text-2xl font-bold text-ep-dark">{{ $kpis['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Pendientes</div>
                <div class="text-2xl font-bold text-red-600">{{ $kpis['pendientes'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Críticos Abiertos</div>
                <div class="text-2xl font-bold text-orange-600">{{ $kpis['criticos'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Vencidos</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $kpis['vencidos'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Resueltos (Mes)</div>
                <div class="text-2xl font-bold text-green-600">{{ $kpis['resueltos_mes'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Búsqueda --}}
            <div class="lg:col-span-2">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por título, descripción o ubicación..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
            </div>

            {{-- Filtro Estado --}}
            <div>
                <select wire:model.live="filterEstado" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="resuelto">Resuelto</option>
                    <option value="verificado">Verificado</option>
                    <option value="cerrado">Cerrado</option>
                </select>
            </div>

            {{-- Filtro Criticidad --}}
            <div>
                <select wire:model.live="filterCriticidad" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todas las criticidades</option>
                    <option value="bajo">Bajo</option>
                    <option value="medio">Medio</option>
                    <option value="alto">Alto</option>
                    <option value="critico">Crítico</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Filtro Sede --}}
            <div>
                <select wire:model.live="filterSede" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todas las sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Técnico --}}
            <div>
                <select wire:model.live="filterTecnico" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todos los técnicos</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}">{{ $tecnico->nombres }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Vencidos --}}
            <div>
                <select wire:model.live="filterVencidos" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Vencimiento (todos)</option>
                    <option value="si">Solo vencidos</option>
                    <option value="no">No vencidos</option>
                </select>
            </div>
        </div>

        @if($search || $filterEstado || $filterCriticidad || $filterSede || $filterTecnico || $filterVencidos)
            <div class="mt-4">
                <button wire:click="clearFilters" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                    Limpiar Filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Tabla de Hallazgos --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($hallazgos->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xl">No se encontraron hallazgos</p>
                <p class="text-sm mt-2">Intenta ajustar los filtros o crea un nuevo hallazgo</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-ep-dark text-white">
                        <tr>
                            <th class="px-4 py-3 text-left cursor-pointer hover:bg-opacity-80" wire:click="sortBy('titulo')">
                                <div class="flex items-center gap-2">
                                    Título
                                    @if($sortField === 'titulo')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left">Sede</th>
                            <th class="px-4 py-3 text-center cursor-pointer hover:bg-opacity-80" wire:click="sortBy('criticidad')">
                                <div class="flex items-center justify-center gap-2">
                                    Criticidad
                                    @if($sortField === 'criticidad')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center cursor-pointer hover:bg-opacity-80" wire:click="sortBy('estado')">
                                <div class="flex items-center justify-center gap-2">
                                    Estado
                                    @if($sortField === 'estado')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center cursor-pointer hover:bg-opacity-80" wire:click="sortBy('plazo_correccion')">
                                <div class="flex items-center justify-center gap-2">
                                    Plazo
                                    @if($sortField === 'plazo_correccion')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left">Técnico</th>
                            <th class="px-4 py-3 text-center">Info</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($hallazgos as $hallazgo)
                            <tr class="hover:bg-gray-50 {{ $hallazgo->estaVencido() ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-ep-dark">{{ $hallazgo->titulo }}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($hallazgo->descripcion, 60) }}</div>
                                    @if($hallazgo->ubicacion_especifica)
                                        <div class="text-xs text-gray-400 mt-1">📍 {{ $hallazgo->ubicacion_especifica }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">{{ $hallazgo->sede->nombre_sede ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($hallazgo->criticidad === 'critico') bg-red-100 text-red-800
                                        @elseif($hallazgo->criticidad === 'alto') bg-orange-100 text-orange-800
                                        @elseif($hallazgo->criticidad === 'medio') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($hallazgo->criticidad) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($hallazgo->estado === 'cerrado') bg-green-100 text-green-800
                                        @elseif($hallazgo->estado === 'verificado') bg-blue-100 text-blue-800
                                        @elseif($hallazgo->estado === 'resuelto') bg-cyan-100 text-cyan-800
                                        @elseif($hallazgo->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $hallazgo->estado)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($hallazgo->plazo_correccion)
                                        <div class="text-sm {{ $hallazgo->estaVencido() ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                            {{ $hallazgo->plazo_correccion->format('d/m/Y') }}
                                        </div>
                                        @if($hallazgo->estaVencido())
                                            <div class="text-xs text-red-600">⚠️ Vencido</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">{{ $hallazgo->user->nombres ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2 text-xs text-gray-600">
                                        @if($hallazgo->seguimientos_count > 0)
                                            <span title="Seguimientos">📝 {{ $hallazgo->seguimientos_count }}</span>
                                        @endif
                                        @if($hallazgo->fotos_count > 0)
                                            <span title="Fotos">📷 {{ $hallazgo->fotos_count }}</span>
                                        @endif
                                        @if($hallazgo->incidentes_count > 0)
                                            <span title="Incidentes" class="text-red-600">⚠️ {{ $hallazgo->incidentes_count }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <a href="{{ route('gestor.hallazgos.show', $hallazgo) }}"
                                           class="px-3 py-1 bg-ep-primary text-white rounded hover:bg-ep-primary-dark text-sm">
                                            Ver
                                        </a>
                                        <a href="{{ route('gestor.hallazgos.edit', $hallazgo) }}"
                                           class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                                            Editar
                                        </a>
                                        <button wire:click="delete({{ $hallazgo->id }})"
                                                wire:confirm="¿Estás seguro de eliminar este hallazgo? Se eliminarán también todos sus seguimientos y fotos."
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $hallazgos->links() }}
            </div>
        @endif
    </div>

    {{-- Mensaje de éxito --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
