<div class="w-full mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Auditoría <span class="text-white">Forense</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestión Centralizada de Hallazgos
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg group-hover:scale-110 transition-transform">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-2 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
            
            {{-- INTELIGENCIA DE RIESGO: KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 animate-fade-in-down">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1">Total Sistema</p>
                        <p class="text-2xl font-black text-white italic leading-none">{{ $kpis['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[8px] font-black text-amber-500/40 uppercase tracking-[0.3em] italic mb-1">Pendientes</p>
                        <p class="text-2xl font-black text-amber-500 italic leading-none">{{ $kpis['pendientes'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-red-500/10 backdrop-blur-md rounded-3xl border border-red-500/20 p-6 flex items-center justify-between group/stat hover:bg-red-500/20 transition-all">
                    <div>
                        <p class="text-[8px] font-black text-red-500/40 uppercase tracking-[0.3em] italic mb-1">Críticos Abiertos</p>
                        <p class="text-2xl font-black text-red-500 italic leading-none animate-pulse">{{ $kpis['criticos'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center text-red-500">
                        <svg class="w-5 h-5 shadow-[0_0_10px_#ef4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[8px] font-black text-ep-primary/40 uppercase tracking-[0.3em] italic mb-1">Vencidos Total</p>
                        <p class="text-2xl font-black text-ep-primary italic leading-none leading-tight">{{ $kpis['vencidos'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-ep-primary/20 flex items-center justify-center text-ep-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div class="bg-emerald-500/10 backdrop-blur-md rounded-3xl border border-emerald-500/20 p-6 flex items-center justify-between group/stat hover:bg-emerald-500/20 transition-all">
                    <div>
                        <p class="text-[8px] font-black text-emerald-500/40 uppercase tracking-[0.3em] italic mb-1">Cerrados (Mes)</p>
                        <p class="text-2xl font-black text-emerald-500 italic leading-none">{{ $kpis['resueltos_mes'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- HUB DE FILTRADO TÁCTICO --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 shadow-2xl relative animate-fade-in-down overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
                
                <div class="space-y-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        <div class="md:col-span-6 relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Buscador Forense</span>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="TÍTULO, DESCRIPCIÓN O UBICACIÓN..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase">
                        </div>

                        <div class="md:col-span-3 relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Estado Operativo</span>
                            <select wire:model.live="filterEstado" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                <option value="">TODOS LOS ESTADOS</option>
                                <option value="pendiente">PENDIENTE</option>
                                <option value="en_proceso">EN PROCESO</option>
                                <option value="resuelto">RESUELTO</option>
                                <option value="verificado">VERIFICADO</option>
                                <option value="cerrado">CERRADO</option>
                            </select>
                        </div>

                        <div class="md:col-span-3 relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Filtro Criticidad</span>
                            <select wire:model.live="filterCriticidad" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                <option value="">TODAS LAS CRITICIDADES</option>
                                <option value="bajo">BAJO RIESGO</option>
                                <option value="medio">RIESGO MEDIO</option>
                                <option value="alto">ALTO RIESGO</option>
                                <option value="critico">CRÍTICO / EMERGENCIA</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede Operativa</span>
                            <select wire:model.live="filterSede" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">TODAS LAS SEDES</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Técnico Auditor</span>
                            <select wire:model.live="filterTecnico" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">TODOS LOS TÉCNICOS</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}">{{ $tecnico->nombres }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Rango Temporal</span>
                            <select wire:model.live="filterVencidos" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">VENCIMIENTO (TODOS)</option>
                                <option value="si">SOLO VENCIDOS</option>
                                <option value="no">DENTRO DE PLAZO</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-end">
                            @if($search || $filterEstado || $filterCriticidad || $filterSede || $filterTecnico || $filterVencidos)
                                <button wire:click="clearFilters" class="px-6 py-3 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all group/clear flex items-center gap-2">
                                    <svg class="w-3 h-3 group-hover/clear:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                                    Limpiar Inteligencia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- MASTER TABLE: MATRIZ FORENSE --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto custom-scrollbar-horizontal">
                    <table class="w-full border-collapse min-w-[1100px]">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-4 lg:px-6 py-6 cursor-pointer group/th" wire:click="sortBy('titulo')">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Hallazgo</span>
                                        @if($sortField === 'titulo') <span class="text-ep-primary text-[10px]">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                    </div>
                                </th>
                                <th class="hidden md:table-cell px-4 lg:px-6 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Sede</span></th>
                                <th class="px-4 lg:px-6 py-6 text-center cursor-pointer" wire:click="sortBy('criticidad')">
                                        <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Riesgo</span>
                                </th>
                                <th class="px-4 lg:px-6 py-6 text-center cursor-pointer" wire:click="sortBy('estado')">
                                        <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Status</span>
                                </th>
                                <th class="hidden xl:table-cell px-4 lg:px-6 py-6 text-center cursor-pointer" wire:click="sortBy('plazo_correccion')">
                                        <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Plazo</span>
                                </th>
                                <th class="hidden lg:table-cell px-4 lg:px-6 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Docs</span></th>
                                <th class="px-4 lg:px-6 py-6 text-right"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Comandos</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($hallazgos as $hallazgo)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative {{ $hallazgo->estaVencido() ? 'bg-red-500/5' : '' }}">
                                    <td class="px-4 lg:px-6 py-6 lg:py-8">
                                        <div class="text-xs sm:text-sm font-black text-white italic uppercase tracking-tight leading-tight mb-1 lg:group-hover/row:text-ep-primary transition-colors">{{ $hallazgo->titulo }}</div>
                                        <div class="text-[8px] sm:text-[10px] font-bold text-white/30 italic max-w-xs truncate">{{ $hallazgo->descripcion }}</div>
                                        @if($hallazgo->ubicacion_especifica)
                                            <div class="text-[7px] sm:text-[9px] font-black text-indigo-400/60 mt-2 uppercase italic tracking-widest leading-none">📍 {{ $hallazgo->ubicacion_especifica }}</div>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell px-4 lg:px-6 py-6 lg:py-8">
                                        <div class="text-[10px] font-black text-white/60 italic uppercase tracking-tight leading-none">{{ $hallazgo->sede->nombre_sede ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6 lg:py-8 text-center">
                                        @php
                                            $critColor = match($hallazgo->criticidad) {
                                                'critico' => 'text-red-500 bg-red-500/10 border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.3)] animate-pulse',
                                                'alto' => 'text-orange-500 bg-orange-500/10 border-orange-500/20',
                                                'medio' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                                default => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'
                                            };
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest italic border {{ $critColor }}">
                                            {{ strtoupper($hallazgo->criticidad) }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6 lg:py-8 text-center">
                                        @php
                                            $stateColor = match($hallazgo->estado) {
                                                'cerrado' => 'text-emerald-400 border-emerald-500/20',
                                                'verificado' => 'text-blue-400 border-blue-500/20',
                                                'resuelto' => 'text-cyan-400 border-cyan-500/20',
                                                'en_proceso' => 'text-amber-400 border-amber-500/20',
                                                default => 'text-red-500 border-red-500/20'
                                            };
                                            $stateLabel = strtoupper(str_replace('_', ' ', $hallazgo->estado));
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest italic border {{ $stateColor }} bg-black/20">
                                            {{ $stateLabel }}
                                        </span>
                                    </td>
                                    <td class="hidden xl:table-cell px-4 lg:px-6 py-6 lg:py-8 text-center">
                                        @if($hallazgo->plazo_correccion)
                                            <div class="text-[10px] font-black italic {{ $hallazgo->estaVencido() ? 'text-red-500 animate-pulse' : 'text-white/60' }}">
                                                {{ $hallazgo->plazo_correccion->format('d/m/y') }}
                                            </div>
                                        @else
                                            <span class="text-white/10 italic text-[10px]">-</span>
                                        @endif
                                    </td>
                                    <td class="hidden lg:table-cell px-4 lg:px-6 py-6 lg:py-8 text-center">
                                        <div class="flex justify-center items-center gap-4">
                                            @if($hallazgo->fotos_count > 0)
                                                <div class="flex items-center gap-1.5" title="{{ $hallazgo->fotos_count }} Fotos">
                                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="text-[10px] font-black text-indigo-400 leading-none">{{ $hallazgo->fotos_count }}</span>
                                                </div>
                                            @endif
                                            @if($hallazgo->seguimientos_count > 0)
                                                <div class="flex items-center gap-1.5" title="{{ $hallazgo->seguimientos_count }} Seguimientos">
                                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="text-[10px] font-black text-emerald-400 leading-none">{{ $hallazgo->seguimientos_count }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6 lg:py-8 text-right">
                                        <div class="flex justify-end gap-2 sm:gap-3 transition-all duration-500">
                                            <a href="{{ route('gestor.hallazgos.show', $hallazgo) }}" class="h-9 w-9 sm:h-10 sm:w-10 bg-ep-primary/10 border border-ep-primary/20 text-ep-primary hover:bg-ep-primary hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Auditar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <button wire:click="delete({{ $hallazgo->id }})" wire:confirm="¿Desea purgar este hallazgo y sus evidencias del sistema?" class="h-9 w-9 sm:h-10 sm:w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectaron hallazgos <br> <span class="text-[9px] tracking-widest text-white/3 italic">Ajuste los filtros para ampliar la búsqueda</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical overflow-x-auto">
                    {{ $hallazgos->links() }}
                </div>
            </div>

            
        </div>
    </div>

    {{-- NOTIFICACIONES HUD --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-8 right-8 bg-emerald-500/10 border border-emerald-500/20 backdrop-blur-xl text-emerald-400 px-8 py-4 rounded-2xl shadow-[0_0_30px_rgba(16,185,129,0.2)] z-50 flex items-center gap-3 animate-fade-in-up">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
            <span class="text-[10px] font-black uppercase tracking-widest italic">{{ session('message') }}</span>
        </div>
    @endif

    <style>
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important; margin: 0 4px !important; padding: 8px 14px !important;
            font-weight: 900 !important; font-size: 10px !important; transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(246,146,57,0.3) !important;
            color: #f69239 !important;
            background: rgba(246,146,57,0.1) !important;
        }
    </style>
</div>

