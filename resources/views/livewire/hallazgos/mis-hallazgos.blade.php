<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-ep-dark">Mis Hallazgos</h1>
            <p class="text-gray-600 mt-2">Hallazgos detectados por ti</p>
    </div>

    {{-- KPIs Personales --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total</div>
            <div class="text-2xl font-bold text-ep-dark">{{ $kpis['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Pendientes</div>
            <div class="text-2xl font-bold text-red-600">{{ $kpis['pendientes'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">En Proceso</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $kpis['en_proceso'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Resueltos</div>
            <div class="text-2xl font-bold text-green-600">{{ $kpis['resueltos'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Críticos Abiertos</div>
            <div class="text-2xl font-bold text-orange-600">{{ $kpis['criticos_abiertos'] }}</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar hallazgos..."
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
            <select wire:model.live="filterEstado" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
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
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-start gap-3 mb-2">
                            <h3 class="text-lg font-bold text-ep-dark">{{ $hallazgo->titulo }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($hallazgo->criticidad === 'critico') bg-red-100 text-red-800
                                @elseif($hallazgo->criticidad === 'alto') bg-orange-100 text-orange-800
                                @elseif($hallazgo->criticidad === 'medio') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($hallazgo->criticidad) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($hallazgo->estado === 'cerrado') bg-green-100 text-green-800
                                @elseif($hallazgo->estado === 'resuelto') bg-cyan-100 text-cyan-800
                                @elseif($hallazgo->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $hallazgo->estado)) }}
                            </span>
                        </div>
                        <p class="text-gray-600 mb-2">{{ Str::limit($hallazgo->descripcion, 150) }}</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>📍 {{ $hallazgo->sede->nombre_sede }}</span>
                            <span>📅 {{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? '-' }}</span>
                            @if($hallazgo->estaVencido())
                                <span class="text-red-600 font-bold">⚠️ VENCIDO</span>
                            @endif
                        </div>
                        <div class="flex gap-3 mt-2 text-xs text-gray-600">
                            @if($hallazgo->seguimientos_count > 0)
                                <span>📝 {{ $hallazgo->seguimientos_count }} seguimientos</span>
                            @endif
                            @if($hallazgo->fotos_count > 0)
                                <span>📷 {{ $hallazgo->fotos_count }} fotos</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('tecnico.hallazgos.show', $hallazgo) }}"
                           class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
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
