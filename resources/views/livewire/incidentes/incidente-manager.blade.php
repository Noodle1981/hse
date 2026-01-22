<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-ep-dark">Gestión de Incidentes y Accidentes</h1>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Incidentes</div>
            <div class="text-2xl font-bold text-ep-dark">{{ $kpis['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Accidentes</div>
            <div class="text-2xl font-bold text-red-600">{{ $kpis['accidentes'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Graves/Fatales</div>
            <div class="text-2xl font-bold text-orange-600">{{ $kpis['graves'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Días Perdidos</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $kpis['dias_perdidos'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Personas Afectadas</div>
            <div class="text-2xl font-bold text-blue-600">{{ $kpis['personas_afectadas'] }}</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <select wire:model.live="filterTipo" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todos los tipos</option>
                <option value="accidente">Accidente</option>
                <option value="incidente">Incidente</option>
                <option value="cuasi_accidente">Cuasi Accidente</option>
            </select>
            <select wire:model.live="filterGravedad" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas las gravedades</option>
                <option value="leve">Leve</option>
                <option value="moderado">Moderado</option>
                <option value="grave">Grave</option>
                <option value="fatal">Fatal</option>
            </select>
            <select wire:model.live="filterSede" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas las sedes</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($incidentes->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <p class="text-xl">No hay incidentes registrados</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-ep-dark text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Gravedad</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Afectados</th>
                        <th class="px-4 py-3 text-center">Días Perdidos</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($incidentes as $incidente)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $incidente->fecha_ocurrencia->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($incidente->tipo === 'accidente') bg-red-100 text-red-800
                                    @elseif($incidente->tipo === 'incidente') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $incidente->tipo)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($incidente->gravedad === 'fatal') bg-red-100 text-red-800
                                    @elseif($incidente->gravedad === 'grave') bg-orange-100 text-orange-800
                                    @elseif($incidente->gravedad === 'moderado') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($incidente->gravedad) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs truncate">{{ $incidente->descripcion }}</div>
                                <div class="text-xs text-gray-500">{{ $incidente->sede->nombre }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $incidente->personas_afectadas }}</td>
                            <td class="px-4 py-3 text-center">{{ $incidente->dias_perdidos ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="delete({{ $incidente->id }})"
                                        wire:confirm="¿Eliminar este incidente?"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">
                {{ $incidentes->links() }}
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
