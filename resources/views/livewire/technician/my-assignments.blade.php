<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mis Tareas Pendientes</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Gestiona y completa los checklists asignados a tus sedes.
                </p>
            </div>
            
            <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-3">
                <!-- Filtro Empresa -->
                <select wire:model.live="selectedEmpresa" 
                        class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                    <option value="">Todas las Empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>

                <!-- Filtro Sede -->
                <select wire:model.live="selectedSede" 
                        class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm"
                        {{ !$selectedEmpresa ? 'disabled' : '' }}>
                    <option value="">Todas las Sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>

                <!-- Buscador -->
                <div class="relative rounded-md shadow-sm w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="block w-full rounded-md border-gray-300 pl-10 focus:border-ep-primary focus:ring-ep-primary sm:text-sm"
                           placeholder="Buscar tareas...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Tareas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($assignments as $assignment)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                @if($assignment->isOverdue()) bg-red-100 text-red-800 border-red-200
                                @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800 border-blue-200
                                @else bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                {{ $assignment->isOverdue() ? 'Vencido' : ($assignment->status === 'in_progress' ? 'En Progreso' : 'Pendiente') }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded">
                                {{ $assignment->getFrequencyLabel() }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight">
                            {{ $assignment->checklist->title }}
                        </h3>
                        
                        <div class="space-y-2 mt-4">
                            <div class="flex items-start text-sm">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-700">{{ $assignment->empresa->nombre }}</p>
                                    <p class="text-gray-500 text-xs">{{ $assignment->sede->nombre_sede }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="{{ $assignment->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    Vence: {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'Sin fecha' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('checklists.fill', ['id' => $assignment->dynamic_checklist_id, 'assignment' => $assignment->id]) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-ep-primary hover:bg-ep-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ep-primary transition-all">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ $assignment->status === 'in_progress' ? 'Continuar Inspección' : 'Iniciar Inspección' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay tareas pendientes</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search || $selectedEmpresa || $selectedSede)
                            Prueba ajustando tus filtros de búsqueda.
                        @else
                            ¡Buen trabajo! No tienes checklists asignados por el momento.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-8">
            {{ $assignments->links() }}
        </div>
    </div>
</div>
