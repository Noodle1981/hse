<div class="w-full mx-auto">
    {{-- Header de Incidentes Premium --}}
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
                            Centro de <span class="text-white">Incidentes</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Control de Emergencias y Seguridad
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
        <div class="max-w-[90rem] mx-auto">
            
            {{-- KPIs Tácticos de Seguridad --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6 mb-10 animate-fade-in-down">
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl border border-white/5 p-4 sm:p-6 shadow-xl relative group">
                    <div class="text-[8px] sm:text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] mb-2 italic">Reportes</div>
                    <div class="text-2xl sm:text-3xl font-black text-white italic leading-none">{{ $kpis['total'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl border border-red-500/20 p-4 sm:p-6 shadow-xl relative group overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-all"></div>
                    <div class="text-[8px] sm:text-[9px] font-black text-red-500/60 uppercase tracking-[0.2em] mb-2 italic">Accidentes</div>
                    <div class="text-2xl sm:text-3xl font-black text-red-500 italic leading-none glow-text-red">{{ $kpis['accidentes'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl border border-orange-500/20 p-4 sm:p-6 shadow-xl relative group">
                    <div class="text-[8px] sm:text-[9px] font-black text-orange-500/60 uppercase tracking-[0.2em] mb-2 italic">Graves</div>
                    <div class="text-2xl sm:text-3xl font-black text-orange-500 italic leading-none">{{ $kpis['graves'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl border border-yellow-500/20 p-4 sm:p-6 shadow-xl relative group">
                    <div class="text-[8px] sm:text-[9px] font-black text-yellow-500/60 uppercase tracking-[0.2em] mb-2 italic">Días Perdidos</div>
                    <div class="text-2xl sm:text-3xl font-black text-yellow-500 italic leading-none">{{ $kpis['dias_perdidos'] }}</div>
                </div>
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl border border-blue-500/20 p-4 sm:p-6 shadow-xl relative group hidden sm:block lg:block">
                    <div class="text-[8px] sm:text-[9px] font-black text-blue-500/60 uppercase tracking-[0.2em] mb-2 italic">Afectados</div>
                    <div class="text-2xl sm:text-3xl font-black text-blue-500 italic leading-none">{{ $kpis['personas_afectadas'] }}</div>
                </div>
            </div>

            {{-- Filtros Premium Dark --}}
            <div class="mb-10 animate-fade-in" style="animation-delay: 0.1s">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 shadow-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="relative group">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Buscar incidente..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-white placeholder-white/10 focus:ring-2 focus:ring-red-500/50 transition-all">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/20 group-focus-within:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                        <select wire:model.live="filterTipo" class="bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] cursor-pointer">
                            <option value="">Todos los Tipos</option>
                            <option value="accidente">Accidente</option>
                            <option value="incidente">Incidente</option>
                            <option value="cuasi_accidente">Cuasi Accidente</option>
                        </select>
                        <select wire:model.live="filterGravedad" class="bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] cursor-pointer">
                            <option value="">Todas las Gravedades</option>
                            <option value="leve">⚪ Leve</option>
                            <option value="moderado">🟡 Moderado</option>
                            <option value="grave">🟠 Grave</option>
                            <option value="fatal">🔴 Fatal</option>
                        </select>
                        <select wire:model.live="filterSede" class="bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] cursor-pointer">
                            <option value="">Todas las Sedes</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Listado de Incidentes --}}
            <div class="space-y-6 animate-fade-in" style="animation-delay: 0.2s">
                @forelse($incidentes as $incidente)
                    <div class="group bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 hover:border-red-500/30 transition-all duration-700 shadow-2xl relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/10 to-transparent"></div>
                        
                        <div class="flex flex-col lg:flex-row justify-between items-start gap-8 relative z-10">
                            <div class="flex-1 w-full">
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-red-500 group-hover:scale-110 group-hover:-rotate-6 transition-transform shadow-inner">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] sm:text-[10px] font-black text-indigo-100/30 uppercase tracking-widest sm:tracking-[0.3em] italic mb-1 leading-none">OCURRENCIA: {{ $incidente->fecha_ocurrencia->format('d/m/y') }}</span>
                                        <h3 class="text-base sm:text-lg font-black text-white italic uppercase tracking-tight lg:group-hover:text-red-500 transition-colors leading-tight">
                                            {{ Str::limit($incidente->descripcion, 60) }}
                                        </h3>
                                    </div>
                                                             <div class="flex flex-wrap gap-2 ml-auto lg:ml-0">
                                        {{-- Gravedad --}}
                                        @php
                                            $gravedadClass = match($incidente->gravedad) {
                                                'fatal' => 'bg-red-500/10 border-red-500/40 text-red-500 glow-text-red',
                                                'grave' => 'bg-orange-500/10 border-orange-500/40 text-orange-500',
                                                'moderado' => 'bg-yellow-500/10 border-yellow-500/40 text-yellow-500',
                                                default => 'bg-white/5 border-white/10 text-white/40'
                                            };
                                        @endphp
                                        <span class="px-2 sm:px-3 py-1 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest italic border {{ $gravedadClass }}">
                                            {{ strtoupper($incidente->gravedad) }}
                                        </span>

                                        {{-- Tipo --}}
                                        <span class="px-2 sm:px-3 py-1 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest italic border border-white/5 bg-white/5 text-indigo-100/40">
                                            {{ strtoupper(str_replace('_', ' ', $incidente->tipo)) }}
                                        </span>
                                    </div>       </div>
                                </div>

                                <p class="text-indigo-100/60 mb-6 text-sm italic font-medium leading-relaxed max-w-4xl">
                                    {{ $incidente->descripcion }}
                                </p>

                                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-[10px] font-black uppercase tracking-widest italic">
                                    <span class="flex items-center gap-2 text-indigo-100/30 group/meta transition-colors">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 group-hover/meta:border-red-500/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        {{ $incidente->sede->nombre_sede }}
                                    </span>
                                    <span class="flex items-center gap-2 text-indigo-100/30">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        Afectados: <span class="text-white/60">{{ $incidente->personas_afectadas }}</span>
                                    </span>
                                    <span class="flex items-center gap-2 text-indigo-100/30">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10">
                                            <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        Días Perdidos: <span class="text-white/60">{{ $incidente->dias_perdidos ?? '0' }}</span>
                                    </span>
                                    
                                    @php
                                        $estadoBadge = $incidente->estado === 'cerrado' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
                                    @endphp
                                    <span class="px-3 sm:px-4 py-1 rounded-xl text-[8px] sm:text-[9px] font-black uppercase tracking-widest border {{ $estadoBadge }}">
                                        Licencia: {{ strtoupper($incidente->estado ?? 'pendiente') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="w-full lg:w-auto mt-6 lg:mt-0 flex flex-wrap lg:flex-col gap-3 sm:gap-4">
                                <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.show', $incidente) : route('tecnico.incidentes.show', $incidente) }}"
                                   class="flex-1 lg:flex-none px-6 h-11 sm:h-12 flex items-center justify-center bg-white/5 text-white text-[9px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all text-center active:scale-95">
                                    Gestionar
                                </a>
                                <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.pdf.preview', $incidente) : route('tecnico.incidentes.pdf.preview', $incidente) }}"
                                   target="_blank"
                                   class="h-11 w-11 sm:h-12 sm:w-12 flex items-center justify-center bg-red-600/10 border border-red-500/20 text-red-500 rounded-2xl hover:bg-red-600 hover:text-white transition-all group/pdf shadow-lg active:scale-95">
                                   <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </a>
                                @if(!auth()->user()->isAdmin())
                                    <button wire:click="delete({{ $incidente->id }})"
                                            wire:confirm="¿Expurgar este registro de incidente?"
                                            class="h-11 w-11 sm:h-12 sm:w-12 flex items-center justify-center bg-white/5 border border-white/10 text-white/20 rounded-2xl hover:bg-red-600/10 hover:text-red-500 transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 sm:p-20 text-center bg-ep-dark/40 rounded-3xl sm:rounded-[3rem] border border-white/5 animate-fade-in">
                        <svg class="h-16 w-16 sm:h-20 sm:w-20 text-emerald-500/20 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h3 class="text-xs sm:text-sm font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-2 leading-none">Zona de Operaciones Segura</h3>
                        <p class="text-[9px] sm:text-[11px] text-indigo-100/20 font-bold uppercase tracking-widest italic">No se han registrado incidentes de seguridad</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación Premium --}}
            @if($incidentes->hasPages())
                <div class="mt-10">
                    {{ $incidentes->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
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
