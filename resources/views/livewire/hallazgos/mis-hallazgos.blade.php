<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="w-full">
            <h1 class="text-2xl sm:text-3xl font-bold text-ep-dark break-words">Mis Hallazgos</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">Hallazgos detectados por ti</p>
        </div>
    </div>

    {{-- KPIs Personales --}}
    <div class="flex overflow-x-auto md:grid md:grid-cols-5 gap-4 mb-6 pb-2 snap-x">
        <div class="bg-white rounded-lg shadow p-4 min-w-[140px] snap-start">
            <div class="text-sm text-gray-600">Total</div>
            <div class="text-2xl font-bold text-ep-dark">{{ $kpis['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 min-w-[140px] snap-start">
            <div class="text-sm text-gray-600">Pendientes</div>
            <div class="text-2xl font-bold text-red-600">{{ $kpis['pendientes'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 min-w-[140px] snap-start">
            <div class="text-sm text-gray-600">En Proceso</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $kpis['en_proceso'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 min-w-[140px] snap-start">
            <div class="text-sm text-gray-600">Resueltos</div>
            <div class="text-2xl font-bold text-green-600">{{ $kpis['resueltos'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 min-w-[160px] snap-start">
            <div class="text-sm text-gray-600">Críticos Abiertos</div>
            <div class="text-2xl font-bold text-orange-600">{{ $kpis['criticos_abiertos'] }}</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar hallazgos..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
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
    </div>

    {{-- Lista de Hallazgos --}}
    <div class="space-y-4">
        @forelse($hallazgos as $hallazgo)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1 w-full">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <h3 class="text-lg font-bold text-ep-dark break-words">{{ $hallazgo->titulo }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                @if($hallazgo->criticidad === 'critico') bg-red-100 text-red-800
                                @elseif($hallazgo->criticidad === 'alto') bg-orange-100 text-orange-800
                                @elseif($hallazgo->criticidad === 'medio') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($hallazgo->criticidad) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                @if($hallazgo->estado === 'cerrado') bg-green-100 text-green-800
                                @elseif($hallazgo->estado === 'resuelto') bg-cyan-100 text-cyan-800
                                @elseif($hallazgo->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $hallazgo->estado)) }}
                            </span>
                        </div>
                        <p class="text-gray-600 mb-3 text-sm sm:text-base">{{ Str::limit($hallazgo->descripcion, 150) }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1">📍 {{ $hallazgo->sede->nombre_sede }}</span>
                            <span class="flex items-center gap-1">📅 {{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? '-' }}</span>
                            @if($hallazgo->estaVencido())
                                <span class="text-red-600 font-bold whitespace-nowrap">⚠️ VENCIDO</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs text-gray-600">
                            @if($hallazgo->seguimientos_count > 0)
                                <span class="bg-gray-100 px-2 py-1 rounded">📝 {{ $hallazgo->seguimientos_count }} seguimientos</span>
                            @endif
                            @if($hallazgo->fotos_count > 0)
                                <span class="bg-gray-100 px-2 py-1 rounded">📷 {{ $hallazgo->fotos_count }} fotos</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full sm:w-auto mt-2 sm:mt-0 flex flex-col items-stretch">
                        <a href="{{ route('tecnico.hallazgos.show', $hallazgo) }}"
                           class="w-full text-center px-4 py-3 sm:py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-medium transition-colors">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
                <p class="text-xl">No tienes hallazgos registrados</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($hallazgos->hasPages())
        <div class="mt-6">
            {{ $hallazgos->links() }}
        </div>
    @endif
</div>
