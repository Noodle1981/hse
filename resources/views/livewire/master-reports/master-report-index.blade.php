<div class="w-full mx-auto">
    {{-- Header de Informes Maestros Premium --}}
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
                            Archivo de <span class="text-white">Informes Maestros</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Consolidación Operativa
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

    <div class="px-2 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[95rem] mx-auto">
            
            {{-- Toolbar & Filtros Glass --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10 animate-fade-in-down">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-4 sm:p-6 shadow-2xl w-full md:w-auto flex flex-col sm:flex-row gap-4 flex-1 max-w-4xl border-l-4 border-l-ep-primary/40">
                    <div class="relative group flex-1">
                        <input wire:model.live="search" type="text" placeholder="Filtrar informes..." 
                               class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-white placeholder-white/10 focus:ring-2 focus:ring-ep-primary/50 transition-all">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="relative group sm:w-64">
                        <select wire:model.live="status" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-6 pr-10 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="">ESTADOS</option>
                            <option value="solicitado">SOLICITADO</option>
                            <option value="en_redaccion">EN REDACCIÓN</option>
                            <option value="finalizado">FINALIZADO</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-ep-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="w-full md:w-auto flex">
                        <a href="{{ route('gestor.master-reports.create') }}" 
                           class="w-full h-14 sm:h-16 px-6 sm:px-10 flex items-center justify-center bg-ep-primary text-white text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] italic rounded-3xl sm:rounded-[2rem] shadow-2xl shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all group overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            Solicitar Informe Maestro
                        </a>
                    </div>
                @endif
            </div>

            {{-- Lista de Informes Maestros (Estilo Cards Tácticas) --}}
            <div class="space-y-6 animate-fade-in" style="animation-delay: 0.2s">
                @forelse($reports as $report)
                    <div class="group bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 hover:border-ep-primary/40 transition-all duration-700 shadow-2xl relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                        
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 relative z-10">
                            <div class="flex-1 w-full">
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-ep-primary group-hover:scale-110 group-hover:rotate-6 transition-transform shadow-inner">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-base sm:text-lg font-black text-white italic uppercase tracking-tight lg:group-hover:text-ep-primary transition-colors leading-tight mb-1">
                                            {{ $report->titulo }}
                                        </h3>
                                        <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] italic">{{ $report->formatted_period }}</span>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2 sm:ml-auto">
                                        {{-- Estado Neón --}}
                                        @php
                                            $estadoClass = match($report->estado) {
                                                'finalizado' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.2)]',
                                                'en_redaccion' => 'bg-yellow-500/10 border-yellow-500/30 text-yellow-400',
                                                default => 'bg-white/5 border-white/10 text-white/40'
                                            };
                                        @endphp
                                        <span class="px-2 sm:px-3 py-1 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest italic border {{ $estadoClass }}">
                                            {{ strtoupper(str_replace('_', ' ', $report->estado)) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Alerta de Revisión --}}
                                @if(auth()->user()->isTechnician() && $report->estado === 'en_redaccion' && $report->observacion_revision)
                                    <div class="mt-4 mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-start gap-4 animate-pulse">
                                        <div class="p-2 bg-red-500/20 rounded-lg text-red-500">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest italic mb-1">Nota de Corrección Requerida</p>
                                            <p class="text-xs font-bold text-red-100/60 leading-tight italic">"{{ $report->observacion_revision }}"</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-[10px] font-black uppercase tracking-widest italic">
                                    <span class="flex items-center gap-2 text-indigo-100/30 group/meta transition-colors">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 group-hover/meta:border-ep-primary/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        Sede: <span class="text-white/60 ml-1">{{ $report->sede->nombre_sede }}</span>
                                    </span>
                                    <span class="flex items-center gap-2 text-indigo-100/30">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        Oficial: <span class="text-white/60 ml-1">{{ $report->tecnico->nombres }} {{ $report->tecnico->apellidos }}</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="w-full lg:w-auto mt-4 lg:mt-0 flex flex-wrap lg:flex-col gap-3 sm:gap-4">
                                @if($report->estado === 'finalizado')
                                    <a href="{{ route(auth()->user()->isAdmin() ? 'gestor.master-reports.pdf.loading' : 'tecnico.master-reports.pdf.loading', $report->id) }}"
                                       target="_blank"
                                       class="flex-1 lg:flex-none h-11 sm:h-12 px-5 sm:px-6 flex items-center justify-center bg-white/5 text-white text-[9px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all group/pdf shadow-lg active:scale-95">
                                        PDF
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <button wire:click="prepararRevision({{ $report->id }})" 
                                                class="h-11 w-11 sm:h-12 sm:w-12 flex items-center justify-center bg-red-600/10 border border-red-500/20 text-red-500 rounded-2xl hover:bg-red-600 hover:text-white transition-all shadow-lg active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    @endif
                                @endif
                                
                                @if(auth()->user()->isTechnician() && $report->estado !== 'finalizado')
                                    <a href="{{ route('tecnico.master-reports.edit', $report->id) }}"
                                       class="flex-1 lg:flex-none h-14 px-8 flex items-center justify-center bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-ep-primary/20 group/btn relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                        Redactar Reporte
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 sm:p-20 text-center bg-ep-dark/40 rounded-3xl sm:rounded-[3rem] border border-white/5 animate-fade-in">
                        <svg class="h-16 w-16 sm:h-20 sm:w-20 text-indigo-100/5 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <h3 class="text-xs sm:text-sm font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-2 leading-none">Bóveda Vacía</h3>
                        <p class="text-[9px] sm:text-[11px] text-indigo-100/20 font-bold uppercase tracking-widest italic leading-none">No hay consolidados registrados</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación Premium --}}
            @if($reports->hasPages())
                <div class="mt-12 px-8 py-4 bg-ep-dark/40 rounded-[2rem] border border-white/5">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
        

    </div>

    {{-- Modales Premium Dark --}}
    @if($showRevisarModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-xl transition-opacity animate-fade-in" aria-hidden="true" wire:click="$set('showRevisarModal', false)"></div>
                <div class="relative bg-ep-dark w-full max-w-lg rounded-3xl sm:rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden animate-fade-in-down">
                    <div class="px-6 sm:px-10 pt-10 pb-8 text-left">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-black text-white tracking-tight uppercase italic mb-1 leading-none">
                                    Solicitar <span class="text-red-500">Corrección</span>
                                </h3>
                                <p class="text-[9px] sm:text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none">Auditoría Operativa</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] sm:text-[11px] font-black text-red-500/60 uppercase tracking-[0.2em] mb-3 italic px-1">Instrucciones de Revisión*</label>
                                <textarea wire:model="observacion_revision" rows="4" 
                                          class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 sm:px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all placeholder-white/5" 
                                          placeholder="Detalle los puntos a rectificar..."></textarea>
                                @error('observacion_revision') <span class="text-red-500 text-[10px] font-black mt-2 italic block uppercase tracking-widest pl-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-black/20 px-6 sm:px-10 py-6 flex flex-col sm:flex-row-reverse gap-4 border-t border-white/5">
                        <button type="button" wire:click="enviarRevision" 
                                class="w-full sm:w-auto h-12 px-10 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl shadow-xl shadow-red-900/40 hover:brightness-110 active:scale-95 transition-all">
                            Enviar Solicitud
                        </button>
                        <button type="button" wire:click="$set('showRevisarModal', false)" 
                                class="w-full sm:w-auto h-12 px-10 bg-white/5 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        .glow-emerald-text { text-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
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

