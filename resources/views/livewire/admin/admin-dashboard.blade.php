<div class="min-h-screen bg-[#11111D] pb-12 relative overflow-hidden">


    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark/80 backdrop-blur-md flex items-center z-20 border-b border-white/5">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
    
                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">Terminal de Inteligencia</h1>
                        <h2 class="text-sm font-black text-white uppercase tracking-tight italic leading-none">
                            Dashboard <span class="text-ep-primary/80">Administrativo</span>
                        </h2>
                    </div>
                </div>
    
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->rol === \App\Models\User::ROLE_GESTOR || auth()->user()->rol === \App\Models\User::ROLE_SUPER_ADMIN)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                           class="hidden xs:flex bg-white/5 backdrop-blur-md text-[9px] font-black uppercase tracking-widest italic text-indigo-200 hover:bg-white/10 px-4 py-2 rounded-xl border border-white/10 transition-all items-center gap-2 group">
                           <svg class="w-3.5 h-3.5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                            Modo Técnico
                        </a>
                    @endif
                    
                    <a href="{{ route('profile') }}" class="flex items-center space-x-3 bg-white/5 backdrop-blur-sm p-1 pr-4 rounded-full border border-white/5 hover:bg-white/10 transition-all group overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <div class="w-8 h-8 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg relative z-10">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1 relative z-10"></div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic relative z-10">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>

        </header>
    @endsection

    <main class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10">
        
        {{-- Dashboard Toolbar (HUD Style) --}}
        {{-- Dashboard Toolbar (Advanced HUD Control) --}}
        <div class="flex flex-col gap-6 mb-12">
            <div class="flex flex-wrap items-end justify-end gap-4">
                
                {{-- Custom Period Selector --}}
                <div x-data="{ 
                        open: false, 
                        selected: @entangle('period'),
                        options: {
                            'this_month': 'ESTE MES',
                            'last_month': 'MES ANTERIOR',
                            'this_quarter': 'ESTE TRIMESTRE',
                            'this_year': 'ESTE AÑO',
                            'last_year': 'AÑO ANTERIOR',
                            'custom': 'PERSONALIZADO'
                        }
                    }" 
                    class="relative flex flex-col gap-1.5"
                    @click.away="open = false">
                    <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2">Periodo Táctico</label>
                    <button @click="open = !open" 
                            class="flex items-center gap-3 bg-white/5 backdrop-blur-xl p-2 rounded-2xl border border-white/10 shadow-2xl hover:border-ep-primary/30 transition-all text-left min-w-[200px]">
                        <div class="p-1.5 bg-ep-primary/20 rounded-lg">
                            <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic flex-1" x-text="options[selected] || 'SELECCIONAR'"></span>
                        <svg class="w-3 h-3 text-white/20 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="absolute top-full left-0 mt-3 w-full z-50 bg-[#1E1E2D]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] py-2 overflow-hidden ring-1 ring-white/5">
                        <template x-for="(label, value) in options" :key="value">
                            <div @click="selected = value; open = false; $wire.set('period', value)"
                                 class="px-4 py-2.5 text-[10px] font-black text-indigo-100/60 uppercase tracking-widest italic hover:bg-ep-primary/20 hover:text-white cursor-pointer transition-colors"
                                 :class="selected === value ? 'bg-ep-primary/10 text-ep-primary' : ''"
                                 x-text="label">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Custom Empresa Selector --}}
                <div x-data="{ 
                        open: false, 
                        selected: @entangle('empresaId'),
                        get selectedLabel() {
                            if(!this.selected) return 'TODAS LAS EMPRESAS';
                            const el = document.querySelector(`[data-empresa-id='${this.selected}']`);
                            return el ? el.innerText : 'CARGANDO...';
                        }
                    }" 
                    class="relative flex flex-col gap-1.5"
                    @click.away="open = false">
                    <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2">Empresa / Nodo</label>
                    <button @click="open = !open" 
                            class="flex items-center gap-3 bg-white/5 backdrop-blur-xl p-2 rounded-2xl border border-white/10 shadow-2xl hover:border-ep-primary/30 transition-all text-left min-w-[220px]">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic flex-1 truncate" x-text="selectedLabel"></span>
                        <svg class="w-3 h-3 text-white/20 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="absolute top-full left-0 mt-3 w-full z-50 bg-[#1E1E2D]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] py-2 overflow-hidden ring-1 ring-white/5">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar-premium">
                            <div @click="selected = null; open = false; $wire.set('empresaId', null)"
                                 class="px-4 py-2.5 text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic hover:bg-white/5 cursor-pointer transition-colors border-b border-white/5">
                                TODAS LAS EMPRESAS
                            </div>
                            @foreach($empresas as $empresa)
                                <div @click="selected = '{{ $empresa->id }}'; open = false; $wire.set('empresaId', '{{ $empresa->id }}')"
                                     data-empresa-id="{{ $empresa->id }}"
                                     class="px-4 py-2.5 text-[10px] font-black text-indigo-100/60 uppercase tracking-widest italic hover:bg-ep-primary/20 hover:text-white cursor-pointer transition-colors"
                                     :class="selected == '{{ $empresa->id }}' ? 'bg-ep-primary/10 text-ep-primary' : ''">
                                    {{ $empresa->nombre }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Custom Sede Selector --}}
                <div x-data="{ 
                        open: false, 
                        selected: @entangle('sedeId'),
                        empresaSelected: @entangle('empresaId'),
                        get selectedLabel() {
                            if(!this.selected) return 'TODAS LAS SEDES';
                            const el = document.querySelector(`[data-sede-id='${this.selected}']`);
                            return el ? el.innerText : 'CARGANDO...';
                        }
                    }" 
                    class="relative flex flex-col gap-1.5"
                    @click.away="open = false">
                    <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2">Sede / Ubicación</label>
                    <button @click="if(empresaSelected) open = !open" 
                            :disabled="!empresaSelected"
                            class="flex items-center gap-3 bg-white/5 backdrop-blur-xl p-2 rounded-2xl border border-white/10 shadow-2xl transition-all text-left min-w-[220px]"
                            :class="empresaSelected ? 'hover:border-ep-primary/30 opacity-100' : 'opacity-40 cursor-not-allowed'">
                        <div class="p-1.5 bg-emerald-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic flex-1 truncate" x-text="selectedLabel"></span>
                        <svg class="w-3 h-3 text-white/20 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="absolute top-full left-0 mt-3 w-full z-50 bg-[#1E1E2D]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] py-2 overflow-hidden ring-1 ring-white/5">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar-premium">
                            <div @click="selected = null; open = false; $wire.set('sedeId', null)"
                                 class="px-4 py-2.5 text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic hover:bg-white/5 cursor-pointer transition-colors border-b border-white/5">
                                TODAS LAS SEDES
                            </div>
                            @foreach($sedes as $sede)
                                <div @click="selected = '{{ $sede->id }}'; open = false; $wire.set('sedeId', '{{ $sede->id }}')"
                                     data-sede-id="{{ $sede->id }}"
                                     class="px-4 py-2.5 text-[10px] font-black text-indigo-100/60 uppercase tracking-widest italic hover:bg-ep-primary/20 hover:text-white cursor-pointer transition-colors"
                                     :class="selected == '{{ $sede->id }}' ? 'bg-ep-primary/10 text-ep-primary' : ''">
                                    {{ $sede->nombre_sede }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Custom Tecnico Selector --}}
                <div x-data="{ 
                        open: false, 
                        selected: @entangle('tecnicoId'),
                        get selectedLabel() {
                            if(!this.selected) return 'TODOS LOS TÉCNICOS';
                            const el = document.querySelector(`[data-tecnico-id='${this.selected}']`);
                            return el ? el.innerText : 'CARGANDO...';
                        }
                    }" 
                    class="relative flex flex-col gap-1.5"
                    @click.away="open = false">
                    <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2">Operador Técnico</label>
                    <button @click="open = !open" 
                            class="flex items-center gap-3 bg-white/5 backdrop-blur-xl p-2 rounded-2xl border border-white/10 shadow-2xl hover:border-ep-primary/30 transition-all text-left min-w-[200px]">
                        <div class="p-1.5 bg-amber-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic flex-1 truncate" x-text="selectedLabel"></span>
                        <svg class="w-3 h-3 text-white/20 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="absolute top-full left-0 mt-3 w-full z-50 bg-[#1E1E2D]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] py-2 overflow-hidden ring-1 ring-white/5">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar-premium">
                            <div @click="selected = null; open = false; $wire.set('tecnicoId', null)"
                                 class="px-4 py-2.5 text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic hover:bg-white/5 cursor-pointer transition-colors border-b border-white/5">
                                TODOS LOS TÉCNICOS
                            </div>
                            @foreach($tecnicos as $tecnico)
                                <div @click="selected = '{{ $tecnico->id }}'; open = false; $wire.set('tecnicoId', '{{ $tecnico->id }}')"
                                     data-tecnico-id="{{ $tecnico->id }}"
                                     class="px-4 py-2.5 text-[10px] font-black text-indigo-100/60 uppercase tracking-widest italic hover:bg-ep-primary/20 hover:text-white cursor-pointer transition-colors"
                                     :class="selected == '{{ $tecnico->id }}' ? 'bg-ep-primary/10 text-ep-primary' : ''">
                                    {{ $tecnico->nombres }} {{ $tecnico->apellidos }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Action & Status Zone --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Status Badge Group --}}
                    <div class="flex items-center gap-2 p-1 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl">
                        {{-- Alpha Phase Badge --}}
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/5 rounded-xl">
                            <div class="w-1 h-1 bg-ep-primary rounded-full animate-pulse"></div>
                            <span class="text-[8px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic leading-none">ALPHA <span class="text-white">PHASE</span></span>
                        </div>

                        {{-- Alerts Badge (Overdue Assignments) --}}
                        @if($alerts['overdue_assignments'] > 0)
                            <div wire:click="$set('showOverdueModal', true)" 
                                 class="flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 border border-red-500/20 rounded-xl animate-pulse cursor-pointer hover:bg-red-500/20 transition-all shadow-[0_0_15px_rgba(239,68,68,0.1)] group/alert" 
                                 title="Clic para ver detalles de alertas">
                                <div class="w-1.5 h-1.5 bg-red-500 rounded-full shadow-[0_0_8px_#ef4444] group-hover/alert:scale-125 transition-transform"></div>
                                <span class="text-[8px] font-black text-red-500 uppercase tracking-[0.2em] italic leading-none">
                                    {{ $alerts['overdue_assignments'] }} <span class="text-white/60">ALERTAS</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button wire:click="resetFilters" 
                                class="p-2.5 bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 text-indigo-100/40 hover:text-red-400 hover:border-red-500/50 hover:bg-red-500/10 transition-all shadow-2xl group"
                                title="Reiniciar Filtros">
                            <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </button>
                        <button wire:click="$refresh" 
                                class="p-2.5 bg-ep-primary/10 backdrop-blur-xl rounded-2xl border border-ep-primary/30 text-ep-primary hover:bg-ep-primary hover:text-white transition-all shadow-lg shadow-ep-primary/20 group"
                                title="Sincronizar Panel">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Custom Date Range (Conditional) --}}
            @if($period === 'custom')
                <div class="flex items-center justify-end gap-4 animate-fade-in">
                    <div class="bg-white/5 backdrop-blur-xl p-3 rounded-2xl border border-white/10 flex items-center gap-4 shadow-2xl">
                        <div class="flex flex-col gap-1">
                            <label class="text-[8px] font-black text-white/20 uppercase tracking-widest pl-1">Desde</label>
                            <input type="date" wire:model.live="dateFrom" class="bg-black/40 border border-white/10 text-[10px] font-black text-white rounded-xl px-4 py-1.5 uppercase italic [color-scheme:dark] focus:ring-ep-primary/30 focus:border-ep-primary/30">
                        </div>
                        <div class="h-8 w-px bg-white/5 self-end mb-1"></div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[8px] font-black text-white/20 uppercase tracking-widest pl-1">Hasta</label>
                            <input type="date" wire:model.live="dateTo" class="bg-black/40 border border-white/10 text-[10px] font-black text-white rounded-xl px-4 py-1.5 uppercase italic [color-scheme:dark] focus:ring-ep-primary/30 focus:border-ep-primary/30">
                        </div>
                    </div>
                </div>
            @else
                <div class="flex justify-end pr-4 text-[10px] font-black text-ep-primary italic tracking-widest opacity-60">
                    <span class="bg-ep-primary/5 px-4 py-1 rounded-full border border-ep-primary/10 tracking-[0.2em] shadow-inner shadow-ep-primary/5">
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Row 1: Core Operations (Glass KPIs) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Inspecciones -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 shadow-2xl hover:border-emerald-500/30 transition-all duration-500 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div class="h-14 w-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.1)] rotate-3 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic drop-shadow-sm">Inspecciones</span>
                        <p class="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none mt-1">Completdas</p>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-5xl font-black text-white tracking-tighter italic">{{ $kpis['inspections']['total_period'] }}</span>
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20 shadow-glow-emerald">~{{ $kpis['inspections']['avg_per_day'] }}/día</span>
                    </div>
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden mt-4">
                        <div class="h-full bg-emerald-500 rounded-full shadow-[0_0_10px_#10b981]" style="width: 70%"></div>
                    </div>
                </div>
            </div>

            <!-- Capacitaciones -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 shadow-2xl hover:border-indigo-500/30 transition-all duration-500 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div class="h-14 w-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shadow-[0_0_20px_rgba(79,70,229,0.1)] -rotate-3 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">Capacitación</span>
                        <p class="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none mt-1">Participantes</p>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-5xl font-black text-white tracking-tighter italic">{{ $kpis['trainings']['attendees_period'] }}</span>
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic bg-indigo-500/10 px-2 py-0.5 rounded-lg border border-indigo-500/20">{{ $kpis['trainings']['total_scheduled'] }} Sesiones</span>
                    </div>
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden mt-4 text-transparent">.</div>
                </div>
            </div>

            <!-- Constancias -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 shadow-2xl hover:border-ep-primary/30 transition-all duration-500 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div class="h-14 w-14 rounded-2xl bg-ep-primary/10 border border-ep-primary/30 flex items-center justify-center text-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.1)] rotate-3 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Visitas</span>
                        <p class="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none mt-1">Registros</p>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-5xl font-black text-white tracking-tighter italic">{{ $kpis['visit_certificates']['total_period'] }}</span>
                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest italic bg-orange-500/10 px-2 py-0.5 rounded-lg border border-orange-500/20 shadow-glow-orange">{{ $kpis['visit_certificates']['pending_signature'] }} PENDIENTES</span>
                    </div>
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden mt-4 text-transparent">.</div>
                </div>
            </div>

            <!-- Ecosistema -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 shadow-2xl hover:border-white/20 transition-all duration-500 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/60 shadow-inner -rotate-3 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic">Ecosistema</span>
                        <p class="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none mt-1">Infraestructura</p>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-5xl font-black text-white tracking-tighter italic">{{ $kpis['ecosystem']['total_companies'] }}</span>
                        <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic bg-white/5 px-2 py-0.5 rounded-lg border border-white/10">{{ $kpis['ecosystem']['active_technicians'] }} TÉCNICOS</span>
                    </div>
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden mt-4 text-transparent">.</div>
                </div>
            </div>
        </div>

        {{-- Row 2: Risk Management (Findings & Incidents) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Hallazgos Card (Tactical Matriz) -->
            <div class="col-span-1 md:col-span-2 bg-[#1E1E2D] rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <div class="px-10 py-6 border-b border-white/5 flex items-center justify-between relative z-10">
                    <h3 class="text-[10px] font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-red-500 rounded-full shadow-[0_0_12px_rgba(239,68,68,0.6)]"></span>
                        Gestión de Hallazgos Operativos
                    </h3>
                    @if($kpis['findings']['critical'] > 0)
                        <div class="px-4 py-1.5 bg-red-500 text-white text-[9px] font-black rounded-xl animate-pulse uppercase tracking-[0.2em] italic shadow-[0_0_20px_rgba(239,68,68,0.3)]">
                            ¡{{ $kpis['findings']['critical'] }} CRÍTICOS DETECTADOS!
                        </div>
                    @endif
                </div>
                <div class="p-10 grid grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    <div class="p-4 rounded-3xl bg-white/5 border border-white/5 hover:border-white/10 transition-colors">
                        <span class="block text-4xl font-black text-white italic tracking-tighter">{{ $kpis['findings']['total'] }}</span>
                        <span class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] block mt-1">Radar: Detectados</span>
                    </div>
                    <div class="p-4 rounded-3xl bg-red-500/5 border border-red-500/10 hover:border-red-500/20 transition-colors">
                        <span class="block text-4xl font-black text-red-500 italic tracking-tighter">{{ $kpis['findings']['pending'] }}</span>
                        <span class="text-[9px] font-black text-red-500/50 uppercase tracking-[0.3em] block mt-1 leading-none shadow-glow-red">Zona Roja: Abiertos</span>
                    </div>
                    <div class="p-4 rounded-3xl bg-emerald-500/5 border border-emerald-500/10 hover:border-emerald-500/20 transition-colors">
                        <span class="block text-4xl font-black text-emerald-400 italic tracking-tighter">{{ $kpis['findings']['resolved'] }}</span>
                        <span class="text-[9px] font-black text-emerald-400/40 uppercase tracking-[0.3em] block mt-1 shadow-glow-emerald">Zona Segura: Resueltos</span>
                    </div>
                    <div class="p-4 rounded-3xl bg-ep-primary/5 border border-ep-primary/10 hover:border-ep-primary/20 transition-colors">
                        <span class="block text-4xl font-black text-ep-primary italic tracking-tighter">
                            {{ $kpis['findings']['total'] > 0 ? round(($kpis['findings']['resolved'] / $kpis['findings']['total']) * 100) : 0 }}<span class="text-xl">%</span>
                        </span>
                        <span class="text-[9px] font-black text-ep-primary/40 uppercase tracking-[0.3em] block mt-1 leading-none shadow-glow-orange">Eficacia Táctica</span>
                    </div>
                </div>
            </div>

            <!-- Incidentes Card (HUD Warning) -->
            <div class="bg-[#1E1E2D] rounded-[3rem] border border-white/5 shadow-2xl p-10 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-[60px] group-hover:bg-indigo-500/20 transition-all duration-1000"></div>
                <div class="flex items-center justify-between mb-10 relative z-10">
                    <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner group-hover:rotate-6 transition-all duration-500">
                        <svg class="w-7 h-7 filter drop-shadow-[0_0_8px_rgba(246,146,57,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-indigo-300 italic border-b border-indigo-500/30 pb-1">Seguridad Crítica</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-[9px] font-bold text-white/30 uppercase tracking-[0.4em] mb-1 italic">Reporte de Incidentes</h3>
                    <div class="flex items-baseline gap-4 mb-8">
                        <span class="text-6xl font-black text-white italic tracking-tighter leading-none">{{ $kpis['incidents']['total'] }}</span>
                        @if($kpis['incidents']['serious'] > 0)
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-red-500 uppercase tracking-widest italic glow-text-red leading-none">{{ $kpis['incidents']['serious'] }} GRAVES</span>
                                <span class="text-[8px] font-bold text-white/10 uppercase tracking-widest mt-1 italic">Protocolo Activado</span>
                            </div>
                        @endif
                    </div>
                    <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em] italic">Días Operativos Perdidos:</span>
                        <div class="flex items-center gap-2">
                             <div class="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></div>
                             <span class="text-2xl font-black text-white italic leading-none">{{ $kpis['incidents']['lost_days'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Corporate Compliance & Ecosystem --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Master Reports Card (Glass Box) -->
            <div class="col-span-1 md:col-span-2 bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 flex items-center gap-10 hover:bg-white/[0.07] transition-all group overflow-hidden relative">
                <div class="absolute inset-0 bg-gradient-to-r from-ep-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="h-20 w-20 rounded-3xl bg-ep-primary/10 border border-ep-primary/30 flex items-center justify-center text-ep-primary shadow-[0_0_30px_rgba(246,146,57,0.1)] rotate-3 group-hover:rotate-0 transition-all duration-700 relative z-10">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 relative z-10">
                    <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-2 italic">Flujo de Informes Maestros</h3>
                    <div class="flex items-center gap-10">
                        <div class="flex flex-col">
                            <span class="text-3xl font-black text-white italic tracking-tighter leading-none">{{ $kpis['reports']['total_master'] }}</span>
                            <span class="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1">Solicitados</span>
                        </div>
                        <div class="h-10 w-px bg-white/5"></div>
                        <div class="flex flex-col">
                            <span class="text-3xl font-black text-emerald-400 italic tracking-tighter leading-none glow-text-success">{{ $kpis['reports']['completed'] }}</span>
                            <span class="text-[9px] font-black text-emerald-500/30 uppercase tracking-widest mt-1 leading-none shadow-glow-emerald">Finalizados</span>
                        </div>
                    </div>
                </div>
                <div class="text-right relative z-10">
                    <div class="px-5 py-2.5 bg-white/5 rounded-2xl border border-white/10 text-[9px] font-black text-white/50 uppercase tracking-[0.2em] italic group-hover:border-ep-primary/30 group-hover:text-ep-primary transition-all">
                        {{ $kpis['reports']['pending'] }} <br> Pendientes
                    </div>
                </div>
            </div>

            <!-- Annual Compliance (Neon Progress) -->
            <div class="bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-8 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-4 italic px-2">Cumplimiento Anual</h3>
                <div class="flex items-center gap-4 relative z-10">
                    <span class="text-4xl font-black text-white italic tracking-tighter leading-none glow-text-success">
                        {{ $kpis['annual']['compliance_percentage'] }}%
                    </span>
                    <div class="flex-1 h-3 bg-white/5 rounded-full overflow-hidden border border-white/10 p-[1px]">
                        <div class="h-full bg-gradient-to-r from-emerald-600 top-emerald-400 to-emerald-300 rounded-full shadow-[0_0_15px_#10b981]" style="width:{{ $kpis['annual']['compliance_percentage'] }}%"></div>
                    </div>
                </div>
                <p class="text-[8px] font-bold text-emerald-500/40 uppercase tracking-[0.3em] mt-3 italic px-2">Objetivo Proyectado: Q4 2026</p>
            </div>

            <!-- Eco-System Link (Dark Action) -->
            <div class="bg-[#1E1E2D] rounded-[2.5rem] border border-white/5 p-8 text-white flex flex-col justify-center relative overflow-hidden group hover:border-ep-primary/30 transition-all cursor-pointer">
                <div class="absolute right-[-10%] bottom-[-10%] text-white/5 group-hover:text-white/10 group-hover:scale-125 transition-all duration-700">
                    <svg class="w-32 h-32 rotate-[-15deg]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.4em] mb-2 italic px-2 leading-none">Terminal de Nodo</h3>
                <p class="text-[11px] font-black text-white/60 uppercase tracking-widest italic px-2 leading-tight">Directorio Central<br><span class="text-white/30 font-bold">Empresas & Técnicos</span></p>
                <div class="mt-4 flex items-center gap-3 text-[9px] font-black text-ep-primary uppercase italic px-2">
                    <div class="h-px w-6 bg-ep-primary animate-pulse"></div>
                    Establecer Conexión
                    <svg class="w-3 h-3 group-hover:translate-x-2 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                <a href="{{ route('gestor.companies.index') }}" class="absolute inset-0 z-10"></a>
            </div>
        </div>

        {{-- Layout Grid for Charts and Lists --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12">
            {{-- Left Column: Charts and Stats (Col 8) --}}
            <div class="lg:col-span-2 space-y-10">
                <!-- Main Trend Chart (HUD Consola) -->
                <div class="bg-[#1E1E2D] rounded-[3.5rem] border border-white/5 p-10 shadow-3xl relative">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6 px-4">
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tighter italic uppercase flex items-center gap-4">
                                <span class="w-1.5 h-8 bg-indigo-500 rounded-full shadow-[0_0_15px_#6366f1]"></span>
                                Evolución Operativa
                            </h3>
                            <p class="text-[10px] text-white/20 font-bold uppercase tracking-[0.4em] mt-2 italic pl-5">Análisis de Tendencia Radar</p>
                        </div>
                        <div class="flex items-center gap-4">
                             <div class="flex items-center gap-3 px-4 py-2 bg-white/5 rounded-2xl border border-white/5 group hover:border-emerald-500/30 transition-all">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]"></span>
                                <span class="text-[9px] font-black text-white/50 uppercase tracking-widest italic group-hover:text-white transition-colors">Inspecciones</span>
                             </div>
                             <div class="flex items-center gap-3 px-4 py-2 bg-white/5 rounded-2xl border border-white/5 group hover:border-red-500/30 transition-all">
                                <span class="h-2 w-2 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444]"></span>
                                <span class="text-[9px] font-black text-white/50 uppercase tracking-widest italic group-hover:text-white transition-colors">Incidentes</span>
                             </div>
                        </div>
                    </div>
                    <div wire:ignore style="height: 380px;" class="px-4">
                        <canvas id="mainTrendChart"></canvas>
                    </div>
                </div>

                <!-- Secondary Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/10 p-10 shadow-2xl group overflow-hidden">
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.4em] mb-10 px-4 border-l-4 border-red-500 italic leading-none">Estado de Hallazgos</h3>
                        <div wire:ignore style="height: 280px;" class="scale-90 group-hover:scale-100 transition-transform duration-1000">
                            <canvas id="findingsStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/10 p-10 shadow-2xl group overflow-hidden">
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.4em] mb-10 px-4 border-l-4 border-ep-primary italic leading-none">Top Servicios Activos</h3>
                        <div wire:ignore style="height: 280px;" class="group-hover:translate-x-2 transition-transform duration-1000 px-2">
                            <canvas id="topChecklistsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Rankings Section ("Technical Nodes") -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Top Technicians -->
                    <div class="bg-[#1E1E2D] rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl group">
                        <div class="px-10 py-6 bg-white/5 border-b border-white/10 relative overflow-hidden">
                             <div class="absolute inset-0 bg-gradient-to-r from-ep-primary/5 to-transparent"></div>
                            <h3 class="text-[11px] font-black text-white italic uppercase tracking-[0.4em] relative z-10">Ranking Desempeño Técnicos</h3>
                        </div>
                        <div class="p-8 space-y-4">
                            @foreach($rankings['top_technicians'] as $tech)
                                <div class="flex items-center justify-between p-4 bg-white/5 border border-white/5 rounded-[1.5rem] hover:bg-white/[0.08] hover:border-white/10 transition-all group/item">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-ep-primary/20 border border-ep-primary/30 rounded-xl flex items-center justify-center text-ep-primary text-[10px] font-black shadow-inner shadow-ep-primary/10 group-hover/item:rotate-12 transition-transform">
                                            {{ substr($tech->nombres, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-white uppercase italic tracking-widest leading-none">{{ $tech->nombres }}</p>
                                            <p class="text-[8px] text-white/20 font-bold uppercase tracking-[0.3em] mt-1 italic">Unidad de Élite</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xl font-black text-ep-primary italic tracking-tighter leading-none shadow-glow-orange">{{ $tech->checklists_count }}</span>
                                        <span class="text-[8px] text-white/20 font-bold uppercase block tracking-widest leading-none mt-1">Nodos</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Companies with Findings -->
                    <div class="bg-[#1E1E2D] rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl group text-transparent">.
                        <div class="px-10 py-6 bg-white/5 border-b border-white/10 relative overflow-hidden">
                             <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 to-transparent"></div>
                            <h3 class="text-[11px] font-black text-white italic uppercase tracking-[0.4em] relative z-10">Consola de Riesgo Corporativo</h3>
                        </div>
                        <div class="p-8 space-y-4 text-white">
                            @foreach($rankings['top_companies_findings'] as $empresa)
                                <div class="flex items-center justify-between p-4 bg-white/5 border border-white/5 rounded-[1.5rem] hover:bg-red-500/[0.03] hover:border-red-500/10 transition-all group/item">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl flex items-center justify-center group-hover/item:-rotate-12 transition-transform">
                                            <svg class="w-5 h-5 filter drop-shadow-[0_0_8px_rgba(239,68,68,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div class="max-w-[150px]">
                                            <p class="text-[11px] font-black text-white uppercase italic tracking-widest leading-none truncate">{{ $empresa->nombre }}</p>
                                            <p class="text-[8px] text-red-500/40 font-bold uppercase tracking-[0.3em] mt-1 italic truncate">Criticidad Operativa</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xl font-black text-red-500 italic tracking-tighter leading-none shadow-glow-red">{{ $empresa->findings_count }}</span>
                                        <span class="text-[8px] text-white/20 font-bold uppercase block tracking-widest leading-none mt-1">Alertas</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Timeline ("Sensor Stream") --}}
            <div class="lg:col-span-1 space-y-10">
                <!-- Activity Timeline -->
                <div class="bg-[#1E1E2D] rounded-[3.5rem] border border-white/5 flex flex-col h-full shadow-3xl overflow-hidden group">
                    <div class="px-10 py-8 bg-white/5 border-b border-white/5 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-ep-primary/5 to-transparent origin-left -translate-x-full group-hover:translate-x-0 transition-transform duration-1000"></div>
                        <h3 class="text-xl font-black text-white italic tracking-tighter uppercase relative z-10 leading-none">
                            Sensor <span class="text-ep-primary leading-none">Stream</span>
                        </h3>
                        <div class="p-3 bg-white/5 rounded-2xl relative z-10 shadow-inner group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-ep-primary/60 filter drop-shadow-[0_0_8px_rgba(246,146,57,0.3)] animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="p-8 overflow-y-auto max-h-[1400px] scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                        <div class="relative ml-6 border-l border-white/5 space-y-10 pb-10">
                            @forelse($recentActivity as $activity)
                                <div class="relative pl-10 group/item">
                                    {{-- Time Dot (Neon Node) --}}
                                    <div class="absolute -left-[5.5px] top-0 w-2.5 h-2.5 rounded-full border border-ep-dark ring-4 ring-ep-dark
                                        {{ $activity->type === 'visita' ? 'bg-indigo-500 shadow-[0_0_12px_#6366f1]' : 
                                           ($activity->type === 'inspeccion' ? 'bg-emerald-500 shadow-[0_0_12px_#10b981]' : 
                                           ($activity->type === 'capacitacion' ? 'bg-amber-500 shadow-[0_0_12px_#f59e0b]' : 'bg-red-500 shadow-[0_0_12px_#ef4444]')) }}
                                        group-hover/item:scale-150 transition-transform duration-500">
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-white/30 italic group-hover/item:text-ep-primary/60 transition-colors">
                                                {{ $activity->type }}
                                            </p>
                                            <span class="text-[8px] font-black text-white/30 bg-white/5 px-3 py-1 rounded-full border border-white/5 tracking-widest italic leading-none">
                                                {{ $activity->date ? \Carbon\Carbon::parse($activity->date)->format('H:i / d—m') : '—' }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] font-black text-white uppercase italic tracking-widest leading-snug group-hover/item:translate-x-1 transition-transform">
                                            {{ $activity->title }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-3">
                                            <div class="flex items-center gap-2 bg-white/5 px-3 py-1.5 rounded-xl border border-white/5 shadow-inner">
                                                 <div class="w-1 h-1 bg-white/40 rounded-full"></div>
                                                <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic truncate max-w-[140px]">
                                                    {{ $activity->empresa }}
                                                </span>
                                            </div>
                                            <span class="text-[8px] font-bold text-white/10 uppercase tracking-widest italic truncate leading-none">
                                                ID: {{ $activity->user_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-20 opacity-20">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                                      <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.4em] italic mb-1 text-transparent">.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        [x-cloak] { display: none !important; }

        .custom-scrollbar-premium::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar-premium::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar-premium::-webkit-scrollbar-thumb {
            background: rgba(246, 146, 57, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar-premium::-webkit-scrollbar-thumb:hover {
            background: rgba(246, 146, 57, 0.4);
        }

        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        .shadow-3xl { shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.6); }
        .shadow-glow-emerald { text-shadow: 0 0 8px rgba(16, 185, 129, 0.4); }
        .shadow-glow-orange { text-shadow: 0 0 8px rgba(246, 146, 57, 0.4); }
        .shadow-glow-red { text-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }
        .glow-text-success { filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.4)); }
        .glow-text-red { filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.4)); }
        
        canvas { filter: drop-shadow(0 0 30px rgba(0,0,0,0.5)); }

        [color-scheme="dark"] ::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>
    {{-- Modal de Asignaciones Vencidas (HUD Style) --}}
    <div x-data="{ show: @entangle('showOverdueModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        {{-- Overlay con desenfoque --}}
        <div class="absolute inset-0 bg-[#0A0A0F]/80 backdrop-blur-xl" @click="show = false"></div>

        {{-- Contenedor del Modal --}}
        <div class="relative w-full max-w-4xl bg-[#11111D] border border-red-500/30 rounded-[2rem] shadow-[0_0_50px_rgba(239,68,68,0.15)] overflow-hidden flex flex-col max-h-[85vh]">
            {{-- Header del Modal --}}
            <div class="relative px-8 py-6 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-500/10 rounded-2xl border border-red-500/20">
                        <svg class="w-6 h-6 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white uppercase italic tracking-wider leading-tight">Protocolo de Alerta Crítica</h3>
                        <p class="text-[10px] font-bold text-red-500/60 uppercase tracking-[0.2em] italic">Asignaciones fuera de plazo detectadas</p>
                    </div>
                </div>
                <button @click="show = false" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition-all text-indigo-200/50 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            {{-- Contenido Scrollable --}}
            <div class="relative flex-1 overflow-y-auto px-8 py-6 custom-scrollbar-premium">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5 text-[9px] font-black text-indigo-200/30 uppercase tracking-[0.2em]">
                            <th class="pb-4 pl-2 font-black">Información del Checklist</th>
                            <th class="pb-4 font-black">Entidad / Ubicación</th>
                            <th class="pb-4 font-black">Vencimiento</th>
                            <th class="pb-4 text-right pr-2 font-black">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($overdueAssignments as $assignment)
                            <tr class="group/row hover:bg-white/[0.02] transition-colors">
                                <td class="py-5 pl-2">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-white uppercase italic tracking-wide">{{ $assignment->checklist->title }}</span>
                                        <span class="text-[10px] font-bold text-indigo-200/40 uppercase tracking-[0.1em] mt-1 flex items-center gap-1.5 line-clamp-1 italic">
                                            <svg class="w-3 h-3 text-red-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5"/></svg>
                                            Responsable: {{ $assignment->user->nombres }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest italic">{{ $assignment->empresa->nombre }}</span>
                                        <span class="text-[9px] font-bold text-indigo-200/20 uppercase tracking-widest mt-0.5">{{ $assignment->sede->nombre_sede }}</span>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-red-500 uppercase italic tracking-widest">{{ $assignment->due_date->format('d/m/Y') }}</span>
                                        <span class="text-[9px] font-bold text-red-500/40 uppercase tracking-[0.15em] mt-1">
                                            Atraso: {{ ceil(abs(now()->diffInDays($assignment->due_date, false))) }} días
                                        </span>
                                    </div>
                                </td>
                                <td class="py-5 text-right pr-2">
                                    <a href="{{ route('gestor.assignments.index', ['search' => $assignment->checklist->title]) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl text-[9px] font-black text-red-500 uppercase tracking-widest italic hover:bg-red-500 hover:text-white transition-all group/btn shadow-lg shadow-red-500/5">
                                        Gestionar
                                        <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center">
                                    <div class="flex flex-col items-center opacity-20">
                                        <svg class="w-16 h-16 text-indigo-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                        <span class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.4em] italic">Sin alertas críticas detectadas</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer del Modal --}}
            <div class="px-8 py-5 border-t border-white/5 bg-white/[0.01] flex items-center justify-between">
                <div class="text-[9px] font-bold text-indigo-200/20 uppercase tracking-[0.2em] italic">
                    Referencia Temporal: {{ now()->format('d/m/Y H:i') }}
                </div>
                <button @click="show = false" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-[10px] font-black text-white uppercase tracking-widest italic transition-all group">
                    Cerrar <span class="group-hover:text-red-500">Protocolo</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let mainTrendChartInstance = null;
    let findingsChartInstance = null;
    let topChecklistsChartInstance = null;

    document.addEventListener('livewire:navigated', initDashboardCharts);
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
    
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('charts-updated', ({ charts }) => {
            updateDashboardCharts(charts);
        });
    });

    function initDashboardCharts() {
        let initialCharts = @json($charts);
        updateDashboardCharts(initialCharts);
    }

    function updateDashboardCharts(charts) {
        const dashboardTheme = {
            font: "'Inter', sans-serif",
            colors: {
                primary: '#F69239',
                secondary: '#1E1E2D',
                success: '#34D399',
                danger: '#EF4444',
                info: '#6366F1',
                warning: '#F59E0B'
            },
            gridColor: 'rgba(255, 255, 255, 0.03)',
            textColor: 'rgba(255, 255, 255, 0.4)'
        };

        Chart.defaults.font.family = dashboardTheme.font;
        Chart.defaults.color = dashboardTheme.textColor;

        // 1. Main Trend Chart (Inspections vs Incidents) - NEON STYLE
        const trendCtx = document.getElementById('mainTrendChart');
        if (trendCtx) {
            if (mainTrendChartInstance) {
                // Determine if we are drawing lines vs points
                let isDays = charts.inspectionsPerMonth.labels.length > 12;
                mainTrendChartInstance.data.labels = charts.inspectionsPerMonth.labels;
                mainTrendChartInstance.data.datasets[0].data = charts.inspectionsPerMonth.data;
                mainTrendChartInstance.data.datasets[0].pointRadius = isDays ? 2 : 4;
                mainTrendChartInstance.data.datasets[1].data = charts.incidentsPerMonth.data;
                mainTrendChartInstance.update();
            } else {
                let isDays = charts.inspectionsPerMonth.labels.length > 12;
                mainTrendChartInstance = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: charts.inspectionsPerMonth.labels,
                        datasets: [
                            {
                                label: 'Inspecciones',
                                data: charts.inspectionsPerMonth.data,
                                borderColor: dashboardTheme.colors.success,
                                borderWidth: 4,
                                backgroundColor: 'rgba(52, 211, 153, 0.05)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: isDays ? 2 : 4,
                                pointBackgroundColor: dashboardTheme.colors.success,
                                pointBorderColor: 'rgba(52, 211, 153, 0.2)',
                                pointBorderWidth: isDays ? 2 : 10
                            },
                            {
                                label: 'Incidentes',
                                data: charts.incidentsPerMonth.data,
                                borderColor: dashboardTheme.colors.danger,
                                borderWidth: 2,
                                borderDash: [5, 5],
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.4,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: {
                            y: { 
                                grid: { color: dashboardTheme.gridColor }, 
                                border: { display: false },
                                ticks: { font: { size: 10, weight: 'bold' } }
                            },
                            x: { 
                                grid: { display: false }, 
                                border: { display: false },
                                ticks: { font: { size: 10, weight: 'bold' } }
                            }
                        },
                        plugins: { 
                            legend: { display: false },
                            tooltip: { 
                                backgroundColor: '#1e1e2d', 
                                cornerRadius: 12, 
                                padding: 12,
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1
                            }
                        }
                    }
                });
            }
        }

        // 2. Findings Status Chart (HUD Doughnut)
        const findingsCtx = document.getElementById('findingsStatusChart');
        if (findingsCtx) {
            if (findingsChartInstance) {
                findingsChartInstance.data.labels = charts.findingsByStatus.labels;
                findingsChartInstance.data.datasets[0].data = charts.findingsByStatus.data;
                findingsChartInstance.update();
            } else {
                findingsChartInstance = new Chart(findingsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: charts.findingsByStatus.labels,
                        datasets: [{
                            data: charts.findingsByStatus.data,
                            backgroundColor: [
                                dashboardTheme.colors.warning, 
                                dashboardTheme.colors.info, 
                                dashboardTheme.colors.success, 
                                dashboardTheme.colors.danger
                            ],
                            borderWidth: 0,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: { 
                            legend: { 
                                position: 'bottom', 
                                labels: { 
                                    usePointStyle: true, 
                                    padding: 25, 
                                    color: 'rgba(255,255,255,0.6)',
                                    font: { size: 10, weight: 'black' } 
                                } 
                            }
                        }
                    }
                });
            }
        }

        // 3. Top Checklists (HUD Bar)
        const topCtx = document.getElementById('topChecklistsChart');
        if (topCtx) {
            if (topChecklistsChartInstance) {
                topChecklistsChartInstance.data.labels = charts.topChecklists.labels;
                topChecklistsChartInstance.data.datasets[0].data = charts.topChecklists.data;
                topChecklistsChartInstance.update();
            } else {
                topChecklistsChartInstance = new Chart(topCtx, {
                    type: 'bar',
                    data: {
                        labels: charts.topChecklists.labels,
                        datasets: [{
                            data: charts.topChecklists.data,
                            backgroundColor: dashboardTheme.colors.primary,
                            borderRadius: 15,
                            barThickness: 18,
                            borderWidth: 0,
                            borderColor: 'transparent',
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { display: false },
                            y: { 
                                grid: { display: false }, 
                                border: { display: false }, 
                                ticks: { 
                                    color: 'rgba(255,255,255,0.8)',
                                    font: { size: 10, weight: 'bold' } 
                                } 
                            }
                        },
                        plugins: { 
                            legend: { display: false },
                            tooltip: { 
                                backgroundColor: '#1E1E2D',
                                padding: 12,
                                titleFont: { size: 12, weight: 'bold' }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush
