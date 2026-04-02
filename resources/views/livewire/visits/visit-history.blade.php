<div class="w-full mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic mb-1 leading-none">
                            Auditoría de <span class="text-white">Visitas</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Consola de Historial Operativo
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic tracking-tight">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto overflow-hidden">
            
            {{-- TOOLBAR DE FILTROS TÁCTICOS --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 lg:p-10 mb-8 shadow-2xl relative animate-fade-in-down">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-end relative z-10">
                    <!-- Search -->
                    <div class="md:col-span-2 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Buscador Inteligente</span>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary">
                                <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/10 italic"
                                   placeholder="Filtrar por Sede, Empresa o Técnico Especialista...">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Estado Operativo</span>
                        <select wire:model.live="filterStatus" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="">TODOS LOS ESTADOS</option>
                            <option value="in_progress">EN PROGRESO / MASTER</option>
                            <option value="completed">COMPLETADA (CIERRE)</option>
                            <option value="signed">AUDITADA (FIRMADA)</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <button wire:click="$set('search', ''); $set('filterStatus', '')" 
                            class="h-14 bg-white/5 border border-white/10 text-white/40 hover:text-white hover:bg-white/10 rounded-2xl font-black text-[9px] uppercase tracking-[0.2em] italic transition-all flex items-center justify-center gap-3 active:scale-95 group/clear">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16m0-16L4 20"/></svg>
                        Limpiar Auditoría
                    </button>
                </div>
            </div>

            {{-- MASTER LIST: MATRIZ DE HISTORIAL --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-20">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                
                {{-- DESKTOP VIEW --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20">
                                <th class="px-8 py-6 text-left whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Cronología</span></th>
                                <th class="px-8 py-6 text-left whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Técnico Asignado</span></th>
                                <th class="px-8 py-6 text-left whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Sede / Protocolo</span></th>
                                <th class="px-8 py-6 text-left whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Rendimiento Temporal</span></th>
                                <th class="px-8 py-6 text-center whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Estado</span></th>
                                <th class="px-8 py-6 text-right whitespace-nowrap"><span class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Acción</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 animate-fade-in">
                            @forelse ($visits as $visit)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 overflow-hidden relative">
                                    <td class="px-8 py-7">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-white italic leading-none mb-2 mb-1 tracking-tight">{{ $visit->visit_date->format('d/m/Y') }}</span>
                                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic group-hover/row:translate-x-1 transition-transform">
                                                {{ $visit->start_time?->format('H:i') }} • {{ $visit->end_time?->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-ep-primary/10 border border-ep-primary/20 flex items-center justify-center text-ep-primary text-xs font-black shadow-inner shadow-black/50 rotate-3 group-hover/row:rotate-0 transition-all duration-700">
                                                {{ substr($visit->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none mb-1">{{ $visit->user->name }}</div>
                                                <div class="text-[9px] text-white/30 font-bold uppercase tracking-widest italic">{{ $visit->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7">
                                        <div>
                                            <div class="text-sm font-black text-ep-primary italic uppercase tracking-tight leading-none mb-1 group-hover/row:glow-text-orange transition-all">{{ $visit->sede->nombre_sede }}</div>
                                            <div class="text-[9px] text-white/50 font-black uppercase tracking-widest italic leading-none flex items-center gap-2">
                                                <svg class="w-3 h-3 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                {{ $visit->sede->empresa->nombre }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7">
                                        @if($visit->end_time && $visit->start_time)
                                            @php
                                                $mins = $visit->start_time->diffInMinutes($visit->end_time);
                                                $hours = floor($mins / 60);
                                                $remainingMins = $mins % 60;
                                                $isExcessive = $hours >= 8;
                                            @endphp
                                            @if($isExcessive)
                                                <div class="inline-flex items-center px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl text-red-500 animate-pulse group/alert">
                                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-xs font-black italic tracking-tighter uppercase">{{ $hours }}h {{ $remainingMins }}m</span>
                                                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 px-3 py-1 bg-red-600 text-[8px] font-black text-white uppercase rounded-lg opacity-0 group-hover/alert:opacity-100 transition-opacity whitespace-nowrap z-30 pointer-events-none">POSIBLE OLVIDO DE CIERRE</div>
                                                </div>
                                            @else
                                                <div class="text-sm font-black text-white/40 italic">
                                                    @if($hours > 0)
                                                        {{ $hours }}<span class="text-[9px] font-bold mx-0.5">H</span> {{ $remainingMins }}<span class="text-[9px] font-bold mx-0.5">M</span>
                                                    @else
                                                        {{ $remainingMins }}<span class="text-[9px] font-bold mx-0.5 ml-1 uppercase">Minutos</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <div class="inline-flex items-center px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg text-blue-400">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping mr-3 shadow-[0_0_8px_#3b82f6]"></div>
                                                <span class="text-[10px] font-black uppercase italic tracking-widest italic leading-none">OPERATIVO VIVO</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-7 text-center">
                                        @if($visit->status === 'in_progress')
                                            <span class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.1)]">En Progreso</span>
                                        @elseif($visit->status === 'completed' && !$visit->responsible_signature && $visit->signature_skip_reason)
                                            <span class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.1)]">Pendiente Firma</span>
                                        @elseif($visit->status === 'completed')
                                            <span class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.1)]">Completada</span>
                                        @elseif($visit->status === 'signed')
                                            <span class="px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(99,102,241,0.1)] group-hover/row:shadow-indigo-500/30 transition-all">Auditada ✅</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-7 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            @if($visit->status === 'in_progress')
                                                <a href="{{ route('gestor.visits.in-progress', $visit) }}" 
                                                   class="h-11 px-5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center shadow-lg hover:scale-105 transition-all">
                                                    Monitorizar
                                                </a>
                                            @endif
            
                                            @if($visit->status === 'completed' || $visit->status === 'signed')
                                                <a href="{{ route('gestor.visits.certificate.preview', $visit->id) }}" 
                                                   target="_blank"
                                                   class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-white/10 hover:text-ep-primary rounded-xl flex items-center justify-center transition-all group/pdf" 
                                                   title="Previsualizar">
                                                    <svg class="w-4 h-4 group-hover/pdf:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('gestor.visits.certificate.download', $visit->id) }}" 
                                                   class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-white/10 hover:text-emerald-500 rounded-xl flex items-center justify-center transition-all group/down" 
                                                   title="Descargar">
                                                    <svg class="w-4 h-4 group-hover/down:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                            @endif
            
                                            @if(auth()->user()->isAdmin())
                                                <button wire:click="editVisit({{ $visit->id }})" 
                                                        class="h-11 w-11 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-xl flex items-center justify-center transition-all"
                                                        title="Corregir Auditoría">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center">
                                        <div class="h-20 w-20 bg-white/5 rounded-[2rem] border border-white/5 flex items-center justify-center mx-auto mb-6 text-white/10">
                                            <svg class="w-10 h-10 italic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h3 class="text-sm font-black text-white/20 uppercase tracking-[0.4em] italic leading-none">Cero Registros Detectados</h3>
                                        <p class="text-[10px] text-white/10 font-bold uppercase tracking-widest italic mt-4">Ajuste los protocolos de búsqueda para sincronizar datos</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- FOOTER DE PAGINACIÓN PREMIUM --}}
                <div class="px-10 py-6 border-t border-white/5 bg-black/20">
                    <div class="flex items-center justify-between">
                        <div class="text-[9px] font-black text-white/20 uppercase tracking-widest italic leading-none">

                        </div>
                        <div class="pagination-tactical">
                            {{ $visits->links() }}
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- MOBILE VIEW: TARJETAS TÁCTICAS --}}
            <div class="lg:hidden flex flex-col gap-6 mb-20 animate-fade-in">
                @forelse ($visits as $visit)
                    <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative group overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <div class="text-sm font-black text-white italic tracking-tight leading-none mb-1 uppercase">{{ $visit->sede->nombre_sede }}</div>
                                <div class="text-[9px] text-ep-primary font-black uppercase tracking-widest italic">{{ $visit->visit_date->format('d/m/Y') }}</div>
                            </div>
                            @if($visit->status === 'in_progress')
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping shadow-[0_0_8px_#3b82f6]"></div>
                            @else
                                <div class="w-2 h-8 rounded-full {{ $visit->status === 'signed' ? 'bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]' : 'bg-white/10' }}"></div>
                            @endif
                        </div>

                        <div class="space-y-4 pt-6 border-t border-white/5">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center text-[10px] font-black text-white opacity-40">
                                    {{ substr($visit->user->name, 0, 1) }}
                                </div>
                                <span class="text-[10px] font-bold text-white/50 uppercase tracking-widest italic">{{ $visit->user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black text-white/20 uppercase tracking-widest italic uppercase leading-none">Estado</span>
                                @if($visit->status === 'signed')
                                    <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic italic leading-none">Firmada ✅</span>
                                @else
                                    <span class="text-[9px] font-black text-white/40 uppercase tracking-widest italic">Consolidada</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-8">
                            @if($visit->status === 'completed' || $visit->status === 'signed')
                                <a href="{{ route('gestor.visits.certificate.preview', $visit->id) }}" target="_blank" class="h-12 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-[9px] font-black uppercase tracking-widest italic text-white/60">PDF PREVIEW</a>
                                <a href="{{ route('gestor.visits.certificate.download', $visit->id) }}" class="h-12 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-[9px] font-black uppercase tracking-widest italic text-emerald-400">DESCARGAR</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 text-white/10 text-[9px] font-black uppercase tracking-[0.4em] italic">Sin registros históricos detectados</div>
                @endforelse
                <div class="mt-4">
                     {{ $visits->links() }}
                </div>
            </div>
            

        </div>
    </div>

    {{-- MODAL ADM: CORRECCIÓN TÁCTICA --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <div class="absolute inset-0 bg-[#0a0a0f]/90 backdrop-blur-xl" wire:click="$set('showEditModal', false)"></div>
            
            <div class="relative bg-ep-dark/80 border border-white/10 w-full max-w-xl rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.8)] overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                
                <div class="p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-16 h-16 rounded-[2rem] bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight leading-none mb-2">Corregir Matriz Temporal</h3>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest italic">Ajuste de Auditoría Post-Operación</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Fecha de Registro</span>
                            <input type="date" wire:model="editVisitDate" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/40 transition-all [color-scheme:dark]">
                            @error('editVisitDate') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Hora Inicio</span>
                                <input type="time" wire:model="editStartTime" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/40 transition-all [color-scheme:dark]">
                            </div>
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Hora Finalización</span>
                                <input type="time" wire:model="editEndTime" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/40 transition-all [color-scheme:dark]">
                            </div>
                        </div>

                        <div class="p-5 bg-white/5 rounded-2xl border border-white/5 italic text-[10px] text-white/30 leading-snug">
                            <span class="text-ep-primary font-black uppercase mr-2 opacity-100 italic">Protocolo:</span>
                            Utilice esta herramienta para rectificar errores de cierre de jornada técnica. Toda modificación quedará registrada en el sistema de auditoría central.
                        </div>
                    </div>

                    <div class="mt-12 flex gap-4">
                        <button wire:click="updateVisit" class="flex-1 h-14 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-indigo-500 transition-all leading-none shadow-xl shadow-indigo-600/20 active:scale-95">Guardar Corrección</button>
                        <button wire:click="$set('showEditModal', false)" class="h-14 px-8 bg-white/5 text-white/40 rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:text-white transition-all leading-none border border-white/5">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .glow-text-orange { text-shadow: 0 0 12px rgba(246,146,57,0.4); }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out 0.2s forwards; opacity: 0; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        /* Tactical Pagination Styling */
        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important;
            margin: 0 4px !important;
            padding: 8px 14px !important;
            font-weight: 900 !important;
            font-size: 10px !important;
            transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(246,146,57,0.3) !important;
            color: #f69239 !important;
            background: rgba(246,146,57,0.1) !important;
        }
        .pagination-tactical nav a:hover {
            border-color: rgba(255,255,255,0.2) !important;
            color: white !important;
            background: rgba(255,255,255,0.05) !important;
        }
    </style>
</div>

