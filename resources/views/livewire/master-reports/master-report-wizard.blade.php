<div class="w-full mx-auto">
    {{-- Header de Redacción Maestro Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('tecnico.master-reports.index') }}" class="p-2 bg-white/5 rounded-xl border border-white/10 text-indigo-100 hover:text-ep-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Consola de <span class="text-white">Redacción</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Consolidado Maestro
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none mb-1">Especialista</span>
                        <span class="text-xs font-black text-white italic uppercase">{{ auth()->user()->nombres }}</span>
                    </div>
                    <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary">
                        <svg class="w-5 h-5 shadow-[0_0_10px_rgba(246,146,57,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="py-4 sm:py-8 min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto px-3 sm:px-6 lg:px-8">
            
            @if($mode === 'create')
                {{-- Formulario de Creación (Modo Gestor usualmente) --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-10 shadow-2xl animate-fade-in-down">
                    <h3 class="text-lg sm:text-xl font-black text-white uppercase italic tracking-widest mb-6 sm:mb-10 border-b border-white/5 pb-4">Nueva Solicitud de Informe</h3>
                    <form wire:submit.prevent="save" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Sede / Cliente Operativo</label>
                                <select wire:model.live="sede_id" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                    <option value="">Seleccione una sede</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }} ({{ $sede->empresa->nombre }})</option>
                                    @endforeach
                                </select>
                                @error('sede_id') <span class="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Especialista Asignado</label>
                                <select wire:model="user_id" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                    <option value="">Seleccione un técnico</option>
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}">{{ $tecnico->nombres }} {{ $tecnico->apellidos }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <span class="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Título de Identificación</label>
                                <input wire:model="titulo" type="text" placeholder="Ej: Reporte Mensual de Seguridad"
                                       class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white placeholder-white/5 focus:ring-2 focus:ring-ep-primary/50 transition-all">
                                @error('titulo') <span class="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Periodo de Auditoría</label>
                                <input wire:model.live="periodo" type="month" 
                                       class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                @error('periodo') <span class="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Frecuencia de Informe</label>
                                <select wire:model.live="frecuencia" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                    <option value="mensual">MENSUAL</option>
                                    <option value="trimestral">TRIMESTRAL</option>
                                    <option value="semestral">SEMESTRAL</option>
                                    <option value="anual">ANUAL</option>
                                </select>
                                @error('frecuencia') <span class="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{{ $message }}</span> @enderror
                                <p class="text-[9px] text-indigo-100/20 italic px-2 mt-1">Define el rango de meses que el sistema consultará para extraer datos.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit" class="h-14 px-10 bg-ep-primary text-white text-[10px] font-black uppercase tracking-[0.2em] italic rounded-2xl shadow-xl shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all">
                                Solicitar Informe
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- MODO EDICIÓN / REDACCIÓN PARA TÉCNICO --}}
                <div class="space-y-10">
                    {{-- Encabezado de Contexto HUD --}}
                    <div class="bg-ep-dark/60 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 border-l-8 border-l-ep-primary shadow-2xl relative overflow-hidden animate-fade-in-down group">
                        <div class="absolute inset-0 bg-gradient-to-r from-ep-primary/5 via-transparent to-transparent opacity-50"></div>
                        <h3 class="text-xl sm:text-3xl font-black text-white uppercase italic tracking-tight mb-3 group-hover:text-ep-primary transition-colors duration-500 break-words">{{ $report->titulo }}</h3>
                        <div class="flex flex-wrap items-center gap-8 relative z-10">
                            <span class="flex items-center text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic group/meta transition-colors">
                                <svg class="w-4 h-4 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $report->formatted_period }}
                            </span>
                            <span class="flex items-center text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic group/meta transition-colors">
                                <svg class="w-4 h-4 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $report->sede->nombre_sede }}
                            </span>
                        </div>
                    </div>

                    {{-- Formulario de Redacción Táctico --}}
                    <form wire:submit.prevent="save" class="space-y-12">

                        {{-- SECCIÓN 0: ESTADO DE PLAN ANUAL (PACTADO) --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 border-t-4 border-t-ep-primary shadow-2xl relative group/section overflow-hidden">
                            <div class="flex flex-col md:flex-row gap-10 items-center">
                                <div class="flex-shrink-0 relative">
                                    <svg class="w-24 h-24 sm:w-32 sm:h-32 transform -rotate-90">
                                        <circle cx="50%" cy="50%" r="45" class="stroke-white/5 fill-none" stroke-width="10"></circle>
                                        <circle cx="50%" cy="50%" r="45" class="stroke-ep-primary fill-none transition-all duration-1000" 
                                                stroke-width="10" stroke-dasharray="283" 
                                                stroke-dashoffset="{{ 283 - (283 * ($monthlyData['annual_plan_status']['percentage'] ?? 0)) / 100 }}"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-xl sm:text-2xl font-black text-white italic leading-none">{{ $monthlyData['annual_plan_status']['percentage'] ?? 0 }}%</span>
                                        <span class="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest italic">CUMPLIMIENTO</span>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic mb-2">Estado de Plan Anual (Pactado)</h4>
                                    <p class="text-[10px] text-indigo-100/30 uppercase tracking-widest italic mb-6">Conciliación de actividades contractuales programadas y realizadas</p>
                                    
                                    @php
                                        $categoryLabels = [
                                            'Inspección' => 'Controles y Checklists Normativos',
                                            'Capacitación' => 'Formación y Capacitación Técnica',
                                            'Informe' => 'Informes y Gestión Administrativa'
                                        ];
                                        $programmedByCat = collect($monthlyData['annual_plan_status']['programmed'] ?? [])->groupBy('category');
                                    @endphp

                                    @foreach($categoryLabels as $catKey => $label)
                                        @if($programmedByCat->has($catKey))
                                            <div class="mb-10 last:mb-0">
                                                <div class="flex items-center gap-3 mb-5 border-l-2 border-ep-primary/40 pl-4 py-0.5">
                                                    <span class="text-[10px] font-black text-indigo-100/50 uppercase tracking-[0.25em] italic">{{ $label }}</span>
                                                    <div class="flex-grow h-px bg-gradient-to-r from-white/5 to-transparent"></div>
                                                </div>

                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                    @foreach($programmedByCat->get($catKey) as $prog)
                                                        <div class="px-5 py-4 rounded-2xl border {{ $prog['status'] === 'completed' ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-white/5 bg-black/40' }} flex items-start justify-between group/prog transition-all hover:bg-white/[0.02]">
                                                            <div class="flex flex-col min-w-0 flex-grow">
                                                                <div class="flex items-center gap-2 mb-1.5">
                                                                    @if($prog['code'])
                                                                        <span class="px-2 py-0.5 rounded-md text-[8px] font-black bg-white/10 text-indigo-100/50 uppercase tracking-widest border border-white/5">{{ $prog['code'] }}</span>
                                                                    @endif
                                                                    <span class="text-xs font-black {{ $prog['status'] === 'completed' ? 'text-emerald-400' : 'text-white' }} uppercase italic tracking-tight leading-tight">{{ $prog['activity'] }}</span>
                                                                </div>
                                                                @if($prog['description'])
                                                                    <p class="text-[10px] text-indigo-100/40 mb-2 italic leading-relaxed group-hover/prog:text-indigo-100/60 transition-colors">{{ $prog['description'] }}</p>
                                                                @endif
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.2em]">{{ $prog['month'] }}</span>
                                                                    @if($prog['status'] === 'completed')
                                                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                                        <span class="text-[8px] font-bold text-emerald-500/40 uppercase italic">Verificado</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="flex-shrink-0 ml-4 pt-1">
                                                                @if($prog['status'] === 'completed')
                                                                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30">
                                                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    </div>
                                                                @else
                                                                    <div class="w-6 h-6 rounded-lg bg-white/5 flex items-center justify-center border border-white/10 group-hover/prog:border-ep-primary/30 transition-colors">
                                                                        <div class="w-1.5 h-1.5 rounded-full bg-white/10 group-hover/prog:bg-ep-primary animate-pulse"></div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        {{-- SECCIÓN 1: VISITAS TÉCNICAS --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 shadow-2xl relative group/section overflow-hidden">
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                            <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/5">
                                <div class="flex items-center gap-4 sm:gap-5">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/5 rounded-xl sm:rounded-2xl border border-white/10 flex items-center justify-center text-white font-black italic text-lg sm:text-xl group-hover/section:bg-ep-primary/20 transition-all shadow-inner">1</div>
                                    <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic">Visitas Técnicas ({{ count($monthlyData['visits'] ?? []) }})</h4>
                                </div>
                            </div>
                            
                            @if(count($monthlyData['visits'] ?? []) > 0)
                                {{-- Tabla Escritorio --}}
                                <div class="hidden md:block overflow-hidden rounded-2xl border border-white/5 bg-black/20 mb-8">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-white/5 border-b border-white/5 text-[9px] font-black text-indigo-100/30 uppercase tracking-widest italic">
                                                <th class="px-6 py-4">Fecha</th>
                                                <th class="px-6 py-4">Técnico</th>
                                                <th class="px-6 py-4">Resumen Operativo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/[0.03] text-xs">
                                            @foreach($monthlyData['visits'] ?? [] as $visit)
                                                <tr class="hover:bg-white/[0.02] transition-colors group/row">
                                                    <td class="px-6 py-5 font-black text-ep-primary italic align-top pt-8">{{ $visit->visit_date->format('d/m/Y') }}</td>
                                                    <td class="px-6 py-5 font-bold text-white/80 uppercase align-top pt-8">{{ $visit->user->nombres }}</td>
                                                    <td class="px-6 py-5 align-top pt-8">
                                                        <div class="space-y-4">
                                                            <p class="italic text-indigo-100/40 leading-relaxed">{{ $visit->general_observations }}</p>
                                                            
                                                            @if($visit->checklistCompletions->count() > 0)
                                                                <div class="space-y-1.5">
                                                                    <span class="text-[8px] font-black text-ep-primary uppercase tracking-widest italic opacity-50 block">Procedimientos Realizados:</span>
                                                                    <div class="flex flex-wrap gap-2">
                                                                        @foreach($visit->checklistCompletions as $comp)
                                                                            <span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase italic">{{ $comp->assignment->checklist->title ?? 'Inspección técnica' }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Cards Móvil --}}
                                <div class="md:hidden space-y-4 mb-8">
                                    @foreach($monthlyData['visits'] ?? [] as $visit)
                                        <div class="p-4 rounded-xl border border-white/5 bg-black/40 space-y-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-[10px] font-black text-ep-primary uppercase italic">{{ $visit->visit_date->format('d/m/Y') }}</span>
                                                <span class="text-[9px] font-bold text-white/80 uppercase tracking-widest">{{ $visit->user->nombres }}</span>
                                            </div>
                                            <p class="text-[11px] italic text-indigo-100/40 leading-relaxed">{{ $visit->general_observations }}</p>
                                            
                                            @if($visit->checklistCompletions->count() > 0)
                                                <div class="flex flex-wrap gap-1.5 pt-2 border-t border-white/5">
                                                    @foreach($visit->checklistCompletions as $comp)
                                                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-500/5 border border-emerald-500/10 text-emerald-400 text-[8px] font-bold uppercase italic">{{ $comp->assignment->checklist->title ?? 'Inspección' }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center bg-black/20 rounded-2xl border border-white/5 mb-8">
                                    <p class="text-[10px] font-black text-indigo-100/10 uppercase tracking-widest italic leading-none">Cero visitas registradas</p>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <label class="block text-[9px] sm:text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] sm:tracking-[0.3em] italic mb-2 px-1 leading-none">Análisis de la Gestión de Visitas</label>
                                <textarea wire:model="observaciones_visitas" rows="4" class="w-full bg-black/40 border border-white/10 rounded-xl sm:rounded-[2rem] px-5 sm:px-8 py-4 sm:py-6 text-indigo-100 text-xs sm:text-sm italic font-medium focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5" placeholder="Redacte aquí sus comentarios..."></textarea>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: CHECKLISTS --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 shadow-2xl relative group/section overflow-hidden">
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                            <div class="flex items-center mb-8 pb-4 border-b border-white/5">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/5 rounded-xl sm:rounded-2xl border border-white/10 flex items-center justify-center text-white font-black italic text-lg sm:text-xl group-hover/section:bg-emerald-500/20 transition-all shadow-inner">2</div>
                                <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic ml-3 sm:ml-5 leading-tight">Protocolos e Inspecciones (Fuera de Contrato) ({{ count($monthlyData['checklists_adhoc'] ?? []) }})</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                                @forelse($monthlyData['checklists_adhoc'] ?? [] as $completion)
                                    <div class="p-5 rounded-2xl border border-white/5 bg-black/40 hover:border-emerald-500/30 transition-all group/item">
                                        <div class="font-black text-white uppercase italic text-[11px] mb-2 leading-none group-hover/item:text-emerald-400 transition-colors">{{ $completion->assignment->checklist->title ?? 'Protocolo Ad-hoc' }}</div>
                                        <div class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic">
                                            Finalizado: {{ \Carbon\Carbon::parse($completion->completed_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full p-10 text-center bg-black/20 rounded-2xl border border-white/5">
                                        <p class="text-[10px] font-black text-indigo-100/10 uppercase tracking-widest italic">Sin inspecciones fuera de contrato en este periodo</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="space-y-4">
                                <label class="block text-[9px] sm:text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] sm:tracking-[0.3em] italic mb-2 px-1 leading-none">Comentarios sobre Inspecciones</label>
                                <textarea wire:model="observaciones_checklists" rows="4" class="w-full bg-black/40 border border-white/10 rounded-xl sm:rounded-[2rem] px-5 sm:px-8 py-4 sm:py-6 text-indigo-100 text-xs sm:text-sm italic font-medium focus:ring-2 focus:ring-emerald-500/30 transition-all placeholder-white/5" placeholder="Análisis sobre los resultados..."></textarea>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: CAPACITACIONES --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 shadow-2xl relative group/section overflow-hidden">
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                            <div class="flex items-center mb-8 pb-4 border-b border-white/5">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/5 rounded-xl sm:rounded-2xl border border-white/10 flex items-center justify-center text-white font-black italic text-lg sm:text-xl group-hover/section:bg-indigo-400/20 transition-all shadow-inner">3</div>
                                <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic ml-3 sm:ml-5 leading-tight">Formación de Personal ({{ count($monthlyData['trainings'] ?? []) }})</h4>
                            </div>

                            @if(count($monthlyData['trainings'] ?? []) > 0)
                                {{-- Tabla Escritorio --}}
                                <div class="hidden md:block overflow-hidden rounded-2xl border border-white/5 bg-black/20 mb-8">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="bg-white/5 border-b border-white/5 text-[9px] font-black text-indigo-100/30 uppercase tracking-widest italic">
                                                <th class="px-6 py-4">Actividad / Título</th>
                                                <th class="px-6 py-4 text-center">Asistentes</th>
                                                <th class="px-6 py-4">Notas Técnicas</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/[0.03]">
                                            @foreach($monthlyData['trainings'] ?? [] as $training)
                                                <tr class="hover:bg-white/[0.02] transition-colors">
                                                    <td class="px-6 py-5 font-black text-indigo-400 italic uppercase">{{ $training->title ?: 'Protocolo de Formación' }}</td>
                                                    <td class="px-6 py-5 text-center font-black text-white">{{ $training->attendees_count }}</td>
                                                    <td class="px-6 py-5 italic text-indigo-100/40 text-xxs leading-relaxed">{{ $training->notes ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Cards Móvil --}}
                                <div class="md:hidden space-y-4 mb-8">
                                    @foreach($monthlyData['trainings'] ?? [] as $training)
                                        <div class="p-4 rounded-xl border border-white/5 bg-black/40">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-[10px] font-black text-indigo-400 uppercase italic">{{ $training->title ?: 'Protocolo' }}</span>
                                                <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[10px] font-black rounded-lg border border-indigo-500/20">{{ $training->attendees_count }} Pers.</span>
                                            </div>
                                            @if($training->notes)
                                                <p class="text-[10px] italic text-indigo-100/40 leading-relaxed">{{ $training->notes }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center bg-black/20 rounded-2xl border border-white/5 mb-8">
                                    <p class="text-[10px] font-black text-indigo-100/10 uppercase tracking-widest italic">Cero actividades formativas registradas</p>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <label class="block text-[9px] sm:text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] sm:tracking-[0.3em] italic mb-2 px-1 leading-none">Comentarios sobre Capacitación</label>
                                <textarea wire:model="observaciones_capacitaciones" rows="4" class="w-full bg-black/40 border border-white/10 rounded-xl sm:rounded-[2rem] px-5 sm:px-8 py-4 sm:py-6 text-indigo-100 text-xs sm:text-sm italic font-medium focus:ring-2 focus:ring-indigo-500/30 transition-all placeholder-white/5" placeholder="Análisis sobre el nivel de formación..."></textarea>
                            </div>
                        </div>

                        {{-- SECCIÓN 4: HALLAZGOS e INCIDENTES --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 shadow-2xl relative group/section overflow-hidden">
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/20 to-transparent"></div>
                            <div class="flex items-center mb-8 pb-4 border-b border-white/5">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/5 rounded-xl sm:rounded-2xl border border-white/10 flex items-center justify-center text-white font-black italic text-lg sm:text-xl group-hover/section:bg-red-500/20 transition-all shadow-inner">4</div>
                                <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic ml-3 sm:ml-5 leading-tight">Análisis Crítico de Seguridad</h4>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-10 mb-8">
                                {{-- Hallazgos --}}
                                <div class="bg-black/40 p-8 rounded-3xl border border-white/5 border-l-4 border-l-yellow-500/50">
                                    <h5 class="text-[9px] sm:text-[10px] font-black text-yellow-500/60 uppercase tracking-[0.2em] sm:tracking-[0.3em] mb-4 sm:mb-6 italic leading-none flex items-center gap-3">
                                        <span class="w-6 sm:w-8 h-px bg-yellow-500/20"></span> Hallazgos ({{ count($monthlyData['hallazgos'] ?? []) }})
                                    </h5>
                                    <div class="max-h-[30rem] overflow-y-auto pr-4 space-y-6 custom-scrollbar">
                                        @forelse($monthlyData['hallazgos'] ?? [] as $hallazgo)
                                            <div class="bg-white/5 rounded-2xl p-5 border border-white/5 group/item transition-all hover:bg-white/[0.08]">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="text-[11px] font-black text-white italic uppercase tracking-tight">#{{ $hallazgo->id }} - {{ $hallazgo->titulo }}</span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-yellow-500/10 text-yellow-500 text-[8px] font-black uppercase tracking-widest border border-yellow-500/20">{{ $hallazgo->criticidad }}</span>
                                                </div>
                                                <p class="text-[11px] text-indigo-100/40 italic leading-relaxed mb-4">{{ $hallazgo->descripcion }}</p>
                                                @if(count($hallazgo->fotos ?? []) > 0)
                                                    <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-thin">
                                                        @foreach($hallazgo->fotos as $foto)
                                                            <img src="{{ $foto->url }}" class="w-20 h-20 flex-none object-cover rounded-xl border border-white/10 shadow-lg hover:scale-110 transition-transform duration-500 cursor-pointer" alt="Evidencia">
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-[10px] text-indigo-100/10 font-bold uppercase tracking-widest italic text-center py-10">Sin desviaciones reportadas</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Incidentes --}}
                                <div class="bg-black/40 p-8 rounded-3xl border border-white/5 border-l-4 border-l-red-600/50 shadow-2xl">
                                    <h5 class="text-[9px] sm:text-[10px] font-black text-red-500 uppercase tracking-[0.2em] sm:tracking-[0.3em] mb-4 sm:mb-6 italic leading-none flex items-center gap-3">
                                        <span class="w-6 sm:w-8 h-px bg-red-500/20"></span> Incidentes ({{ count($monthlyData['incidentes'] ?? []) }})
                                    </h5>
                                    <div class="max-h-[30rem] overflow-y-auto pr-4 space-y-6 custom-scrollbar">
                                        @forelse($monthlyData['incidentes'] ?? [] as $incidente)
                                            <div class="bg-white/5 rounded-2xl p-5 border border-white/5 group/item transition-all hover:bg-white/[0.08]">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="text-[11px] font-black text-red-500 italic uppercase tracking-widest glow-text-red">EVENTO: {{ str_replace('_', ' ', $incidente->tipo) }}</span>
                                                    <span class="text-[10px] font-black text-indigo-100/40 italic">{{ $incidente->fecha_ocurrencia->format('d/m/Y') }}</span>
                                                </div>
                                                <p class="text-[11px] text-indigo-100/60 italic leading-relaxed mb-4">{{ $incidente->descripcion }}</p>
                                                <div class="flex items-center gap-6 pt-3 border-t border-white/5 mb-4">
                                                    <div class="text-center flex-1">
                                                        <span class="block text-xs font-black text-white italic leading-none">{{ $incidente->personas_afectadas }}</span>
                                                        <span class="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest italic">Afectados</span>
                                                    </div>
                                                    <div class="text-center flex-1">
                                                        <span class="block text-xs font-black text-white italic leading-none">{{ $incidente->dias_perdidos ?? 0 }}</span>
                                                        <span class="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest italic">Días Perdidos</span>
                                                    </div>
                                                    <div class="flex-1 text-right">
                                                        <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-widest {{ $incidente->estado === 'cerrado' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 shadow-[0_0_10px_rgba(234,179,8,0.1)] animate-pulse' }}">
                                                            {{ $incidente->estado ?? 'PENDIENTE' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if(count($incidente->fotos ?? []) > 0)
                                                    <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-thin">
                                                        @foreach($incidente->fotos as $foto)
                                                            <img src="{{ $foto->getUrl() }}" class="w-20 h-20 flex-none object-cover rounded-xl border border-white/10 shadow-lg hover:scale-110 transition-transform duration-500" alt="Evidencia">
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-[10px] text-indigo-100/10 font-bold uppercase tracking-widest italic text-center py-10">Sin eventos de riesgo registrados</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-[9px] sm:text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] sm:tracking-[0.3em] italic mb-2 px-1 leading-none">Análisis de Desvíos y Eventos</label>
                                <textarea wire:model="observaciones_hallazgos_incidentes" rows="4" class="w-full bg-black/40 border border-white/10 rounded-xl sm:rounded-[2rem] px-5 sm:px-8 py-4 sm:py-6 text-indigo-100 text-xs sm:text-sm italic font-medium focus:ring-2 focus:ring-red-500/30 transition-all placeholder-white/5" placeholder="Comentarios tácticos..."></textarea>
                            </div>
                        </div>
               
                        {{-- SECCIÓN 5: RESUMEN EJECUTIVO (HUD Central) --}}
                        <div class="bg-ep-dark p-6 sm:p-12 rounded-[2rem] sm:rounded-[3.5rem] shadow-2xl border-t-8 border-ep-primary relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-b from-ep-primary/5 to-transparent pointer-events-none opacity-50"></div>
                            <div class="flex items-center mb-8 pb-4 border-b border-white/10 relative z-10">
                                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-ep-primary rounded-xl sm:rounded-2xl flex items-center justify-center text-white font-black mr-4 sm:mr-6 text-lg sm:text-2xl shadow-[0_0_20px_rgba(246,146,57,0.4)] rotate-3 group-hover:rotate-0 transition-all duration-700">5</div>
                                <h4 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight sm:tracking-[0.1em] italic">Resumen Ejecutivo</h4>
                            </div>
                            <textarea wire:model="resumen_ejecutivo" rows="10" 
                                      class="w-full bg-black/40 border-2 border-white/5 rounded-[1.5rem] sm:rounded-[2.5rem] text-white placeholder-white/5 shadow-inner focus:ring-2 focus:ring-ep-primary/50 text-base sm:text-xl font-bold p-5 sm:p-10 italic leading-relaxed transition-all relative z-10" 
                                      placeholder="Consolide aquí la visión técnica final..."></textarea>
                            @error('resumen_ejecutivo') <div class="mt-4 px-6 py-2 bg-red-500/20 rounded-xl border border-red-500/30 text-red-500 text-[10px] font-black uppercase tracking-widest italic animate-pulse relative z-10">{{ $message }}</div> @enderror
                        </div>

                        {{-- SECCIÓN 6: FIRMA PROFESIONAL --}}
                        <div class="bg-ep-dark/40 backdrop-blur-md p-6 sm:p-10 rounded-[1.5rem] sm:rounded-[2.5rem] border border-white/5 shadow-2xl relative group/section overflow-hidden" 
                             x-data="{ 
                                canvas: null, ctx: null, isDrawing: false, lastX: 0, lastY: 0, hasSignature: false,
                                init() {
                                    this.$nextTick(() => {
                                        if (this.$refs.signatureCanvas) {
                                            this.canvas = this.$refs.signatureCanvas;
                                            this.ctx = this.canvas.getContext('2d');
                                            const rect = this.canvas.getBoundingClientRect();
                                            this.canvas.width = rect.width;
                                            this.canvas.height = rect.height;
                                            this.ctx.strokeStyle = '#F69239'; // Tinta Naranja Epic sobre negro
                                            this.ctx.lineWidth = 3;
                                            this.ctx.lineCap = 'round';
                                            this.ctx.lineJoin = 'round';
                                        }
                                    });
                                },
                                startDrawing(e) {
                                    this.isDrawing = true;
                                    const rect = this.canvas.getBoundingClientRect();
                                    this.lastX = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                                    this.lastY = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;
                                },
                                draw(e) {
                                    if (!this.isDrawing) return;
                                    const rect = this.canvas.getBoundingClientRect();
                                    const x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                                    const y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;
                                    this.ctx.beginPath(); this.ctx.moveTo(this.lastX, this.lastY); this.ctx.lineTo(x, y); this.ctx.stroke();
                                    this.lastX = x; this.lastY = y; this.hasSignature = true;
                                },
                                stopDrawing() {
                                    this.isDrawing = false;
                                    if (this.hasSignature) { @this.set('signature', this.canvas.toDataURL('image/png')); }
                                },
                                clearSignature() {
                                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                                    this.hasSignature = false; @this.set('signature', null);
                                }
                             }"
                             @mouseup="stopDrawing()" @mouseleave="stopDrawing()" @touchend="stopDrawing()">
                            <div class="flex items-center mb-8 pb-4 border-b border-white/5">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/5 rounded-xl sm:rounded-2xl border border-white/10 flex items-center justify-center text-white font-black italic text-lg sm:text-xl group-hover/section:bg-ep-primary/20 transition-all shadow-inner">6</div>
                                <h4 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight italic ml-3 sm:ml-5 leading-tight">Validación y Firma</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-10 items-center">
                                <div class="order-2 md:order-1">
                                    @if(auth()->user()->firma)
                                        <div class="mb-6 p-4 bg-black/20 rounded-2xl border border-white/10 shadow-inner group/prev inline-block relative overflow-hidden">
                                            <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent opacity-0 group-hover/prev:opacity-100 transition-opacity"></div>
                                            <span class="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest italic mb-3">Registro de Perfil:</span>
                                            <img src="{{ asset('storage/' . auth()->user()->firma) }}" class="h-16 sm:h-24 brightness-0 invert opacity-60 group-hover/prev:scale-105 transition-transform duration-700" alt="Firma Perfil">
                                        </div>
                                    @endif
                                    <p class="text-[10px] sm:text-[11px] text-indigo-100/30 italic font-medium leading-relaxed uppercase tracking-wide">
                                        Dibuje su rúbrica en el panel digital adjunto para validar el presente informe.
                                    </p>
                                </div>
                                
                                <div class="relative order-1 md:order-2">
                                    <div wire:ignore class="relative bg-black shadow-2xl border-2 border-dashed border-white/10 rounded-2xl sm:rounded-[2.5rem] overflow-hidden sm:h-52 h-44 focus-within:border-ep-primary/50 transition-all">
                                        <canvas x-ref="signatureCanvas" 
                                                @mousedown="startDrawing($event)" @mousemove="draw($event)"
                                                @touchstart="startDrawing($event)" @touchmove="draw($event)"
                                                class="w-full h-full cursor-crosshair"></canvas>
                                        <button type="button" @click="clearSignature()" 
                                                class="absolute top-3 right-3 p-2 bg-red-600/10 border border-red-500/20 rounded-lg text-red-500 hover:bg-red-600 hover:text-white transition-all shadow-xl">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <p class="text-[8px] text-indigo-100/10 font-black uppercase tracking-[0.3em] sm:tracking-[0.4em] italic mt-3 text-center leading-none">Panel Táctil Digital</p>
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN FINAL DE DESPEGUE --}}
                        <div class="flex justify-center pt-10 pb-20">
                            <button type="submit" class="group relative h-16 sm:h-20 w-full sm:w-[400px] flex items-center justify-center bg-ep-primary text-white italic rounded-xl sm:rounded-[2rem] shadow-2xl shadow-ep-primary/30 overflow-hidden transition-all hover:scale-105 active:scale-95">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1500 ease-in-out"></div>
                                <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <span class="flex items-center text-base sm:text-xl font-black uppercase tracking-widest relative z-10">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 sm:mr-4 group-hover:animate-bounce transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    GENERAR INFORME
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
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
        .cursor-crosshair { cursor: crosshair; }
    </style>
</div>
