<div class="w-full mx-auto" wire:poll.60s="calculateDuration">
    {{-- Header de Visita Premium --}}
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

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Visita en <span class="text-white">Progreso</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 truncate max-w-[150px] sm:max-w-xs">
                            {{ $visit->sede->nombre_sede }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    {{-- Cronómetro --}}
                    <div class="hidden sm:flex items-center px-4 py-1.5 bg-white/5 rounded-2xl border border-white/10 text-white gap-2 shadow-inner">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-2.5 h-2.5 rounded-full bg-ep-primary animate-pulse shadow-[0_0_10px_rgba(246,146,57,0.5)]"></div>
                            <span class="text-xs font-black uppercase tracking-wider tabular-nums italic">
                                {{ floor($durationMinutes / 60) }}h {{ $durationMinutes % 60 }}m
                            </span>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <!-- Botón Pausar/Salir -->
                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.visits.history') : route('tecnico.visits.index') }}" 
                       class="bg-white/5 text-white hover:bg-white/10 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1 uppercase tracking-widest hidden xs:flex">
                        <svg class="w-3.5 h-3.5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Pausar
                    </a>
                    
                    <button wire:click="$set('showFinalizeModal', true)"
                            class="bg-emerald-600 text-white hover:brightness-110 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1 uppercase tracking-widest shadow-lg shadow-emerald-900/20 flex">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Finalizar
                    </button>
                </div>
            </div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <!-- Alerta de Duración Excedida Premium -->
        @if($isVisitaExcedida)
            <div class="mb-8 flex items-center justify-between bg-ep-dark border-l-4 border-red-500 p-5 rounded-2xl shadow-xl overflow-hidden relative group animate-pulse">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative flex items-center gap-4">
                    <div class="p-3 bg-white/5 rounded-xl border border-white/10 text-red-500 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-black uppercase tracking-widest italic leading-none mb-1">
                            Aviso: Jornada <span class="text-red-500">Excedida</span>
                        </p>
                        <p class="text-indigo-100/60 text-[11px] font-medium italic">
                            La visita lleva {{ floor($durationMinutes / 60) }}h {{ $durationMinutes % 60 }}min. ¿Has finalizado tus tareas?
                        </p>
                    </div>
                </div>
                <button wire:click="dismissAlert" 
                        class="relative px-4 py-2 bg-white/5 hover:bg-white/10 text-indigo-100 text-[10px] font-black rounded-xl border border-white/10 transition-all uppercase tracking-widest">
                    Ignorar
                </button>
            </div>
        @endif

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Columna Izquierda: Checklists (7 cols) -->
        <div class="lg:col-span-7 space-y-8">
            <!-- Checklists Pendientes -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-ep-primary/10 rounded-lg text-ep-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="font-black text-ep-dark uppercase tracking-widest text-xs italic">Checklists <span class="text-ep-primary">Pendientes</span></h2>
                    </div>
                    <button wire:click="openAutoAssignModal"
                            class="text-[10px] font-black uppercase tracking-widest text-ep-primary hover:text-orange-600 flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Auto-asignar
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($assignedChecklists as $assignment)
                        <div class="group relative bg-gray-50/30 border border-gray-100 rounded-2xl p-4 hover:border-ep-primary/30 hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 pr-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-bold text-gray-800 text-sm leading-tight group-hover:text-ep-primary transition-colors italic">
                                            {{ $assignment->checklist->title }}
                                        </h3>
                                        @if($assignment->status === 'in_progress')
                                            <div class="flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-[9px] font-black uppercase tracking-tighter text-blue-600 rounded-full border border-blue-100 italic animate-pulse">
                                                En curso
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">
                                        {{ $assignment->checklist->categoryRelation?->name }} <span class="mx-1 text-gray-300">/</span> {{ $assignment->checklist->subcategoryRelation?->name }}
                                    </p>
                                </div>
                                <button wire:click="openFillModal({{ $assignment->id }})"
                                        class="flex-shrink-0 px-5 py-2 {{ $assignment->status === 'in_progress' ? 'bg-blue-600 shadow-blue-200' : 'bg-ep-primary shadow-ep-primary/20' }} text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg hover:brightness-110 transition-all">
                                    {{ $assignment->status === 'in_progress' ? 'Continuar' : 'Completar' }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest italic">No hay checklists pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Checklists Completados -->
            @if($completedChecklists->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden opacity-80 group">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h2 class="font-black text-gray-500 uppercase tracking-widest text-[10px] italic">Completados en esta <span class="text-emerald-500">visita</span></h2>
                    </div>
                    <div class="p-6 space-y-3">
                        @foreach($completedChecklists as $completion)
                            <div class="flex items-center justify-between bg-emerald-50/30 p-3 rounded-xl border border-emerald-100/50">
                                <div class="flex items-center gap-3">
                                    <div class="p-1 bg-emerald-500 rounded text-white italic">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 italic">{{ $completion->assignment->checklist->title }}</span>
                                </div>
                                <span class="text-[9px] font-black text-emerald-600 uppercase tracking-tighter bg-white px-2 py-1 rounded-lg border border-emerald-100">
                                    {{ $completion->completed_at->format('H:i') }} hs
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Columna Derecha (5 cols) -->
        <div class="lg:col-span-5 space-y-8">
            <!-- Capacitaciones -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="font-black text-ep-dark uppercase tracking-widest text-xs italic">Capacitaciones</h2>
                    </div>
                    <button wire:click="openTrainingModal" 
                            class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 flex items-center gap-1.5 transition-colors">
                        + Registrar
                    </button>
                </div>

                <div class="p-6">
                    <!-- Planificadas -->
                    @if(count($assignedTrainings) > 0)
                        <div class="mb-6">
                            <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                <span class="w-8 h-px bg-gray-100"></span>
                                Planificadas Hoy
                                <span class="flex-1 h-px bg-gray-100"></span>
                            </h3>
                            <div class="space-y-3">
                                @foreach($assignedTrainings as $assigned)
                                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 group transition-all hover:bg-white hover:shadow-lg hover:shadow-blue-200/30">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-800 italic leading-tight group-hover:text-blue-600 transition-colors">{{ $assigned->title }}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-tighter bg-white px-2 py-0.5 rounded border border-blue-100 italic">
                                                        {{ $assigned->duration }} min • {{ $assigned->date->format('H:i') }} hs
                                                    </span>
                                                </div>
                                            </div>
                                            <button wire:click="executeTraining({{ $assigned->id }})"
                                                    class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-900/20 hover:brightness-110 transition-all">
                                                Realizar
                                            </button>
                                        </div>
                                        @if($assigned->description)
                                            <p class="text-[10px] text-gray-500 italic line-clamp-1 border-t border-blue-100/50 pt-2">{{ $assigned->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Realizadas -->
                    <div>
                        <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <span class="w-8 h-px bg-gray-100"></span>
                            Realizadas
                            <span class="flex-1 h-px bg-gray-100"></span>
                        </h3>
                        
                        @if(count($trainings) > 0)
                            <div class="space-y-3">
                                @foreach($trainings as $training)
                                    <div class="p-4 bg-gray-50/30 rounded-2xl border border-gray-100 hover:bg-white transition-all duration-300 group">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-gray-700 italic">{{ $training->title }}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[9px] font-black {{ $training->training?->status === 'completed' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-blue-600 bg-blue-50 border-blue-100' }} uppercase tracking-tighter px-2 py-0.5 rounded border italic">
                                                        {{ $training->duration_minutes }} min
                                                        @if($training->training?->status === 'completed') • Finalizada @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="$dispatch('open-confirm-modal', { 
                                                            id: {{ $training->id }}, 
                                                            title: '¿Eliminar Capacitación?', 
                                                            message: '¿Estás seguro de eliminar este registro de capacitación?',
                                                            action: 'removeTraining',
                                                            confirmLabel: 'Eliminar',
                                                            confirmColor: 'bg-red-600'
                                                        })" 
                                                        class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Eliminar registro">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100/50">
                                            <div class="flex items-center text-[9px] font-black text-gray-400 uppercase tracking-tighter italic">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                {{ $training->attendees_count }} asistentes
                                            </div>
                                            @if($training->training_id && (!$training->training || $training->training->status !== 'completed'))
                                                <button wire:click="openAttendance({{ $training->id }})" 
                                                        class="px-4 py-1.5 bg-indigo-50 text-indigo-700 text-[9px] font-black uppercase tracking-widest rounded-lg border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all">
                                                    Asistencia
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">Sin capacitaciones hoy</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hallazgos / Incidentes Premium -->
            <div class="grid grid-cols-2 gap-4">
                <button wire:click="openHallazgoModal" 
                        class="group relative p-6 bg-ep-dark rounded-3xl border border-white/5 hover:border-ep-primary/30 transition-all duration-500 shadow-2xl">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-ep-primary/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    </div>
                    <div class="relative flex flex-col items-center">
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/10 text-ep-primary mb-3 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] italic mb-1 group-hover:text-ep-primary transition-colors">Registrar</span>
                        <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.2em] italic">Hallazgo</span>
                    </div>
                    @if(count($hallazgos) > 0)
                        <span class="absolute -top-3 -right-3 h-10 w-10 flex items-center justify-center rounded-full bg-ep-primary text-white text-[15px] font-black shadow-[0_0_20px_rgba(246,146,57,0.4)] border-2 border-ep-dark italic animate-bounce-subtle z-10 transition-all">
                             {{ count($hallazgos) }}
                        </span>
                    @endif
                </button>

                <button wire:click="openIncidenteModal" 
                        class="group relative p-6 bg-ep-dark rounded-3xl border border-white/5 hover:border-red-500/30 transition-all duration-500 shadow-2xl">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-500/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    </div>
                    <div class="relative flex flex-col items-center">
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/10 text-red-500 mb-3 shadow-inner group-hover:scale-110 group-hover:-rotate-3 transition-transform">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] italic mb-1 group-hover:text-red-500 transition-colors">Reportar</span>
                        <span class="text-[10px] font-black text-red-500 uppercase tracking-[0.2em] italic">Incidente</span>
                    </div>
                    @if(count($incidentes) > 0)
                        <span class="absolute -top-3 -right-3 h-10 w-10 flex items-center justify-center rounded-full bg-red-500 text-white text-[15px] font-black shadow-[0_0_20px_rgba(239,68,68,0.4)] border-2 border-ep-dark italic animate-bounce-subtle z-10 transition-all">
                            {{ count($incidentes) }}
                        </span>
                    @endif
                </button>
            </div>
                </div>
            </div>
        </div>
       <!-- Modal Auto-asignar Premium -->
    @if($showAutoAssignModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" aria-hidden="true" wire:click="$set('showAutoAssignModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-ep-dark text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full rounded-3xl border border-white/10">
                    <div class="px-6 pt-5 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-3 bg-white/5 rounded-2xl border border-white/10 text-ep-primary">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">
                                Auto-Asignar <span class="text-ep-primary">Checklist</span>
                            </h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic">Seleccionar Checklist</label>
                                <div class="relative group">
                                    <select wire:model="selectedChecklistIdToAssign" 
                                            class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all appearance-none cursor-pointer group-hover:bg-white/10">
                                        <option value="" class="bg-ep-dark text-gray-400">Seleccione un checklist...</option>
                                        @foreach($availableChecklists as $checklist)
                                            <option value="{{ $checklist->id }}" class="bg-ep-dark text-white">{{ $checklist->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-ep-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-black/20 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-white/10">
                        <button type="button" wire:click="autoAssignChecklist" 
                                class="w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-ep-primary text-white text-[10px] font-black shadow-lg shadow-ep-primary/20 hover:brightness-110 focus:outline-none transition-all sm:w-auto uppercase tracking-widest disabled:opacity-50 disabled:grayscale">
                            Asignar
                        </button>
                        <button type="button" wire:click="$set('showAutoAssignModal', false)" 
                                class="mt-3 w-full inline-flex justify-center rounded-xl bg-white/5 px-6 py-2.5 text-white text-[10px] font-black border border-white/10 hover:bg-white/10 focus:outline-none transition-all sm:mt-0 sm:w-auto uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modal Finalizar Visita Premium -->
    @if($showFinalizeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             x-data="{ 
                canvas: null,
                ctx: null,
                isDrawing: false,
                lastX: 0,
                lastY: 0,
                signatureUnavailable: @entangle('signatureUnavailable'),
                init() {
                    this.$nextTick(() => {
                        if (this.$refs.signatureCanvas) {
                            this.canvas = this.$refs.signatureCanvas;
                            this.ctx = this.canvas.getContext('2d');
                            const rect = this.canvas.getBoundingClientRect();
                            this.canvas.width = rect.width;
                            this.canvas.height = rect.height;
                            this.ctx.strokeStyle = '#F69239'; // Primary color for signature
                            this.ctx.lineWidth = 2.5;
                            this.ctx.lineCap = 'round';
                            this.ctx.lineJoin = 'round';
                        }
                    });
                },
                startDrawing(e) {
                    this.isDrawing = true;
                    const rect = this.canvas.getBoundingClientRect();
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    this.lastX = clientX - rect.left;
                    this.lastY = clientY - rect.top;
                },
                draw(e) {
                    if (!this.isDrawing) return;
                    const rect = this.canvas.getBoundingClientRect();
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    const x = clientX - rect.left;
                    const y = clientY - rect.top;
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
             }" x-cloak>
            
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" aria-hidden="true" wire:click="$set('showFinalizeModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-ep-dark text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full rounded-3xl border border-white/10">
                    <div class="px-6 pt-8 pb-6 sm:px-10">
                        <div class="flex flex-col items-center mb-8">
                            <div class="p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-emerald-500 mb-4 shadow-inner">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-white tracking-tight uppercase italic text-center">
                                Finalizar <span class="text-emerald-500">Visita</span>
                            </h3>
                            <p class="text-xs font-medium text-indigo-100/40 uppercase tracking-[0.2em] mt-2 italic">Constancia de Visita Interactiva</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Observaciones -->
                            <div>
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Observaciones Finales *</label>
                                <textarea wire:model="visitNotes" 
                                          rows="3"
                                          class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all placeholder-indigo-100/20"
                                          placeholder="Resumen de actividades, hallazgos críticos, recomendaciones..."></textarea>
                                @error('visitNotes') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Responsable -->
                            <div>
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Responsable de Sede *</label>
                                <input type="text" 
                                       wire:model="responsibleName" 
                                       class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all placeholder-indigo-100/20"
                                       placeholder="Nombre completo de quien recibe la visita">
                                @error('responsibleName') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Firma -->
                            <div>
                                <div class="flex items-center justify-between mb-2 px-1">
                                    <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Firma Digital *</label>
                                    
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" wire:model.live="signatureUnavailable" class="rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500">
                                        <span class="text-[9px] font-black text-indigo-100/60 uppercase tracking-widest group-hover:text-white transition-colors italic">No disponible</span>
                                    </label>
                                </div>
                                
                                <div x-show="!signatureUnavailable">
                                    <div wire:ignore>
                                        <div class="relative bg-indigo-50 border-2 border-dashed border-indigo-200 rounded-2xl overflow-hidden touch-none h-48 shadow-inner">
                                            <canvas x-ref="signatureCanvas" 
                                                    @mousedown="startDrawing($event)"
                                                    @mousemove="draw($event)"
                                                    @mouseup="stopDrawing()"
                                                    @mouseleave="stopDrawing()"
                                                    @touchstart="startDrawing($event)"
                                                    @touchmove="draw($event)"
                                                    @touchend="stopDrawing()"
                                                    class="w-full h-full cursor-crosshair"></canvas>
                                            <button type="button" @click="clearSignature()" 
                                                    class="absolute top-3 right-3 p-2 bg-white shadow-lg rounded-xl text-gray-400 hover:text-red-500 transition-all hover:scale-110">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-[9px] text-indigo-100/30 mt-2 font-bold uppercase tracking-widest text-center italic">Firme dentro del recuadro (Mouse o Pantalla Táctil)</p>
                                    </div>
                                    @error('responsibleSignature') <span class="text-red-400 text-[10px] mt-1 italic block font-bold text-center">{{ $message }}</span> @enderror
                                </div>

                                <div x-show="signatureUnavailable" class="space-y-4 bg-white/5 p-5 rounded-2xl border border-white/10 shadow-inner">
                                    <div>
                                        <label class="block text-[9px] font-black text-indigo-100/60 uppercase tracking-widest mb-2 italic">Motivo de falta de firma *</label>
                                        <textarea wire:model="signatureUnavailableReason" 
                                                  rows="2"
                                                  class="block w-full bg-black/20 border border-white/5 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                                                  placeholder="Explique el motivo..."></textarea>
                                        @error('signatureUnavailableReason') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-black text-indigo-100/60 uppercase tracking-widest mb-1 italic">Email del Responsable (Opcional)</label>
                                        <input type="email" wire:model="responsibleEmail" 
                                               class="block w-full bg-black/20 border border-white/5 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                                               placeholder="email@ejemplo.com">
                                        @error('responsibleEmail') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-black/40 px-10 py-6 sm:flex sm:flex-row-reverse gap-4 border-t border-white/10">
                        <button @click="submitForm()" 
                                type="button"
                                class="w-full inline-flex justify-center rounded-xl px-8 py-3 bg-emerald-600 text-white text-[10px] font-black shadow-lg shadow-emerald-900/40 hover:brightness-110 focus:outline-none transition-all sm:w-auto uppercase tracking-widest italic group">
                            <span>Generar Constancia</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <button wire:click="$set('showFinalizeModal', false)" 
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-xl bg-white/5 px-8 py-3 text-white text-[10px] font-black border border-white/10 hover:bg-white/10 focus:outline-none transition-all sm:mt-0 sm:w-auto uppercase tracking-widest italic">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modales de Carga Rápidos -->
    @if($showHallazgoModal)
        @livewire('visits.hallazgo-quick', ['visit' => $visit], key('hallazgo-form'))
    @endif

    @if($showIncidenteModal)
        @livewire('visits.incidente-quick', ['visit' => $visit], key('incidente-form'))
    @endif

    <x-modal-confirm />

    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite;
        }
    </style>
            </div>
        </div>
    </div>
</div>
