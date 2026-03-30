<div class="min-h-screen bg-[#11111d] pb-12 text-white">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
    
                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            SISTEMA DE <span class="text-white">OPERACIONES</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            CENTRO DE MANDO
                        </h2>
                    </div>

                    @if($myAlerts['overdue_tasks'] > 0)
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-red-500/10 border border-red-500/30 rounded-xl animate-pulse">
                            <span class="w-2 h-2 bg-red-500 rounded-full shadow-[0_0_10px_rgba(239,68,68,0.8)]"></span>
                            <span class="text-[9px] font-black text-red-400 uppercase tracking-widest italic">{{ $myAlerts['overdue_tasks'] }} ALERTAS</span>
                        </div>
                    @endif
                </div>
    
                <div class="flex items-center space-x-4">
                    @php
                        $user = auth()->user();
                        $canSwitch = $user->rol === \App\Models\User::ROLE_GESTOR || 
                                     $user->rol === \App\Models\User::ROLE_SUPER_ADMIN || 
                                     session('active_role') == \App\Models\User::ROLE_GESTOR;
                    @endphp

                    @if($canSwitch)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                           class="hidden md:flex bg-white/5 hover:bg-white/10 text-indigo-100/60 hover:text-white px-4 py-1.5 rounded-xl text-[9px] font-black border border-white/10 transition-all items-center gap-2 uppercase tracking-widest italic">
                           <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Modo Gestor
                        </a>
                    @endif
                    
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 bg-white/5 p-1 pr-4 rounded-2xl border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-8 h-8 bg-ep-primary rounded-xl flex items-center justify-center text-white text-xs font-black shadow-lg group-hover:scale-105 transition-transform">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10">
        
        {{-- Dashboard Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in-down">
            <div>
                <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic mb-2 leading-none">
                    {{ strtoupper($greeting) }}
                </h2>
                <div class="flex items-center gap-3">
                    <span class="w-8 h-1 bg-ep-primary rounded-full"></span>
                    <p class="text-sm text-indigo-100/40 font-bold italic uppercase tracking-wider">Monitoreo de actividad y protocolos pendientes</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button wire:click="$refresh" class="bg-white/5 p-3 rounded-2xl border border-white/10 hover:bg-ep-primary/20 hover:text-ep-primary text-indigo-100/40 transition-all shadow-inner group">
                    <svg class="w-6 h-6 group-hover:rotate-180 transition-transform duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Row 1: Tactical Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Tareas Pendientes -->
            <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden transition-all hover:border-orange-500/30">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-orange-500/5 rounded-full blur-2xl group-hover:bg-orange-500/10 transition-all duration-700"></div>
                <div class="flex items-center justify-between mb-8">
                    <div class="h-12 w-12 bg-white/5 rounded-2xl border border-white/10 text-orange-500 flex items-center justify-center rotate-3 group-hover:rotate-0 transition-transform shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.3em] mb-2 italic">Misiones Pendientes</h3>
                <div class="flex items-end gap-4 leading-none">
                    <span class="text-5xl font-black text-white italic">{{ $kpis['my_tasks']['pending'] }}</span>
                    <div class="pb-1">
                        <span class="text-[11px] font-black text-orange-500 uppercase italic leading-none border-b border-orange-500/30">{{ $kpis['my_tasks']['overdue'] }} Vencidas</span>
                    </div>
                </div>
            </div>

            <!-- Inspecciones Realizadas -->
            <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden transition-all hover:border-emerald-500/30">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all duration-700"></div>
                <div class="flex items-center justify-between mb-8">
                    <div class="h-12 w-12 bg-white/5 rounded-2xl border border-white/10 text-emerald-400 flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.3em] mb-2 italic">Protocolos Mes</h3>
                <div class="flex items-end gap-4 leading-none text-white">
                    <span class="text-5xl font-black text-white italic glow-emerald">{{ $kpis['my_inspections']['this_month'] }}</span>
                    <div class="pb-1">
                        <span class="text-[11px] font-black text-emerald-400 uppercase italic leading-none border-b border-emerald-500/30">{{ $kpis['my_inspections']['today'] }} Hoy</span>
                    </div>
                </div>
            </div>

            <!-- Capacitaciones -->
            <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden transition-all hover:border-indigo-500/30">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all duration-700"></div>
                <div class="flex items-center justify-between mb-8">
                    <div class="h-12 w-12 bg-white/5 rounded-2xl border border-white/10 text-indigo-400 flex items-center justify-center rotate-6 group-hover:rotate-0 transition-transform shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.3em] mb-2 italic">Capacitación</h3>
                <div class="flex items-end gap-4 leading-none">
                    <span class="text-5xl font-black text-white italic">{{ $kpis['my_trainings']['attended_this_month'] }}</span>
                    <div class="pb-1">
                        <span class="text-[11px] font-black text-indigo-400 uppercase italic leading-none border-b border-indigo-500/30">{{ $kpis['my_trainings']['upcoming'] }} Próximas</span>
                    </div>
                </div>
            </div>

            <!-- Constancias -->
            <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden transition-all hover:border-ep-primary/30">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-ep-primary/5 rounded-full blur-2xl group-hover:bg-ep-primary/10 transition-all duration-700"></div>
                <div class="flex items-center justify-between mb-8">
                    <div class="h-12 w-12 bg-white/5 rounded-2xl border border-white/10 text-ep-primary flex items-center justify-center -rotate-6 group-hover:rotate-0 transition-transform shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.3em] mb-2 italic">Certificación</h3>
                <div class="flex items-end gap-4 leading-none">
                    <span class="text-5xl font-black text-white italic">{{ $kpis['my_visit_certificates']['this_month'] }}</span>
                    <div class="pb-1">
                        <span class="text-[11px] font-black text-ep-primary uppercase italic leading-none border-b border-ep-primary/30">{{ $kpis['my_visit_certificates']['pending_signature'] }} Firmar</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Risk Meter --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Gestión de Hallazgos -->
            <div class="col-span-1 md:col-span-2 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                <div class="px-8 py-5 bg-black/20 border-b border-white/5 flex items-center justify-between relative z-10">
                    <h3 class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] flex items-center gap-3 italic leading-none">
                        <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Hallazgos Identificados
                    </h3>
                    @if($kpis['my_findings']['critical'] > 0)
                        <span class="px-3 py-1 bg-red-600 shadow-[0_0_15px_rgba(239,68,68,0.4)] text-white text-[9px] font-black rounded-lg animate-pulse uppercase tracking-widest italic">¡{{ $kpis['my_findings']['critical'] }} CRÍTICOS!</span>
                    @endif
                </div>
                <div class="p-10 grid grid-cols-3 gap-8 relative z-10">
                    <div class="text-center group">
                        <span class="block text-3xl font-black text-white italic mb-1">{{ $kpis['my_findings']['total'] }}</span>
                        <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.2em] italic group-hover:text-indigo-100/40 transition-colors">Registrados</span>
                    </div>
                    <div class="text-center group">
                        <span class="block text-3xl font-black text-red-500 italic mb-1 glow-text-red">{{ $kpis['my_findings']['pending'] }}</span>
                        <span class="text-[9px] font-black text-red-500/30 uppercase tracking-[0.2em] italic group-hover:text-red-500/50 transition-colors">Abiertos</span>
                    </div>
                    <div class="text-center group">
                        @php $eficacia = $kpis['my_findings']['total'] > 0 ? round((($kpis['my_findings']['total'] - $kpis['my_findings']['pending']) / $kpis['my_findings']['total']) * 100) : 0; @endphp
                        <span class="block text-3xl font-black {{ $eficacia > 80 ? 'text-emerald-400 glow-emerald-text' : 'text-indigo-400' }} italic mb-1">
                            {{ $eficacia }}%
                        </span>
                        <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.2em] italic group-hover:text-indigo-100/40 transition-colors">Eficacia</span>
                    </div>
                </div>
            </div>

            <!-- Incidentes Personales -->
            <div class="bg-indigo-900/60 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 text-white relative overflow-hidden group shadow-2xl">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div class="h-12 w-12 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-center text-white shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-300 italic">Métrica de Riesgo</span>
                </div>
                <h3 class="text-sm font-black text-indigo-100/60 mb-2 uppercase tracking-tight italic leading-none">Incidentes Personales</h3>
                <div class="flex items-baseline gap-3 mb-8 relative z-10">
                    <span class="text-6xl font-black text-white italic leading-none tracking-tighter">{{ $kpis['my_incidents']['total'] }}</span>
                    <span class="text-xs font-bold text-indigo-300 italic">TOTAL</span>
                </div>
                <div class="pt-6 border-t border-white/10 flex items-center justify-between relative z-10">
                    <span class="text-[10px] font-black text-indigo-200/40 uppercase tracking-widest italic">Reporte Mes:</span>
                    <span class="text-xl font-black italic">{{ $kpis['my_incidents']['this_month'] }}</span>
                </div>
            </div>
        </div>

        {{-- Row 3: Reports & Efficiency --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            <!-- Informes Maestros -->
            <div class="lg:col-span-7 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl flex flex-col md:flex-row items-center gap-10">
                <div class="h-20 w-20 flex-shrink-0 bg-white/5 rounded-3xl border border-white/10 text-ep-primary flex items-center justify-center shadow-inner group">
                    <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.4em] mb-4 italic">Mis Informes Maestros</h3>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-8">
                        <div>
                            <span class="block text-3xl font-black text-white italic leading-none">{{ $kpis['my_reports']['total_master'] }}</span>
                            <span class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">Totales</span>
                        </div>
                        <div class="h-10 w-px bg-white/5 hidden sm:block"></div>
                        <div>
                            <span class="block text-3xl font-black text-emerald-400 italic leading-none glow-emerald-text">{{ $kpis['my_reports']['completed'] }}</span>
                            <span class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">Finalizados</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 px-6 py-2 bg-indigo-500/10 rounded-2xl border border-indigo-500/20">
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                        {{ $kpis['my_reports']['pending'] }} EN EDICIÓN
                    </span>
                </div>
            </div>

            <!-- Rendimiento -->
            <div class="lg:col-span-5 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl flex flex-col justify-center gap-4 border-l-8 border-emerald-500/40 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
                <h3 class="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.4em] italic mb-1 relative z-10">Eficiencia Operativa (7D)</h3>
                <div class="flex flex-col gap-6 relative z-10">
                    <div class="flex items-end justify-between">
                        @php $rendimiento = count($myTasks) == 0 ? 100 : round(($kpis['my_inspections']['this_month'] / max(1, $kpis['my_tasks']['pending'] + $kpis['my_inspections']['this_month'])) * 100); @endphp
                        <span class="text-5xl font-black text-white italic leading-none">{{ $rendimiento }}%</span>
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic">Objetivo Superado</span>
                    </div>
                    <div class="h-3 bg-white/5 rounded-full overflow-hidden border border-white/5 shadow-inner">
                        <div class="h-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)] rounded-full transition-all duration-1000" style="width: {{ $rendimiento }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lower Section: Modules --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 mb-12">
            {{-- Left: Tasks HUB (Col 8) --}}
            <div class="lg:col-span-8 space-y-8 animate-fade-in" style="animation-delay: 0.4s">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                    <div class="px-10 py-8 bg-black/20 border-b border-white/5 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-ep-primary shadow-inner">
                                <svg class="w-6 h-6 rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-white tracking-tighter uppercase italic leading-none">PRÓXIMAS MISIONES</h3>
                        </div>
                        <span class="px-4 py-1.5 bg-ep-primary/10 rounded-xl text-[9px] font-black text-ep-primary border border-ep-primary/30 uppercase tracking-widest italic">
                            {{ count($myTasks) }} TAREAS ACTIVAS
                        </span>
                    </div>
                    <div class="divide-y divide-white/[0.03] relative z-10">
                        @forelse($myTasks as $task)
                            <div class="p-8 hover:bg-white/[0.04] transition-all duration-500 group">
                                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                                    <div class="flex items-center gap-6 w-full md:w-auto">
                                        <div class="w-4 h-12 bg-white/5 rounded-full group-hover:bg-ep-primary transition-all duration-700"></div>
                                        <div>
                                            <p class="text-lg font-black text-white italic group-hover:text-ep-primary transition-colors leading-none mb-2 uppercase tracking-tight">{{ $task->checklist->title }}</p>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">{{ $task->sede->nombre_sede }}</span>
                                                <span class="h-1 w-1 bg-white/10 rounded-full"></span>
                                                <span class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">{{ $task->checklist->categoryRelation?->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-indigo-100/10 uppercase tracking-widest leading-none mb-1 italic">FECHA LÍMITE</p>
                                            <p class="text-[13px] font-black {{ $task->isOverdue() ? 'text-red-500 glow-text-red' : 'text-indigo-100/60' }} italic">
                                                {{ $task->due_date?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('tecnico.checklists.fill', ['id' => $task->dynamic_checklist_id, 'assignment' => $task->id]) }}" 
                                           class="px-8 h-12 flex items-center justify-center bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-ep-primary/20 group/btn relative overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                            <span class="relative">EJECUTAR</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center bg-black/10">
                                <h3 class="text-sm font-black text-indigo-100/10 uppercase tracking-[0.4em] italic mb-2 leading-none">SECTOR DESPEJADO</h3>
                                <p class="text-[10px] text-indigo-100/5 font-bold uppercase tracking-widest italic leading-none">No hay tareas programadas en curso</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Analytics (Col 4) --}}
            <div class="lg:col-span-4 space-y-8 animate-fade-in" style="animation-delay: 0.5s">
                <!-- Trend Chart -->
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-8 w-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black italic">!</div>
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic leading-none">HISTORIAL 30D</h3>
                    </div>
                    <div style="height: 220px;" class="filter drop-shadow-lg">
                        <canvas id="myInspectionsChart"></canvas>
                    </div>
                </div>

                <!-- Productivity Gauge -->
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-8 w-8 rounded-lg bg-orange-500/10 border border-orange-500/20 text-orange-400 flex items-center justify-center font-black italic">⚡</div>
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic leading-none">PRODUCTIVIDAD 7D</h3>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="productivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-20 text-center">
            <p class="text-[9px] font-black text-indigo-100/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Tactical Command Unit • Model-03 • OS-V2.5</p>
        </div>
    </div>

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        .glow-emerald-text { text-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
        .glow-emerald { text-shadow: 0 0 15px rgba(16, 185, 129, 0.6); }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
    </style>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', initDash);
    document.addEventListener('DOMContentLoaded', initDash);

    function initDash() {
        const config = {
            font: "'Inter', sans-serif",
            colors: {
                primary: '#F69239',
                info: '#6366f1',
                success: '#10b981',
                muted: 'rgba(255, 255, 255, 0.05)',
                text: '#FFFFFF'
            }
        };

        Chart.defaults.font.family = config.font;
        Chart.defaults.color = config.text;

        // 1. Trend Line (Dark Version)
        const trendCtx = document.getElementById('myInspectionsChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($charts['myInspectionsPerMonth']['labels']),
                    datasets: [{
                        data: @json($charts['myInspectionsPerMonth']['data']),
                        borderColor: config.colors.info,
                        borderWidth: 4,
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: config.colors.info,
                        pointBorderColor: '#11111d',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false },
                        x: { 
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { 
                                font: { size: 9, weight: 'bold' },
                                padding: 10
                            }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Productivity Bar (Dark Version)
        const prodCtx = document.getElementById('productivityChart');
        if (prodCtx) {
            new Chart(prodCtx, {
                type: 'bar',
                data: {
                    labels: @json($charts['myProductivity']['labels']),
                    datasets: [{
                        data: @json($charts['myProductivity']['data']),
                        backgroundColor: config.colors.primary,
                        borderRadius: 8,
                        barThickness: 12,
                        hoverBackgroundColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            display: false 
                        },
                        x: { 
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { 
                                font: { size: 9, weight: 'bold' },
                                padding: 10
                            }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
</script>
@endpush
