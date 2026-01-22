<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    
    <!-- Header de Visita -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="sm:flex sm:justify-between sm:items-center">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        En Progreso
                    </span>
                    <span class="text-sm text-gray-500">{{ $visit->visit_date->format('d/m/Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $visit->sede->nombre_sede }}</h1>
                <p class="text-gray-600">{{ $visit->empresa->nombre }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col sm:items-end">
                <div class="text-sm text-gray-500">Iniciado a las {{ $visit->start_time->format('H:i') }}</div>
                <div class="text-xs text-gray-400">Duración: {{ $visit->start_time->diffInMinutes(now()) }} min</div>
                
                <div class="mt-2 flex space-x-2">
                    <!-- Botón temporal para pausar (volver atrás) -->
                    <a href="{{ route('visits.index') }}" class="btn-sm bg-white border-gray-200 hover:border-gray-300 text-gray-600">
                        <svg class="w-4 h-4 fill-current mr-2" viewBox="0 0 16 16">
                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6zM7 7V4h2v3h3v2H9v3H7V9H4V7h3z" />
                        </svg>
                        Pausar / Salir
                    </a>
                    
                    <button 
                        wire:click="$set('showFinalizeModal', true)"
                        class="btn-sm bg-green-600 hover:bg-green-700 text-white"
                    >
                        <svg class="w-4 h-4 fill-current mr-2" viewBox="0 0 16 16">
                            <path d="M12 0H4a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V2a2 2 0 00-2-2zM4 2h8v12H4V2z"/>
                            <path d="M11 6L7.5 9.5 5 7 3.5 8.5 7.5 12.5 12.5 7.5z"/>
                        </svg>
                        Finalizar Visita
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Columna Izquierda: Checklists -->
        <div class="space-y-6">
            <!-- Checklists Pendientes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800">Checklists Pendientes</h2>
                    <button 
                        wire:click="openAutoAssignModal"
                        class="text-sm text-ep-primary hover:text-orange-600 font-medium flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Auto-asignar
                    </button>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($assignedChecklists as $assignment)
                        <div class="border border-gray-200 rounded-lg p-3 hover:border-ep-primary transition-colors cursor-pointer group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-800 group-hover:text-ep-primary">
                                        {{ $assignment->checklist->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $assignment->checklist->categoryRelation?->name }} / {{ $assignment->checklist->subcategoryRelation?->name }}
                                    </p>
                                </div>
                                <button 
                                    wire:click="openFillModal({{ $assignment->id }})"
                                    class="btn-xs bg-ep-primary text-white rounded-full hover:bg-orange-600 transition-colors z-10 relative"
                                >
                                    Completar
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            No hay checklists pendientes. ¡Buen trabajo!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Checklists Completados -->
            @if($completedChecklists->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 opacity-75">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Completados en esta visita</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($completedChecklists as $completion)
                            <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $completion->assignment->checklist->title }}
                                <span class="ml-auto text-xs text-gray-400">
                                    {{ $completion->completed_at->format('H:i') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Columna Derecha: Otras Actividades -->
        <div class="space-y-6">
            <!-- Capacitaciones -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Capacitaciones</h2>
                        <button wire:click="openTrainingModal" class="text-sm bg-blue-50 text-blue-700 px-2 py-1 rounded hover:bg-blue-100">
                            + Registrar
                        </button>
                    </div>
                    
                    @if(count($trainings) > 0)
                        <div class="space-y-3">
                            @foreach($trainings as $training)
                                <div class="p-3 bg-gray-50 rounded border border-gray-100 transition-colors hover:border-blue-200">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $training->title }}</h3>
                                        <span class="text-xs text-blue-600 font-semibold">{{ $training->duration_minutes }} min</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $training->description }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="text-xs text-gray-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $training->attendees_count }} asistentes
                                        </div>
                                        @if($training->training_id)
                                            <button wire:click="openAttendance({{ $training->id }})" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                                                Tomar Asistencia
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400 text-sm italic">
                            No se han registrado capacitaciones
                        </div>
                    @endif
                </div>
            </div>

            <!-- Entregas EPP -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Entregas EPP</h2>
                        <button wire:click="openEppModal" class="text-sm bg-orange-50 text-orange-700 px-2 py-1 rounded hover:bg-orange-100">
                            + Entregar
                        </button>
                    </div>

                    @if(count($eppDeliveries) > 0)
                        <div class="space-y-3">
                            @foreach($eppDeliveries as $delivery)
                                <div class="p-3 bg-gray-50 rounded border border-gray-100">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $delivery->employee->name }} {{ $delivery->employee->last_name }}</h3>
                                        <span class="text-xs text-orange-600 font-semibold">{{ count($delivery->epp_items ?? []) }} ítems</span>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        @foreach(array_slice($delivery->epp_items ?? [], 0, 2) as $item)
                                            <span class="inline-block bg-white border border-gray-200 rounded px-1">{{ $item['name'] }}</span>
                                        @endforeach
                                        @if(count($delivery->epp_items ?? []) > 2)
                                            <span class="text-gray-400">+{{ count($delivery->epp_items) - 2 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                         <div class="text-center py-6 text-gray-400 text-sm italic">
                            No se han registrado entregas
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hallazgos / Incidentes (Accesos rápidos) -->
            <div class="grid grid-cols-2 gap-4">
                <button wire:click="openHallazgoModal" class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 font-medium hover:bg-yellow-100 transition-colors flex flex-col items-center justify-center">
                    <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Registrar Hallazgo
                </button>
                <button wire:click="openIncidenteModal" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 font-medium hover:bg-red-100 transition-colors flex flex-col items-center justify-center">
                    <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Reportar Incidente
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Auto-asignar -->
    @if($showAutoAssignModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showAutoAssignModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Auto-asignar Checklist
                        </h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Checklist</label>
                            <select wire:model="selectedChecklistIdToAssign" class="w-full border-gray-300 rounded-md shadow-sm focus:border-ep-primary focus:ring focus:ring-ep-primary focus:ring-opacity-50">
                                <option value="">Seleccione...</option>
                                @foreach($availableChecklists as $checklist)
                                    <option value="{{ $checklist->id }}">{{ $checklist->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="autoAssignChecklist" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ep-primary text-base font-medium text-white hover:bg-orange-600 sm:ml-3 sm:w-auto sm:text-sm">
                            Asignar
                        </button>
                        <button type="button" wire:click="$set('showAutoAssignModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modal Finalizar (Placeholder) -->
    @if($showFinalizeModal)
        @livewire('visits.visit-finalizer', ['visit' => $visit], key('finalizer-'.$visit->id))
    @endif
    
    <!-- Modal Llenado Checklist -->
    @if($showFillModal && $selectedAssignmentToFill)
        @livewire('visits.checklist-filler', [
            'visit' => $visit, 
            'assignment' => $selectedAssignmentToFill
        ], key('filler-'.$selectedAssignmentToFill->id))
    @endif



    <!-- Modal Entrega EPP -->
    @if($showEppModal)
        @livewire('visits.epp-delivery', [
            'visit' => $visit
        ], key('epp-form'))
    @endif

    <!-- Modal Hallazgo Rápido -->
    @if($showHallazgoModal)
        @livewire('visits.hallazgo-quick', [
            'visit' => $visit
        ], key('hallazgo-form'))
    @endif

    <!-- Modal Incidente Rápido -->
    @if($showIncidenteModal)
        @livewire('visits.incidente-quick', [
            'visit' => $visit
        ], key('incidente-form'))
    @endif

</div>
