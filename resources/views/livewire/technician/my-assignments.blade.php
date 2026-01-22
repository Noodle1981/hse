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

        @if($trainings->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Capacitaciones Pendientes
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($trainings as $training)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow relative">
                        <div class="absolute top-0 right-0 mt-3 mr-3 flex gap-2">
                             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                {{ ucfirst($training->type) }}
                            </span>
                            @if($training->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Completado
                                </span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight pr-24">
                                {{ $training->title }}
                            </h3>
                            
                            <div class="space-y-2 mt-4">
                                <div class="flex items-start text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-700">{{ $training->empresa->nombre }}</p>
                                        <p class="text-gray-500 text-xs">{{ $training->sede->nombre_sede }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">
                                        {{ $training->date->format('d/m/Y H:i') }} hs
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-gray-600">
                                        Dictado por: {{ $training->trainer_name ?? 'Por definir' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($eppRequests->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Solicitudes de Entrega EPP
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($eppRequests as $request)
                    <div class="bg-white rounded-xl shadow-sm border border-orange-200 overflow-hidden hover:shadow-md transition-shadow">
                         <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $request->employee->name }} {{ $request->employee->last_name }}
                                </h3>
                                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-0.5 rounded-full font-medium">Pendiente</span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3">
                                {{ count($request->items) }} ítems solicitados por {{ $request->requester->nombres }}
                            </p>

                            <div class="space-y-1 mb-4">
                                @foreach(array_slice($request->items, 0, 3, true) as $itemId => $itemData)
                                    @php 
                                        // selectedItems format: [item_id => ['cantidad' => X, 'tipo_entrega' => Y]]
                                        $eppItem = \App\Models\EppItem::find($itemId); 
                                        $cantidad = is_array($itemData) ? ($itemData['cantidad'] ?? 1) : 1;
                                    @endphp
                                    <div class="text-xs bg-gray-50 border px-2 py-1 rounded text-gray-600 flex justify-between">
                                        <span>{{ $eppItem->nombre ?? 'Item #'.$itemId }}</span>
                                        <span class="font-bold">x{{ $cantidad }}</span>
                                    </div>
                                @endforeach
                                @if(count($request->items) > 3)
                                    <span class="text-xs text-gray-400 italic">+{{ count($request->items) - 3 }} más...</span>
                                @endif
                            </div>

                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $request->sede->nombre_sede }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($assignments->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Checklists Asignados
        </h2>
        @endif

        <!-- Grid de Tareas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($assignments as $assignment)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                @if($assignment->status === 'completed') bg-green-100 text-green-800 border-green-200
                                @elseif($assignment->isOverdue()) bg-red-100 text-red-800 border-red-200
                                @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800 border-blue-200
                                @else bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                @if($assignment->status === 'completed') Completado
                                @elseif($assignment->isOverdue()) Vencido
                                @elseif($assignment->status === 'in_progress') En Progreso
                                @else Pendiente @endif
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
                                <span class="{{ $assignment->isOverdue() && $assignment->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    Vence: {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'Sin fecha' }}
                                </span>
                            </div>
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
