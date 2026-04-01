<div> {{-- Unico Elemento Raiz --}}
    <div class="px-4 sm:px-6 lg:px-8 py-10 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[98rem] mx-auto">
            
            {{-- TACTICAL HUD: LOCALIZED SELECTORS --}}
            <div class="flex flex-wrap items-center justify-between gap-6 mb-12 animate-fade-in-down">
                <div class="flex flex-col">
                    <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1">DASHBOARD ESTRATÉGICO</h1>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 bg-ep-primary rounded-full animate-pulse shadow-[0_0_8px_var(--tw-shadow-color)] shadow-ep-primary"></div>
                        <h2 class="text-2xl font-black text-white italic tracking-tighter uppercase">COMANDO CENTRAL</h2>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 bg-black/20 backdrop-blur-3xl p-2 rounded-[2rem] border border-white/5 shadow-2xl">
                    {{-- Selector Empresa --}}
                    <div class="flex items-center bg-white/5 border border-white/5 rounded-2xl px-4 py-1.5 group hover:border-ep-primary/30 transition-all">
                        <span class="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mr-3 italic group-hover:text-ep-primary transition-colors">Empresa</span>
                        <select wire:model.live="empresaId" class="bg-transparent border-none text-[10px] font-black text-white uppercase tracking-widest focus:ring-0 cursor-pointer [color-scheme:dark]">
                            <option value="">Todas las Empresas</option>
                            @foreach($empresas as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector Sede --}}
                    <div class="flex items-center bg-white/5 border border-white/5 rounded-2xl px-4 py-1.5 group hover:border-ep-primary/30 transition-all">
                        <span class="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mr-3 italic group-hover:text-ep-primary transition-colors">Sede</span>
                        <select wire:model.live="sedeId" class="bg-transparent border-none text-[10px] font-black text-white uppercase tracking-widest focus:ring-0 cursor-pointer [color-scheme:dark]">
                            @if(!$empresaId) <option value="">Todas las Sedes</option> @endif
                            @foreach($sedes as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector Ciclo (Year) --}}
                    <div x-data="{ open: false, selected: @entangle('year') }" class="relative" @click.away="open = false text-white">
                        <button @click="open = !open" class="h-10 px-5 bg-ep-primary/10 border border-ep-primary/20 rounded-2xl flex items-center gap-3 hover:bg-ep-primary/20 transition-all shadow-inner group">
                            <span class="text-[8px] font-black text-ep-primary italic tracking-[0.2em] uppercase">CICLO</span>
                            <span class="text-xs font-black text-white italic" x-text="selected"></span>
                            <svg class="w-3 h-3 text-ep-primary group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute top-full right-0 mt-3 w-32 bg-[#1E1E2D] border border-white/10 rounded-[1.5rem] shadow-2xl py-3 z-50">
                            @foreach([2024, 2025, 2026] as $y)
                                <div @click="selected = {{ $y }}; open = false; $wire.set('year', {{ $y }})" class="px-6 py-2.5 text-[10px] font-black text-white/50 hover:text-white hover:bg-ep-primary/20 cursor-pointer transition-colors uppercase italic tracking-widest">
                                    {{ $y }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- HERO SECTION: STRATEGIC HEALTH --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                {{-- Main Compliance Gauge --}}
                <div class="lg:col-span-4 bg-gradient-to-br from-indigo-600/30 to-indigo-900/10 backdrop-blur-xl p-10 rounded-[3.5rem] border border-white/10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-[80px] group-hover:scale-125 transition-transform duration-1000"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] italic mb-8">SALUD OPERATIVA GLOBAL</span>
                        <div class="relative w-48 h-48 mb-8">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-white/5" stroke-width="3"></circle>
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-indigo-500 shadow-glow-indigo" stroke-width="3" stroke-dasharray="{{ $efficiency['global'] }}, 100" stroke-linecap="round" style="transition: stroke-dasharray 1.5s ease-in-out;"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-5xl font-black text-white italic tracking-tighter">{{ $efficiency['global'] }}%</span>
                                <span class="text-[8px] font-bold text-indigo-100/40 uppercase tracking-widest mt-1">NIVEL DE ÉXITO</span>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="px-4 py-2 bg-black/40 border border-white/5 rounded-2xl text-center min-w-[80px]">
                                <p class="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mb-1 italic">Vencidos</p>
                                <p class="text-xl font-black text-red-500 italic">{{ $efficiency['counts']['overdue'] }}</p>
                            </div>
                            <div class="px-4 py-2 bg-black/40 border border-white/5 rounded-2xl text-center min-w-[80px]">
                                <p class="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mb-1 italic">Conforme</p>
                                <p class="text-xl font-black text-emerald-400 italic">{{ $efficiency['counts']['completed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Operational KPI Matrix --}}
                <div class="lg:col-span-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                    @php
                        $mainKpis = [
                            ['label' => 'Inspecciones', 'val' => $kpis['inspections']['total_period'], 'sub' => 'Realizadas', 'color' => 'orange'],
                            ['label' => 'Hallazgos', 'val' => $kpis['findings']['total'], 'sub' => 'Detectados', 'color' => 'red'],
                            ['label' => 'Capacitaciones', 'val' => $kpis['trainings']['total_scheduled'], 'sub' => 'Programadas', 'color' => 'blue'],
                            ['label' => 'Informes', 'val' => $kpis['reports']['total_master'], 'sub' => 'Generados', 'color' => 'emerald'],
                        ];
                    @endphp
                    @foreach($mainKpis as $kpi)
                        <div class="bg-ep-dark/40 border border-white/5 backdrop-blur-md p-6 rounded-[2.5rem] shadow-xl relative overflow-hidden group hover:border-{{ $kpi['color'] }}-500/30 transition-all">
                            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-{{ $kpi['color'] }}-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                            <div class="relative z-10">
                                <span class="text-[8px] font-black text-white/20 uppercase tracking-[0.2em] italic mb-4 block">{{ $kpi['label'] }}</span>
                                <p class="text-4xl font-black text-white italic tracking-tighter mb-2">{{ $kpi['val'] }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-0.5 bg-{{ $kpi['color'] }}-500 rounded-full shadow-[0_0_8px_var(--tw-shadow-color)] shadow-{{ $kpi['color'] }}-500"></div>
                                    <span class="text-[9px] font-bold text-indigo-100/40 uppercase tracking-widest italic">{{ $kpi['sub'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Secondary Matrix (Large) --}}
                    @php
                        $periodLabels = [
                            'this_month' => 'Este Mes',
                            'last_month' => 'Mes Anterior',
                            'this_quarter' => 'Este Trimestre',
                            'this_year' => 'Este Año',
                            'last_year' => 'Año Anterior',
                            'custom' => 'Período',
                        ];
                        $periodLabel = $periodLabels[$period] ?? 'Este Mes';
                    @endphp
                    <div class="col-span-2 md:col-span-4 bg-ep-dark/40 border border-white/5 backdrop-blur-md p-8 rounded-[3rem] shadow-xl flex items-center justify-between">
                        <div class="flex items-center gap-10">
                            @foreach([
                                ['label' => 'Visitas Registradas', 'val' => $kpis['visit_certificates']['total_period'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                ['label' => 'Incidentes Graves', 'val' => $kpis['incidents']['serious'], 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'text-red-500'],
                            ] as $skpi)
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 bg-white/5 rounded-2xl flex items-center justify-center text-white/50 border border-white/10">
                                        <svg class="w-6 h-6 {{ $skpi['color'] ?? '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $skpi['icon'] }}"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-black text-white italic leading-none">{{ $skpi['val'] }}</p>
                                        <span class="text-[8px] font-black text-indigo-100/30 uppercase tracking-[0.2em] italic">{{ $skpi['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white/5 rounded-full border border-white/5">
                            <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></div>
                            <span class="text-[8px] font-black text-indigo-100/30 uppercase tracking-[0.15em] italic">{{ $periodLabel }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STRATEGIC EFFICIENCY PILLARS WITH MICRO-TIMELINES --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @foreach([
                    ['id' => 'inspeccion', 'label' => 'Inspecciones Técnicas', 'color' => 'orange', 'route' => 'Inspección'],
                    ['id' => 'capacitacion', 'label' => 'Capacitación HSE', 'color' => 'blue', 'route' => 'Capacitación'],
                    ['id' => 'informe', 'label' => 'Reportes Maestros', 'color' => 'emerald', 'route' => 'Informe'],
                ] as $pilar)
                    <a href="{{ route('gestor.annual-contract.index', ['sede_id' => $sedeId, 'year' => $year, 'category' => $pilar['route']]) }}" 
                       class="bg-ep-dark/60 backdrop-blur-md p-8 rounded-[3.5rem] border border-white/5 shadow-2xl relative group overflow-hidden border-l-4 border-{{ $pilar['color'] }}-500/40 hover:bg-{{ $pilar['color'] }}-500/5 transition-all">
                        
                        <div class="flex items-center justify-between mb-8">
                            <div class="h-14 w-14 bg-{{ $pilar['color'] }}-500/10 rounded-2xl border border-{{ $pilar['color'] }}-500/20 text-{{ $pilar['color'] }}-500 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                @if($pilar['id'] == 'inspeccion')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2"/></svg>
                                @elseif($pilar['id'] == 'capacitacion')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @else
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div class="text-right">
                                 <span class="text-4xl font-black text-white italic leading-none">{{ $efficiency[$pilar['id']] }}%</span>
                                 <p class="text-[8px] font-black text-indigo-100/30 uppercase tracking-[0.2em] mt-1 italic">CUMPLIMIENTO</p>
                            </div>
                        </div>

                        <h3 class="text-sm font-black text-white uppercase tracking-[0.1em] mb-6 italic">{{ $pilar['label'] }}</h3>

                        {{-- TRAZABILIDAD: MICRO-TIMELINE --}}
                        <div class="flex items-center justify-between gap-1 mb-8">
                            @foreach(range(1, 12) as $m)
                                @php
                                    $status = $efficiency['timelines'][$pilar['id']][$m];
                                    $colorClass = match($status) {
                                        'completed' => 'bg-emerald-500 shadow-[0_0_8px_#10b981]',
                                        'overdue' => 'bg-red-500 shadow-[0_0_8px_#ef4444]',
                                        'pending' => 'bg-yellow-400 shadow-[0_0_8px_#fbbf24]',
                                        default => 'bg-white/5 border border-white/5'
                                    };
                                @endphp
                                <div class="flex-1 h-3 rounded-full {{ $colorClass }} transition-all" title="Mes {{ $m }}"></div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between">
                             <span class="text-[9px] font-bold text-indigo-100/20 uppercase tracking-widest italic">VER DETALLES EN MATRIZ</span>
                             <svg class="w-4 h-4 text-{{ $pilar['color'] }}-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="3"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- SECONDARY SECTIONS: RISK & ACTIVITY --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Risk Management --}}
                <div class="lg:col-span-8 bg-ep-dark/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 p-10">
                    <div class="flex items-center justify-between mb-10">
                        @if($kpis['findings']['critical'] > 0 || $kpis['findings']['pending'] > 0 || ($alerts['overdue_assignments'] ?? 0) > 0)
                            <h3 class="text-[10px] font-black text-red-400 uppercase tracking-[0.4em] italic flex items-center gap-4">
                                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse shadow-[0_0_10px_#ef4444]"></span>
                                ALERTAS DE RIESGO OPERATIVO
                            </h3>
                        @else
                            <h3 class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] italic flex items-center gap-4">
                                <span class="w-3 h-3 bg-emerald-500 rounded-full shadow-[0_0_10px_#10b981]"></span>
                                ESTADO DE RIESGO
                            </h3>
                        @endif
                        <a href="{{ route('gestor.hallazgos.index') }}" class="text-[8px] font-black text-indigo-100/40 uppercase hover:text-white transition-colors">Ver Todo</a>
                    </div>

                    @if($kpis['findings']['critical'] == 0 && $kpis['findings']['pending'] == 0 && ($alerts['overdue_assignments'] ?? 0) == 0)
                        {{-- Empty State: All Clear --}}
                        <div class="flex flex-col items-center justify-center py-10">
                            <div class="h-20 w-20 bg-emerald-500/10 rounded-full flex items-center justify-center mb-6 border border-emerald-500/20 shadow-[0_0_30px_rgba(16,185,129,0.1)]">
                                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm font-black text-white italic uppercase tracking-widest mb-2">Sin Alertas Activas</p>
                            <p class="text-[10px] font-bold text-indigo-100/30 italic text-center max-w-xs">No se han detectado hallazgos críticos ni tareas vencidas. El ecosistema operativo se encuentra en estado óptimo.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Critical Findings --}}
                            <div class="p-6 {{ $kpis['findings']['critical'] > 0 ? 'bg-red-500/5 border-red-500/20' : 'bg-white/[0.02] border-white/5' }} border rounded-[2rem] flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-black text-white italic">{{ $kpis['findings']['critical'] }}</p>
                                    <span class="text-[9px] font-black {{ $kpis['findings']['critical'] > 0 ? 'text-red-400/60' : 'text-indigo-100/20' }} uppercase tracking-widest italic">Hallazgos Críticos</span>
                                </div>
                                @if($kpis['findings']['critical'] > 0)
                                    <div class="h-10 w-10 bg-red-500/20 rounded-xl flex items-center justify-center text-red-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                @else
                                    <div class="h-10 w-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500/50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Pending Action --}}
                            <div class="p-6 {{ $kpis['findings']['pending'] > 0 ? 'bg-amber-500/5 border-amber-500/20' : 'bg-white/[0.02] border-white/5' }} border rounded-[2rem] flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-black text-white italic">{{ $kpis['findings']['pending'] }}</p>
                                    <span class="text-[9px] font-black {{ $kpis['findings']['pending'] > 0 ? 'text-amber-500/60' : 'text-indigo-100/20' }} uppercase tracking-widest italic">Pendientes Acción</span>
                                </div>
                                @if($kpis['findings']['pending'] == 0)
                                    <div class="h-10 w-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500/50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Overdue Assignments --}}
                            <div class="p-6 {{ ($alerts['overdue_assignments'] ?? 0) > 0 ? 'bg-orange-500/5 border-orange-500/20' : 'bg-white/[0.02] border-white/5' }} border rounded-[2rem] flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-black text-white italic">{{ $alerts['overdue_assignments'] ?? 0 }}</p>
                                    <span class="text-[9px] font-black {{ ($alerts['overdue_assignments'] ?? 0) > 0 ? 'text-orange-400/60' : 'text-indigo-100/20' }} uppercase tracking-widest italic">Tareas Vencidas</span>
                                </div>
                                @if(($alerts['overdue_assignments'] ?? 0) == 0)
                                    <div class="h-10 w-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500/50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Activity Stream --}}
                <div class="lg:col-span-4 bg-ep-dark/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 p-10 flex flex-col">
                    <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] italic mb-10">ACTIVIDAD RECIENTE</h3>
                    <div class="space-y-8 flex-1 overflow-y-auto max-h-[400px] custom-scrollbar pr-4">
                        @forelse($recentActivity as $activity)
                            <div class="relative pl-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-[-2rem] before:w-px before:bg-white/5 last:before:hidden">
                                <div class="absolute left-0 top-1.5 h-6 w-6 bg-ep-dark border border-white/10 rounded-lg flex items-center justify-center z-10 shadow-lg">
                                    @if($activity->type == 'inspeccion')
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                    @elseif($activity->type == 'capacitacion')
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    @elseif($activity->type == 'visita')
                                        <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black text-white uppercase italic tracking-tight">{{ $activity->title }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[8px] font-bold text-indigo-200/40 uppercase">{{ $activity->empresa }}</span>
                                        <span class="text-[8px] text-white/10 italic">via {{ $activity->user_name }}</span>
                                    </div>
                                    <p class="text-[8px] font-black text-indigo-400/60 mt-2 italic">{{ \Carbon\Carbon::parse($activity->date)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            {{-- Empty State --}}
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="h-16 w-16 bg-white/[0.02] rounded-full flex items-center justify-center mb-6 border border-white/5">
                                    <svg class="w-8 h-8 text-indigo-100/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <p class="text-xs font-black text-white/30 italic uppercase tracking-widest mb-2">Sin Actividad</p>
                                <p class="text-[9px] font-bold text-indigo-100/15 italic leading-relaxed max-w-[180px]">Las inspecciones, capacitaciones y visitas aparecerán aquí en tiempo real</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .shadow-glow-indigo { filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.4)); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.1); }
    </style>
</div>
