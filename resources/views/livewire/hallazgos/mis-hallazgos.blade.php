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
                        Mis <span class="text-ep-primary italic">Hallazgos</span>
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

    {{-- KPIs Personales --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
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
        <div class="bg-white rounded-lg shadow p-4 col-span-2 sm:col-span-1">
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
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $hallazgo->sede->nombre_sede }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? '-' }}
                            </span>
                            @if($hallazgo->estaVencido())
                                <span class="text-red-600 font-bold whitespace-nowrap flex items-center gap-1">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    VENCIDO
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                            @if($hallazgo->seguimientos_count > 0)
                                <span class="bg-gray-100 px-2.5 py-1 rounded-md flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $hallazgo->seguimientos_count }} seguimientos
                                </span>
                            @endif
                            @if($hallazgo->fotos_count > 0)
                                <span class="bg-gray-100 px-2.5 py-1 rounded-md flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $hallazgo->fotos_count }} fotos
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full sm:w-auto mt-2 sm:mt-0 flex flex-col items-stretch">
                        <a href="{{ route('tecnico.hallazgos.show', $hallazgo) }}"
                           class="w-full text-center px-4 py-3 sm:py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-medium transition-colors shadow-sm text-sm"
                        >
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
