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
                        <h1 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic leading-none mb-1">
                            Centro de <span class="text-white">Entrenamiento</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestión de Capacitaciones
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
            
            {{-- HUB DE COMANDOS: FILTROS Y ACCIÓN MAESTRA --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end relative z-10">
                    {{-- BUSCADOR TÁCTICO --}}
                    <div class="md:col-span-5 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Terminal de Escaneo</span>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="BUSCAR ENTRENAMIENTO O INSTRUCTOR..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-500/30 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- FILTROS DE INTELIGENCIA --}}
                    <div class="md:col-span-4 grid grid-cols-2 gap-4">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Empresa</span>
                            <select wire:model.live="empresaFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer">
                                <option value="">TODAS</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede</span>
                            <select wire:model.live="sedeFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer {{ !$empresaFilter ? 'opacity-20 cursor-not-allowed' : '' }}" {{ !$empresaFilter ? 'disabled' : '' }}>
                                <option value="">TODAS</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ACCIÓN PRIMARIA (FUERA DEL BANNER) --}}
                    <div class="md:col-span-3">
                        <a href="{{ route('gestor.trainings.create') }}" 
                           class="flex h-14 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-3 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-14 group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                            <svg class="w-5 h-5 group-hover/btn:rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="relative z-10">Asignar Capacitación</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- MATRIZ DE ENTRENAMIENTO --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Capacitación / Instructor</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Responsable Unidad</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Punto de Despliegue</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Cronograma</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Asistencia</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Status HUD</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($trainings as $training)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="relative group/icon">
                                                <div class="w-12 h-12 rounded-2xl {{ ($training->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} border flex items-center justify-center text-xs font-black group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" stroke-width="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke-width="2"/></svg>
                                                </div>
                                                @if(($training->source ?? 'manual') === 'contract')
                                                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-indigo-500 rounded-full border-2 border-[#11111d] animate-pulse"></div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1.5">
                                                    <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-indigo-400 transition-colors">
                                                        {{ $training->title }}
                                                    </div>
                                                    @if(($training->source ?? 'manual') === 'contract')
                                                        <span class="px-2 py-0.5 rounded-lg border border-indigo-500/30 bg-indigo-500/10 text-[8px] font-black text-indigo-400 uppercase italic tracking-widest leading-none">🚢 CONTRATO</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-[8px] font-black text-emerald-400 uppercase italic tracking-widest leading-none">⚡ SOLICITUD</span>
                                                    @endif
                                                </div>
                                                <div class="text-[9px] font-black text-white/20 uppercase tracking-widest italic flex items-center gap-2">
                                                    @if($training->registry_code)
                                                        <span class="font-mono text-white/30">[{{ $training->registry_code }}]</span> • 
                                                    @endif
                                                    Instructor: <span class="text-white/40">{{ $training->trainer_name ?: 'NO ASIGNADO' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            @if($training->responsibleUser)
                                                <div class="w-8 h-8 rounded-xl border {{ ($training->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} flex items-center justify-center text-[10px] font-black">
                                                    {{ substr($training->responsibleUser->nombres ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-white/60 italic uppercase leading-none">{{ $training->responsibleUser->nombres }} {{ $training->responsibleUser->apellidos }}</div>
                                                    <div class="text-[9px] font-bold text-white/20 italic mt-0.5">{{ $training->responsibleUser->email }}</div>
                                                </div>
                                            @else
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic leading-none mb-1">Técnico Sede</span>
                                                    <span class="text-[8px] font-bold text-white/10 uppercase tracking-widest italic">Asignación Directa</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-black text-white italic uppercase tracking-tight leading-none mb-1 group-hover/row:text-ep-primary transition-colors">{{ $training->empresa->nombre ?? 'N/A' }}</div>
                                        <div class="text-[9px] font-bold text-white/20 italic tracking-widest uppercase">{{ $training->sede->nombre_sede ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2 text-[11px] font-black text-white/60 italic">
                                                <svg class="w-3.5 h-3.5 text-indigo-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $training->date->format('d/m/Y') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-black {{ ($training->source ?? 'manual') === 'contract' ? 'text-indigo-400' : 'text-emerald-400' }} uppercase italic">{{ $training->duration ?? '60 min' }}</span>
                                                <span class="text-[10px] text-white/20">•</span>
                                                <span class="text-[9px] font-bold text-white/30 uppercase italic tracking-tighter">{{ $training->type ?? 'Presencial' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if(isset($training->attendances_count) || isset($training->view_attendances_count))
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                {{ $training->attendances_count ?? 0 }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-black text-white/5 uppercase italic tracking-widest leading-none">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $statusBadge = match($training->status) {
                                                'scheduled' => 'text-amber-400 border-amber-500/30 bg-amber-500/10 shadow-[0_0_10px_rgba(245,158,11,0.1)]',
                                                'completed' => 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                                                'cancelled' => 'text-red-400 border-red-500/30 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.1)]',
                                                default => 'text-white/20 border-white/5 bg-white/5'
                                            };
                                            $statusText = match($training->status) {
                                                'scheduled' => 'PROGRAMADA',
                                                'completed' => 'FINALIZADA',
                                                'cancelled' => 'CANCELADA',
                                                default => strtoupper($training->status)
                                            };
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border {{ $statusBadge }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            @if(($training->source ?? 'manual') === 'contract')
                                                <a href="{{ route('gestor.annual-contract.index') }}" class="h-10 px-4 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg text-[9px] font-black uppercase italic tracking-widest">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    Ver Plan Maestro
                                                </a>
                                            @else
                                                <a href="{{ route('gestor.trainings.pdf.preview', $training->id) }}" target="_blank" class="h-10 w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Ver Reporte">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                                <a href="{{ route('gestor.trainings.edit', $training->id) }}" class="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <button wire:click="delete({{ $training->id }})" 
                                                        wire:confirm="¿Estás seguro de eliminar esta capacitación?"
                                                        class="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">Sin registros de entrenamiento <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inyecte una nueva capacitación para iniciar el seguimiento</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $trainings->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important; margin: 0 4px !important; padding: 8px 14px !important;
            font-weight: 900 !important; font-size: 10px !important; transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(99,102,241,0.3) !important;
            color: #6366f1 !important;
            background: rgba(99,102,241,0.1) !important;
        }
    </style>
</div>
