<div class="w-full mx-auto">
    {{-- Header de Asignaciones Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Consola de <span class="text-white">Operaciones</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight">
                            Mis Asignaciones Técnicas
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none mb-1">Especialista</span>
                        <span class="text-xs font-black text-white italic uppercase">{{ auth()->user()->nombres }}</span>
                    </div>
                    <a href="{{ route('profile') }}" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary group hover:bg-white/10 transition-all shadow-inner">
                        <span class="text-sm font-black">{{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-0.5 w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="py-8 min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filtros Premium Dark --}}
            <div class="mb-10 animate-fade-in-down">
                <div class="bg-ep-dark/50 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative z-10">
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-[10px] font-black text-ep-primary uppercase tracking-[0.2em] italic ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Búsqueda Libre
                            </label>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Checklist o Código..." 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white placeholder-white/10 focus:outline-none focus:ring-2 focus:ring-ep-primary/50 focus:border-ep-primary transition-all backdrop-blur-md">
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Entidad / Cliente
                            </label>
                            <select wire:model.live="selectedEmpresa" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                <option value="">Todas las Entidades</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Sede Técnica
                            </label>
                            <select wire:model.live="selectedSede" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]" {{ !$selectedEmpresa ? 'disabled' : '' }}>
                                <option value="">Todas las Sedes</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Estado de Misión
                            </label>
                            <select wire:model.live="statusFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                <option value="">Todos los Estados</option>
                                <option value="pending">🟡 Pendientes</option>
                                <option value="in_progress">🔵 En Curso</option>
                                <option value="completed">🟢 Finalizadas</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vista Desktop Table Premium --}}
            <div class="hidden md:block animate-fade-in" style="animation-delay: 0.2s">
                <div class="bg-ep-dark/40 backdrop-blur-sm rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none opacity-20"></div>
                    
                    <table class="min-w-full divide-y divide-white/5 relative z-10">
                        <thead>
                            <tr class="bg-black/20">
                                <th class="px-8 py-6 text-left text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic">Checklist Técnico</th>
                                <th class="px-8 py-6 text-left text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Ubicación de Sede</th>
                                <th class="px-8 py-6 text-left text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Planificación</th>
                                <th class="px-8 py-6 text-center text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Estado</th>
                                <th class="px-8 py-6 text-right text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Operaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($assignments as $assignment)
                                <tr class="group hover:bg-white/[0.03] transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white uppercase italic tracking-tight group-hover:text-ep-primary transition-colors">
                                                    {{ $assignment->checklist->title }}
                                                </div>
                                                    <div class="text-[11px] text-indigo-100/50 mt-1 uppercase tracking-widest font-black italic leading-none">
                                                        CID: {{ $assignment->checklist->registry_code }}
                                                    </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-black text-ep-primary/80 uppercase italic tracking-wide">{{ $assignment->empresa->nombre }}</div>
                                        <div class="text-[10px] text-indigo-100/40 font-bold uppercase mt-1">{{ $assignment->sede->nombre_sede }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[10px] font-black text-indigo-100 uppercase italic tracking-widest mb-1">{{ $assignment->getFrequencyLabel() }}</div>
                                        <div class="text-[11px] font-black {{ $assignment->isOverdue() && $assignment->status !== 'completed' ? 'text-red-500 glow-text-red' : 'text-white/60' }}">
                                            {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'S/F' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $vencido = $assignment->isOverdue() && $assignment->status !== 'completed';
                                            $statusClass = match($assignment->status) {
                                                'pending' => 'bg-yellow-500/10 border-yellow-500/30 text-yellow-500',
                                                'in_progress' => 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400',
                                                'completed' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500',
                                                default => 'bg-white/5 border-white/10 text-white/40'
                                            };
                                            if($vencido) $statusClass = 'bg-red-500/10 border-red-500/30 text-red-500 animate-pulse';
                                        @endphp
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black tracking-[0.2em] uppercase italic border {{ $statusClass }}">
                                            @if($vencido) CRÍTICO @else {{ strtoupper($assignment->status) }} @endif
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            @if($assignment->status === 'completed')
                                                @if($assignment->inspection)
                                                    <a href="{{ route('tecnico.inspections.show', $assignment->inspection->id) }}" 
                                                       class="h-10 w-10 flex items-center justify-center bg-white/5 border border-white/10 rounded-xl text-indigo-100 hover:bg-ep-primary hover:text-white hover:scale-110 transition-all shadow-inner group/btn"
                                                       title="Consultar Registro">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                    <a href="{{ route('tecnico.inspections.pdf.download', $assignment->inspection->id) }}" 
                                                       class="h-10 w-10 flex items-center justify-center bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-500 hover:bg-emerald-500 hover:text-white hover:scale-110 transition-all shadow-inner group/btn"
                                                       title="Exportar Protocolo">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('tecnico.checklists.fill', ['id' => $assignment->dynamic_checklist_id, 'assignment' => $assignment->id]) }}" 
                                                   class="h-10 px-6 flex items-center gap-2 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 group/exec overflow-hidden relative">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/exec:translate-x-full transition-transform duration-1000"></div>
                                                    <span class="relative">Ejecutar Misión</span>
                                                    <svg class="w-4 h-4 animate-pulse relative" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="relative overflow-hidden inline-flex p-10 bg-white/5 rounded-[3rem] border border-white/5">
                                             <div class="relative z-10 flex flex-col items-center">
                                                <svg class="h-16 w-16 text-indigo-100/10 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                <h3 class="text-sm font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Frecuencia despejada</h3>
                                                <p class="text-[10px] text-indigo-100/20 mt-1 font-bold uppercase">No se detectaron asignaciones en curso</p>
                                             </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($assignments->hasPages())
                        <div class="px-8 py-6 border-t border-white/5 bg-black/20">
                            {{ $assignments->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vista Mobile Premium Dark Cards --}}
            <div class="md:hidden space-y-6 animate-fade-in" style="animation-delay: 0.3s">
                @forelse($assignments as $assignment)
                    <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-white/10 p-6 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
                        
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-2xl border border-white/10 text-ep-primary">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-white italic uppercase leading-none">{{ $assignment->checklist->title }}</h3>
                                    <p class="text-[11px] text-indigo-100/50 font-black tracking-widest mt-1.5 uppercase italic leading-none">CID: {{ $assignment->checklist->registry_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @php
                                $vencido = $assignment->isOverdue() && $assignment->status !== 'completed';
                                $statusClass = match($assignment->status) {
                                    'pending' => 'text-yellow-500 border-yellow-500/20 bg-yellow-500/5',
                                    'in_progress' => 'text-indigo-400 border-indigo-400/20 bg-indigo-400/5',
                                    'completed' => 'text-emerald-500 border-emerald-500/20 bg-emerald-500/5',
                                    default => 'text-white/20 border-white/5 bg-white/5'
                                };
                                if($vencido) $statusClass = 'text-red-500 border-red-500/30 bg-red-500/10 animate-pulse';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border {{ $statusClass }}">
                                {{ $vencido ? 'VENCIDO' : strtoupper($assignment->status) }}
                            </span>
                        </div>

                        <div class="space-y-4 mb-6 bg-black/20 p-4 rounded-3xl border border-white/5">
                            <div>
                                <p class="text-[9px] font-black text-ep-primary/60 uppercase tracking-widest mb-1 italic">Entidad / Sede</p>
                                <p class="text-xs font-black text-white uppercase italic">{{ $assignment->empresa->nombre }}</p>
                                <p class="text-[10px] text-indigo-100/40 font-bold uppercase">{{ $assignment->sede->nombre_sede }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                                <div>
                                    <p class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest">Frecuencia</p>
                                    <p class="text-[10px] font-black text-indigo-100 uppercase italic">{{ $assignment->getFrequencyLabel() }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest">Límite</p>
                                    <p class="text-[10px] font-black {{ $vencido ? 'text-red-500' : 'text-white' }} uppercase italic">{{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'S/F' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($assignment->status === 'completed')
                                <a href="{{ route('tecnico.inspections.show', $assignment->inspection->id) }}" class="flex-1 h-12 flex items-center justify-center bg-white/5 border border-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest italic">Ver Registro</a>
                                <a href="{{ route('tecnico.inspections.pdf.download', $assignment->inspection->id) }}" class="h-12 w-12 flex items-center justify-center bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            @else
                                <a href="{{ route('tecnico.checklists.fill', ['id' => $assignment->dynamic_checklist_id, 'assignment' => $assignment->id]) }}" class="flex-1 h-12 flex items-center justify-center bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl shadow-xl shadow-ep-primary/20">Ejecutar Misión</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-ep-dark/40 rounded-[2rem] border border-white/5">
                        <p class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">No hay misiones activas registradas</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-[9px] font-black text-indigo-100/10 uppercase tracking-[0.4em] italic leading-relaxed">XamaSafe Tactical Monitoring System • v1.0.0</p>
            </div>
        </div>

        <style>
            .glow-text-red { text-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }
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
</div>
