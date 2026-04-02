<div class="w-full mx-auto">
    {{-- Header de Hallazgos Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1 leading-none">
                            Bitácora de <span class="text-white">Hallazgos</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Mis Reportes Técnicos
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

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[90rem] mx-auto">
            
            {{-- KPIs Personales Tácticos --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 mb-10 animate-fade-in-down">
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-white/5 p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute -right-2 -top-2 w-10 h-10 bg-white/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] mb-2 italic">Total</div>
                    <div class="text-3xl font-black text-white italic leading-none">{{ $kpis['total'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-red-500/10 p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute -right-2 -top-2 w-10 h-10 bg-red-500/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="text-[9px] font-black text-red-500/60 uppercase tracking-[0.2em] mb-2 italic">Pendientes</div>
                    <div class="text-3xl font-black text-red-500 italic leading-none glow-text-red">{{ $kpis['pendientes'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-yellow-500/10 p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute -right-2 -top-2 w-10 h-10 bg-yellow-500/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="text-[9px] font-black text-yellow-500/60 uppercase tracking-[0.2em] mb-2 italic">En Proceso</div>
                    <div class="text-3xl font-black text-yellow-500 italic leading-none">{{ $kpis['en_proceso'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-emerald-500/10 p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute -right-2 -top-2 w-10 h-10 bg-emerald-500/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="text-[9px] font-black text-emerald-400/60 uppercase tracking-[0.2em] mb-2 italic">Resueltos</div>
                    <div class="text-3xl font-black text-emerald-400 italic leading-none glow-emerald-text">{{ $kpis['resueltos'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2rem] border border-ep-primary/20 p-6 shadow-xl relative overflow-hidden group col-span-2 sm:col-span-1">
                    <div class="absolute -right-2 -top-2 w-10 h-10 bg-ep-primary/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="text-[9px] font-black text-ep-primary/60 uppercase tracking-[0.2em] mb-2 italic">Críticos</div>
                    <div class="text-3xl font-black text-ep-primary italic leading-none">{{ $kpis['criticos_abiertos'] }}</div>
                </div>
            </div>

            {{-- Filtros Premium Dark --}}
            <div class="mb-10 animate-fade-in" style="animation-delay: 0.2s">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-indigo-100/20 group-focus-within:text-ep-primary transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Filtrar por hallazgo, código o sede..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white placeholder-white/10 focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all backdrop-blur-lg">
                        </div>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-indigo-100/20 group-focus-within:text-ep-primary transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <select wire:model.live="filterEstado" 
                                    class="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] appearance-none cursor-pointer">
                                <option value="">Todos los Estados Operativos</option>
                                <option value="pendiente">🔴 Pendiente</option>
                                <option value="en_proceso">🟡 En Proceso</option>
                                <option value="resuelto">🔵 Resuelto</option>
                                <option value="verificado">🟢 Verificado</option>
                                <option value="cerrado">🔘 Cerrado</option>
                            </select>
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-ep-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Hallazgos Estilo Táctico --}}
            <div class="space-y-6 animate-fade-in" style="animation-delay: 0.3s">
                @forelse($hallazgos as $hallazgo)
                    <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 hover:border-white/20 transition-all duration-700 shadow-2xl relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                        
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 relative z-10">
                            <div class="flex-1 w-full">
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-ep-primary group-hover:scale-110 group-hover:rotate-6 transition-transform shadow-inner">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-black text-white italic uppercase tracking-tight group-hover:text-ep-primary transition-colors leading-none">
                                        {{ $hallazgo->titulo }}
                                    </h3>
                                    
                                    <div class="flex gap-2">
                                        {{-- Criticidad con Glow --}}
                                        @php
                                            $criticidadClass = match($hallazgo->criticidad) {
                                                'critico' => 'bg-red-500/10 border-red-500/40 text-red-500 glow-text-red',
                                                'alto' => 'bg-orange-500/10 border-orange-500/40 text-orange-500',
                                                'medio' => 'bg-yellow-500/10 border-yellow-500/40 text-yellow-500',
                                                default => 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400'
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic border {{ $criticidadClass }}">
                                            {{ strtoupper($hallazgo->criticidad) }}
                                        </span>

                                        {{-- Estado --}}
                                        @php
                                            $estadoClass = match($hallazgo->estado) {
                                                'cerrado' => 'bg-white/5 border-white/10 text-white/40',
                                                'resuelto', 'verificado' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.2)]',
                                                'en_proceso' => 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400',
                                                default => 'bg-red-500/10 border-red-500/30 text-red-500'
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic border {{ $estadoClass }}">
                                            {{ strtoupper(str_replace('_', ' ', $hallazgo->estado)) }}
                                        </span>
                                    </div>
                                </div>

                                <p class="text-indigo-100/60 mb-6 text-sm italic font-medium leading-relaxed max-w-4xl">
                                    {{ Str::limit($hallazgo->descripcion, 180) }}
                                </p>

                                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-[10px] font-black uppercase tracking-widest text-indigo-100/30 italic">
                                    <span class="flex items-center gap-2 group/meta">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 group-hover/meta:border-ep-primary/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        {{ $hallazgo->sede->nombre_sede }}
                                    </span>
                                    <span class="flex items-center gap-2 group/meta">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 group-hover/meta:border-ep-primary/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        Límite: <span class="text-indigo-100/60 {{ $hallazgo->estaVencido() ? 'text-red-500 font-black' : '' }}">{{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? 'S/F' }}</span>
                                    </span>
                                    
                                    @if($hallazgo->estaVencido() && $hallazgo->estado !== 'cerrado')
                                        <span class="flex items-center gap-2 text-red-500 animate-pulse">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            RETRASADO
                                        </span>
                                    @endif

                                    <div class="flex gap-4">
                                        @if($hallazgo->seguimientos_count > 0)
                                            <span class="flex items-center gap-1.5 text-indigo-100/20 font-black italic">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                {{ $hallazgo->seguimientos_count }} Seq.
                                            </span>
                                        @endif
                                        @if($hallazgo->fotos_count > 0)
                                            <span class="flex items-center gap-1.5 text-indigo-100/20 font-black italic">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $hallazgo->fotos_count }} Multimedia
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full lg:w-auto mt-6 lg:mt-0 flex flex-col items-center">
                                <a href="{{ route('tecnico.hallazgos.show', $hallazgo) }}"
                                   class="px-10 h-14 flex items-center justify-center bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-ep-primary/20 group/btn relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                    <span class="relative">Ver Evidencia</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center bg-ep-dark/40 rounded-[3rem] border border-white/5 animate-fade-in">
                        <div class="relative inline-flex p-10 bg-white/5 rounded-[3rem] border border-white/5 mb-8">
                             <div class="relative z-10 flex flex-col items-center">
                                <svg class="h-20 w-20 text-indigo-100/5 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <h3 class="text-sm font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-2 leading-none">Canal de Hallazgos Limpio</h3>
                                <p class="text-[11px] text-indigo-100/20 font-bold uppercase tracking-widest italic">No se han detectado anomalías pendientes</p>
                             </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginación Premium --}}
            @if($hallazgos->hasPages())
                <div class="mt-10 px-8 py-6 bg-ep-dark/40 rounded-[2.5rem] border border-white/5">
                    {{ $hallazgos->links() }}
                </div>
            @endif
        </div>
        
        <div class="mt-16 text-center">
            <p class="text-[9px] font-black text-indigo-100/10 uppercase tracking-[0.6em] italic leading-relaxed">XamaSafe Tactical Monitoring System • Specialist HUD</p>
        </div>
    </div>

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

