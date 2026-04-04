<div class="w-full mx-auto" wire:poll.60s="calculateDuration">
    {{-- Header de Visita Premium --}}
    @section('header_custom')
    <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
        <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-col">
                    <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Visita en <span class="text-white">Operación</span>
                    </h1>
                    <h2 class="text-sm font-bold text-indigo-100 truncate max-w-[150px] sm:max-w-xs uppercase tracking-tight">
                        {{ $visit->sede->nombre_sede }}
                    </h2>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                {{-- Espacio para acciones futuras si se requieren --}}
            </div>
        </div>
        <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent opacity-50"></div>
    </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-7xl mx-auto">

            <!-- Alerta de Duración Excedida Premium -->
            @if($isVisitaExcedida)
            <div class="mb-10 flex items-center justify-between bg-red-500/10 border border-red-500/20 p-6 rounded-[2rem] shadow-2xl overflow-hidden relative group animate-fade-in-down">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                <div class="relative flex items-center gap-5">
                    <div class="h-12 w-12 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center justify-center shadow-inner animate-pulse">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-black uppercase tracking-[0.2em] italic leading-none mb-2">
                            Alerta de <span class="text-red-500">Tiempo Límite</span>
                        </p>
                        <p class="text-indigo-100/60 text-[11px] font-bold italic uppercase tracking-wider">
                            Jornada excedida: {{ floor($durationMinutes / 60) }}h {{ $durationMinutes % 60 }}min
                        </p>
                    </div>
                </div>
                <button wire:click="dismissAlert"
                    class="relative px-5 py-2 bg-white/5 hover:bg-white/10 text-indigo-100/40 hover:text-white text-[10px] font-black rounded-xl border border-white/5 transition-all uppercase tracking-widest italic">
                    Ignorar
                </button>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                <!-- Columna Izquierda: Checklists (7 cols) -->
                <div class="lg:col-span-7 space-y-10 animate-fade-in">

                    @if($highlightTaskId)
                    <div class="bg-ep-primary/10 border border-ep-primary/30 rounded-3xl p-6 flex items-center justify-between animate-pulse">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-ep-primary text-white rounded-xl flex items-center justify-center shadow-lg italic">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-1">Misión Detectada</h4>
                                <p class="text-xs font-bold text-white uppercase italic">Protocolo sugerido para inicio inmediato</p>
                            </div>
                        </div>
                        <button @click="document.getElementById('task-' + {{ $highlightTaskId }}).scrollIntoView({behavior: 'smooth'})" class="text-[10px] font-black text-white/40 hover:text-white uppercase tracking-widest italic">Ubicar</button>
                    </div>
                    @endif
                    <!-- Checklists Pendientes -->
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                        <div class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent pointer-events-none opacity-50"></div>

                        <div class="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-white/5 border border-white/10 text-ep-primary flex items-center justify-center rotate-3 group-hover:rotate-0 transition-transform shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="font-black text-white uppercase tracking-[0.2em] text-[11px] italic leading-none">
                                    Checklists <span class="text-ep-primary">Pendientes</span>
                                </h2>
                            </div>
                            <button wire:click="openAutoAssignModal"
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-100/40 hover:text-ep-primary flex items-center gap-2 transition-all group/btn italic">
                                <svg class="w-4 h-4 group-hover/btn:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Auto-asignar
                            </button>
                        </div>

                        <div class="p-8 space-y-5 relative z-10">
                            @forelse($assignedChecklists as $assignment)
                            @php
                            $isHighlighted = ($assignment->id == $highlightTaskId || $assignment->dynamic_checklist_id == $highlightTaskId);
                            @endphp
                            <div id="task-{{ $assignment->id }}"
                                class="group relative {{ $isHighlighted ? 'bg-ep-primary/10 border-ep-primary/50 shadow-[0_0_25px_rgba(246,146,57,0.15)] ring-1 ring-ep-primary/20' : 'bg-white/[0.04] border-white/5' }} border rounded-3xl p-6 hover:border-ep-primary/30 hover:bg-white/[0.07] transition-all duration-500">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1 pr-6 flex items-center gap-4">
                                        <div class="w-1.5 h-8 rounded-full bg-ep-primary/20 group-hover:bg-ep-primary transition-all duration-500"></div>
                                        <div>
                                            <div class="flex items-center gap-3 mb-1.5">
                                                <h3 class="font-black text-white text-[13px] uppercase italic tracking-tight group-hover:text-ep-primary transition-colors">
                                                    {{ $assignment->checklist->title }}
                                                </h3>
                                                @if($isHighlighted)
                                                <div class="h-2 w-2 rounded-full bg-ep-primary animate-ping"></div>
                                                @endif
                                                @if($assignment->status === 'in_progress')
                                                <div class="flex items-center gap-1.5 px-3 py-0.5 bg-indigo-500/10 text-[9px] font-black uppercase tracking-widest text-indigo-400 rounded-full border border-indigo-400/20 italic animate-pulse">
                                                    Pulsando
                                                </div>
                                                @endif
                                            </div>
                                            <p class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.2em] italic">
                                                {{ $assignment->checklist->categoryRelation?->name }} <span class="mx-2 text-indigo-100/10">|</span> {{ $assignment->checklist->subcategoryRelation?->name }}
                                            </p>
                                        </div>
                                    </div>
                                    <button wire:click="openFillModal({{ $assignment->id }})"
                                        class="flex-shrink-0 px-6 py-2.5 h-10 {{ $assignment->status === 'in_progress' ? 'bg-indigo-600 shadow-indigo-900/40' : 'bg-ep-primary shadow-ep-primary/30' }} text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] italic shadow-lg hover:brightness-110 active:scale-95 transition-all overflow-hidden relative group/btn">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                        <span class="relative">{{ $assignment->status === 'in_progress' ? 'Reanudar' : 'Iniciar' }}</span>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-16 bg-black/10 rounded-[2rem] border border-dashed border-white/5">
                                <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-5 border border-white/10 text-indigo-100/20 rotate-12">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-[11px] font-black text-indigo-100/20 uppercase tracking-[0.3em] italic">Sector despejado de tareas</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Capacitaciones (Movido desde la derecha) -->
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent"></div>

                        <div class="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-blue-500/5 border border-blue-500/10 text-blue-500 flex items-center justify-center rotate-3 shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h2 class="font-black text-white uppercase tracking-[0.2em] text-[11px] italic leading-none">Capacitaciones</h2>
                            </div>
                            <button wire:click="openTrainingModal"
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 hover:text-blue-400 flex items-center gap-2 transition-all italic">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path d="M12 4v16m8-8H4" />
                                </svg>
                                Registrar
                            </button>
                        </div>

                        <div class="p-8 relative z-10">
                            <!-- Planificadas -->
                            @if(count($assignedTrainings) > 0)
                            <div class="mb-10">
                                <h3 class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                                    PLANIFICADAS HOY
                                    <span class="flex-1 h-px bg-white/5"></span>
                                </h3>
                                <div class="space-y-4">
                                    @foreach($assignedTrainings as $assigned)
                                    <div class="p-5 bg-blue-500/[0.03] rounded-3xl border border-blue-500/10 group transition-all hover:bg-blue-500/[0.06] hover:border-blue-500/30">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 pr-4">
                                                <h4 class="text-sm font-black text-white italic uppercase leading-tight group-hover:text-blue-500 transition-colors">{{ $assigned->title }}</h4>
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-3 py-0.5 rounded-lg border border-blue-500/20 italic">
                                                        {{ $assigned->duration }} min • {{ $assigned->date->format('H:i') }} hs
                                                    </span>
                                                </div>
                                            </div>
                                            <button wire:click="executeTraining({{ $assigned->id }})"
                                                class="h-10 px-5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-900/40 hover:brightness-110 active:scale-95 transition-all italic">
                                                Realizar
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Realizadas -->
                            <div>
                                <h3 class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                                    BITÁCORA DE HOY
                                    <span class="flex-1 h-px bg-white/5"></span>
                                </h3>

                                @if(count($trainings) > 0)
                                <div class="space-y-4">
                                    @foreach($trainings as $training)
                                    <div class="p-5 bg-white/[0.02] rounded-3xl border border-white/5 hover:bg-white/[0.05] transition-all duration-500 group">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex-1">
                                                <h4 class="text-[13px] font-black text-white italic uppercase tracking-tight">{{ $training->title }}</h4>
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span class="text-[9px] font-black {{ $training->training?->status === 'completed' ? 'text-emerald-500 bg-emerald-500/5 border-emerald-500/20' : 'text-indigo-400 bg-indigo-400/5 border-indigo-400/20' }} uppercase tracking-widest px-3 py-1 rounded-lg border italic">
                                                        {{ $training->duration_minutes }} min
                                                        @if($training->training?->status === 'completed') • FINALIZADA @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <button @click="$dispatch('open-confirm-modal', { 
                                                                    id: {{ $training->id }}, 
                                                                    title: '¿Eliminar Capacitación?', 
                                                                    message: 'Esta acción purgará el registro de capacitación. ¿Deseas continuar?',
                                                                    action: 'removeTraining',
                                                                    confirmLabel: 'Purgar Registro',
                                                                    confirmColor: 'bg-red-600'
                                                                })"
                                                class="h-10 w-10 flex items-center justify-center bg-white/5 border border-white/10 rounded-xl text-indigo-100/20 hover:text-red-500 hover:bg-red-500/10 active:scale-90 transition-all"
                                                title="Eliminar registro">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                                            <div class="flex items-center text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                {{ $training->attendees_count }} Asistentes
                                            </div>
                                            @if($training->training_id && (!$training->training || $training->training->status !== 'completed'))
                                            <button wire:click="openAttendance({{ $training->id }})"
                                                class="px-5 py-2 bg-indigo-500/10 text-indigo-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl border border-indigo-500/20 hover:bg-indigo-500 hover:text-white transition-all">
                                                Asistencia
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-10 bg-black/10 rounded-3xl border border-dashed border-white/5">
                                    <p class="text-[10px] font-black text-indigo-100/10 uppercase tracking-[0.3em] italic">Bitácora vacía para hoy</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Checklists Completados -->
                    @if($completedChecklists->count() > 0)
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-xl opacity-60 hover:opacity-100 transition-opacity duration-700">
                        <div class="px-8 py-5 border-b border-white/5 bg-black/10">
                            <h2 class="font-black text-indigo-100/40 uppercase tracking-[0.2em] text-[10px] italic">Protocolos <span class="text-emerald-500">Asegurados</span></h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($completedChecklists as $completion)
                            <div class="flex items-center justify-between bg-emerald-500/[0.05] p-4 rounded-2xl border border-emerald-500/30 group shadow-[0_0_15px_rgba(16,185,129,0.05)]">
                                <div class="flex items-center gap-4">
                                    <div class="h-8 w-8 bg-emerald-500/20 rounded-lg text-emerald-400 flex items-center justify-center italic shadow-[0_0_10px_rgba(16,185,129,0.3)] group-hover:scale-110 transition-transform">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black text-emerald-400/90 italic uppercase tracking-tight">{{ $completion->assignment->checklist->title }}</span>
                                </div>
                                <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-lg border border-emerald-500/40 italic shadow-[0_0_10px_rgba(16,185,129,0.1)]">
                                    {{ $completion->completed_at->format('H:i') }} hs
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Columna Derecha (5 cols) -->
                <div class="lg:col-span-5 space-y-10 animate-fade-in" style="animation-delay: 0.2s">
                    <!-- Control de Jornada -->
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>
                        <div class="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center shadow-inner">
                                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" />
                                    </svg>
                                </div>
                                <h2 class="font-black text-white uppercase tracking-[0.2em] text-[11px] italic leading-none">Control de <span class="text-emerald-500">Jornada</span></h2>
                            </div>
                        </div>
                        <div class="p-6 space-y-8 relative z-10">
                            <!-- Duración Principal -->
                            <div class="flex flex-col items-center justify-center p-6 bg-white/5 rounded-3xl border border-white/10 shadow-inner">
                                <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-[0.4em] mb-2 italic">Tiempo de Operación</span>
                                <div class="flex items-center gap-4">
                                    <span class="text-4xl font-black text-white italic tracking-tighter tabular-nums drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                                        {{ floor($durationMinutes / 60) }}h {{ $durationMinutes % 60 }}m
                                    </span>
                                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping shadow-[0_0_12px_rgba(16,185,129,0.5)]"></div>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <div class="h-1 w-8 rounded-full bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
                                    <div class="h-1 w-2 rounded-full bg-emerald-500/20"></div>
                                    <div class="h-1 w-2 rounded-full bg-emerald-500/20"></div>
                                </div>
                            </div>

                            <!-- Acciones Críticas -->
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ auth()->user()->isAdmin() ? route('gestor.visits.history') : route('tecnico.visits.index') }}"
                                    class="flex flex-col items-center justify-center gap-3 p-4 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all group">
                                    <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-indigo-100/40 group-hover:text-ep-primary transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" stroke-width="2.5" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest italic group-hover:text-white">Pausar</span>
                                </a>

                                <button wire:click="$set('showFinalizeModal', true)"
                                    class="flex flex-col items-center justify-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl hover:bg-emerald-500/20 hover:border-emerald-500/40 transition-all group">
                                    <div class="h-10 w-10 bg-emerald-500 border border-emerald-400 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-900/40 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" stroke-width="3" />
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest italic">Finalizar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hallazgos / Incidentes Premium -->
                    <div class="grid grid-cols-2 gap-6 relative">
                        <button wire:click="openHallazgoModal"
                            class="group relative p-8 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 hover:border-ep-primary/40 transition-all duration-700 shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center">
                                <div class="h-16 w-16 bg-white/5 rounded-2xl border border-white/10 text-ep-primary mb-4 flex items-center justify-center shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic mb-1 group-hover:text-white transition-colors">REGISTRAR</span>
                                <span class="text-xs font-black text-ep-primary uppercase tracking-[0.3em] italic">HALLAZGO</span>
                            </div>
                            @if(count($hallazgos) > 0)
                            <span class="absolute -top-3 -right-3 h-[42px] w-[42px] flex items-center justify-center rounded-2xl bg-ep-primary text-white text-xl font-black shadow-[0_0_25px_rgba(246,146,57,0.5)] border-2 border-[#11111d] italic z-20 transition-all animate-bounce-subtle">
                                {{ count($hallazgos) }}
                            </span>
                            @endif
                        </button>

                        <button wire:click="openIncidenteModal"
                            class="group relative p-8 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 hover:border-red-500/40 transition-all duration-700 shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-bl from-red-500/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center">
                                <div class="h-16 w-16 bg-white/5 rounded-2xl border border-white/10 text-red-500 mb-4 flex items-center justify-center shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic mb-1 group-hover:text-white transition-colors">REPORTAR</span>
                                <span class="text-xs font-black text-red-500 uppercase tracking-[0.3em] italic">INCIDENTE</span>
                            </div>
                            @if(count($incidentes) > 0)
                            <span class="absolute -top-3 -right-3 h-[42px] w-[42px] flex items-center justify-center rounded-2xl bg-red-500 text-white text-xl font-black shadow-[0_0_25px_rgba(239,68,68,0.5)] border-2 border-[#11111d] italic z-20 transition-all animate-bounce-subtle">
                                {{ count($incidentes) }}
                            </span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center">
                <p class="text-[10px] font-black text-indigo-100/10 uppercase tracking-[0.6em] italic leading-relaxed">XamaSafe Tactical Protocol • Access Level: Specialist</p>
            </div>
        </div>
    </div>

    {{-- Modales con Estética Dark Premium --}}

    <!-- Modal Auto-asignar Premium -->
    @if($showAutoAssignModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-black/80 backdrop-blur-xl transition-opacity animate-fade-in" aria-hidden="true" wire:click="$set('showAutoAssignModal', false)"></div>
            <div class="relative bg-ep-dark w-full max-w-lg rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden animate-fade-in-down">
                <div class="px-10 pt-10 pb-8 text-left">
                    <div class="flex items-center gap-5 mb-10">
                        <div class="h-14 w-14 rounded-2xl bg-ep-primary/10 border border-ep-primary/20 text-ep-primary flex items-center justify-center shadow-inner">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic leading-none mb-2">
                                Auto-Asignar <span class="text-ep-primary">Checklist</span>
                            </h3>
                            <p class="text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic">Expansión de Misión en Tiempo Real</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-black text-ep-primary uppercase tracking-[0.2em] mb-3 italic px-1">Seleccionar Protocolo</label>
                            <div class="relative group">
                                <select wire:model="selectedChecklistIdToAssign"
                                    class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all appearance-none cursor-pointer group-hover:bg-black/60 [color-scheme:dark]">
                                    <option value="" class="bg-ep-dark text-gray-500">Seleccione un checklist térmico...</option>
                                    @foreach($availableChecklists as $checklist)
                                    <option value="{{ $checklist->id }}" class="bg-ep-dark text-white">{{ $checklist->title }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-ep-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-black/20 px-10 py-6 flex flex-col sm:flex-row-reverse gap-4 border-t border-white/5">
                    <button type="button" wire:click="autoAssignChecklist"
                        class="w-full sm:w-auto h-12 px-10 bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl shadow-xl shadow-ep-primary/30 hover:brightness-110 active:scale-95 transition-all">
                        Vincular Checklist
                    </button>
                    <button type="button" wire:click="$set('showAutoAssignModal', false)"
                        class="w-full sm:w-auto h-12 px-10 bg-white/5 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all">
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
                            this.ctx.lineWidth = 3;
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
             }" x-show="true" x-cloak>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-black/80 backdrop-blur-xl transition-opacity animate-fade-in" aria-hidden="true" wire:click="$set('showFinalizeModal', false)"></div>

            <div class="relative bg-ep-dark w-full max-w-2xl rounded-[3rem] border border-white/10 shadow-3xl overflow-hidden animate-fade-in-down">
                <div class="px-8 pt-12 pb-8 sm:px-12">
                    <div class="flex flex-col items-center mb-10">
                        <div class="h-16 w-16 bg-emerald-500/10 rounded-3xl border border-emerald-500/20 text-emerald-500 mb-5 flex items-center justify-center shadow-inner">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-white tracking-tight uppercase italic text-center leading-none mb-3">
                            Finalizar <span class="text-emerald-500">Operación</span>
                        </h3>
                        <p class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.4em] italic text-center">Protocolo de Cierre Tactico</p>
                    </div>

                    <div class="space-y-8">
                        <!-- Observaciones -->
                        <div>
                            <label class="block text-[11px] font-black text-indigo-100/40 uppercase tracking-[0.2em] mb-3 italic px-1">Comandos de Cierre (Observaciones)*</label>
                            <textarea wire:model="visitNotes"
                                rows="3"
                                class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5"
                                placeholder="Describa el estado final de la visita técnica..."></textarea>
                            @error('visitNotes') <span class="text-red-500 text-[10px] font-black mt-2 italic block uppercase tracking-widest pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Responsable -->
                        <div>
                            <label class="block text-[11px] font-black text-indigo-100/40 uppercase tracking-[0.2em] mb-3 italic px-1">Oficial Responsable en Sede*</label>
                            <input type="text"
                                wire:model="responsibleName"
                                class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5"
                                placeholder="Nombre de la autoridad que certifica">
                            @error('responsibleName') <span class="text-red-500 text-[10px] font-black mt-2 italic block uppercase tracking-widest pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Firma -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between px-1">
                                <label class="block text-[11px] font-black text-emerald-500 uppercase tracking-[0.2em] italic">Panel de Certificación Digital*</label>

                                <label class="flex items-center space-x-3 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="signatureUnavailable" class="w-4 h-4 rounded border-white/10 bg-black/40 text-emerald-500 focus:ring-emerald-500/50">
                                    <span class="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest group-hover:text-white transition-colors italic">Inaccesible</span>
                                </label>
                            </div>

                            <div x-show="!signatureUnavailable" class="animate-fade-in">
                                <div wire:ignore class="relative h-56 bg-indigo-50 border-2 border-dashed border-indigo-200/30 rounded-[2rem] overflow-hidden shadow-inner group">
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
                                        class="absolute top-4 right-4 h-10 w-10 flex items-center justify-center bg-white shadow-xl rounded-xl text-gray-400 hover:text-red-500 hover:scale-110 active:scale-90 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-4 inset-x-0 text-center pointer-events-none opacity-20 group-hover:opacity-10 transition-opacity">
                                        <p class="text-[10px] font-black text-indigo-900 uppercase tracking-[0.4em] italic">Sello Manual Requerido</p>
                                    </div>
                                </div>
                                @error('responsibleSignature') <span class="text-red-500 text-[10px] font-black mt-3 italic block uppercase tracking-widest text-center">{{ $message }}</span> @enderror
                            </div>

                            <div x-show="signatureUnavailable" class="space-y-6 bg-black/30 p-8 rounded-[2rem] border border-white/5 shadow-inner animate-fade-in">
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest mb-3 italic">Justificación de Omisión*</label>
                                    <textarea wire:model="signatureUnavailableReason"
                                        rows="2"
                                        class="block w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                                        placeholder="Razón técnica o administrativa..."></textarea>
                                    @error('signatureUnavailableReason') <span class="text-red-500 text-[10px] font-black mt-2 italic block uppercase tracking-widest">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest mb-2 italic">Canal de Notificación (Email)</label>
                                    <input type="email" wire:model="responsibleEmail"
                                        class="block w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                                        placeholder="director@sede.com">
                                    @error('responsibleEmail') <span class="text-red-500 text-[10px] font-black mt-1 italic block uppercase tracking-widest">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-black/40 px-12 py-8 flex flex-col sm:flex-row-reverse gap-4 border-t border-white/5">
                    <button @click="submitForm()"
                        type="button"
                        class="w-full sm:w-auto h-14 px-12 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl shadow-2xl shadow-emerald-900/40 hover:brightness-110 active:scale-95 transition-all group flex items-center justify-center gap-3">
                        <span>Sellar Operación</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    <button wire:click="$set('showFinalizeModal', false)"
                        type="button"
                        class="w-full sm:w-auto h-14 px-12 bg-white/5 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all text-center">
                        Modificar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modales de Hallazgos e Incidentes -->
    @if($showHallazgoModal)
        <livewire:visits.hallazgo-quick :visit="$visit" wire:key="modal-hallazgo-{{ $visit->id }}" />
    @endif

    @if($showIncidenteModal)
        <livewire:visits.incidente-quick :visit="$visit" wire:key="modal-incidente-{{ $visit->id }}" />
    @endif


</div>