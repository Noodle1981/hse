<div class="px-4 sm:px-6 lg:px-8 pt-8 w-full max-w-9xl mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        Gestión de <span class="text-ep-primary italic">Incidentes</span>
                    </h1>
                </div>

                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                    <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                        {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                    </div>
                    <div class="h-4 w-px bg-white/10 mx-1"></div>
                    <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                </a>
            </div>
        </header>
    @endsection
    <div class="mb-6">

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
                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
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
                        <th class="px-4 py-3 text-center">Estado Licencias</th>
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
                                <div class="text-xs text-gray-500">{{ $incidente->sede->nombre_sede }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $incidente->personas_afectadas }}</td>
                            <td class="px-4 py-3 text-center">{{ $incidente->dias_perdidos ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($incidente->estado === 'cerrado') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($incidente->estado ?? 'pendiente') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.show', $incidente) : route('tecnico.incidentes.show', $incidente) }}" 
                                       class="p-1 px-2 bg-green-50 text-green-600 rounded hover:bg-green-100 text-xs font-bold"
                                       title="Ver Detalle">
                                        VER
                                    </a>
                                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.pdf.preview', $incidente) : route('tecnico.incidentes.pdf.preview', $incidente) }}" 
                                       target="_blank"
                                       class="p-1 px-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-xs font-bold"
                                       title="Ver Reporte">
                                        PDF
                                    </a>
                                    <button wire:click="delete({{ $incidente->id }})"
                                            wire:confirm="¿Eliminar este incidente?"
                                            class="p-1 text-red-600 hover:text-red-800"
                                            title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
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
