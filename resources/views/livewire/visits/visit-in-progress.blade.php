<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    
    <!-- Header de Visita -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5 animate-pulse"></span>
                        En Progreso
                    </span>
                    <span class="text-sm font-medium text-gray-400">{{ $visit->visit_date->format('d \d\e F, Y') }}</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $visit->sede->nombre_sede }}</h1>
                <p class="text-lg text-gray-500 font-medium">{{ $visit->empresa->nombre }}</p>
                
                <div class="flex items-center mt-3 text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Iniciado a las <span class="font-bold text-gray-700 ml-1">{{ $visit->start_time->format('H:i') }} hs</span>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-2 md:mt-0">
                <!-- Botón Pausar/Salir -->
                @if(auth()->user()->hasRole('Administrador'))
                    <a href="{{ route('gestor.visits.history') }}" 
                @else
                    <a href="{{ route('tecnico.visits.index') }}" 
                @endif
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Salir / Pausar
                </a>
                
                <button 
                    wire:click="$set('showFinalizeModal', true)"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg shadow-green-600/20 transition-all hover:-translate-y-0.5"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Finalizar Visita
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
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
                                    @if($assignment->status === 'in_progress')
                                        <span class="inline-flex items-center text-xs text-blue-600 font-medium mt-1">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1 animate-pulse"></span>
                                            En progreso
                                        </span>
                                    @endif
                                </div>
                                <button 
                                    wire:click="openFillModal({{ $assignment->id }})"
                                    class="btn-xs {{ $assignment->status === 'in_progress' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-ep-primary hover:bg-orange-600' }} text-white rounded-full transition-colors z-10 relative"
                                >
                                    {{ $assignment->status === 'in_progress' ? 'Continuar' : 'Completar' }}
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

                    <!-- Capacitaciones Planificadas / Asignadas -->
                    @if(count($assignedTrainings) > 0)
                        <div class="mb-4 space-y-2">
                             <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Planificadas para hoy</h3>
                             @foreach($assignedTrainings as $assigned)
                                <div class="p-3 bg-blue-50 rounded border border-blue-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-800">{{ $assigned->title }}</h3>
                                            <p class="text-xs text-blue-600 mt-0.5">{{ $assigned->duration }} min • {{ $assigned->date->format('H:i') }} hs</p>
                                        </div>
                                        <button 
                                            wire:click="executeTraining({{ $assigned->id }})"
                                            class="btn-xs bg-blue-600 text-white rounded px-3 py-1 hover:bg-blue-700 transition-colors shadow-sm"
                                        >
                                            Realizar
                                        </button>
                                    </div>
                                    @if($assigned->description)
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $assigned->description }}</p>
                                    @endif
                                </div>
                             @endforeach
                        </div>
                    @endif
                    
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Realizadas en esta visita</h3>
                    
                    @if(count($trainings) > 0)
                        <div class="space-y-3">
                            @foreach($trainings as $training)
                                <div class="p-3 bg-gray-50 rounded border border-gray-100 transition-colors hover:border-blue-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $training->title }}</h3>
                                            @if($training->training)
                                                <span class="text-xs {{ $training->training->status === 'completed' ? 'text-green-600' : 'text-blue-600' }} font-semibold">
                                                    {{ $training->duration_minutes }} min
                                                    @if($training->training->status === 'completed')
                                                        • Completada
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-xs text-blue-600 font-semibold">{{ $training->duration_minutes }} min</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($training->training && $training->training->status === 'completed')
                                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                                                    ✓ Completada
                                                </span>
                                            @endif
                                            <button wire:click="removeTraining({{ $training->id }})" 
                                                    wire:confirm="¿Estás seguro de eliminar este registro de capacitación?"
                                                    class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                                                    title="Eliminar registro">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $training->description }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="text-xs text-gray-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $training->attendees_count }} asistentes
                                        </div>
                                        @if($training->training_id && (!$training->training || $training->training->status !== 'completed'))
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

                    <!-- Solicitudes de Entrega Pendientes -->
                    @php
                        $pendingRequests = $assignedEppRequests->where('status', 'pending');
                    @endphp
                    @if($pendingRequests->count() > 0)
                        <div class="mb-4 space-y-2">
                             <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Solicitudes Pendientes</h3>
                             @foreach($pendingRequests as $eppRequest)
                                <div class="p-3 bg-orange-50 rounded border border-orange-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-800">{{ $eppRequest->employee->name }} {{ $eppRequest->employee->last_name }}</h3>
                                            <p class="text-xs text-orange-600 mt-0.5">{{ count($eppRequest->items) }} ítems solicitados</p>
                                        </div>
                                        <button 
                                            wire:click="executeEppRequest({{ $eppRequest->id }})"
                                            class="btn-xs bg-orange-600 text-white rounded px-3 py-1 hover:bg-orange-700 transition-colors shadow-sm"
                                        >
                                            Entregar
                                        </button>
                                    </div>
                                </div>
                             @endforeach
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Entregas Realizadas</h3>
                    @endif

                    @if(count($eppDeliveries) > 0)
                        <div class="space-y-3">
                            @foreach($eppDeliveries as $delivery)
                                <div class="p-3 bg-gray-50 rounded border border-gray-100">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $delivery->employee->name }} {{ $delivery->employee->last_name }}</h3>
                                            <span class="text-xs text-orange-600 font-semibold">{{ count($delivery->epp_items ?? []) }} ítems</span>
                                        </div>
                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                                            ✓ Entregado
                                        </span>
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
                <button wire:click="openHallazgoModal" class="relative p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 font-medium hover:bg-yellow-100 transition-colors flex flex-col items-center justify-center">
                    @if(count($hallazgos) > 0)
                        <span class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-yellow-500 text-white text-xs font-bold shadow-sm">
                            {{ count($hallazgos) }}
                        </span>
                    @endif
                    <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Registrar Hallazgo
                </button>
                <button wire:click="openIncidenteModal" class="relative p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 font-medium hover:bg-red-100 transition-colors flex flex-col items-center justify-center">
                    @if(count($incidentes) > 0)
                        <span class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold shadow-sm">
                            {{ count($incidentes) }}
                        </span>
                    @endif
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
    
    <!-- Modal Finalizar Visita -->
    @if($showFinalizeModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 p-6 max-h-[90vh] overflow-y-auto" 
                 x-data="{ 
                    canvas: null,
                    ctx: null,
                    isDrawing: false,
                    lastX: 0,
                    lastY: 0,
                    signatureUnavailable: @entangle('signatureUnavailable'),
                    init() {
                        this.$nextTick(() => {
                            // Ensure canvas exists before init
                            if (this.$refs.signatureCanvas) {
                                this.canvas = this.$refs.signatureCanvas;
                                this.ctx = this.canvas.getContext('2d');
                                const rect = this.canvas.getBoundingClientRect();
                                this.canvas.width = rect.width;
                                this.canvas.height = rect.height;
                                this.ctx.strokeStyle = '#059669';
                                this.ctx.lineWidth = 2;
                                this.ctx.lineCap = 'round';
                                this.ctx.lineJoin = 'round';
                            }
                        });
                    },
                    startDrawing(e) {
                        this.isDrawing = true;
                        const rect = this.canvas.getBoundingClientRect();
                        this.lastX = e.clientX - rect.left;
                        this.lastY = e.clientY - rect.top;
                    },
                    draw(e) {
                        if (!this.isDrawing) return;
                        const rect = this.canvas.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        this.ctx.beginPath();
                        this.ctx.moveTo(this.lastX, this.lastY);
                        this.ctx.lineTo(x, y);
                        this.ctx.stroke();
                        this.lastX = x;
                        this.lastY = y;
                    },
                    stopDrawing() {
                        this.isDrawing = false;
                    },
                    clearSignature() {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    },
                    submitForm() {
                        if (!this.signatureUnavailable && this.canvas) {
                            const signatureData = this.canvas.toDataURL('image/png');
                            @this.set('responsibleSignature', signatureData);
                        }
                        @this.finalizeVisit();
                    }
                 }">
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Finalizar Visita</h3>
                    <p class="text-sm text-gray-500 text-center">
                        Complete los siguientes datos para generar la constancia de visita
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Observaciones de la Visita *</label>
                        <textarea wire:model="visitNotes" 
                                  rows="4"
                                  class="block w-full border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 text-sm"
                                  placeholder="Describa las actividades realizadas, hallazgos importantes, estado general de la sede, etc."></textarea>
                        @error('visitNotes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nombre del Responsable -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Responsable de la Sede *</label>
                        <input type="text" 
                               wire:model="responsibleName" 
                               class="block w-full border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 text-sm"
                               placeholder="Nombre completo de quien recibe la visita">
                        @error('responsibleName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Firma Digital -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-gray-700">Firma del Responsable *</label>
                            
                            <label class="flex items-center space-x-2 cursor-pointer text-sm">
                                <input type="checkbox" wire:model.live="signatureUnavailable" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-600">No disponible</span>
                            </label>
                        </div>
                        
                        <div x-show="!signatureUnavailable" style="display: none;">
                            <div wire:ignore>
                                <div class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden touch-none h-40">
                                    <canvas x-ref="signatureCanvas" 
                                            @mousedown="startDrawing($event)"
                                            @mousemove="draw($event)"
                                            @mouseup="stopDrawing()"
                                            @mouseleave="stopDrawing()"
                                            class="w-full h-full cursor-crosshair"></canvas>
                                    <button type="button" @click="clearSignature()" 
                                            class="absolute top-2 right-2 p-2 bg-white/90 backdrop-blur shadow-sm rounded-lg text-gray-500 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Firme dentro del recuadro usando mouse o pantalla táctil.</p>
                            </div>
                            <!-- Error solo visible si es required -->
                            @error('responsibleSignature') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div x-show="signatureUnavailable" style="display: none;" class="space-y-4 bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Motivo de la falta de firma *</label>
                                <textarea wire:model="signatureUnavailableReason" 
                                          rows="2"
                                          class="block w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                                          placeholder="Explique por qué no se pudo obtener la firma..."></textarea>
                                @error('signatureUnavailableReason') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email del Responsable (Opcional)</label>
                                <input type="email" wire:model="responsibleEmail" 
                                       class="block w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="cliente@ejemplo.com">
                                <p class="text-xs text-gray-500 mt-1">Si ingresa un email, se enviará un enlace para firmar digitalmente.</p>
                                @error('responsibleEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3 pt-4">
                        <button wire:click="$set('showFinalizeModal', false)" 
                                type="button"
                                class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button @click="submitForm()" 
                                type="button"
                                class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                            Generar Constancia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    


    <!-- Modal Entrega EPP -->
    @if($showEppModal)
        @livewire('visits.epp-delivery', [
            'visit' => $visit,
            'requestId' => $selectedEppRequestId
        ], key('epp-form-'.($selectedEppRequestId ?? 'new')))
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
</div>
